<?php

use Spatie\Image\Image;
use Spatie\TemporaryDirectory\TemporaryDirectory;

uses()
    ->beforeAll(function () {
        (new TemporaryDirectory(getTempPath()))->delete();
    })
    ->beforeEach(function () {
        $this
            ->tempDir = (new TemporaryDirectory(getTestSupportPath()))
            ->name('temp');
    })
    ->in('.');

function getTestJpg(): string
{
    return getTestFile('test.jpg');
}

function getTestFile($fileName): string
{
    return getTestSupportPath('testFiles/'.$fileName);
}

function getTempPath($suffix = ''): string
{
    return getTestSupportPath('temp/'.$suffix);
}

function getTestSupportPath($suffix = ''): string
{
    return __DIR__."/TestSupport/{$suffix}";
}

function assertImageType(string $filePath, $expectedType): void
{
    $expectedType = image_type_to_mime_type($expectedType);

    $type = image_type_to_mime_type(exif_imagetype($filePath));

    expect($type)->toBe($expectedType);
}

dataset('drivers', [
    'imagick' => [Image::useImageDriver('imagick')],
    'gd' => [Image::useImageDriver('gd')],
]);
