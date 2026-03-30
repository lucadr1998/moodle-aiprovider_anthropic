<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace aiprovider_anthropic;

use aiprovider_anthropic\aimodel\anthropic_base;
use aiprovider_anthropic\aimodel\anthropic_text;
use core\http_client;
use core_ai\aimodel\base;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use MoodleQuickForm;

/**
 * Helper class for the Anthropic provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {
    /** @var int Freshness window for model discovery in seconds. */
    public const MODEL_CACHE_FRESHNESS = 43200;

    /**
     * Get all model classes.
     *
     * @return array
     */
    public static function get_model_classes(): array {
        $models = [];
        $modelclasses = \core_component::get_component_classes_in_namespace('aiprovider_anthropic', 'aimodel');
        foreach ($modelclasses as $class => $path) {
            if (!class_exists($class) || !is_a($class, base::class, true)) {
                throw new \coding_exception("Model class not valid: {$class}");
            }
            $models[] = $class;
        }

        return $models;
    }

    /**
     * Get model class by model name.
     *
     * @param string $modelname Model name.
     * @return base|null
     */
    public static function get_model_class(string $modelname): ?base {
        foreach (static::get_model_classes() as $classname) {
            $model = new $classname();
            if ($model instanceof anthropic_base && $model->supports_model($modelname)) {
                return $model;
            }
        }

        return null;
    }

    /**
     * Get the model list for the form.
     *
     * Values are real model IDs. Labels remain human-readable.
     *
     * @param int $providerid Provider instance id.
     * @param int $modeltype Model type.
     * @return array
     */
    public static function get_model_options(int $providerid, int $modeltype): array {
        $models = ['custom' => get_string('custom', 'core_form')];
        if ($modeltype !== anthropic_base::MODEL_TYPE_TEXT) {
            return $models;
        }

        foreach (self::get_available_models($providerid) as $id => $displayname) {
            $models[$id] = $displayname;
        }

        return $models;
    }

    /**
     * Resolve the preferred default model id.
     *
     * @param int $providerid Provider instance id.
     * @return string
     */
    public static function get_default_model_id(int $providerid): string {
        $models = self::get_available_models($providerid);
        if (!empty($models)) {
            return array_key_first($models);
        }

        return self::get_fallback_models()[0]['id'];
    }

    /**
     * Fetch available model IDs and labels, using API + cache + static fallback.
     *
     * @param int $providerid Provider instance id.
     * @return array
     */
    public static function get_available_models(int $providerid): array {
        $cached = self::get_cached_models($providerid);
        if (self::is_cache_fresh($cached)) {
            return self::to_model_options($cached['models']);
        }

        $models = self::fetch_models_from_api($providerid);
        if (!empty($models)) {
            self::store_cached_models($providerid, $models);
            return self::to_model_options($models);
        }

        if (!empty($cached['models'])) {
            return self::to_model_options($cached['models']);
        }

        return self::to_model_options(self::get_fallback_models());
    }

    /**
     * Return the generic Anthropic settings definition.
     *
     * @return array
     */
    public static function get_text_model_settings(): array {
        return self::get_text_model()->get_model_settings();
    }

    /**
     * Get settings for a selected model.
     *
     * @param string $modelname Selected model id.
     * @return array
     */
    public static function get_model_settings_for_model(string $modelname): array {
        if ($modelname === '' || $modelname === 'custom') {
            return [];
        }

        $modelclass = self::get_model_class($modelname) ?? self::get_text_model();
        return $modelclass->get_model_settings();
    }

    /**
     * Add generic Anthropic settings to the form.
     *
     * @param MoodleQuickForm $mform Form instance.
     * @param string $modelname Selected model id.
     * @return void
     */
    public static function add_model_settings(MoodleQuickForm $mform, string $modelname): void {
        $modelclass = self::get_model_class($modelname) ?? self::get_text_model();
        if ($modelclass->has_model_settings()) {
            $modelclass->add_model_settings($mform);
        }
    }

    /**
     * Get the generic Anthropic text model definition.
     *
     * @return anthropic_text
     */
    protected static function get_text_model(): anthropic_text {
        return new anthropic_text();
    }

    /**
     * Fetch the model list from Anthropic.
     *
     * @param int $providerid Provider instance id.
     * @return array
     */
    protected static function fetch_models_from_api(int $providerid): array {
        $provider = self::get_provider_instance($providerid);
        if (!$provider || empty($provider->config['apikey'])) {
            return [];
        }

        $client = \core\di::get(http_client::class);
        $models = [];
        $afterid = null;

        try {
            do {
                $query = $afterid ? ('?' . http_build_query(['after_id' => $afterid, 'limit' => 100])) : '?limit=100';
                $request = new Request(
                    method: 'GET',
                    uri: 'https://api.anthropic.com/v1/models' . $query,
                );
                $request = $provider->add_authentication_headers($request);

                $response = $client->send($request, [
                    RequestOptions::HTTP_ERRORS => false,
                ]);
                if ($response->getStatusCode() !== 200) {
                    return [];
                }

                $payload = json_decode($response->getBody()->getContents());
                if (empty($payload->data) || !is_array($payload->data)) {
                    return [];
                }

                foreach ($payload->data as $modelinfo) {
                    $id = $modelinfo->id ?? null;
                    $displayname = $modelinfo->display_name ?? null;
                    if (!$id || !str_starts_with($id, 'claude-')) {
                        continue;
                    }

                    $models[$id] = [
                        'id' => $id,
                        'display_name' => $displayname ?: $id,
                    ];
                }

                $hasmore = !empty($payload->has_more);
                $afterid = $payload->last_id ?? null;
            } while ($hasmore && !empty($afterid));
        } catch (\Exception $e) {
            return [];
        }

        return array_values($models);
    }

    /**
     * Load cached model data for a provider.
     *
     * @param int $providerid Provider instance id.
     * @return array
     */
    protected static function get_cached_models(int $providerid): array {
        $cache = \cache::make('aiprovider_anthropic', 'models');
        $cached = $cache->get((string) $providerid);
        if (!is_array($cached)) {
            return [];
        }

        return $cached;
    }

    /**
     * Determine whether cached data is still fresh.
     *
     * @param array $cached Cached payload.
     * @return bool
     */
    protected static function is_cache_fresh(array $cached): bool {
        if (empty($cached['fetchedat']) || empty($cached['models']) || !is_array($cached['models'])) {
            return false;
        }

        return (time() - (int) $cached['fetchedat']) < self::MODEL_CACHE_FRESHNESS;
    }

    /**
     * Persist discovered models to cache.
     *
     * @param int $providerid Provider instance id.
     * @param array $models Model payload.
     * @return void
     */
    protected static function store_cached_models(int $providerid, array $models): void {
        $cache = \cache::make('aiprovider_anthropic', 'models');
        $cache->set((string) $providerid, [
            'fetchedat' => time(),
            'models' => array_values($models),
        ]);
    }

    /**
     * Convert raw model payload into form options.
     *
     * @param array $models Model payload.
     * @return array
     */
    protected static function to_model_options(array $models): array {
        $options = [];
        foreach ($models as $model) {
            if (empty($model['id'])) {
                continue;
            }
            $displayname = $model['display_name'] ?? $model['id'];
            $options[$model['id']] = $displayname . ' (' . $model['id'] . ')';
        }

        return $options;
    }

    /**
     * Return a static fallback list when API discovery is unavailable.
     *
     * @return array Raw model payload compatible with {@see to_model_options()}.
     */
    protected static function get_fallback_models(): array {
        return [
            ['id' => 'claude-opus-4-6', 'display_name' => 'Claude Opus 4.6'],
            ['id' => 'claude-sonnet-4-6', 'display_name' => 'Claude Sonnet 4.6'],
            ['id' => 'claude-haiku-4-5-20251001', 'display_name' => 'Claude Haiku 4.5'],
        ];
    }

    /**
     * Get the provider instance by id.
     *
     * @param int $providerid Provider instance id.
     * @return provider|null
     */
    protected static function get_provider_instance(int $providerid): ?provider {
        if (empty($providerid)) {
            return null;
        }

        $manager = \core\di::get(\core_ai\manager::class);
        $providers = $manager->get_provider_instances(['id' => $providerid]);
        $provider = reset($providers);
        if (!$provider instanceof provider) {
            return null;
        }

        return $provider;
    }
}
