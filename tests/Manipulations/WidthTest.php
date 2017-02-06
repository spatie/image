<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;
use Spatie\Image\Exceptions\InvalidManipulation;

class WidthTest extends TestCase
{
    /** @test */
    public function it_can_set_the_width()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->width(100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_will_throw_an_exception_when_passing_an_invalid_width()
    {
        $this->expectException(InvalidManipulation::class);

        Image::load($this->getTestJpg())->width(-10);
    }
}
