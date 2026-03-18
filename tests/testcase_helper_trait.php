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

/**
 * Trait for Anthropic test cases.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait testcase_helper_trait {
    /**
     * Create a provider instance for a given action.
     *
     * @param string $actionclass Action class.
     * @param array $actionconfig Action settings override.
     * @return \core_ai\provider
     */
    public function create_provider(
        string $actionclass,
        array $actionconfig = [],
    ): \core_ai\provider {
        $manager = \core\di::get(\core_ai\manager::class);
        $config = [
            'enableuserratelimit' => true,
            'userratelimit' => 1,
            'enableglobalratelimit' => true,
            'globalratelimit' => 1,
            'apikey' => 'anthropic-key-123',
        ];
        $defaultactionconfig = [
            $actionclass => [
                'settings' => [
                    'model' => 'claude-sonnet-4-6',
                    'endpoint' => 'https://api.anthropic.com/v1/messages',
                    'max_tokens' => 2048,
                ],
            ],
        ];

        foreach ($actionconfig as $key => $value) {
            $defaultactionconfig[$actionclass]['settings'][$key] = $value;
        }

        return $manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: $config,
            actionconfig: $defaultactionconfig,
        );
    }
}
