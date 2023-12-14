---
title: Saving images
weight: 2
---

By default, calling the `save` method on the `Image`  will apply all manipulations to your original image file. 

```php
Image::load('example.jpg')
    ->sepia()
    ->save();
```

To save the image as a copy in a new location pass in the optional `$outputPath`.

```php
Image::load('example.jpg')
    ->sepia()
    ->save('sepia-example.jpg');
```

## Saving in a different image format

To save your image as a different image format you can simply change the extension in your output path.

```php
Image::load('example.jpg')
    ->save('example.png');
```

You can find the supported image formats here: [Supported image formats](/image/v3/formats)

## Changing JPEG quality

By calling the `quality` method on the `Image` you can specify the JPEG quality in percent. This only applies to saving JPEG files. 

The `$quality` argument should be an integer ranging from `0` to `100`.

```php
Image::load('example.jpg')
    ->quality(20)
    ->save();
```

![JPEG quality lowered](../../images/example-quality.jpg)
