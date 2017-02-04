<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;

class BorderTest extends TestCase
{
    /** @test */
    public function it_can_add_a_border_to_an_image()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->border(10, 'black', Manipulations::BORDER_OVERLAY)->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}