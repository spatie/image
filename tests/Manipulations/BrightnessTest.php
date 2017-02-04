<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class BrightnessTest extends TestCase
{
    /** @test */
    public function it_can_adjust_the_brightness()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->brightness(-75)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}