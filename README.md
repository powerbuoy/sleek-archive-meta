# [Sleek Archive Meta](https://github.com/powerbuoy/sleek-archive-meta/)

[![Packagist](https://img.shields.io/packagist/vpre/powerbuoy/sleek-archive-meta.svg?style=flat-square)](https://packagist.org/packages/powerbuoy/sleek-archive-meta)
[![GitHub license](https://img.shields.io/github/license/powerbuoy/sleek-archive-meta.svg?style=flat-square)](https://github.com/powerbuoy/sleek-archive-meta/blob/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/powerbuoy/sleek-archive-meta.svg?style=flat-square)](https://github.com/powerbuoy/sleek-archive-meta/issues)
[![GitHub forks](https://img.shields.io/github/forks/powerbuoy/sleek-archive-meta.svg?style=flat-square)](https://github.com/powerbuoy/sleek-archive-meta/network)
[![GitHub stars](https://img.shields.io/github/stars/powerbuoy/sleek-archive-meta.svg?style=flat-square)](https://github.com/powerbuoy/sleek-archive-meta/stargazers)

Hooks into the `the_archive_title()` and `the_archive_description()` functions to provide better search results texts, remove prefixes and more.

Also adds a new `Sleek\ArchiveMeta\the_archive_image()` (which, without ACF, only works for the user archive (using the avatar)).

If used together with `Sleek\PostTypes`' settings pages `the_archive_image()` returns the image used on the settings page.

## Theme Support

N/A

## Hooks

N/A

## Functions

### `Sleek\ArchiveMeta\get_the_archive_image($size)`

Returns potential archive images as `<img>`.

### `Sleek\ArchiveMeta\get_the_archive_image_url(size)`

Returns potential archive image URL.

### `Sleek\ArchiveMeta\the_archive_image(size)`

Renders potential archive image.

### `Sleek\ArchiveMeta\the_archive_image_url(size)`

Renders potential archive image URL.

## Classes

N/A
