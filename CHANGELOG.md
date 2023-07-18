# Changelog

All notable changes to `image` will be documented in this file

## 2.2.6 - 2023-05-06

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/spatie/image/pull/185
- Bump dependabot/fetch-metadata from 1.3.6 to 1.4.0 by @dependabot in https://github.com/spatie/image/pull/188
- Fit with only width or height by @gdebrauwer in https://github.com/spatie/image/pull/190

### New Contributors

- @dependabot made their first contribution in https://github.com/spatie/image/pull/185
- @gdebrauwer made their first contribution in https://github.com/spatie/image/pull/190

**Full Changelog**: https://github.com/spatie/image/compare/2.2.5...2.2.6

## 2.2.5 - 2023-01-19

### What's Changed

- Refactor tests to pest by @AyoobMH in https://github.com/spatie/image/pull/176
- Add Dependabot Automation by @patinthehat in https://github.com/spatie/image/pull/177
- Add PHP 8.2 Support by @patinthehat in https://github.com/spatie/image/pull/180
- Update Dependabot Automation by @patinthehat in https://github.com/spatie/image/pull/181
- Add fill-max fit mode by @Tofandel in https://github.com/spatie/image/pull/183

### New Contributors

- @AyoobMH made their first contribution in https://github.com/spatie/image/pull/176
- @patinthehat made their first contribution in https://github.com/spatie/image/pull/177
- @Tofandel made their first contribution in https://github.com/spatie/image/pull/183

**Full Changelog**: https://github.com/spatie/image/compare/2.2.4...2.2.5

## 2.2.4 - 2022-08-09

### What's Changed

- Add zero orientation support ignoring EXIF by @danielcastrobalbi in https://github.com/spatie/image/pull/171

### New Contributors

- @danielcastrobalbi made their first contribution in https://github.com/spatie/image/pull/171

**Full Changelog**: https://github.com/spatie/image/compare/2.2.3...2.2.4

## 2.2.3 - 2022-05-21

## What's Changed

- Fix permission issue with temporary directory by @sebastianpopp in https://github.com/spatie/image/pull/163

## New Contributors

- @sebastianpopp made their first contribution in https://github.com/spatie/image/pull/163

**Full Changelog**: https://github.com/spatie/image/compare/2.2.2...2.2.3

## 2.2.2 - 2022-02-22

- add TIFF support

## 1.11.0 - 2022-02-21

## What's Changed

- Fix docs link by @pascalbaljet in https://github.com/spatie/image/pull/154
- Update .gitattributes by @PaolaRuby in https://github.com/spatie/image/pull/158
- Add TIFF support by @Synchro in https://github.com/spatie/image/pull/159

## New Contributors

- @PaolaRuby made their first contribution in https://github.com/spatie/image/pull/158

**Full Changelog**: https://github.com/spatie/image/compare/2.2.1...1.11.0

## 2.2.1 - 2021-12-17

## What's Changed

- Use match expression in convertToGlideParameter method  by @mohprilaksono in https://github.com/spatie/image/pull/149
- [REF] updated fit docs description by @JeremyRed in https://github.com/spatie/image/pull/150
- Adding compatibility to Symfony 6 by @spackmat in https://github.com/spatie/image/pull/152

## New Contributors

- @mohprilaksono made their first contribution in https://github.com/spatie/image/pull/149
- @JeremyRed made their first contribution in https://github.com/spatie/image/pull/150
- @spackmat made their first contribution in https://github.com/spatie/image/pull/152

**Full Changelog**: https://github.com/spatie/image/compare/2.2.0...2.2.1

## 2.2.0 - 2021-10-31

- add avif support (#148)

## 2.1.0 - 2021-07-15

- Drop support for PHP 7
- Make codebase more strict with type hinting

## 2.0.0 - 2021-07-15

- Bump league/glide to v2 [#134](https://github.com/spatie/image/pull/134)

## 1.10.4 - 2021-04-07

- Allow spatie/temporary-directory v2

## 1.10.3 - 2021-03-10

- Bump league/glide to 2.0 [#123](https://github.com/spatie/image/pull/123)

## 1.10.2 - 2020-01-26

- change condition to delete $conversionResultDirectory (#118)

## 1.10.1 - 2020-12-27

- adds zoom option to focalCrop (#112)

## 1.9.0 - 2020-11-13

- allow usage of a custom `OptimizerChain` #110

## 1.8.1 - 2020-11-12

- revert changes from 1.8.0

## 1.8.0 - 2020-11-12

- allow usage of a custom `OptimizerChain` (#108)

## 1.7.7 - 2020-11-12

- add support for PHP 8

## 1.7.6 - 2020-01-26

- change uppercase function to mb_strtoupper instead of strtoupper (#99)

## 1.7.5 - 2019-11-23

- allow symfony 5 components

## 1.7.4 - 2019-08-28

- do not export docs

## 1.7.3 - 2019-08-03

- fix duplicated files (fixes #84)

## 1.7.2 - 2019-05-13

- fixes `optimize()` when used with `apply()` (#78)

## 1.7.1 - 2019-04-17

- change GlideConversion sequence (#76)

## 1.7.0 - 2019-02-22

- add support for `webp`

## 1.6.0 - 2019-01-27

- add `setTemporaryDirectory`

## 1.5.3 - 2019-01-10

- update lower deps

## 1.5.2 - 2018-05-05

- fix exception message

## 1.5.1 - 2018-04-18

- Prevent error when trying to remove `/tmp`

## 1.5.0 - 2018-04-13

- add `flip`

## 1.4.2 - 2018-04-11

- Use the correct driver for getting widths and height of images.

## 1.4.1 - 2018-02-08

- Support symfony ^4.0
- Support phpunit ^7.0

## 1.4.0 - 2017-12-05

- add `getWidth` and `getHeight`

## 1.3.5 - 2017-12-04

- fix for problems when creating directories in the temporary directory

## 1.3.4 - 2017-07-25

- fix `optimize` docblock

## 1.3.3 - 2017-07-11

- make `optimize` method fluent

## 1.3.2 - 2017-07-05

- swap out underlying optimization package

## 1.3.1 - 2017-07-02

- internally treat `optimize` as a manipulation

## 1.3.0 - 2017-07-02

- add `optimize` method

## 1.2.1 - 2017-06-29

- add methods to determine emptyness to `Manipulations` and `ManipulationSequence`

## 1.2.0 - 2017-04-17

- allow `Manipulations` to be constructed with an array of arrays

## 1.1.3 - 2017-04-07

- improve support for multi-volume systems

## 1.1.2 - 2017-04-04

- remove conversion directory after converting image

## 1.1.1 - 2017-03-17

- avoid processing empty manipulations groups

## 1.1.0 - 2017-02-06

- added support for watermarks

## 1.0.0 - 2017-02-06

- initial release
