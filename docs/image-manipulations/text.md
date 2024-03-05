---
title: Adding text
weight: 9
---

Using the `text` method you can easily position and add text. By default, it will be placed in the top left corner of the image.

```php
$image->text('Hello there!');
```

## Text position

Using the `x` and `y` parameters, you can set the location of the text.

### Example usage

```php
$image->text('Hello there!', x: 10, y: 10);
```

## Font size

Using the `fontSize` parameter, you can set the font size in pixels.

### Example usage

```php
$image->text('Hello there!', fontSize: 100);
```

## Font color

Using the `color` parameter, you can set the font color.

The color can be a color name (see [all available color names](https://developer.mozilla.org/en/docs/Web/CSS/color_value#Color_keywords)) or hexadecimal RGB(A).

### Example usage

```php
$image->text('Hello there!', color: '');
```

## Font family

Using the `fontPath` parameter, which is required when using `GD` you can specify a path to a font to use

### Example usage

```php
$image->text('Hello there!', fontPath: __DIR__ . '/arial.ttf');
```

## Wrapping text

Using the `width` parameter, you can define a max width in pixels that the text should be and the package will wrap the text automatically.

### Example usage

```php
$image->text('Hello there! This is a long piece of text that we should wrap.', width: 1000);
```
