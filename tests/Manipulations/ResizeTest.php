<?php

use Spatie\Image\Drivers\ImageDriver;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can resize an image', function (ImageDriver $driver) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/resize.png");

    $image = $driver->load(getTestJpg())->resize(100, 70)->save($targetFile);

    ray($image->getWidth(), $image->getHeight());

    expect($image->getWidth())->toBe(100);
    expect($image->getHeight())->toBe(70);

    assertMatchesImageSnapshot($targetFile);

    $this->markTestIncomplete('TODO: results of resize on GD look like a crop, not a resize.');
})->with('drivers');
