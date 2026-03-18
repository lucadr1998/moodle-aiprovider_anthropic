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

use GuzzleHttp\Psr7\Response;

/**
 * Test Anthropic helper model discovery and caching.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers     \aiprovider_anthropic\helper
 */
final class helper_test extends \advanced_testcase {
    /**
     * Test live model discovery populates the option values with model IDs and fills cache.
     */
    public function test_get_available_models_fetches_ids_and_caches_them(): void {
        $this->resetAfterTest();
        \cache_helper::purge_by_definition('aiprovider_anthropic', 'models');

        $manager = \core\di::get(\core_ai\manager::class);
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: ['apikey' => 'anthropic-key-123'],
        );

        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode([
                'data' => [
                    [
                        'id' => 'claude-opus-4-6',
                        'created_at' => '2026-02-04T00:00:00Z',
                        'display_name' => 'Claude Opus 4.6',
                        'type' => 'model',
                    ],
                    [
                        'id' => 'claude-sonnet-4-6',
                        'created_at' => '2026-02-17T00:00:00Z',
                        'display_name' => 'Claude Sonnet 4.6',
                        'type' => 'model',
                    ],
                ],
                'first_id' => 'claude-opus-4-6',
                'has_more' => false,
                'last_id' => 'claude-sonnet-4-6',
            ]),
        ));

        $models = helper::get_available_models($provider->id);

        $this->assertArrayHasKey('claude-opus-4-6', $models);
        $this->assertArrayHasKey('claude-sonnet-4-6', $models);
        $this->assertEquals('Claude Opus 4.6 (claude-opus-4-6)', $models['claude-opus-4-6']);
        $this->assertEquals('claude-opus-4-6', helper::get_default_model_id($provider->id));

        $cache = \cache::make('aiprovider_anthropic', 'models');
        $cached = $cache->get((string) $provider->id);
        $this->assertNotEmpty($cached['models']);
        $this->assertEquals('claude-opus-4-6', $cached['models'][0]['id']);
    }

    /**
     * Test stale cache is reused when the Anthropic API call fails.
     */
    public function test_get_available_models_uses_stale_cache_on_failure(): void {
        $this->resetAfterTest();
        \cache_helper::purge_by_definition('aiprovider_anthropic', 'models');

        $manager = \core\di::get(\core_ai\manager::class);
        $provider = $manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: ['apikey' => 'anthropic-key-123'],
        );

        $cache = \cache::make('aiprovider_anthropic', 'models');
        $cache->set((string) $provider->id, [
            'fetchedat' => time() - helper::MODEL_CACHE_FRESHNESS - 10,
            'models' => [
                [
                    'id' => 'claude-haiku-4-5-20251001',
                    'display_name' => 'Claude Haiku 4.5',
                ],
            ],
        ]);

        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            500,
            ['Content-Type' => 'application/json'],
            json_encode(['error' => ['message' => 'Server error']]),
        ));

        $models = helper::get_available_models($provider->id);

        $this->assertArrayHasKey('claude-haiku-4-5-20251001', $models);
        $this->assertEquals(
            'Claude Haiku 4.5 (claude-haiku-4-5-20251001)',
            $models['claude-haiku-4-5-20251001'],
        );
    }

    /**
     * Test generic Anthropic settings are returned for dynamically discovered model ids.
     */
    public function test_get_model_settings_for_dynamic_model_uses_generic_schema(): void {
        $settings = helper::get_model_settings_for_model('claude-sonnet-4-5-20250929');

        $this->assertArrayHasKey('max_tokens', $settings);
        $this->assertArrayHasKey('temperature', $settings);
        $this->assertArrayHasKey('top_p', $settings);
        $this->assertArrayHasKey('top_k', $settings);
        $this->assertEquals(2048, $settings['max_tokens']['default']);
    }

    /**
     * Test dynamic Anthropic model ids resolve to the generic text aimodel class.
     */
    public function test_get_model_class_returns_generic_text_model_for_claude_ids(): void {
        $model = helper::get_model_class('claude-sonnet-4-5-20250929');

        $this->assertInstanceOf(\aiprovider_anthropic\aimodel\anthropic_text::class, $model);
    }
}
