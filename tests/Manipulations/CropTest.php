<?php

use Spatie\Image\Enums\CropPosition;
use Spatie\Image\Image;

it('can crop an image relative to a position', function() {
    $driver = Image::useImageDriver('gd');

    $targetFile = $this->tempDir->path("{$driver->driverName()}/manual-crop.jpg");

    $driver->load(getTestJpg())->crop(500, 100, CropPosition::TopLeft)->save($targetFile);

    expect($targetFile)->toBeFile();

    $savedImage = $driver->load($targetFile);
    expect($savedImage->getWidth())->toBe(50);
    expect($savedImage->getHeight())->toBe(50);
});
