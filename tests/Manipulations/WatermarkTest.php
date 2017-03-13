<?php

namespace Spatie\Image\Test\Manipulations;

use League\Flysystem\FileNotFoundException;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;
use Spatie\Image\Exceptions\InvalidManipulation;

class WatermarkTest extends TestCase
{
    /** @test */
    public function it_can_add_a_watermark()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_throws_an_exception_when_the_watermark_is_not_found()
    {
        $this->expectException(FileNotFoundException::class);

        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('not-a-file.png'))
            ->save($targetFile);
    }
}
