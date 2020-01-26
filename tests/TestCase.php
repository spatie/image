<?php

namespace Spatie\Image\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;

abstract class TestCase extends PHPUnitTestCase
{
    /** @var \Spatie\TemporaryDirectory\TemporaryDirectory */
    protected $tempDir;

    public function setUp()
    {
        $this->tempDir = (new TemporaryDirectory(__DIR__))
            ->name('temp')
            ->force()
            ->create()
            ->empty();
    }

    protected function getTestJpg(): string
    {
        return $this->getTestFile('test.jpg');
    }

    protected function getTestFile($fileName): string
    {
        return __DIR__."/testfiles/{$fileName}";
    }
}
