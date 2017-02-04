<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;
use Spatie\Image\Exceptions\InvalidManipulation;

class CropTest extends TestCase
{
    /** @test */
    public function it_can_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, 100, 500)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_will_throw_an_exception_when_passing_an_invalid_crop_method()
    {
        $this->expectException(InvalidManipulation::class);

        Image::load($this->getTestJpg())->crop('blabla', 10, 10);
    }

    /** @test */
    public function it_will_throw_an_exception_when_passing_a_negative_width()
    {
        $this->expectException(InvalidManipulation::class);

        Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, -10, 10);
    }

    /** @test */
    public function it_will_throw_an_exception_when_passing_a_negative_height()
    {
        $this->expectException(InvalidManipulation::class);

        Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, 10, -10);
    }
}
