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

use core_ai\hook\after_ai_action_settings_form_hook;
use core_ai\hook\after_ai_provider_form_hook;

/**
 * Hook listener for Anthropic provider forms.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_listener {
    /**
     * Add provider configuration fields.
     *
     * @param after_ai_provider_form_hook $hook Hook payload.
     */
    public static function set_form_definition_for_aiprovider_anthropic(after_ai_provider_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_anthropic') {
            return;
        }

        $mform = $hook->mform;
        $mform->addElement(
            'passwordunmask',
            'apikey',
            get_string('apikey', 'aiprovider_anthropic'),
            ['size' => 75],
        );
        $mform->addHelpButton('apikey', 'apikey', 'aiprovider_anthropic');
        $mform->addRule('apikey', get_string('required'), 'required', null, 'client');
    }

    /**
     * Add model-specific action settings.
     *
     * @param after_ai_action_settings_form_hook $hook Hook payload.
     */
    public static function set_model_form_definition_for_aiprovider_anthropic(after_ai_action_settings_form_hook $hook): void {
        if ($hook->plugin !== 'aiprovider_anthropic') {
            return;
        }

        $mform = $hook->mform;
        if (!isset($mform->_elementIndex['modeltemplate'])) {
            return;
        }

        $model = $mform->getElementValue('modeltemplate');
        if (is_array($model)) {
            $model = $model[0];
        }

        if ($model == 'custom') {
            $mform->addElement('header', 'modelsettingsheader', get_string('settings', 'aiprovider_anthropic'));
            $settingshelp = \html_writer::tag('p', get_string('settings_help', 'aiprovider_anthropic'));
            $mform->addElement('html', $settingshelp);
            $mform->addElement(
                'textarea',
                'modelextraparams',
                get_string('extraparams', 'aiprovider_anthropic'),
                ['rows' => 5, 'cols' => 20],
            );
            $mform->setType('modelextraparams', PARAM_TEXT);
            $mform->addElement('static', 'modelextraparams_help', null, get_string('extraparams_help', 'aiprovider_anthropic'));
            return;
        }

        $modelsettings = helper::get_model_settings_for_model($model);
        if (!empty($modelsettings)) {
            $mform->addElement('header', 'modelsettingsheader', get_string('settings', 'aiprovider_anthropic'));
            $settingshelp = \html_writer::tag('p', get_string('settings_help', 'aiprovider_anthropic'));
            $mform->addElement('html', $settingshelp);
            helper::add_model_settings($mform, $model);
        }
    }
}
