<?php

namespace Spatie\Image\Test\Manipulations;

use League\Glide\Filesystem\FileNotFoundException;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can add a watermark', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('throws an exception when the watermark is not found', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('not-a-file.png'))
        ->save($targetFile);
})->throws(FileNotFoundException::class);

it('can set the width of the watermark in px', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkWidth(100)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can set the width of the watermark in percent', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkWidth(50, Manipulations::UNIT_PERCENT)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can set the height of the watermark in px', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkHeight(100)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can set the height of the watermark in percent', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkHeight(50, Manipulations::UNIT_PERCENT)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can fit the watermark within dimensions', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkHeight(200)
        ->watermarkWidth(50)
        ->watermarkFit(Manipulations::FIT_CROP)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('throws an exception when using invalid fit method', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkFit('not-a-real-fit-method')
        ->save($targetFile);

    expect($targetFile)->toBeFile();
})->throws(InvalidManipulation::class);

it('can add padding to the watermark', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkPadding(50, 50, Manipulations::UNIT_PERCENT)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can set the watermarks position', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkPosition(Manipulations::POSITION_CENTER)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('throws an exception when using an invalid position', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->watermark(getTestFile('watermark.png'))
        ->watermarkPosition('in-a-galaxy-far-far-away')
        ->save($targetFile);

    expect($targetFile)->toBeFile();
})->throws(InvalidManipulation::class);

it('can set the opacity of a watermark', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->useImageDriver('imagick')
        ->watermark(getTestFile('watermark.png'))
        ->watermarkOpacity(50)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('throws an exception when watermark opacity is out of range', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())
        ->useImageDriver('imagick')
        ->watermark(getTestFile('watermark.png'))
        ->watermarkOpacity(500)
        ->save($targetFile);

    expect($targetFile)->toBeFile();
})->throws(InvalidManipulation::class);
