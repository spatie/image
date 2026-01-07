<?php

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

function isRunningOnGitHub(): bool
{
    // Check multiple sources as getenv() may not work in all contexts
    if (getenv('GITHUB_ACTIONS') !== false) {
        return true;
    }

    if (isset($_SERVER['GITHUB_ACTIONS'])) {
        return true;
    }

    if (isset($_ENV['GITHUB_ACTIONS'])) {
        return true;
    }

    // Also check CI environment variable as fallback
    if (getenv('CI') !== false || isset($_SERVER['CI']) || isset($_ENV['CI'])) {
        return true;
    }

    return false;
}

function vipsIsAvailable(): bool
{
    if (! extension_loaded('ffi') || ! ini_get('ffi.enable')) {
        return false;
    }

    $libraryPaths = ['/opt/homebrew/lib/', '/usr/local/lib/', '/usr/lib/', '/usr/lib/x86_64-linux-gnu/'];
    $vipsLib = PHP_OS_FAMILY === 'Darwin' ? 'libvips.42.dylib' : 'libvips.so.42';

    foreach ($libraryPaths as $path) {
        if (file_exists($path.$vipsLib)) {
            return true;
        }
    }

    return false;
}

dataset('drivers', function () {
    yield 'imagick' => [Image::useImageDriver('imagick')];
    yield 'gd' => [Image::useImageDriver('gd')];

    if (vipsIsAvailable()) {
        yield 'vips' => [Image::useImageDriver('vips')];
    }
});

expect()->extend('toHaveMime', function (string $expectedMime) {
    $file = finfo_open(FILEINFO_MIME_TYPE);
    $actualMime = finfo_file($file, $this->value);

    expect($actualMime)->toBe($expectedMime);
});

function avifIsSupported(string $driverName): bool
{
    if ($driverName === 'vips') {
        // Check if vips can actually save AVIF by trying to find the saver
        // The saver must be a HEIF saver (vips uses HEIF for AVIF) - not a fallback
        try {
            $ffi = \Jcupitt\Vips\FFI::vips();
            $saver = $ffi->vips_foreign_find_save('.avif');

            // Ensure the saver is actually a HEIF/AVIF saver, not a fallback
            return ! empty($saver) && (
                str_contains(strtolower($saver), 'heif') ||
                str_contains(strtolower($saver), 'avif')
            );
        } catch (\Throwable) {
            return false;
        }
    }

    if ($driverName === 'gd') {
        return function_exists('imageavif');
    }

    if ($driverName === 'imagick') {
        return count(Imagick::queryFormats('AVIF*')) > 0;
    }

    return false;
}

function skipIfImagickDoesNotSupportFormat(string $format)
{
    if (! in_array(strtoupper($format), Imagick::queryFormats('*'))) {
        test()->markTestSkipped('Imagick does not support this format.');
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
