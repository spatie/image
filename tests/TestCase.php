<?php

namespace Spatie\Image\Test;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;

beforeEach(function () {
    $this->tempDir = (new TemporaryDirectory(__DIR__))
        ->name('temp')
        ->force()
        ->create()
        ->empty();
});

abstract class TestCase extends PHPUnitTestCase
{
    /** @var \Spatie\TemporaryDirectory\TemporaryDirectory */
    protected $tempDir;

    protected function setUp(): void
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