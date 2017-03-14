<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;
use League\Flysystem\FileNotFoundException;
use Spatie\Image\Exceptions\InvalidManipulation;

class WatermarkTest extends TestCase
{
    /** @test */
    public function it_can_add_a_watermark()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_throws_an_exception_when_the_watermark_is_not_found()
    {
        $this->expectException(FileNotFoundException::class);

        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('not-a-file.png'))
            ->save($targetFile);
    }

    /** @test */
    public function it_can_set_the_width_of_the_watermark_in_px()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkWidth(100)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_width_of_the_watermark_in_percent()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkWidth(50, Manipulations::UNIT_PERCENT)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_height_of_the_watermark_in_px()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkHeight(100)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_height_of_the_watermark_in_percent()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkHeight(50, Manipulations::UNIT_PERCENT)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_fit_the_watermark_within_dimensions()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkHeight(200)
            ->watermarkWidth(50)
            ->watermarkFit(Manipulations::FIT_CROP)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_throws_an_exception_when_using_invalid_fit_method()
    {
        $this->expectException(InvalidManipulation::class);

        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkFit('not-a-real-fit-method')
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_add_padding_to_the_watermark()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkPadding(50, 50, Manipulations::UNIT_PERCENT)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_watermarks_position()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkPosition(Manipulations::POSITION_CENTER)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_throws_an_exception_when_using_an_invalid_position()
    {
        $this->expectException(InvalidManipulation::class);

        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkPosition('in-a-galaxy-far-far-away')
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_can_set_the_opacity_of_a_watermark()
    {
        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->useImageDriver('imagick')
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkOpacity(50)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }

    /** @test */
    public function it_throws_an_exception_when_watermark_opacity_is_out_of_range()
    {
        $this->expectException(InvalidManipulation::class);

        $targetFile = $this->tempDir->path('conversion.jpg');

        Image::load($this->getTestJpg())
            ->useImageDriver('imagick')
            ->watermark($this->getTestFile('watermark.png'))
            ->watermarkOpacity(500)
            ->save($targetFile);

        $this->assertFileExists($targetFile);
    }
}
