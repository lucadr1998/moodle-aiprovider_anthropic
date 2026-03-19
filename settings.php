<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin administration pages are defined here.
 *
 * @package     aiprovider_anthropic
 * @copyright   2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_ai\admin\admin_settingspage_provider;

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingspage_provider(
        'aiprovider_anthropic',
        new lang_string('pluginname', 'aiprovider_anthropic'),
        'moodle/site:config',
        true,
    );

    $settings->add(new admin_setting_heading(
        'aiprovider_anthropic/general',
        new lang_string('settings', 'core'),
        '',
    ));

    // Anthropic API key.
    $settings->add(new admin_setting_configpasswordunmask(
        'aiprovider_anthropic/apikey',
        new lang_string('apikey', 'aiprovider_anthropic'),
        new lang_string('apikey_desc', 'aiprovider_anthropic'),
        '',
    ));

    // Global rate limiting.
    $settings->add(new admin_setting_configcheckbox(
        'aiprovider_anthropic/enableglobalratelimit',
        new lang_string('enableglobalratelimit', 'aiprovider_anthropic'),
        new lang_string('enableglobalratelimit_desc', 'aiprovider_anthropic'),
        0,
    ));

    $settings->add(new admin_setting_configtext(
        'aiprovider_anthropic/globalratelimit',
        new lang_string('globalratelimit', 'aiprovider_anthropic'),
        new lang_string('globalratelimit_desc', 'aiprovider_anthropic'),
        100,
        PARAM_INT,
    ));
    $settings->hide_if('aiprovider_anthropic/globalratelimit', 'aiprovider_anthropic/enableglobalratelimit', 'eq', 0);

    // User rate limiting.
    $settings->add(new admin_setting_configcheckbox(
        'aiprovider_anthropic/enableuserratelimit',
        new lang_string('enableuserratelimit', 'aiprovider_anthropic'),
        new lang_string('enableuserratelimit_desc', 'aiprovider_anthropic'),
        0,
    ));

    $settings->add(new admin_setting_configtext(
        'aiprovider_anthropic/userratelimit',
        new lang_string('userratelimit', 'aiprovider_anthropic'),
        new lang_string('userratelimit_desc', 'aiprovider_anthropic'),
        10,
        PARAM_INT,
    ));
    $settings->hide_if('aiprovider_anthropic/userratelimit', 'aiprovider_anthropic/enableuserratelimit', 'eq', 0);
}
