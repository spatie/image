<?php

namespace Spatie\Image\Test\Manipulations;

use League\Glide\Filesystem\FileNotFoundException;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;
use Spatie\Image\Test\TestCase;

it('can add a watermark', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('throws an exception when the watermark is not found', function () {
    $this->expectException(FileNotFoundException::class);

    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('not-a-file.png'))
        ->save($targetFile);
});

it('can set the width of the watermark in px', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkWidth(100)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can set the width of the watermark in percent', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkWidth(50, Manipulations::UNIT_PERCENT)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can set the height of the watermark in px', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkHeight(100)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can set the height of the watermark in percent', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkHeight(50, Manipulations::UNIT_PERCENT)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can fit the watermark within dimensions', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkHeight(200)
        ->watermarkWidth(50)
        ->watermarkFit(Manipulations::FIT_CROP)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('throws an exception when using invalid fit method', function () {
    $this->expectException(InvalidManipulation::class);

    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkFit('not-a-real-fit-method')
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can add padding to the watermark', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkPadding(50, 50, Manipulations::UNIT_PERCENT)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can set the watermarks position', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkPosition(Manipulations::POSITION_CENTER)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('throws an exception when using an invalid position', function () {
    $this->expectException(InvalidManipulation::class);

    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkPosition('in-a-galaxy-far-far-away')
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('can set the opacity of a watermark', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->useImageDriver('imagick')
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkOpacity(50)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});

it('throws an exception when watermark opacity is out of range', function () {
    $this->expectException(InvalidManipulation::class);

    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load($this->getTestJpg())
        ->useImageDriver('imagick')
        ->watermark($this->getTestFile('watermark.png'))
        ->watermarkOpacity(500)
        ->save($targetFile);

    $this->assertFileExists($targetFile);
});
