<?php

use PHPUnit\Framework\TestCase;

final class MemcheckTest extends TestCase
{
    public function testNoCommand()
    {
        $result = $this->checkCommand([]);
        $this->assertEquals(1, $result['status']);
        $this->assertEquals("Usage: php-memcheck [command]\n", $result['stderr']);
    }

    public function testFailedCommand()
    {
        $result = $this->checkCommand(['cat', 'missing.txt']);
        $this->assertEquals(1, $result['status']);
        $this->assertEquals("cat: missing.txt: No such file or directory\n", $result['stderr']);
    }

    public function testEnvVar()
    {
        $code = <<<'PHP'
        echo getenv('PHP_MEMCHECK_RUNNING');
        PHP;
        $result = $this->checkCode($code);
        $this->assertEquals(0, $result['status']);
        $this->assertEquals("1", $result['stdout']);
    }

    public function testNoLeak()
    {
        $code = <<<'PHP'
        FFI::cdef('')->new('char');
        PHP;
        $result = $this->checkCode($code);
        $this->assertEquals(0, $result['status']);
    }

    public function testLeak()
    {
        $code = <<<'PHP'
        FFI::cdef('')->new('char', false, true);
        PHP;
        $result = $this->checkCode($code);
        $this->assertEquals(1, $result['status']);
        $this->assertStringContainsString('1 bytes in 1 blocks are definitely lost', $result['stderr']);
    }

    public function testDoubleFree()
    {
        $code = <<<'PHP'
        $a = FFI::cdef('')->new('char', false, true);
        FFI::free(FFI::addr($a));
        FFI::free(FFI::addr($a));
        PHP;
        $result = $this->checkCode($code);
        $this->assertEquals(1, $result['status']);
        $this->assertStringContainsString('Invalid free()', $result['stderr']);
    }

    private function checkCode($code)
    {
        return $this->checkCommand(['php', '-r', $code]);
    }

    private function checkCommand($args)
    {
        $descriptorspec = array(1 => array('pipe', 'w'), 2 => array('pipe', 'w'));
        $proc = proc_open(['bin/php-memcheck', ...$args], $descriptorspec, $pipes);
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($proc);
        return ['status' => $status, 'stdout' => $stdout, 'stderr' => $stderr];
    }
}
