<?php

use Spatie\Image\Drivers\Imagick\ImagickDriver;
use Spatie\Image\Image;
use Spatie\TemporaryDirectory\TemporaryDirectory;

uses()
    ->beforeAll(function () {
        (new TemporaryDirectory(getTempPath()))->delete();
    })
    ->beforeEach(function () {
        ray()->newScreen($this->name());

        $this
            ->tempDir = (new TemporaryDirectory(getTestSupportPath()))
            ->name('temp');
    })
    ->in('.');

function getTestJpg(): string
{
    return getTestFile('test.jpg');
}

function getTestGif(): string
{
    return getTestFile('test.gif');
}

function getTestPhoto(): string
{
    return getTestFile('test-photo.jpg');
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
    'vips' => [Image::useImageDriver('vips')],
]);

class CustomDriver extends ImagickDriver
{
    public function driverName(): string
    {
        return 'custom';
    }
}

expect()->extend('toHaveMime', function (string $expectedMime) {
    $file = finfo_open(FILEINFO_MIME_TYPE);
    $actualMime = finfo_file($file, $this->value);

    expect($actualMime)->toBe($expectedMime);
});

function avifIsSupported(string $driverName): bool
{
    static $supported = [];

    if (array_key_exists($driverName, $supported)) {
        return $supported[$driverName];
    }

    // Reporting the format as available is not enough: a driver can be built
    // against a libheif without an AVIF encoder, which only fails once we
    // actually encode something. Write a tiny image to find out for sure.
    return $supported[$driverName] = match ($driverName) {
        'gd' => function_exists('imageavif') && canEncodeAvif('gd'),
        'imagick' => count(Imagick::queryFormats('AVIF*')) > 0 && canEncodeAvif('imagick'),
        'vips' => canEncodeAvif('vips'),
        default => false,
    };
}

function canEncodeAvif(string $driverName): bool
{
    $path = tempnam(sys_get_temp_dir(), 'avif-probe').'.avif';

    try {
        Spatie\Image\Image::useImageDriver($driverName)
            ->loadFile(getTestJpg())
            ->width(16)
            ->save($path);

        return file_exists($path) && filesize($path) > 0;
    } catch (Throwable) {
        return false;
    } finally {
        if (file_exists($path)) {
            unlink($path);
        }
    }
}

function skipIfImagickDoesNotSupportFormat(string $format)
{
    $formats = Imagick::queryFormats('*');

    if (! in_array(strtoupper($format), $formats)) {
        test()->markTestSkipped('Imagick does not support this format. FOO');
    }
}

function skipWhenRunningOnGitHub(): void
{
    if (getenv('GITHUB_ACTIONS') !== false) {
        test()->markTestSkipped('This test cannot run on GitHub actions');
    }
}

function skipWhenRunningLocally(): void
{
    if (getenv('GITHUB_ACTIONS') === false) {
        test()->markTestSkipped('This test cannot run locally');
    }
}
