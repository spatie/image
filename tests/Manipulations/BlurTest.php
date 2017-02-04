<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class BlurTest extends TestCase
{
    /** @test */
    public function it_can_blur()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->blur(5)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}