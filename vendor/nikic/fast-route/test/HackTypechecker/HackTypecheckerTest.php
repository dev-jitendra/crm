<?php

namespace FastRoute;

use PHPUnit\Framework\TestCase;

class HackTypecheckerTest extends TestCase
{
    const SERVER_ALREADY_RUNNING_CODE = 77;

    public function testTypechecks($recurse = true)
    {
        if (!defined('HHVM_VERSION')) {
            $this->markTestSkipped('HHVM only');
        }
        if (!version_compare(HHVM_VERSION, '3.9.0', '>=')) {
          $this->markTestSkipped('classname<T> requires HHVM 3.9+');
        }

        
        

        $output = [];
        $exit_code = null;
        exec(
            'hh_server --check ' . escapeshellarg(__DIR__ . '/../../') . ' 2>&1',
            $output,
            $exit_code
        );
        if ($exit_code === self::SERVER_ALREADY_RUNNING_CODE) {
            $this->assertTrue(
              $recurse,
              'Typechecker still running after running hh_client stop'
            );
            
            
            exec('hh_client stop 2>/dev/null');
            $this->testTypechecks( false);
            return;

        }
        $this->assertSame(0, $exit_code, implode("\n", $output));
    }
}
