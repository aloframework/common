<?php
    /**
 *    Copyright (c) Arturas Molcanovas <a.molcanovas@gmail.com> 2016.
 *    https://github.com/aloframework/common
 *
 *    Licensed under the Apache License, Version 2.0 (the "License");
 *    you may not use this file except in compliance with the License.
 *    You may obtain a copy of the License at
 *
 *        http://www.apache.org/licenses/LICENSE-2.0
 *
 *    Unless required by applicable law or agreed to in writing, software
 *    distributed under the License is distributed on an "AS IS" BASIS,
 *    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *    See the License for the specific language governing permissions and
 *    limitations under the License.
 */

    namespace AloFramework\Common;

    use PHPUnit_Framework_TestCase;

    class AloTest extends PHPUnit_Framework_TestCase {

        /** @dataProvider pathProvider */
        function testIncludeOnceIfExists($path, $shouldBe) {
            $assert = $shouldBe ? 'assertTrue' : 'assertFalse';

            call_user_func([$this, $assert],
                           Alo::includeOnceIfExists($path),
                           'includeOnceIfExists failed: path ' . $path . ' should be ' . $assert);
        }

        /** @dataProvider pathProvider */
        function testIncludeIfExists($path, $shouldBe) {
            $assert = $shouldBe ? 'assertTrue' : 'assertFalse';

            call_user_func([$this, $assert],
                           Alo::includeIfExists($path),
                           'includeIfExists failed: path ' . $path . ' should be ' . $assert);
        }

        function testIsAjax() {
            $this->assertFalse(Alo::isAjaxRequest());
        }

        function testIsCLI() {
            $this->assertTrue(Alo::isCliRequest());
        }

        function testIfnull() {
            $v1 = 'string';
            $v2 = 0;
            $v3 = false;
            $v4 = null;
            $v5 = '';

            $planB = 'replacementVar';

            $this->assertEquals($v1, Alo::ifnull($v1, $planB, false), '::get() $v1 failed');
            $this->assertEquals($v2, Alo::ifnull($v2, $planB, false), '::get() $v2 failed');
            $this->assertEquals($v3, Alo::ifnull($v3, $planB, false), '::get() $v3 failed');
            $this->assertEquals($planB, Alo::ifnull($v4, $planB, false), '::get() $v4 failed');
            $this->assertEquals($v5, Alo::ifnull($v5, $planB, false), '::get() $v5 failed');
            $this->assertEquals($planB, Alo::ifnull($nope, $planB, false), '::get() $nope failed');

            $this->assertEquals($v1, Alo::ifnull($v1, $planB, true), '::nullget() $v1 failed');
            $this->assertEquals($planB, Alo::ifnull($v2, $planB, true), '::nullget() $v2 failed');
            $this->assertEquals($planB, Alo::ifnull($v3, $planB, true), '::nullget() $v3 failed');
            $this->assertEquals($planB, Alo::ifnull($v4, $planB, true), '::nullget() $v4 failed');
            $this->assertEquals($planB, Alo::ifnull($v5, $planB, true), '::nullget() $v5 failed');
            $this->assertEquals($planB, Alo::ifnull($nope, $planB, true), '::nullget() $nope failed');
        }

        function testGet() {
            $v1 = 'string';
            $v2 = 0;
            $v3 = false;
            $v4 = null;
            $v5 = '';

            $this->assertTrue(Alo::get($v1) !== null, '$v1 failed');
            $this->assertTrue(Alo::get($v2) !== null, '$v2 failed');
            $this->assertTrue(Alo::get($v3) !== null, '$v3 failed');
            $this->assertTrue(Alo::get($v4) === null, '$v4 failed');
            $this->assertTrue(Alo::get($v5) !== null, '$v5 failed');
            $this->assertTrue(Alo::get($nope) === null, '$nope failed');
        }

        function testNullget() {
            $v1 = 'string';
            $v2 = 0;
            $v3 = false;
            $v4 = null;
            $v5 = '';

            $this->assertTrue(Alo::nullget($v1) !== null, '$v1 failed');
            $this->assertTrue(Alo::nullget($v2) === null, '$v2 failed');
            $this->assertTrue(Alo::nullget($v3) === null, '$v3 failed');
            $this->assertTrue(Alo::nullget($v4) === null, '$v4 failed');
            $this->assertTrue(Alo::nullget($v5) === null, '$v5 failed');
            $this->assertTrue(Alo::nullget($nope) === null, '$nope failed');
        }

        function pathProvider() {
            return [[__DIR__ . DIRECTORY_SEPARATOR . 'IncludeTestFile.php',
                     true],
                    [__DIR__, false],
                    ['/nonexistent/' . mt_rand(~PHP_INT_MAX, PHP_INT_MAX), false]];
        }

        function testIfundefined() {
            $const1 = self::generateConstName();
            $const2 = self::generateConstName();
            $planB = 'bar';

            while (defined($const1)) {
                $const1 = self::generateConstName();
            }

            while (defined($const2)) {
                $const2 = self::generateConstName();
            }

            define($const1, 'foo');

            $this->assertEquals(constant($const1), Alo::ifundefined($const1, $planB));
            $this->assertEquals($planB, Alo::ifundefined($const2, $planB));
        }

        /** @dataProvider isTraversableDataProvider */
        function testIsTraversable($input, $expected) {
            $this->assertEquals($expected, Alo::isTraversable($input));
        }

        function testGetFingerprint() {
            $default = Alo::getFingerprint();
            $this->assertEquals(64, strlen($default));
            $this->assertEquals(128, strlen(Alo::getFingerprint('sha512')));
            $this->assertEquals($default, Alo::getFingerprint('sha256'));
        }

        function isTraversableDataProvider() {
            return [['foo', false],
                    [1.1, false],
                    [new \stdClass(), false],
                    [[], true],
                    [new \ArrayObject(), true]];
        }

        function testUnXSS() {
            $strRaw = '"foo"&\'bar\'';
            $strClean = '&quot;foo&quot;&amp;&apos;bar&apos;';
            $arrRaw = ['one' => $strRaw,
                       'two' => $strRaw];
            $arrClean = ['one' => $strClean,
                         'two' => $strClean];

            $this->assertEquals(new \stdClass(), Alo::unXss(new \stdClass()));
            $this->assertEquals('foo', Alo::unXss('foo'));
            $this->assertEquals($strClean, Alo::unXss($strRaw));
            $this->assertEquals($arrClean, Alo::unXss($arrRaw));
            $this->assertEquals(new \ArrayObject($arrClean), Alo::unXss(new \ArrayObject($arrRaw)));
        }

        function testAsciiRand() {
            $alphanum = Alo::asciiRand(1000, Alo::ASCII_ALPHANUM);
            $nonalphanum = Alo::asciiRand(1000, Alo::ASCII_NONALPHANUM);
            $all = Alo::asciiRand(1000, Alo::ASCII_ALL);

            $this->assertEquals(1000, strlen($alphanum));
            $this->assertEquals(1, preg_match('~^[a-z0-9]+$~i', $alphanum));
            $this->assertEquals(0, preg_match('~^[a-z0-9]+$~i', $nonalphanum));
            $this->assertEquals([1, 1], [preg_match('~[a-z0-9]+~i', $all), preg_match('~[^a-z0-9]+~i', $all)]);
        }

        function testIsRegularRequest() {
            $this->assertFalse(Alo::isRegularRequest());
        }

        private static function generateConstName() {
            return hash('sha512',
                        mt_rand(~PHP_INT_MAX, PHP_INT_MAX) . uniqid(mt_rand(~PHP_INT_MAX, PHP_INT_MAX), true));
        }
    }
