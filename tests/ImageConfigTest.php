<?php

namespace Spatie\Image\Test;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Exceptions\DefectiveConfiguration;

class ImageConfigTest extends TestCase
{
    /** @test */
    public function it_can_modify_an_image_while_setting_temporary_path_by_static_method()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');
        Image::setTemporaryDirectory(__DIR__.'/temp_conf');
        Image::load($this->getTestJpg())
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
        $this->expectException(DefectiveConfiguration::class);
        $targetFile = $this->tempDir->path('conversion.jpg');
        Image::setTemporaryDirectory('/user/willmostprobablynotexistandcreatable');
        Image::load($this->getTestJpg())
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(50);
            })
            ->save($targetFile);
    }

    protected function tearDown()
    {
        Image::setTemporaryDirectory(__DIR__.'/');
    }
}
