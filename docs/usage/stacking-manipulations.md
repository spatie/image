---
title: Stacking manipulations
weight: 5
---

Every manipulation you call will be applied. When calling a manipulation method multiple times each call will be applied immediately.

### Example usage

```php
// This will lower the brightness first by 40% and then by 20%
Image::load('example.jpg')
    ->brightness(-40)
    ->brightness(-20)
    ->save();
```
