<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Test\TestCase;

class DevicePixelRatioTest extends TestCase
{
    /** @test */
    public function it_can_set_the_device_pixel_ratio()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->devicePixelRatio(2)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}