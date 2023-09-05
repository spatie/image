<?php

use Spatie\Image\Drivers\ImagickColor;
use Spatie\Image\Exceptions\InvalidColor;

it('can create an imagick color', function () {
    $color = new ImagickColor();
    validateImagickColor($color, 255, 255, 255, 0);
});

it('can parse null', function () {
    $color = (new ImagickColor())->parse(null);
    validateImagickColor($color, 255, 255, 255, 0);
});

it('can parse an integer', function () {
    $color = (new ImagickColor())->parse(16777215);
    validateImagickColor($color, 255, 255, 255, 0);

    $color = (new ImagickColor())->parse(4294967295);
    validateImagickColor($color, 255, 255, 255, 1);
});

it('can parse an array', function () {
    $color = (new ImagickColor())->parse([181, 55, 23, 0.5]);
    validateImagickColor($color, 181, 55, 23, 0.5);
});

it('can parse a hex string', function () {
    $color = (new ImagickColor())->parse('#b53717');
    validateImagickColor($color, 181, 55, 23, 1);
});

it('can parse an rgba string', function () {
    $color = (new ImagickColor())->parse('rgba(181, 55, 23, 1)');
    validateImagickColor($color, 181, 55, 23, 1);
});

it('can init from an integer', function (int $int, int $red, int $green, int $blue, float $alpha) {
    $color = (new ImagickColor())->initFromInteger($int);
    validateImagickColor($color, $red, $green, $blue, $alpha);
})->with([
    [0, 0, 0, 0, 0],
    [2147483647, 255, 255, 255, 0.5],
    [16777215, 255, 255, 255, 0],
    [2130706432, 0, 0, 0, 0.5],
    [867514135, 181, 55, 23, 0.2],
]);

it('can init from an array', function (array $input, int $red, int $green, int $blue, float $alpha) {
    $color = (new ImagickColor())->initFromArray($input);
    validateImagickColor($color, $red, $green, $blue, $alpha);
})->with([
    [[0, 0, 0, 0], 0, 0, 0, 0],
    [[0, 0, 0, 1], 0, 0, 0, 1],
    [[255, 255, 255, 1], 255, 255, 255, 1],
    [[255, 255, 255, 0], 255, 255, 255, 0],
    [[255, 255, 255, 0.5], 255, 255, 255, 0.5],
    [[0, 0, 0], 0, 0, 0, 1],
    [[255, 255, 255], 255, 255, 255, 1],
    [[181, 55, 23], 181, 55, 23, 1],
    [[181, 55, 23, 0.5], 181, 55, 23, 0.5],

]);

it('can init from a hex string', function (string $hexColor, int $red, int $green, int $blue, float $alpha) {
    $color = (new ImagickColor())->initFromString($hexColor);
    validateImagickColor($color, $red, $green, $blue, $alpha);
})->with([
    ['#cccccc', 204, 204, 204, 1],
    ['#b53717', 181, 55, 23, 1],
    ['ffffff', 255, 255, 255, 1],
    ['ff00ff', 255, 0, 255, 1],
    ['#000', 0, 0, 0, 1],
    ['000', 0, 0, 0, 1],
]);

it('can init from an rgb string', function (string $rgbString, int $red, int $green, int $blue, float $alpha) {
    $color = (new ImagickColor())->initFromString($rgbString);
    validateImagickColor($color, $red, $green, $blue, $alpha);
})->with([
    ['rgb(1, 14, 144)',  1, 14, 144, 1],
    ['rgb (255, 255, 255)', 255, 255, 255, 1],
    ['rgb(0,0,0)', 0, 0, 0, 1],
    ['rgba(0,0,0,0)', 0, 0, 0, 0],
    ['rgba(0,0,0,0.5)', 0, 0, 0, 0.5],
    ['rgba(255, 0, 0, 0.5)', 255, 0, 0, 0.5],
    ['rgba(204, 204, 204, 0.9)', 204, 204, 204, 0.9],
]);

it('can init from rgb values', function (array $inputValues, array $expectedValues) {
    $color = (new ImagickColor())->initFromRgb(...$inputValues);
    validateImagickColor($color, ...$expectedValues);
})->with([
    [[0, 0, 0], [0, 0, 0, 1]],
    [[255, 255, 255], [255, 255, 255, 1]],
    [[181, 55, 23], [181, 55, 23, 1]],
]);

it('can init from an rgba value', function (array $inputValues, array $expectedValues) {
    $color = (new ImagickColor())->initFromRgba(...$inputValues);
    validateImagickColor($color, ...$expectedValues);
})->with([
    [[0, 0, 0, 1], [0, 0, 0, 1]],
    [[255, 255, 255, 1], [255, 255, 255, 1]],
    [[181, 55, 23, 1], [181, 55, 23, 1]],
    [[181, 55, 23, 0], [181, 55, 23, 0]],
    [[181, 55, 23, 0.5], [181, 55, 23, 0.5]],
]);

test('it can get an int', function (array|null $input, int $expected) {
    $color = new ImagickColor($input);
    expect($color->getInt())->toEqual($expected);
})->with([
    [null, 16777215],
    [[255, 255, 255], 4294967295],
    [[255, 255, 255, 1], 4294967295],
    [[181, 55, 23, 0.2], 867514135],
    [[255, 255, 255, 0.5], 2164260863],
    [[181, 55, 23, 1], 4290066199],
    [[0, 0, 0, 0], 0],
]);

it('can get the hex value', function (array|null $input, string $expectedHex) {
    $color = new ImagickColor($input);
    expect($color->getHex())->toEqual($expectedHex);
})->with([
    [null, 'ffffff'],
    [[255, 255, 255], 'ffffff'],
    [[255, 255, 255, 1], 'ffffff'],
    [[181, 55, 23, 0.2], 'b53717'],
    [[255, 255, 255, 0.5], 'ffffff'],
    [[181, 55, 23, 1], 'b53717'],
    [[0, 0, 0, 0], '000000'],
]);

it('can get the array value', function (array|null $input, array $expected) {
    $color = new ImagickColor($input);
    expect($color->getArray())->toEqual($expected);
})->with([
    [null, [255, 255, 255, 0]],
    [[255, 255, 255], [255, 255, 255, 1]],
    [[255, 255, 255, 1], [255, 255, 255, 1]],
    [[181, 55, 23, 0.2], [181, 55, 23, 0.2]],
    [[255, 255, 255, 0.5], [255, 255, 255, 0.5]],
    [[181, 55, 23, 0.5], [181, 55, 23, 0.5]],
    [[0, 0, 0, 1], [0, 0, 0, 1]],
]);

it('can get the rgba value', function (array|null $input, string $expected) {
    $rgbaString = (new ImagickColor($input))->getRgba();
    expect($rgbaString)->toEqual($expected);
})->with([
    [[255, 255, 255, 1], 'rgba(255, 255, 255, 1.00)'],
    [[181, 55, 23, 0.5], 'rgba(181, 55, 23, 0.50)'],
    [[0, 0, 0, 1], 'rgba(0, 0, 0, 1.00)'],
    [[255, 255, 255, 0.5], 'rgba(255, 255, 255, 0.50)'],
]);

it('can check if a color is different', function () {
    $color1 = new ImagickColor([0, 0, 0]);
    $color2 = new ImagickColor([0, 0, 0]);
    expect($color1->differs($color2))->toBeFalse();

    $color1 = new ImagickColor([1, 0, 0]);
    $color2 = new ImagickColor([0, 0, 0]);
    expect($color1->differs($color2))->toBeTrue();

    $color1 = new ImagickColor([1, 0, 0]);
    $color2 = new ImagickColor([0, 0, 0]);
    expect($color1->differs($color2, 10))->toBeFalse();

    $color1 = new ImagickColor([127, 127, 127]);
    $color2 = new ImagickColor([0, 0, 0]);
    expect($color1->differs($color2, 49))->toBeTrue();

    $color1 = new ImagickColor([127, 127, 127]);
    $color2 = new ImagickColor([0, 0, 0]);
    expect($color1->differs($color2, 50))->toBeFalse();
});

it('will throw an exception for an invalid value', function () {
    new ImagickColor('invalid value');
})->throws(InvalidColor::class);

function validateImagickColor($color, int $red, int $green, int $blue, float $alpha): void
{
    expect($color)->toBeInstanceOf(ImagickColor::class);

    expect(round($color->getRedValue(), 2))->toEqual($red);
    expect(round($color->getGreenValue(), 2))->toEqual($green);
    expect(round($color->getBlueValue(), 2))->toEqual($blue);
    expect(round($color->getAlphaValue(), 2))->toEqual($alpha);
}
