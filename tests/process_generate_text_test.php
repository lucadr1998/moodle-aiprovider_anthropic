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

use core_ai\aiactions\base;
use core_ai\provider as core_ai_provider;
use GuzzleHttp\Psr7\Response;

/**
 * Test Anthropic text generation processor.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \aiprovider_anthropic\provider
 * @covers     \aiprovider_anthropic\process_generate_text
 * @covers     \aiprovider_anthropic\abstract_processor
 */
final class process_generate_text_test extends \advanced_testcase {
    /** @var string Successful response fixture. */
    protected string $responsebodyjson;

    /** @var core_ai_provider Provider under test. */
    protected core_ai_provider $provider;

    /** @var base Action under test. */
    protected base $action;

    /**
     * Set up the test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        set_config('apikey', 'test-api-key', 'aiprovider_anthropic');
        set_config('action_generate_text_endpoint', 'https://api.anthropic.com/v1/messages', 'aiprovider_anthropic');
        set_config('action_generate_text_model', 'claude-sonnet-4-6', 'aiprovider_anthropic');
        set_config('action_generate_text_systeminstruction', 'Use concise answers.', 'aiprovider_anthropic');
        set_config('action_generate_text_maxtokens', 2048, 'aiprovider_anthropic');

        $this->responsebodyjson = file_get_contents(
            self::get_fixture_path('aiprovider_anthropic', 'text_request_success.json'),
        );
        $this->create_provider();
        $this->create_action();
    }

    /**
     * Create the provider object.
     */
    private function create_provider(): void {
        $this->provider = new provider();
    }

    /**
     * Create the action object.
     *
     * @param int $userid User id for the action.
     */
    private function create_action(int $userid = 1): void {
        $this->action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: $userid,
            prompttext: 'This is a test prompt',
        );
    }

    /**
     * Test create_request_object.
     */
    public function test_create_request_object(): void {
        $processor = new process_generate_text($this->provider, $this->action);

        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 'hashed-user-id');
        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertSame('claude-sonnet-4-6', $body->model);
        $this->assertSame(2048, $body->max_tokens);
        $this->assertSame('Use concise answers.', $body->system);
        $this->assertSame('user', $body->messages[0]->role);
        $this->assertSame('This is a test prompt', $body->messages[0]->content);
    }

    /**
     * Test handle_api_error.
     */
    public function test_handle_api_error(): void {
        $responses = [
            500 => new Response(500, ['Content-Type' => 'application/json']),
            503 => new Response(503, ['Content-Type' => 'application/json']),
            401 => new Response(
                401,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => ['message' => 'Invalid Authentication']]),
            ),
            429 => new Response(
                429,
                ['Content-Type' => 'application/json'],
                json_encode(['error' => ['message' => 'Rate limit reached for requests']]),
            ),
        ];

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        foreach ($responses as $status => $response) {
            $result = $method->invoke($processor, $response);

            $this->assertSame($status, $result['errorcode']);
            if ($status === 500) {
                $this->assertSame('Internal Server Error', $result['errormessage']);
            } else if ($status === 503) {
                $this->assertSame('Service Unavailable', $result['errormessage']);
            } else if ($status === 401) {
                $this->assertSame('Invalid Authentication', $result['errormessage']);
            } else {
                $this->assertSame('Rate limit reached for requests', $result['errormessage']);
            }
        }
    }

    /**
     * Test handle_api_success.
     */
    public function test_handle_api_success(): void {
        $response = new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        );

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');
        $result = $method->invoke($processor, $response);

        $this->assertTrue($result['success']);
        $this->assertSame('msg_01A2B3C4D5E6F7G8H9I0J', $result['id']);
        $this->assertNull($result['fingerprint']);
        $this->assertStringContainsString('Sure, here is some sample text', $result['generatedcontent']);
        $this->assertSame('end_turn', $result['finishreason']);
        $this->assertSame(11, $result['prompttokens']);
        $this->assertSame(568, $result['completiontokens']);
    }

    /**
     * Test query_ai_api for a successful call.
     */
    public function test_query_ai_api_success(): void {
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertSame('msg_01A2B3C4D5E6F7G8H9I0J', $result['id']);
        $this->assertStringContainsString('Sure, here is some sample text', $result['generatedcontent']);
    }

    /**
     * Test process method.
     */
    public function test_process(): void {
        $this->setUser($this->getDataGenerator()->create_user());

        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertSame('generate_text', $result->get_actionname());
    }
}
