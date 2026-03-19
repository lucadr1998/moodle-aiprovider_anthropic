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
 * Strings for component aiprovider_anthropic, language 'it'.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['action:explain_text:endpoint'] = 'Endpoint API';
$string['action:explain_text:model'] = 'Modello per la spiegazione del testo';
$string['action:explain_text:model_help'] = 'Il modello usato per spiegare il testo fornito.';
$string['action:explain_text:systeminstruction'] = 'Istruzione di sistema';
$string['action:explain_text:systeminstruction_help'] = 'Questa istruzione viene inviata al modello di IA insieme al prompt dell’utente. Non è consigliabile modificare questa istruzione se non quando strettamente necessario.';
$string['action:generate_text:endpoint'] = 'Endpoint API';
$string['action:generate_text:model'] = 'Modello di IA';
$string['action:generate_text:model_help'] = 'Il modello usato per generare la risposta testuale.';
$string['action:generate_text:systeminstruction'] = 'Istruzione di sistema';
$string['action:generate_text:systeminstruction_help'] = 'Questa istruzione viene inviata al modello di IA insieme al prompt dell’utente. Non è consigliabile modificare questa istruzione se non quando strettamente necessario.';
$string['action:summarise_text:endpoint'] = 'Endpoint API';
$string['action:summarise_text:model'] = 'Modello di IA';
$string['action:summarise_text:model_help'] = 'Il modello usato per riassumere il testo fornito.';
$string['action:summarise_text:systeminstruction'] = 'Istruzione di sistema';
$string['action:summarise_text:systeminstruction_help'] = 'Questa istruzione viene inviata al modello di IA insieme al prompt dell’utente. Non è consigliabile modificare questa istruzione se non quando strettamente necessario.';
$string['apikey'] = 'Chiave API di Anthropic';
$string['apikey_help'] = 'Ottieni una chiave dalle <a href="https://platform.claude.com/settings/keys" target="_blank">chiavi API della Claude Console</a>.';
$string['cachedef_models'] = 'Risultati memorizzati nella cache del rilevamento dei modelli Anthropic';
$string['custom_model_name'] = 'Nome personalizzato del modello';
$string['extraparams'] = 'Parametri aggiuntivi';
$string['extraparams_help'] = 'Qui possono essere configurati parametri aggiuntivi. È supportato il formato JSON. Per esempio:
<pre>
{
    "max_tokens": 2048,
    "temperature": 0.3
}
</pre>';
$string['invalidjson'] = 'Stringa JSON non valida';
$string['pluginname'] = 'Provider API di Anthropic';
$string['privacy:metadata'] = 'Il plugin provider API di Anthropic non memorizza alcun dato personale.';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'Queste informazioni vengono inviate all’API di Anthropic per generare una risposta. Le impostazioni del tuo account Anthropic possono modificare il modo in cui Anthropic archivia e conserva questi dati. Questo plugin non invia esplicitamente dati utente ad Anthropic né li memorizza in Moodle LMS.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'Il modello usato per generare la risposta.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'Il prompt di testo inserito dall\'utente usato per generare la risposta.';
$string['settings'] = 'Impostazioni';
$string['settings_help'] = 'Modifica le impostazioni seguenti per personalizzare il modo in cui le richieste vengono inviate ad Anthropic.';
$string['settings_max_tokens'] = 'max_tokens';
$string['settings_max_tokens_help'] = 'Numero massimo di token da generare. Anthropic richiede questo campo e il valore predefinito è 2048.';
$string['settings_temperature'] = 'temperature';
$string['settings_temperature_help'] = 'Quantità di casualità introdotta nella risposta. Valori più bassi sono più deterministici.';
$string['settings_top_k'] = 'top_k';
$string['settings_top_k_help'] = 'Esegue il campionamento solo tra le prime K opzioni per ogni token successivo. Consigliato solo per casi d’uso avanzati.';
$string['settings_top_p'] = 'top_p';
$string['settings_top_p_help'] = 'Usa il campionamento nucleus. In generale, modifica temperature oppure top_p, ma non entrambi.';
