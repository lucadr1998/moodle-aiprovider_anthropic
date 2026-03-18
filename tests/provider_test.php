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

/**
 * Test Anthropic provider methods.
 *
 * @package    aiprovider_anthropic
 * @copyright  2026 Luca Demicheli Rubio <lucademichelirubio.portfolio@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers     \aiprovider_anthropic\provider
 */
final class provider_test extends \advanced_testcase {
    /** @var \core_ai\manager */
    private $manager;

    /** @var \core_ai\provider */
    private $provider;

    /**
     * Set up the test.
     */
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();

        $this->manager = \core\di::get(\core_ai\manager::class);
        $config = ['apikey' => 'anthropic-key-123'];
        $this->provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: $config,
        );
    }

    /**
     * Test get_action_list.
     */
    public function test_get_action_list(): void {
        $actionlist = $this->provider->get_action_list();
        $this->assertIsArray($actionlist);
        $this->assertCount(3, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\summarise_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\explain_text::class, $actionlist);
    }

    /**
     * Test authentication headers.
     */
    public function test_add_authentication_headers(): void {
        $request = new Request('POST', 'https://api.anthropic.com/v1/messages');
        $request = $this->provider->add_authentication_headers($request);

        $this->assertEquals('anthropic-key-123', $request->getHeaderLine('x-api-key'));
        $this->assertEquals('2023-06-01', $request->getHeaderLine('anthropic-version'));
    }

    /**
     * Test provider configuration state.
     */
    public function test_is_provider_configured(): void {
        $this->assertTrue($this->provider->is_provider_configured());

        $unconfigured = $this->manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: [],
        );
        $this->assertFalse($unconfigured->is_provider_configured());
    }

    /**
     * Test is_request_allowed.
     */
    public function test_is_request_allowed(): void {
        $config = [
            'enableuserratelimit' => true,
            'userratelimit' => 3,
            'enableglobalratelimit' => true,
            'globalratelimit' => 5,
            'apikey' => 'anthropic-key-123',
        ];

        $provider = $this->manager->create_provider_instance(
            classname: '\aiprovider_anthropic\provider',
            name: 'dummy',
            config: $config,
        );

        $contextid = 1;
        $userid = 1;
        $prompttext = 'This is a test prompt';
        $action = new \core_ai\aiactions\generate_text(
            contextid: $contextid,
            userid: $userid,
            prompttext: $prompttext,
        );

        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action));
        }

        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(
            'You have reached the maximum number of AI requests you can make in an hour. Try again later.',
            $result['errormessage'],
        );

        $action = new \core_ai\aiactions\generate_text(
            contextid: $contextid,
            userid: 2,
            prompttext: $prompttext,
        );
        $this->assertTrue($provider->is_request_allowed($action));
        $this->assertTrue($provider->is_request_allowed($action));

        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertEquals(
            'The AI service has reached the maximum number of site-wide requests per hour. Try again later.',
            $result['errormessage'],
        );
    }
}
