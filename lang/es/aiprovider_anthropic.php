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

$string['action:explain_text:endpoint'] = 'Punto de conexión de la API';
$string['action:explain_text:model'] = 'Modelo de explicación del texto';
$string['action:explain_text:model_help'] = 'El modelo utilizado para explicar el texto proporcionado.';
$string['action:explain_text:systeminstruction'] = 'Instrucción del sistema';
$string['action:explain_text:systeminstruction_help'] = 'Esta instrucción se envía al modelo de IA junto con el prompt del usuario. No se recomienda editar esta instrucción salvo que sea absolutamente necesario.';
$string['action:generate_text:endpoint'] = 'Punto de conexión de la API';
$string['action:generate_text:model'] = 'Modelo de IA';
$string['action:generate_text:model_help'] = 'El modelo utilizado para generar la respuesta de texto.';
$string['action:generate_text:systeminstruction'] = 'Instrucción del sistema';
$string['action:generate_text:systeminstruction_help'] = 'Esta instrucción se envía al modelo de IA junto con el prompt del usuario. No se recomienda editar esta instrucción salvo que sea absolutamente necesario.';
$string['action:summarise_text:endpoint'] = 'Punto de conexión de la API';
$string['action:summarise_text:model'] = 'Modelo de IA';
$string['action:summarise_text:model_help'] = 'El modelo utilizado para resumir el texto proporcionado.';
$string['action:summarise_text:systeminstruction'] = 'Instrucción del sistema';
$string['action:summarise_text:systeminstruction_help'] = 'Esta instrucción se envía al modelo de IA junto con el prompt del usuario. No se recomienda editar esta instrucción salvo que sea absolutamente necesario.';
$string['apikey'] = 'Clave de API de Anthropic';
$string['apikey_help'] = 'Obtenga una clave desde sus <a href="https://platform.claude.com/settings/keys" target="_blank">claves de API de Claude Console</a>.';
$string['cachedef_models'] = 'Resultados en caché del descubrimiento de modelos de Anthropic';
$string['custom_model_name'] = 'Nombre personalizado del modelo';
$string['extraparams'] = 'Parámetros adicionales';
$string['extraparams_help'] = 'Aquí se pueden configurar parámetros adicionales. Admitimos el formato JSON. Por ejemplo:
<pre>
{
    "max_tokens": 2048,
    "temperature": 0.3
}
</pre>';
$string['invalidjson'] = 'Cadena JSON no válida';
$string['pluginname'] = 'Proveedor de API de Anthropic';
$string['privacy:metadata'] = 'El plugin proveedor de API de Anthropic no almacena ningún dato personal.';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'Esta información se envía a la API de Anthropic para generar una respuesta. La configuración de su cuenta de Anthropic puede cambiar la forma en que Anthropic almacena y conserva estos datos. Este plugin no envía explícitamente datos de usuario a Anthropic ni los almacena en Moodle LMS.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'El modelo utilizado para generar la respuesta.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'El texto introducido por el usuario que se utiliza para generar la respuesta.';
$string['settings'] = 'Configuración';
$string['settings_help'] = 'Ajuste la configuración siguiente para personalizar cómo se envían las solicitudes a Anthropic.';
$string['settings_max_tokens'] = 'max_tokens';
$string['settings_max_tokens_help'] = 'Número máximo de tokens que se generarán. Anthropic requiere este campo y el valor predeterminado es 2048.';
$string['settings_temperature'] = 'temperature';
$string['settings_temperature_help'] = 'Cantidad de aleatoriedad introducida en la respuesta. Los valores más bajos son más deterministas.';
$string['settings_top_k'] = 'top_k';
$string['settings_top_k_help'] = 'Solo toma muestras de las K opciones principales para cada token siguiente. Recomendado solo para casos de uso avanzados.';
$string['settings_top_p'] = 'top_p';
$string['settings_top_p_help'] = 'Usa muestreo de núcleo. En general, modifique temperature o top_p, pero no ambos.';
