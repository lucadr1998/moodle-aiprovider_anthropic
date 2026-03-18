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

/**
 * Strings for component aiprovider_anthropic, language 'en'.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:endpoint'] = 'API endpoint';
$string['action:explain_text:model'] = 'Text explanation model';
$string['action:explain_text:model_help'] = 'The model used to explain the provided text.';
$string['action:explain_text:systeminstruction'] = 'System instruction';
$string['action:explain_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:generate_text:endpoint'] = 'API endpoint';
$string['action:generate_text:model'] = 'AI model';
$string['action:generate_text:model_help'] = 'The model used to generate the text response.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:endpoint'] = 'API endpoint';
$string['action:summarise_text:model'] = 'AI model';
$string['action:summarise_text:model_help'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_help'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey'] = 'Anthropic API key';
$string['apikey_help'] = 'Get a key from your <a href="https://platform.claude.com/settings/keys" target="_blank">Claude Console API keys</a>.';
$string['cachedef_models'] = 'Cached Anthropic model discovery results';
$string['custom_model_name'] = 'Custom model name';
$string['extraparams'] = 'Extra parameters';
$string['extraparams_help'] = 'Extra parameters can be configured here. We support JSON format. For example:
<pre>
{
    "max_tokens": 2048,
    "temperature": 0.3
}
</pre>';
$string['invalidjson'] = 'Invalid JSON string';
$string['pluginname'] = 'Anthropic API provider';
$string['privacy:metadata'] = 'The Anthropic API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'This information is sent to the Anthropic API in order for a response to be generated. Your Anthropic account settings may change how Anthropic stores and retains this data. No user data is explicitly sent to Anthropic or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['settings'] = 'Settings';
$string['settings_help'] = 'Adjust the settings below to customise how requests are sent to Anthropic.';
$string['settings_max_tokens'] = 'max_tokens';
$string['settings_max_tokens_help'] = 'Maximum number of tokens to generate. Anthropic requires this field, and the default is 2048.';
$string['settings_temperature'] = 'temperature';
$string['settings_temperature_help'] = 'Amount of randomness injected into the response. Lower values are more deterministic.';
$string['settings_top_k'] = 'top_k';
$string['settings_top_k_help'] = 'Only sample from the top K options for each subsequent token. Recommended for advanced use cases only.';
$string['settings_top_p'] = 'top_p';
$string['settings_top_p_help'] = 'Use nucleus sampling. In general, alter either temperature or top_p, but not both.';
