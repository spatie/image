<?php

namespace Spatie\Image\Test;

use Spatie\Image\Image;
use PHPUnit_Framework_TestCase;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImageTest extends PHPUnit_Framework_TestCase
{
    /** @var TemporaryDirectory */
    protected $tempDir;

    public function setUp()
    {
        $this->tempDir = (new TemporaryDirectory(__DIR__))
            ->name('temp')
            ->force()
            ->create()
            ->empty();
    }

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

    protected function getTestJpg(): string
    {
        return __DIR__.'/testfiles/test.jpg';
    }

    protected function assertImageType(string $filePath, $expectedType)
    {
        $expectedType = image_type_to_mime_type($expectedType);

        $type = image_type_to_mime_type(exif_imagetype($filePath));

        $this->assertTrue($expectedType === $type, "The file `{$filePath}` isn't an `{$expectedType}`, but an `{$type}`");
    }
}
