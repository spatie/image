<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;

class CropTest extends TestCase
{
    /** @test */
    public function it_can_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, 100, 500)->save($targetFile);

        $this->assertFileExists($targetFile);
    }


}