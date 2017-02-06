<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class GreyscaleTest extends TestCase
{
    /** @test */
    public function it_can_make_an_image_greyscale()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->greyscale()->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}
