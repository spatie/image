---
title: Retrieving properties
weight: 3
---

You can retrieve the width and height of an image:

```php
Image::load('example.jpg')->getWidth() // returns 1600
Image::load('example.jpg')->getHeight() // returns 1052
```