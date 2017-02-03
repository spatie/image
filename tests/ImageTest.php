<?php

namespace Spatie\Image\Test;

use PHPUnit\Framework\TestCase;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ImageTest extends TestCase
{
    /** @var \Spatie\TemporaryDirectory\TemporaryDirectory */
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

    /** @test */
    public function it_will_not_force_the_format_according_to_the_output_extension_when_a_format_manipulation_was_already_set()
    {
        $targetFile = $this->tempDir->path('conversion.gif');

        Image::load($this->getTestJpg())->format('jpg')->save($targetFile);

        $this->assertImageType($targetFile, IMAGETYPE_JPEG);
    }

    /** @test */
    public function it_can_set_the_orientation()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->orientation(Manipulations::ORIENTATION_90)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->crop(Manipulations::CROP_BOTTOM, 100, 500)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_focal_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->focalCrop(100, 500, 100, 100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_manual_crop()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->manualCrop(100, 500, 30, 30)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_width()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->width(100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_height()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->height(100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_fit_an_image()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->fit(Manipulations::FIT_CONTAIN, 500, 300)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_device_pixel_ratio()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->devicePixelRatio(2)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_adjust_the_brightness()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->brightness(-75)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_adjust_the_gamma()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->gamma(9.5)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_adjust_the_contrast()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->contrast(100)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_sharpen()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->sharpen(50)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_blur()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->blur(5)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_pixelate()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->pixelate(50)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_make_an_image_greyscale()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->greyscale()->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_make_an_image_sepia()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->sepia()->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_add_a_border_to_an_image()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())->border(10, 'black', Manipulations::BORDER_OVERLAY)->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    protected function assertImageType(string $filePath, $expectedType)
    {
        $expectedType = image_type_to_mime_type($expectedType);

        $type = image_type_to_mime_type(exif_imagetype($filePath));

        $this->assertTrue($expectedType === $type, "The file `{$filePath}` isn't an `{$expectedType}`, but an `{$type}`");
    }

    protected function getTestJpg(): string
    {
        return $this->getTestFile('test.jpg');
    }

    protected function getTestFile($fileName): string
    {
        return __DIR__."/testfiles/{$fileName}";
    }
}
