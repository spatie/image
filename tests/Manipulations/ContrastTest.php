<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class ContrastTest extends TestCase
{
    /** @test */
    public function it_can_adjust_the_contrast()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->contrast(100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_will_throw_an_exception_when_passing_an_invalid_contrast()
    {
        $this->expectException(InvalidManipulation::class);

        Image::load($this->getTestJpg())->contrast(101);
    }
}
