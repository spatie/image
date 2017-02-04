<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class WidthTest extends TestCase
{
    /** @test */
    public function it_can_set_the_width()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->width(100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}