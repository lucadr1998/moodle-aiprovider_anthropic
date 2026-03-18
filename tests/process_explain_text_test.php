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
 * Test Anthropic explain text processor.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers     \aiprovider_anthropic\provider
 * @covers     \aiprovider_anthropic\process_explain_text
 * @covers     \aiprovider_anthropic\abstract_processor
 */
final class process_explain_text_test extends \advanced_testcase {
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
            actionclass: \core_ai\aiactions\explain_text::class,
            actionconfig: [
                'systeminstruction' => get_string('action_explain_text_instruction', 'core_ai'),
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
        $this->action = new \core_ai\aiactions\explain_text(
            contextid: 1,
            userid: $userid,
            prompttext: 'This is a test prompt',
        );
    }

    /**
     * Test create_request_object.
     */
    public function test_create_request_object(): void {
        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals(get_string('action_explain_text_instruction', 'core_ai'), $body->system);
        $this->assertEquals('This is a test prompt', $body->messages[0]->content);
    }

    /**
     * Test create_request_object with extra model settings.
     */
    public function test_create_request_object_with_model_settings(): void {
        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\explain_text::class,
            actionconfig: [
                'systeminstruction' => get_string('action_explain_text_instruction', 'core_ai'),
                'temperature' => 0.5,
                'top_p' => 0.75,
                'top_k' => 15,
                'max_tokens' => 100,
            ],
        );
        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-sonnet-4-6', $body->model);
        $this->assertEquals(0.5, $body->temperature);
        $this->assertEquals(0.75, $body->top_p);
        $this->assertEquals(15, $body->top_k);
        $this->assertEquals(100, $body->max_tokens);

        $this->provider = $this->create_provider(
            actionclass: \core_ai\aiactions\explain_text::class,
            actionconfig: [
                'model' => 'claude-custom',
                'systeminstruction' => get_string('action_explain_text_instruction', 'core_ai'),
                'modelextraparams' => '{"max_tokens":100,"temperature":0.5,"top_p":0.75,"top_k":15}',
            ],
        );
        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'create_request_object');
        $request = $method->invoke($processor, 1);

        $body = (object) json_decode($request->getBody()->getContents());

        $this->assertEquals('claude-custom', $body->model);
        $this->assertEquals(0.5, $body->temperature);
        $this->assertEquals(0.75, $body->top_p);
        $this->assertEquals(15, $body->top_k);
        $this->assertEquals(100, $body->max_tokens);
    }

    /**
     * Test handle_api_error.
     */
    public function test_handle_api_error(): void {
        $responses = [
            500 => new Response(500, ['Content-Type' => 'application/json']),
            401 => new Response(
                401,
                ['Content-Type' => 'application/json'],
                '{"type":"error","error":{"type":"authentication_error","message":"Invalid API key"}}'
            ),
        ];

        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_error');

        foreach ($responses as $status => $response) {
            $result = $method->invoke($processor, $response);
            $this->assertEquals($status, $result['errorcode']);
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

        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'handle_api_success');
        $result = $method->invoke($processor, $response);

        $this->assertTrue($result['success']);
        $this->assertEquals('end_turn', $result['finishreason']);
        $this->assertNull($result['fingerprint']);
        $this->assertStringContainsString('structured multi-turn conversations', $result['generatedcontent']);
    }

    /**
     * Test query_ai_api success.
     */
    public function test_query_ai_api_success(): void {
        ['mock' => $mock] = $this->get_mocked_http_client();
        $mock->append(new Response(
            200,
            ['Content-Type' => 'application/json'],
            $this->responsebodyjson,
        ));

        $processor = new process_explain_text($this->provider, $this->action);
        $method = new \ReflectionMethod($processor, 'query_ai_api');
        $result = $method->invoke($processor);

        $this->assertTrue($result['success']);
        $this->assertEquals('end_turn', $result['finishreason']);
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

        $processor = new process_explain_text($this->provider, $this->action);
        $result = $processor->process();

        $this->assertTrue($result->get_success());
        $this->assertEquals('explain_text', $result->get_actionname());
    }
}
