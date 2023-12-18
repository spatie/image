<?php

use Spatie\Image\Drivers\ImageDriver;
use Spatie\Image\Enums\AlignPosition;
use Spatie\Image\Enums\Unit;

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
]);
it('can insert with custom padding', function (
    ImageDriver $driver,
    array $insertPaddingArguments,
) {
    $targetFile = $this->tempDir->path("{$driver->driverName()}/watermark.png");

    $testImage = $driver->loadFile(getTestJpg());

    $testImage
        ->insertPadding(...$insertPaddingArguments)
        ->insert(getTestFile('watermark.png'), AlignPosition::BottomRight)
        ->save($targetFile);

    assertMatchesImageSnapshot($targetFile);
})->with('drivers')->with([
    [[20, 10]],
    [[10, 20]],
    [[10, 20, Unit::Percent]],
    [[20, 10, Unit::Percent]],
]);
