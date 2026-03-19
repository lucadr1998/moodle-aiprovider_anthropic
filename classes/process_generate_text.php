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

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * Process text generation with Anthropic.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class process_generate_text extends abstract_processor {
    #[\Override]
    protected function get_endpoint(): UriInterface {
        return new Uri(get_config('aiprovider_anthropic', 'action_generate_text_endpoint'));
    }

    #[\Override]
    protected function get_model(): string {
        return get_config('aiprovider_anthropic', 'action_generate_text_model');
    }

    #[\Override]
    protected function get_system_instruction(): string {
        return get_config('aiprovider_anthropic', 'action_generate_text_systeminstruction');
    }

    #[\Override]
    protected function get_max_tokens(): int {
        return (int) get_config('aiprovider_anthropic', 'action_generate_text_maxtokens') ?: 2048;
    }

    #[\Override]
    protected function create_request_object(string $userid): RequestInterface {
        $requestobj = new \stdClass();
        $requestobj->model = $this->get_model();
        $requestobj->max_tokens = $this->get_max_tokens();
        $requestobj->messages = [
            (object) [
                'role' => 'user',
                'content' => $this->action->get_configuration('prompttext'),
            ],
        ];

        $systeminstruction = $this->get_system_instruction();
        if (!empty($systeminstruction)) {
            $requestobj->system = $systeminstruction;
        }

        return new Request(
            method: 'POST',
            uri: '',
            headers: [
                'Content-Type' => 'application/json',
            ],
            body: json_encode($requestobj),
        );
    }

    #[\Override]
    protected function handle_api_success(ResponseInterface $response): array {
        $bodyobj = json_decode($response->getBody()->getContents());

        $generatedcontent = '';
        if (!empty($bodyobj->content) && is_array($bodyobj->content)) {
            foreach ($bodyobj->content as $block) {
                if (($block->type ?? null) === 'text' && isset($block->text)) {
                    $generatedcontent .= $block->text;
                }
            }
        }

        return [
            'success' => true,
            'id' => $bodyobj->id ?? null,
            'fingerprint' => null,
            'generatedcontent' => $generatedcontent,
            'finishreason' => $bodyobj->stop_reason ?? null,
            'prompttokens' => $bodyobj->usage->input_tokens ?? null,
            'completiontokens' => $bodyobj->usage->output_tokens ?? null,
        ];
    }
}
