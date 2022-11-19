<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can focal crop', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->focalCrop(100, 500, 100, 100)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can focal crop with zoom', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->focalCrop(100, 500, 100, 100, 2)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('will throw an exception when passing an invalid width', function () {
    Image::load(getTestJpg())->focalCrop(-100, 500, 100, 100);
})->throws(InvalidManipulation::class);

it('will throw an exception when passing an zoom value not in range', function () {
    Image::load(getTestJpg())->focalCrop(100, 500, 100, 100, 900);
})->throws(InvalidManipulation::class);
