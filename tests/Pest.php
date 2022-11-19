<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
*/

use Spatie\Image\Test\TestCase;
use Spatie\TemporaryDirectory\TemporaryDirectory;

uses(TestCase::class)->in('.');

//uses()
//    ->beforeEach(function () {
//        $this->tempDir = (new TemporaryDirectory(__DIR__))
//            ->name('temp')
//            ->force()
//            ->create()
//            ->empty();
//    })
//    ->in('.');

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

//function getTestJpg(): string
//{
//    return getTestFile('test.jpg');
//}
//
//function getTestFile($fileName): string
//{
//    return __DIR__."/testfiles/{$fileName}";
//}
