<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\Fit;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

it('can fit an image in the given dimensions', function () {
    /** @var \Spatie\Image\Drivers\ImagickImageDriver $driver */
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit.jpg");

    $driver->load(getTestJpg())->fit(Fit::Contain, 10, 60)->save($targetFile);

    expect($targetFile)->toBeFile();
});
