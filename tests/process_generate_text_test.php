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
use core_ai\provider;
use GuzzleHttp\Psr7\Response;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/testcase_helper_trait.php');

/**
 * Test Anthropic generate text processor.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers     \aiprovider_anthropic\provider
 * @covers     \aiprovider_anthropic\process_generate_text
 * @covers     \aiprovider_anthropic\abstract_processor
 */
final class process_generate_text_test extends \advanced_testcase {
    use testcase_helper_trait;

    /** @var string */
    protected string $responsebodyjson;

    /** @var \core_ai\manager */
    private $manager;

    /** @var provider */
    protected provider $provider;

    /** @var base */
    protected base $action;

    /**
     * Set up the test.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
        $this->responsebodyjson = file_get_contents(self::get_fixture_path('aiprovider_anthropic', 'text_request_success.json'));
        $this->manager = \core\di::get(\core_ai\manager::class);
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'systeminstruction' => get_string('action_generate_text_instruction', 'core_ai'),
            ],
        );
        $this->create_action();
    }

    /**
     * Create the action object.
     *
     * @param int $userid User id.
     */
    private function create_action(int $userid = 1): void {
        $this->action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: $userid,
            prompttext: 'This is a test prompt',
        );
    }

    /**
     * Test authentication headers.
     */
    public function test_add_authentication_headers(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);
        $request = $this->provider->add_authentication_headers($request);

        $this->assertEquals('anthropic-key-123', $request->getHeaderLine('x-api-key'));
        $this->assertEquals('2023-06-01', $request->getHeaderLine('anthropic-version'));
    }

    /**
     * Test create_request_object.
     */
    public function test_create_request_object(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-sonnet-4-6', $body->model);
        $this->assertEquals(2048, $body->max_tokens);
        $this->assertEquals(get_string('action_generate_text_instruction', 'core_ai'), $body->system);
        $this->assertEquals('user', $body->messages[0]->role);
        $this->assertEquals('This is a test prompt', $body->messages[0]->content);
        $this->assertObjectNotHasProperty('user', $body);
    }

    /**
     * Test create_request_object with extra model settings.
     */
    public function test_create_request_object_with_model_settings(): void {
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'systeminstruction' => get_string('action_generate_text_instruction', 'core_ai'),
                'temperature' => 0.5,
                'top_p' => 0.9,
                'top_k' => 25,
                'max_tokens' => 100,
            ],
        );
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-sonnet-4-6', $body->model);
        $this->assertEquals(0.5, $body->temperature);
        $this->assertEquals(0.9, $body->top_p);
        $this->assertEquals(25, $body->top_k);
        $this->assertEquals(100, $body->max_tokens);

        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'model' => 'claude-custom',
                'systeminstruction' => get_string('action_generate_text_instruction', 'core_ai'),
                'modelextraparams' => '{"max_tokens":100,"temperature":0.5,"top_p":0.9,"top_k":25}',
            ],
        );
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-custom', $body->model);
        $this->assertEquals(0.5, $body->temperature);
        $this->assertEquals(0.9, $body->top_p);
        $this->assertEquals(25, $body->top_k);
        $this->assertEquals(100, $body->max_tokens);
    }

    /**
     * Test create_request_object default max_tokens for custom models.
     */
    public function test_create_request_object_defaults_max_tokens(): void {
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\generate_text::class,
            actionconfig: [
                'model' => 'claude-custom',
                'modelextraparams' => '{"temperature":0.2}',
            ],
        );
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());
        $this->assertEquals(2048, $body->max_tokens);
        $this->assertEquals(0.2, $body->temperature);
    }

    /**
     * Test the API error response handler.
     */
    public function test_handle_api_error(): void {
        $responses = [
            500 => new Response(500, ['Content-Type' => 'application/json']),
            503 => new Response(503, ['Content-Type' => 'application/json']),
            401 => new Response(
                401,
                ['Content-Type' => 'application/json'],
                '{"type":"error","error":{"type":"authentication_error","message":"Invalid API key"}}'
            ),
            429 => new Response(
                429,
                ['Content-Type' => 'application/json'],
                '{"type":"error","error":{"type":"rate_limit_error","message":"Rate limit reached"}}'
            ),
            529 => new Response(
                529,
                ['Content-Type' => 'application/json'],
                '{"type":"error","error":{"type":"overloaded_error","message":"Anthropic API overloaded"}}'
            ),
        ];

        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        foreach ($responses as $status => $response) {
            $result = $method->invoke($processor, $response);
            $this->assertEquals($status, $result['errorcode']);
            if ($status == 500) {
                $this->assertEquals('Internal Server Error', $result['errormessage']);
            } else if ($status == 503) {
                $this->assertEquals('Service Unavailable', $result['errormessage']);
            } else if ($status == 529) {
                $this->assertEquals('Anthropic API overloaded', $result['errormessage']);
            } else {
                $this->assertNotEmpty($result['errormessage']);
            }
        }
    }

    /**
     * Test the API success response handler.
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
        $this->assertEquals('msg_01A1b2C3d4E5f6G7h8I9j0', $result['id']);
        $this->assertNull($result['fingerprint']);
        $expectedcontent = 'Anthropic can help with drafting, summarising, and explaining text.'
            . ' It also supports structured multi-turn conversations.';
        $this->assertEquals(
            $expectedcontent,
            $result['generatedcontent'],
        );
        $this->assertEquals('end_turn', $result['finishreason']);
        $this->assertEquals('23', $result['prompttokens']);
        $this->assertEquals('150', $result['completiontokens']);
        $this->assertEquals('claude-sonnet-4-6', $result['model']);
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
        $this->assertEquals('msg_01A1b2C3d4E5f6G7h8I9j0', $result['id']);
        $this->assertNull($result['fingerprint']);
        $this->assertEquals('end_turn', $result['finishreason']);
        $this->assertEquals('23', $result['prompttokens']);
        $this->assertEquals('150', $result['completiontokens']);
        $this->assertEquals('claude-sonnet-4-6', $result['model']);
    }

    /**
     * Test prepare_response success.
     */
    public function test_prepare_response_success(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => true,
            'id' => 'msg_01A1b2C3d4E5f6G7h8I9j0',
            'fingerprint' => null,
            'generatedcontent' => 'Anthropic sample response',
            'finishreason' => 'end_turn',
            'prompttokens' => '23',
            'completiontokens' => '150',
            'model' => 'claude-sonnet-4-6',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertTrue($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
        $this->assertEquals($response['generatedcontent'], $result->get_response_data()['generatedcontent']);
        $this->assertEquals($response['model'], $result->get_response_data()['model']);
    }

    /**
     * Test prepare_response error.
     */
    public function test_prepare_response_error(): void {
        $processor = new process_generate_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'prepare_response');

        $response = [
            'success' => false,
            'errorcode' => 500,
            'error' => 'server_error',
            'errormessage' => 'Try again later',
        ];

        $result = $method->invoke($processor, $response);

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
        $this->assertEquals($response['error'], $result->get_error());
        $this->assertEquals($response['errorcode'], $result->get_errorcode());
        $this->assertEquals($response['errormessage'], $result->get_errormessage());
    }

    /**
     * Test process.
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
        $this->assertEquals('generate_text', $result->get_actionname());
    }

    /**
     * Test process with error.
     */
    public function test_process_error(): void {
        $this->setUser($this->getDataGenerator()->create_user());
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            401,
            ['Content-Type' => 'application/json'],
            '{"type":"error","error":{"type":"authentication_error","message":"Invalid API key"}}',
        ));

        $processor = new process_generate_text($this->provider, $this->action);
        $result = $processor->process();

        $this->assertInstanceOf(\core_ai\aiactions\responses\response_base::class, $result);
        $this->assertFalse($result->get_success());
        $this->assertEquals('generate_text', $result->get_actionname());
        $this->assertEquals(401, $result->get_errorcode());
        $this->assertEquals('Invalid API key', $result->get_errormessage());
    }
}
