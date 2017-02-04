<?php

namespace Spatie\Image\Test\Manipulations;

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


}