---
title: Colors
weight: 1
---

## Picking a color
The `pickColor` method will allow you to pick the color of a pixel at a given coordinate.

```php
$image = Image::load(string $pathToImage)->pickColor($x, $y, ColorFormat::Rgba);
```

The `pickColor` method will return a color base on the format you pass through.

These are the available formats:
- `ColorFormat::Rgba` - Returns a color in the format `rgba(255, 255, 255, 1)`
- `ColorFormat::Hex` - Returns a color in the format `#ffffff`
- `ColorFormat::Int` - Returns a color in the format `16777215`
- `ColorFormat::Array` - Returns a color in the format `[255, 255, 255, 1]`
