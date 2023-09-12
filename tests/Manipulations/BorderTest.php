<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\BorderType;
use Spatie\Image\Exceptions\InvalidManipulation;

use Spatie\Image\Image;
use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can add a border to an image', function (ImageDriver $driver, array $borderArguments) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/border.png");

    $driver->load(getTestJpg())->border(...$borderArguments)->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[100, BorderType::Overlay, 'FAAAAA']],
    [[50, BorderType::Overlay, '333333']],
    [[100, BorderType::Shrink, 'FAAAAA']],
    [[100, BorderType::Expand, 'FAAAAA']],
]);
