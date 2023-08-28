<?php

namespace Spatie\Image\Test\Manipulations;

use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

it('can fit an image in the given dimensions', function () {
    /** @var \Spatie\Image\Drivers\ImagickImageDriver $driver */
    $driver = Image::useImageDriver('imagick');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/fit.jpg");

    $driver->load(getTestJpg())->fit(Fit::Contain, 100, 60)->save($targetFile);

    expect($targetFile)->toBeFile();
});
