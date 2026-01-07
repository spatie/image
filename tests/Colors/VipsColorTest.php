<?php

use Spatie\Image\Drivers\Vips\VipsColor;
use Spatie\Image\Exceptions\InvalidColor;

it('can create a new vips color object', function () {
    $color = new VipsColor;

    validateVipsColor($color, 255, 255, 255, 127);
});

it('can parse null', function () {
    $color = (new VipsColor)->parse(null);

    validateVipsColor($color, 255, 255, 255, 127);
});

it('can parse an integer', function () {
    $color = (new VipsColor)->parse(850736919);

    validateVipsColor($color, 181, 55, 23, 50);
});

it('can parse an array', function () {
    $color = (new VipsColor)->parse([181, 55, 23, 0.5]);

    validateVipsColor($color, 181, 55, 23, 64);
});

it('can parse a hex string', function () {
    $color = new VipsColor;
    $color->parse('#b53717');
    validateVipsColor($color, 181, 55, 23, 0);
});

it('can parse an rgba string', function () {
    $color = (new VipsColor)->parse('rgba(181, 55, 23, 1)');

    validateVipsColor($color, 181, 55, 23, 0);
});

it('can initialize from an integer', function () {
    $color = (new VipsColor)->initFromInteger(0);
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromInteger(2147483647);
    validateVipsColor($color, 255, 255, 255, 127);

    $color->initFromInteger(16777215);
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromInteger(2130706432);
    validateVipsColor($color, 0, 0, 0, 127);

    $color->initFromInteger(850736919);
    validateVipsColor($color, 181, 55, 23, 50);
});

it('can initialize from array', function () {
    $color = (new VipsColor)->initFromArray([0, 0, 0, 0]);
    validateVipsColor($color, 0, 0, 0, 127);

    $color->initFromArray([0, 0, 0, 1]);
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromArray([255, 255, 255, 1]);
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromArray([255, 255, 255, 0]);
    validateVipsColor($color, 255, 255, 255, 127);

    $color->initFromArray([255, 255, 255, 0.5]);
    validateVipsColor($color, 255, 255, 255, 64);

    $color->initFromArray([0, 0, 0]);
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromArray([255, 255, 255]);
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromArray([181, 55, 23]);
    validateVipsColor($color, 181, 55, 23, 0);

    $color->initFromArray([181, 55, 23, 0.5]);
    validateVipsColor($color, 181, 55, 23, 64);
});

it('init can initialize from a hex string', function () {
    $color = (new VipsColor)->initFromString('#cccccc');
    validateVipsColor($color, 204, 204, 204, 0);

    $color->initFromString('#b53717');
    validateVipsColor($color, 181, 55, 23, 0);

    $color->initFromString('ffffff');
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromString('ff00ff');
    validateVipsColor($color, 255, 0, 255, 0);

    $color->initFromString('#000');
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromString('000');
    validateVipsColor($color, 0, 0, 0, 0);
});

it('can initialize from an rgb string', function () {
    $color = (new VipsColor)->initFromString('rgb(1, 14, 144)');
    validateVipsColor($color, 1, 14, 144, 0);

    $color->initFromString('rgb (255, 255, 255)');
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromString('rgb(0,0,0)');
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromString('rgba(0,0,0,0)');
    validateVipsColor($color, 0, 0, 0, 127);

    $color->initFromString('rgba(0,0,0,0.5)');
    validateVipsColor($color, 0, 0, 0, 64);

    $color->initFromString('rgba(255, 0, 0, 0.5)');
    validateVipsColor($color, 255, 0, 0, 64);

    $color->initFromString('rgba(204, 204, 204, 0.9)');
    validateVipsColor($color, 204, 204, 204, 13);
});

it('can initialize from rgb value', function () {
    $color = (new VipsColor)->initFromRgb(0, 0, 0);
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromRgb(255, 255, 255);
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromRgb(181, 55, 23);
    validateVipsColor($color, 181, 55, 23, 0);
});

it('can initialize from rgba values', function () {
    $color = (new VipsColor)->initFromRgba(0, 0, 0, 1);
    validateVipsColor($color, 0, 0, 0, 0);

    $color->initFromRgba(255, 255, 255, 1);
    validateVipsColor($color, 255, 255, 255, 0);

    $color->initFromRgba(181, 55, 23, 1);
    validateVipsColor($color, 181, 55, 23, 0);

    $color->initFromRgba(181, 55, 23, 0);
    validateVipsColor($color, 181, 55, 23, 127);

    $color->initFromRgba(181, 55, 23, 0.5);
    validateVipsColor($color, 181, 55, 23, 64);
});

it('can get the int value of a color', function () {
    $color = new VipsColor;
    expect($color->getInt())->toEqual(2147483647);

    $color = new VipsColor([255, 255, 255]);
    expect($color->getInt())->toEqual(16777215);

    $color = new VipsColor([255, 255, 255, 1]);
    expect($color->getInt())->toEqual(16777215);

    $color = new VipsColor([181, 55, 23, 0.5]);
    expect($color->getInt())->toEqual(1085617943);

    $color = new VipsColor([181, 55, 23, 1]);
    expect($color->getInt())->toEqual(11876119);

    $color = new VipsColor([0, 0, 0, 0]);
    expect($color->getInt())->toEqual(2130706432);
});

it('can get the hex value from a color', function () {
    $color = new VipsColor;
    expect($color->getHex())->toEqual('ffffff');

    $color = new VipsColor([255, 255, 255, 1]);
    expect($color->getHex())->toEqual('ffffff');

    $color = new VipsColor([181, 55, 23, 0.5]);
    expect($color->getHex())->toEqual('b53717');

    $color = new VipsColor([0, 0, 0, 0]);
    expect($color->getHex('#'))->toEqual('#000000');
});

it('can convert the color to an array', function () {
    $color = new VipsColor;
    $i = $color->getArray();

    expect([255, 255, 255, 0])->toEqual($i);

    $color = new VipsColor([255, 255, 255, 1]);
    $i = $color->getArray();

    expect([255, 255, 255, 1])->toEqual($i);

    $color = new VipsColor([181, 55, 23, 0.5]);
    $i = $color->getArray();

    expect([181, 55, 23, 0.5])->toEqual($i);

    $color = new VipsColor([0, 0, 0, 1]);
    $i = $color->getArray();

    expect([0, 0, 0, 1])->toEqual($i);
});

it('can get the rgba values', function () {
    $color = (new VipsColor);
    expect($color->getRgba())->toEqual('rgba(255, 255, 255, 0.00)');

    $color = new VipsColor([255, 255, 255, 1]);
    expect($color->getRgba())->toEqual('rgba(255, 255, 255, 1.00)');

    $color = new VipsColor([181, 55, 23, 0.5]);
    expect($color->getRgba())->toEqual('rgba(181, 55, 23, 0.50)');

    $color = new VipsColor([0, 0, 0, 1]);
    expect($color->getRgba())->toEqual('rgba(0, 0, 0, 1.00)');
});

it('can check if colors differ from each other', function () {
    $color1 = new VipsColor([0, 0, 0]);
    $color2 = new VipsColor([0, 0, 0]);
    expect($color1->differs($color2))->toBeFalse();

    $color1 = new VipsColor([1, 0, 0]);
    $color2 = new VipsColor([0, 0, 0]);
    expect($color1->differs($color2))->toBeTrue();

    $color1 = new VipsColor([1, 0, 0]);
    $color2 = new VipsColor([0, 0, 0]);
    expect($color1->differs($color2, 10))->toBeFalse();

    $color1 = new VipsColor([127, 127, 127]);
    $color2 = new VipsColor([0, 0, 0]);
    expect($color1->differs($color2, 49))->toBeTrue();

    $color1 = new VipsColor([127, 127, 127]);
    $color2 = new VipsColor([0, 0, 0]);
    expect($color1->differs($color2, 50))->toBeFalse();
});

it('will thrown an exception for an invalid color', function () {
    new VipsColor('invalid-color');
})->throws(InvalidColor::class);

function validateVipsColor(VipsColor $color, $red, $green, $blue, $alpha): void
{
    expect($color)->toBeInstanceOf(VipsColor::class)
        ->and($color)
        ->red->toEqual($red)
        ->green->toEqual($green)
        ->blue->toEqual($blue)
        ->alpha->toEqual($alpha);
}
