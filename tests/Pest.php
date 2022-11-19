<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

use Spatie\TemporaryDirectory\TemporaryDirectory;

uses()
    ->beforeEach(function () {
        $this->tempDir = (new TemporaryDirectory(__DIR__))
            ->name('temp')
            ->force()
            ->create()
            ->empty();
    })
    ->in('.');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
*/

function getTestJpg(): string
{
    return getTestFile('test.jpg');
}

function getTestFile($fileName): string
{
    return __DIR__."/testfiles/{$fileName}";
}

function assertImageType(string $filePath, $expectedType)
{
    $expectedType = image_type_to_mime_type($expectedType);

    $type = image_type_to_mime_type(exif_imagetype($filePath));

    expect($type)->toBe($expectedType);
}
