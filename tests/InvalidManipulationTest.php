<?php

namespace Spatie\Image\Test;

use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Manipulations;

beforeEach(fn() => true)->skip();

it('an exception will be throw when passing a negative number to width', function () {
    $this->manipulations->width(-50);
})->throws(InvalidManipulation::class);
