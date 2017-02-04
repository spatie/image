<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class GammaTest extends TestCase
{
    /** @test */
    public function it_can_adjust_the_gamma()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->gamma(9.5)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}