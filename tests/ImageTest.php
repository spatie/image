<?php

namespace Spatie\Image\Test;

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
    }

    /** @test */
    public function it_will_not_force_the_format_according_to_the_output_extension_when_a_format_manipulation_was_already_set()
    {
        $targetFile = $this->tempDir->path('conversion.gif');

        Image::load($this->getTestJpg())->format('jpg')->save($targetFile);

        $this->assertImageType($targetFile, IMAGETYPE_JPEG);
    }

    /** @test */
    public function it_can_optimize_an_image()
    {
        $targetFile = $this->tempDir->path('optimized.jpg');

        Image::load($this->getTestFile('test.jpg'))
            ->optimize()
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    protected function assertImageType(string $filePath, $expectedType)
    {
        $expectedType = image_type_to_mime_type($expectedType);

        $type = image_type_to_mime_type(exif_imagetype($filePath));

        $this->assertTrue($expectedType === $type, "The file `{$filePath}` isn't an `{$expectedType}`, but an `{$type}`");
    }
}
