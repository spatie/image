<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;

class FitTest extends TestCase
{
    /** @test */
    public function it_can_fit_an_image()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->fit(Manipulations::FIT_CONTAIN, 500, 300)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}