<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class ManualCropTest extends TestCase
{
    /** @test */
    public function it_can_manual_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->manualCrop(100, 500, 30, 30)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}