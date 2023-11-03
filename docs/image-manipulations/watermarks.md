---
title: Watermarks
weight: 7
---

Adding a watermark to an `Image` is really simple with the `insert` method. You can use any value on the AlignPosition enum to specify the position.

```php
$image->insert('watermark.png', AlignPosition::BottomRight);
```

![Example](../../images/example-watermark.jpg)

If you want to additional modifications to your watermark you can also load it and pass it as the parameter:

```php
$image->insert(Image::load('watermark.png')->resize(100, 100));
```

### Example usage

```php
$image->insert('watermark.png', AlignPosition::center);
```

![Example](../../images/example-watermark-position.jpg)


## Adjusting the position

In addition to the AlignPosition you can also pass $x and $y coordinates to further specify the position of your inserted image:

```php
$image->insert('watermark.png', AlignPosition::BottomRight, 100, 100);
```
