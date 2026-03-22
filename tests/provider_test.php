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
 * @covers     \aiprovider_anthropic\provider
 */
final class provider_test extends \advanced_testcase {
    /**
     * Test get_action_list.
     */
    public function test_get_action_list(): void {
        $provider = new provider();
        $actionlist = $provider->get_action_list();

        $this->assertIsArray($actionlist);
        $this->assertCount(2, $actionlist);
        $this->assertContains(\core_ai\aiactions\generate_text::class, $actionlist);
        $this->assertContains(\core_ai\aiactions\summarise_text::class, $actionlist);
    }

    /**
     * Test generate_userid.
     */
    public function test_generate_userid(): void {
        $provider = new provider();
        $userid = $provider->generate_userid('1');

        $this->assertIsString($userid);
        $this->assertSame(64, strlen($userid));
    }

    /**
     * Test add_authentication_headers.
     */
    public function test_add_authentication_headers(): void {
        $this->resetAfterTest(true);
        set_config('apikey', 'test-api-key', 'aiprovider_anthropic');

        $provider = new provider();
        $request = new Request('POST', 'https://api.anthropic.com/v1/messages');
        $request = $provider->add_authentication_headers($request);

        $this->assertSame(['test-api-key'], $request->getHeader('x-api-key'));
        $this->assertSame(['2023-06-01'], $request->getHeader('anthropic-version'));
    }

    /**
     * Test is_request_allowed.
     */
    public function test_is_request_allowed(): void {
        $this->resetAfterTest(true);

        set_config('enableglobalratelimit', 1, 'aiprovider_anthropic');
        set_config('globalratelimit', 5, 'aiprovider_anthropic');
        set_config('enableuserratelimit', 1, 'aiprovider_anthropic');
        set_config('userratelimit', 3, 'aiprovider_anthropic');

        $action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: 1,
            prompttext: 'This is a test prompt',
        );
        $provider = new provider();

        for ($i = 0; $i < 3; $i++) {
            $this->assertTrue($provider->is_request_allowed($action));
        }

        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertSame('User rate limit exceeded', $result['errormessage']);

        $action = new \core_ai\aiactions\generate_text(
            contextid: 1,
            userid: 2,
            prompttext: 'This is a test prompt',
        );
        $this->assertTrue($provider->is_request_allowed($action));
        $this->assertTrue($provider->is_request_allowed($action));

        $result = $provider->is_request_allowed($action);
        $this->assertFalse($result['success']);
        $this->assertSame('Global rate limit exceeded', $result['errormessage']);
    }

    /**
     * Test is_provider_configured.
     */
    public function test_is_provider_configured(): void {
        $this->resetAfterTest(true);

        $provider = new provider();
        $this->assertFalse($provider->is_provider_configured());

        set_config('apikey', 'test-api-key', 'aiprovider_anthropic');
        $provider = new provider();
        $this->assertTrue($provider->is_provider_configured());
    }
}
