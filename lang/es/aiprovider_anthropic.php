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
 * Strings for component aiprovider_anthropic, language 'es'.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:generate_text:endpoint'] = 'Punto de conexión de la API';
$string['action:generate_text:maxtokens'] = 'Número máximo de tokens';
$string['action:generate_text:maxtokens_desc'] = 'Número máximo de tokens que se generarán. Anthropic requiere este campo.';
$string['action:generate_text:model'] = 'Modelo de IA';
$string['action:generate_text:model_desc'] = 'El modelo utilizado para generar la respuesta de texto.';
$string['action:generate_text:systeminstruction'] = 'Instrucción del sistema';
$string['action:generate_text:systeminstruction_desc'] = 'Esta instrucción se envía al modelo de IA junto con el prompt del usuario. No se recomienda editarla salvo que sea absolutamente necesario.';
$string['action:summarise_text:endpoint'] = 'Punto de conexión de la API';
$string['action:summarise_text:maxtokens'] = 'Número máximo de tokens';
$string['action:summarise_text:maxtokens_desc'] = 'Número máximo de tokens que se generarán. Anthropic requiere este campo.';
$string['action:summarise_text:model'] = 'Modelo de IA';
$string['action:summarise_text:model_desc'] = 'El modelo utilizado para resumir el texto proporcionado.';
$string['action:summarise_text:systeminstruction'] = 'Instrucción del sistema';
$string['action:summarise_text:systeminstruction_desc'] = 'Esta instrucción se envía al modelo de IA junto con el prompt del usuario. No se recomienda editarla salvo que sea absolutamente necesario.';
$string['apikey'] = 'Clave API de Anthropic';
$string['apikey_desc'] = 'Obtenga una clave desde sus <a href="https://console.anthropic.com/settings/keys" target="_blank">claves API de la Consola de Anthropic</a>.';
$string['enableglobalratelimit'] = 'Establecer límite de solicitudes para todo el sitio';
$string['enableglobalratelimit_desc'] = 'Limita el número de solicitudes que el proveedor de API Anthropic puede recibir en todo el sitio cada hora.';
$string['enableuserratelimit'] = 'Establecer límite de solicitudes por usuario';
$string['enableuserratelimit_desc'] = 'Limita el número de solicitudes que cada usuario puede realizar al proveedor de API Anthropic cada hora.';
$string['globalratelimit'] = 'Número máximo de solicitudes para todo el sitio';
$string['globalratelimit_desc'] = 'El número de solicitudes para todo el sitio permitidas por hora.';
$string['pluginname'] = 'Proveedor de API Anthropic';
$string['privacy:metadata'] = 'El plugin del proveedor de API Anthropic no almacena ningún dato personal.';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'Esta información se envía a la API de Anthropic para que se pueda generar una respuesta. La configuración de su cuenta de Anthropic puede cambiar la forma en que Anthropic almacena y conserva estos datos. Este plugin no envía explícitamente datos de usuario a Anthropic ni los almacena en Moodle LMS.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'El modelo utilizado para generar la respuesta.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'El texto del prompt introducido por el usuario y utilizado para generar la respuesta.';
$string['userratelimit'] = 'Número máximo de solicitudes por usuario';
$string['userratelimit_desc'] = 'El número de solicitudes permitidas por hora y por usuario.';
