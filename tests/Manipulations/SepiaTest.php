<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class SepiaTest extends TestCase
{
    /** @test */
    public function it_can_make_an_image_sepia()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->sepia()->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}
