<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class FocalCropTest extends TestCase
{
    /** @test */
    public function it_can_focal_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->focalCrop(100, 500, 100, 100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}