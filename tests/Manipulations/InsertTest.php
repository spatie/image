<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;

use function Spatie\Snapshots\assertMatchesImageSnapshot;

it('can insert another image', function (
    ImageDriver $driver,
    array $insertArguments,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/watermark.png");

    $testImage = $driver->loadFile(getTestJpg());

    $testImage
        ->insert(getTestFile('watermark.png'), ...$insertArguments)
        ->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[AlignPosition::Center]],
    [[AlignPosition::Top]],
    [[AlignPosition::Left]],
    [[AlignPosition::TopLeft]],
    [[AlignPosition::Bottom]],
    [[AlignPosition::BottomRight]],
    [[AlignPosition::BottomRight, 50, 75]],
    [[AlignPosition::Center, 50, 75]],
    [[AlignPosition::Center, 50, 75,50]],
    [[AlignPosition::Center, 50, 75,80]],
    [[AlignPosition::Center, 50, 75,90]],
]);
