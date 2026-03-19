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

$string['action:generate_text:endpoint'] = 'API endpoint';
$string['action:generate_text:maxtokens'] = 'Max tokens';
$string['action:generate_text:maxtokens_desc'] = 'Maximum number of tokens to generate. Anthropic requires this field.';
$string['action:generate_text:model'] = 'AI model';
$string['action:generate_text:model_desc'] = 'The model used to generate the text response.';
$string['action:generate_text:systeminstruction'] = 'System instruction';
$string['action:generate_text:systeminstruction_desc'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['action:summarise_text:endpoint'] = 'API endpoint';
$string['action:summarise_text:maxtokens'] = 'Max tokens';
$string['action:summarise_text:maxtokens_desc'] = 'Maximum number of tokens to generate. Anthropic requires this field.';
$string['action:summarise_text:model'] = 'AI model';
$string['action:summarise_text:model_desc'] = 'The model used to summarise the provided text.';
$string['action:summarise_text:systeminstruction'] = 'System instruction';
$string['action:summarise_text:systeminstruction_desc'] = 'This instruction is sent to the AI model along with the user\'s prompt. Editing this instruction is not recommended unless absolutely required.';
$string['apikey'] = 'Anthropic API key';
$string['apikey_desc'] = 'Get a key from your <a href="https://console.anthropic.com/settings/keys" target="_blank">Anthropic Console API keys</a>.';
$string['enableglobalratelimit'] = 'Set site-wide rate limit';
$string['enableglobalratelimit_desc'] = 'Limit the number of requests that the Anthropic API provider can receive across the entire site every hour.';
$string['enableuserratelimit'] = 'Set user rate limit';
$string['enableuserratelimit_desc'] = 'Limit the number of requests each user can make to the Anthropic API provider every hour.';
$string['globalratelimit'] = 'Maximum number of site-wide requests';
$string['globalratelimit_desc'] = 'The number of site-wide requests allowed per hour.';
$string['pluginname'] = 'Anthropic API provider';
$string['privacy:metadata'] = 'The Anthropic API provider plugin does not store any personal data.';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'This information is sent to the Anthropic API in order for a response to be generated. Your Anthropic account settings may change how Anthropic stores and retains this data. No user data is explicitly sent to Anthropic or stored in Moodle LMS by this plugin.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'The model used to generate the response.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'The user entered text prompt used to generate the response.';
$string['userratelimit'] = 'Maximum number of requests per user';
$string['userratelimit_desc'] = 'The number of requests allowed per hour, per user.';
