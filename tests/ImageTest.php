<?php

namespace Spatie\Image\Test;

use Imagick;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

class ImageTest extends TestCase
{
    /** @test */
    public function it_can_modify_an_image_using_manipulations()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->manipulate(function (Manipulations $manipulations) {
                $manipulations
                    ->blur(50);
            })
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_modify_an_image_using_a_direct_manipulation_call()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->width(5)
            ->width(500)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_will_create_a_file_in_the_format_according_to_its_extension()
    {
        $targetFile = $this->tempDir->path('conversion.png');
        Image::load($this->getTestJpg())->save($targetFile);
        $this->assertImageType($targetFile, IMAGETYPE_PNG);

        $targetFile = $this->tempDir->path('conversion.gif');
        Image::load($this->getTestJpg())->save($targetFile);
        $this->assertImageType($targetFile, IMAGETYPE_GIF);

        $targetFile = $this->tempDir->path('conversion.jpg');
        Image::load($this->getTestJpg())->save($targetFile);
        $this->assertImageType($targetFile, IMAGETYPE_JPEG);

        $targetFile = $this->tempDir->path('conversion.pjpg');
        Image::load($this->getTestJpg())->save($targetFile);
        $this->assertImageType($targetFile, IMAGETYPE_JPEG);

        if (function_exists('imagecreatefromwebp')) {
            $targetFile = $this->tempDir->path('conversion.webp');
            Image::load($this->getTestJpg())->save($targetFile);
            $this->assertImageType($targetFile, IMAGETYPE_WEBP);
        }

        //test avif format with gd driver
        if (function_exists('imagecreatefromavif')) {
            $targetFile = $this->tempDir->path('conversion.avif');
            Image::load($this->getTestJpg())->save($targetFile);
            $this->assertImageType($targetFile, IMAGETYPE_AVIF);
        }

        //test avif format with imagick
        if (! empty(Imagick::queryFormats('AVIF*'))) {
            $targetFile = $this->tempDir->path('conversion.avif');
            Image::load($this->getTestJpg())->useImageDriver('imagick')->save($targetFile);
            $image = new Imagick($targetFile);
            $this->assertSame('AVIF', $image->getImageFormat());
        }
    }

    /** @test */
    public function it_will_not_force_the_format_according_to_the_output_extension_when_a_format_manipulation_was_already_set()
    {
        $targetFile = $this->tempDir->path('conversion.gif');

        Image::load($this->getTestJpg())->format('jpg')->save($targetFile);

        $this->assertImageType($targetFile, IMAGETYPE_JPEG);
    }

    /** @test */
    public function it_can_get_the_width_and_height_of_an_image()
    {
        $this->assertSame(340, Image::load($this->getTestJpg())->getWidth());

        $this->assertSame(280, Image::load($this->getTestJpg())->getHeight());
    }

    /** @test */
    public function the_image_driver_is_set_on_the_intervention_static_manager()
    {
        $image = Image::load($this->getTestJpg());

        $this->assertSame('gd', InterventionImage::getManager()->config['driver'] ?? null);

        $image->useImageDriver('imagick');

        $this->assertSame('imagick', InterventionImage::getManager()->config['driver'] ?? null);
    }

    /** @test */
    public function it_can_modify_files_with_the_same_name_if_they_are_in_different_folders()
    {
        $firstTargetFile = $this->tempDir->path('first.jpg');
        $secondTargetFile = $this->tempDir->path('second.jpg');

        Image::load($this->getTestFile('test.jpg'))
            ->sepia()
            ->apply()
            ->crop(Manipulations::CROP_CENTER, 100, 100)
            ->save($firstTargetFile);

        Image::load($this->getTestFile('testdir/test.jpg'))
            ->sepia()
            ->apply()
            ->crop(Manipulations::CROP_CENTER, 100, 100)
            ->save($secondTargetFile);

        $this->assertNotSame(file_get_contents($firstTargetFile), file_get_contents($secondTargetFile));
    }

    protected function assertImageType(string $filePath, $expectedType)
    {
        $expectedType = image_type_to_mime_type($expectedType);

        $type = image_type_to_mime_type(exif_imagetype($filePath));

        $this->assertSame($expectedType, $type, "The file `{$filePath}` isn't an `{$expectedType}`, but an `{$type}`");
    }
}
