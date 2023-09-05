<?php

use Spatie\Image\Drivers\GdColor;
use Spatie\Image\Exceptions\InvalidColor;

it('can create a new gd color object', function () {
    $color = new GdColor();

    validateGdColor($color, 255, 255, 255, 127);
});

it('can parse null', function () {
    $color = (new GdColor())->parse(null);

    validateGdColor($color, 255, 255, 255, 127);
});

it('can parse an integer', function () {
    $color = (new GdColor())->parse(850736919);

    validateGdColor($color, 181, 55, 23, 50);
});

it('can parse an array', function () {
    $color = (new GdColor())->parse([181, 55, 23, 0.5]);

    validateGdColor($color, 181, 55, 23, 64);
});

it('can parse a hex string', function () {
    $color = new GdColor();
    $color->parse('#b53717');
    validateGdColor($color, 181, 55, 23, 0);
});

it('can parse an rgba string', function () {
    $color = (new GdColor())->parse('rgba(181, 55, 23, 1)');

    validateGdColor($color, 181, 55, 23, 0);
});

it('can initialize from an integer', function () {
    $color = (new GdColor())->initFromInteger(0);
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromInteger(2147483647);
    validateGdColor($color, 255, 255, 255, 127);

    $color->initFromInteger(16777215);
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromInteger(2130706432);
    validateGdColor($color, 0, 0, 0, 127);

    $color->initFromInteger(850736919);
    validateGdColor($color, 181, 55, 23, 50);
});

it('can initialize from array', function () {
    $color = (new GdColor())->initFromArray([0, 0, 0, 0]);
    validateGdColor($color, 0, 0, 0, 127);

    $color->initFromArray([0, 0, 0, 1]);
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromArray([255, 255, 255, 1]);
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromArray([255, 255, 255, 0]);
    validateGdColor($color, 255, 255, 255, 127);

    $color->initFromArray([255, 255, 255, 0.5]);
    validateGdColor($color, 255, 255, 255, 64);

    $color->initFromArray([0, 0, 0]);
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromArray([255, 255, 255]);
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromArray([181, 55, 23]);
    validateGdColor($color, 181, 55, 23, 0);

    $color->initFromArray([181, 55, 23, 0.5]);
    validateGdColor($color, 181, 55, 23, 64);
});

it('init can initialize from a hex string', function () {
    $color = (new GdColor())->initFromString('#cccccc');
    validateGdColor($color, 204, 204, 204, 0);

    $color->initFromString('#b53717');
    validateGdColor($color, 181, 55, 23, 0);

    $color->initFromString('ffffff');
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromString('ff00ff');
    validateGdColor($color, 255, 0, 255, 0);

    $color->initFromString('#000');
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromString('000');
    validateGdColor($color, 0, 0, 0, 0);
});

it('can initialize from an rgb string', function () {
    $color = (new GdColor())->initFromString('rgb(1, 14, 144)');
    validateGdColor($color, 1, 14, 144, 0);

    $color->initFromString('rgb (255, 255, 255)');
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromString('rgb(0,0,0)');
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromString('rgba(0,0,0,0)');
    validateGdColor($color, 0, 0, 0, 127);

    $color->initFromString('rgba(0,0,0,0.5)');
    validateGdColor($color, 0, 0, 0, 64);

    $color->initFromString('rgba(255, 0, 0, 0.5)');
    validateGdColor($color, 255, 0, 0, 64);

    $color->initFromString('rgba(204, 204, 204, 0.9)');
    validateGdColor($color, 204, 204, 204, 13);
});

it('can initialize from rgb value', function () {
    $color = (new GdColor())->initFromRgb(0, 0, 0);
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromRgb(255, 255, 255);
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromRgb(181, 55, 23);
    validateGdColor($color, 181, 55, 23, 0);
});

it('can initialize from rgba values', function () {
    $color = (new GdColor())->initFromRgba(0, 0, 0, 1);
    validateGdColor($color, 0, 0, 0, 0);

    $color->initFromRgba(255, 255, 255, 1);
    validateGdColor($color, 255, 255, 255, 0);

    $color->initFromRgba(181, 55, 23, 1);
    validateGdColor($color, 181, 55, 23, 0);

    $color->initFromRgba(181, 55, 23, 0);
    validateGdColor($color, 181, 55, 23, 127);

    $color->initFromRgba(181, 55, 23, 0.5);
    validateGdColor($color, 181, 55, 23, 64);
});

it('can get the int value of a color', function () {
    $color = new GdColor();
    expect($color->getInt())->toEqual(2147483647);

    $color = new GdColor([255, 255, 255]);
    expect($color->getInt())->toEqual(16777215);

    $color = new GdColor([255, 255, 255, 1]);
    expect($color->getInt())->toEqual(16777215);

    $color = new GdColor([181, 55, 23, 0.5]);
    expect($color->getInt())->toEqual(1085617943);

    $color = new GdColor([181, 55, 23, 1]);
    expect($color->getInt())->toEqual(11876119);

    $color = new GdColor([0, 0, 0, 0]);
    expect($color->getInt())->toEqual(2130706432);
});

it('can get the hex value from a color', function () {
    $color = new GdColor();
    expect($color->getHex())->toEqual('ffffff');

    $color = new GdColor([255, 255, 255, 1]);
    expect($color->getHex())->toEqual('ffffff');

    $color = new GdColor([181, 55, 23, 0.5]);
    expect($color->getHex())->toEqual('b53717');

    $color = new GdColor([0, 0, 0, 0]);
    expect($color->getHex('#'))->toEqual('#000000');
});

it('can convert the color to an array', function () {
    $color = new GdColor();
    $i = $color->getArray();

    expect([255, 255, 255, 0])->toEqual($i);

    $color = new GdColor([255, 255, 255, 1]);
    $i = $color->getArray();

    expect([255, 255, 255, 1])->toEqual($i);

    $color = new GdColor([181, 55, 23, 0.5]);
    $i = $color->getArray();

    expect([181, 55, 23, 0.5])->toEqual($i);

    $color = new GdColor([0, 0, 0, 1]);
    $i = $color->getArray();

    expect([0, 0, 0, 1])->toEqual($i);
});

it('can get the rgba values', function () {
    $color = (new GdColor());
    expect($color->getRgba())->toEqual('rgba(255, 255, 255, 0.00)');

    $color = new GdColor([255, 255, 255, 1]);
    expect($color->getRgba())->toEqual('rgba(255, 255, 255, 1.00)');

    $color = new GdColor([181, 55, 23, 0.5]);
    expect($color->getRgba())->toEqual('rgba(181, 55, 23, 0.50)');

    $color = new GdColor([0, 0, 0, 1]);
    expect($color->getRgba())->toEqual('rgba(0, 0, 0, 1.00)');
});

it('can check if colors differ from each other', function () {
    $color1 = new GdColor([0, 0, 0]);
    $color2 = new GdColor([0, 0, 0]);
    expect($color1->differs($color2))->toBeFalse();

    $color1 = new GdColor([1, 0, 0]);
    $color2 = new GdColor([0, 0, 0]);
    expect($color1->differs($color2))->toBeTrue();

    $color1 = new GdColor([1, 0, 0]);
    $color2 = new GdColor([0, 0, 0]);
    expect($color1->differs($color2, 10))->toBeFalse();

    $color1 = new GdColor([127, 127, 127]);
    $color2 = new GdColor([0, 0, 0]);
    expect($color1->differs($color2, 49))->toBeTrue();

    $color1 = new GdColor([127, 127, 127]);
    $color2 = new GdColor([0, 0, 0]);
    expect($color1->differs($color2, 50))->toBeFalse();
});

it('will thrown an exception for an invalid color', function () {
    new GdColor('invalid-color');
})->throws(InvalidColor::class);

function validateGdColor(GdColor $color, $red, $green, $blue, $alpha): void
{
    expect($color)->toBeInstanceOf(GdColor::class)
        ->and($color)
        ->red->toEqual($red)
        ->green->toEqual($green)
        ->blue->toEqual($blue)
        ->alpha->toEqual($alpha);
}
