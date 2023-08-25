<?php

use Spatie\TemporaryDirectory\TemporaryDirectory;

uses()
    ->beforeEach(function () {
        $this->tempDir = (new TemporaryDirectory(getTestPath()))
            ->name('temp')
            ->force()
            ->create()
            ->empty();
    })
    ->in('.');

function getTestJpg(): string
{
    return getTestFile('test.jpg');
}

function getTestFile($fileName): string
{
    return getTestPath($fileName);
}

function getTestPath($suffix = ''): string
{
    return __DIR__."/TestSupport/testFiles/{$suffix}";
}

function assertImageType(string $filePath, $expectedType): void
{
    $expectedType = image_type_to_mime_type($expectedType);

    $type = image_type_to_mime_type(exif_imagetype($filePath));

    expect($type)->toBe($expectedType);
}
