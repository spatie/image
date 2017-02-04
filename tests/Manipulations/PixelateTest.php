<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class PixelateTest extends TestCase
{
    /** @test */
    public function it_can_pixelate()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->pixelate(50)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}