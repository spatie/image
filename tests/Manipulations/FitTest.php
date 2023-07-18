<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;
use Spatie\Image\Manipulations;

it('can fit an image', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->fit(Manipulations::FIT_CONTAIN, 500, 300)->save($targetFile);

    expect($targetFile)->toBeFile();
});

it('can fit an image with only width', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->fit(Manipulations::FIT_FILL_MAX, width: 500)->save($targetFile);

    expect($targetFile)->toBeFile();
    expect(getimagesize($targetFile)[0])->toBe(500);
    expect(getimagesize($targetFile)[1])->toBe(412);
});


it('can fit an image with only height', function () {
    $targetFile = $this->tempDir->path('conversion.jpg');

    Image::load(getTestJpg())->fit(Manipulations::FIT_FILL_MAX, height: 500)->save($targetFile);

    expect($targetFile)->toBeFile();
    expect(getimagesize($targetFile)[0])->toBe(607);
    expect(getimagesize($targetFile)[1])->toBe(500);
});

it('will throw an exception when passing an invalid fit', function () {
    Image::load(getTestJpg())->fit('blabla', 500, 300);
})->throws(InvalidManipulation::class);

it('will throw an exception when passing no width and no height', function () {
    Image::load(getTestJpg())->fit(Manipulations::FIT_CONTAIN);
})->throws(InvalidManipulation::class);
