<?php

namespace Spatie\Image\Test;

use Spatie\Image\Exceptions\InvalidManipulation;

beforeEach(fn () => true)->todo();

it('an exception will be throw when passing a negative number to width', function () {
    $this->manipulations->width(-50);
})->throws(InvalidManipulation::class);
