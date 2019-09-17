---
title: Preparing manipulations
weight: 4
---

In all  other examples in the docs image manipulations like `brightness` and `blur` are called directly on the `Image` instance. You could also opt to build up a `Manipulations` instance.

```php
$manipulations = (new Manipulations())
	->blur(20)
	->brightness(-20);
```

Then you can use that to manipulate a collection of images.

```php
//using Laravel's collect function

collect($images)->each(function(Image $image) use ($manipulations) {
	$image
	   ->manipulate($manipulations)
	   ->save();
});
```

The `manipulate` function can also accept a closure.

```php
$image->manipulate(function(Manipulations $manipulations) {
	$manipulations
	   ->blur(20)
	   ->brightness(-20);
});
```
