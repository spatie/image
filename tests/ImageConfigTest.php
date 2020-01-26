<?php

namespace Spatie\Image\Test;

use Spatie\Image\Exceptions\InvalidTemporaryDirectory;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class ImageConfigTest extends TestCase
{
    /** @test */
    public function it_can_modify_an_image_while_setting_temporary_path()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->setTemporaryDirectory(__DIR__.'/temp_conf')
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(50);
            })
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_will_throw_an_error_if_tempdir_corrupt()
    {
        $this->expectException(InvalidTemporaryDirectory::class);
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->setTemporaryDirectory('/user/willmostprobablynotexistandbecreatable')
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(50);
            })
            ->save($targetFile);
    }
}
