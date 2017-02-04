<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;
use Spatie\Image\Exceptions\InvalidManipulation;

class FitTest extends TestCase
{
    /** @test */
    public function it_can_fit_an_image()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->fit(Manipulations::FIT_CONTAIN, 500, 300)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_will_throw_an_exception_when_passing_an_invalid_fit()
    {
        $this->expectException(InvalidManipulation::class);

        Image::load($this->getTestJpg())->fit('blabla', 500, 300);
    }
}
