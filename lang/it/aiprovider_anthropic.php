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

$string['action:generate_text:endpoint'] = 'Endpoint API';
$string['action:generate_text:maxtokens'] = 'Numero massimo di token';
$string['action:generate_text:maxtokens_desc'] = 'Numero massimo di token da generare. Anthropic richiede questo campo.';
$string['action:generate_text:model'] = 'Modello di IA';
$string['action:generate_text:model_desc'] = 'Il modello usato per generare la risposta testuale.';
$string['action:generate_text:systeminstruction'] = 'Istruzione di sistema';
$string['action:generate_text:systeminstruction_desc'] = 'Questa istruzione viene inviata al modello di IA insieme al prompt dell\'utente. Si consiglia di non modificarla se non strettamente necessario.';
$string['action:summarise_text:endpoint'] = 'Endpoint API';
$string['action:summarise_text:maxtokens'] = 'Numero massimo di token';
$string['action:summarise_text:maxtokens_desc'] = 'Numero massimo di token da generare. Anthropic richiede questo campo.';
$string['action:summarise_text:model'] = 'Modello di IA';
$string['action:summarise_text:model_desc'] = 'Il modello usato per riassumere il testo fornito.';
$string['action:summarise_text:systeminstruction'] = 'Istruzione di sistema';
$string['action:summarise_text:systeminstruction_desc'] = 'Questa istruzione viene inviata al modello di IA insieme al prompt dell\'utente. Si consiglia di non modificarla se non strettamente necessario.';
$string['apikey'] = 'Chiave API di Anthropic';
$string['apikey_desc'] = 'Ottieni una chiave dalle <a href="https://console.anthropic.com/settings/keys" target="_blank">chiavi API della Console Anthropic</a>.';
$string['enableglobalratelimit'] = 'Imposta limite di richieste per l\'intero sito';
$string['enableglobalratelimit_desc'] = 'Limita il numero di richieste che il provider API Anthropic può ricevere in tutto il sito ogni ora.';
$string['enableuserratelimit'] = 'Imposta limite di richieste per utente';
$string['enableuserratelimit_desc'] = 'Limita il numero di richieste che ciascun utente può effettuare al provider API Anthropic ogni ora.';
$string['error:globalratelimitexceeded'] = 'Limite di richieste globale superato';
$string['error:userratelimitexceeded'] = 'Limite di richieste dell\'utente superato';
$string['globalratelimit'] = 'Numero massimo di richieste per l\'intero sito';
$string['globalratelimit_desc'] = 'Il numero di richieste consentite per l\'intero sito ogni ora.';
$string['pluginname'] = 'Provider API Anthropic';
$string['privacy:metadata'] = 'Il plugin del provider API Anthropic non memorizza alcun dato personale.';
$string['privacy:metadata:aiprovider_anthropic:externalpurpose'] = 'Queste informazioni vengono inviate all\'API di Anthropic per generare una risposta. Le impostazioni del tuo account Anthropic possono modificare il modo in cui Anthropic archivia e conserva questi dati. Questo plugin non invia esplicitamente dati dell\'utente ad Anthropic né li memorizza in Moodle LMS.';
$string['privacy:metadata:aiprovider_anthropic:model'] = 'Il modello usato per generare la risposta.';
$string['privacy:metadata:aiprovider_anthropic:prompttext'] = 'Il testo del prompt inserito dall\'utente e usato per generare la risposta.';
$string['userratelimit'] = 'Numero massimo di richieste per utente';
$string['userratelimit_desc'] = 'Il numero di richieste consentite per ora, per utente.';
