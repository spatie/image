<?php

namespace Spatie\Image\Test;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Intervention\Image\ImageManagerStatic as InterventionImage;

class ImageConfigTest extends TestCase
{
    /** @test */
    public function it_can_modify_an_image_while_setting_temporary_path_by_static_method()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');
        Image::setConfig("temp_dir", __DIR__ . "/temp_conf");
        Image::load($this->getTestJpg())
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(50);
            })
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_modify_an_image_while_setting_temporary_path_by_instance_method()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');
        Image::load($this->getTestJpg())
            ->setTemporaryDirectory(__DIR__ . "/temp_conf")
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(50);
            })
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

}
