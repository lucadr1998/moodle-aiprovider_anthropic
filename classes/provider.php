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

use core_ai\aiactions;
use core_ai\rate_limiter;
use Psr\Http\Message\RequestInterface;

/**
 * Anthropic AI provider.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider extends \core_ai\provider {
    /** @var string The Anthropic API key. */
    private string $apikey;

    /** @var bool Is global rate limiting enabled. */
    private bool $enableglobalratelimit;

    /** @var int The global rate limit. */
    private int $globalratelimit;

    /** @var bool Is user rate limiting enabled. */
    private bool $enableuserratelimit;

    /** @var int The user rate limit. */
    private int $userratelimit;

    /**
     * Class constructor.
     */
    public function __construct() {
        $this->apikey = get_config('aiprovider_anthropic', 'apikey');
        $this->enableglobalratelimit = get_config('aiprovider_anthropic', 'enableglobalratelimit');
        $this->globalratelimit = get_config('aiprovider_anthropic', 'globalratelimit');
        $this->enableuserratelimit = get_config('aiprovider_anthropic', 'enableuserratelimit');
        $this->userratelimit = get_config('aiprovider_anthropic', 'userratelimit');
    }

    #[\Override]
    public function get_action_list(): array {
        return [
            \core_ai\aiactions\generate_text::class,
            \core_ai\aiactions\summarise_text::class,
        ];
    }

    /**
     * Generate a hashed user id.
     *
     * @param string $userid The user id.
     * @return string The generated user id hash.
     */
    public function generate_userid(string $userid): string {
        global $CFG;
        return hash('sha256', $CFG->siteidentifier . $userid);
    }

    /**
     * Add authentication headers to the request.
     *
     * @param RequestInterface $request The request object.
     * @return RequestInterface The request with authentication headers.
     */
    public function add_authentication_headers(RequestInterface $request): RequestInterface {
        return $request
            ->withAddedHeader('x-api-key', $this->apikey)
            ->withAddedHeader('anthropic-version', '2023-06-01');
    }

    #[\Override]
    public function is_request_allowed(aiactions\base $action): array|bool {
        $ratelimiter = \core\di::get(rate_limiter::class);
        $component = \core\component::get_component_from_classname(get_class($this));

        if ($this->enableuserratelimit) {
            if (!$ratelimiter->check_user_rate_limit(
                component: $component,
                ratelimit: $this->userratelimit,
                userid: $action->get_configuration('userid')
            )) {
                return [
                    'success' => false,
                    'errorcode' => 429,
                    'errormessage' => 'User rate limit exceeded',
                ];
            }
        }

        if ($this->enableglobalratelimit) {
            if (!$ratelimiter->check_global_rate_limit(
                component: $component,
                ratelimit: $this->globalratelimit
            )) {
                return [
                    'success' => false,
                    'errorcode' => 429,
                    'errormessage' => 'Global rate limit exceeded',
                ];
            }
        }

        return true;
    }

    #[\Override]
    public function get_action_settings(
        string $action,
        \admin_root $ADMIN,
        string $section,
        bool $hassiteconfig
    ): array {
        $actionname = substr($action, (strrpos($action, '\\') + 1));
        $settings = [];
        if ($actionname === 'generate_text' || $actionname === 'summarise_text') {
            // Model setting.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_anthropic/action_{$actionname}_model",
                new \lang_string("action:{$actionname}:model", 'aiprovider_anthropic'),
                new \lang_string("action:{$actionname}:model_desc", 'aiprovider_anthropic'),
                'claude-sonnet-4-6',
                PARAM_TEXT,
            );
            // API endpoint.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_anthropic/action_{$actionname}_endpoint",
                new \lang_string("action:{$actionname}:endpoint", 'aiprovider_anthropic'),
                '',
                'https://api.anthropic.com/v1/messages',
                PARAM_URL,
            );
            // System instruction.
            $settings[] = new \admin_setting_configtextarea(
                "aiprovider_anthropic/action_{$actionname}_systeminstruction",
                new \lang_string("action:{$actionname}:systeminstruction", 'aiprovider_anthropic'),
                new \lang_string("action:{$actionname}:systeminstruction_desc", 'aiprovider_anthropic'),
                $action::get_system_instruction(),
                PARAM_TEXT,
            );
            // Max tokens.
            $settings[] = new \admin_setting_configtext(
                "aiprovider_anthropic/action_{$actionname}_maxtokens",
                new \lang_string("action:{$actionname}:maxtokens", 'aiprovider_anthropic'),
                new \lang_string("action:{$actionname}:maxtokens_desc", 'aiprovider_anthropic'),
                2048,
                PARAM_INT,
            );
        }

        return $settings;
    }

    #[\Override]
    public function is_provider_configured(): bool {
        return !empty($this->apikey);
    }
}
