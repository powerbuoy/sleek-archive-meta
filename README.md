Hooks into the `the_archive_title()` and `the_archive_description()` functions to provide better search results texts, remove prefixes and more.

Also adds a new `Sleek\ArchiveMeta\the_archive_image()` (which, without ACF, only works for the user archive (using the avatar) and post archive (using the `page_for_posts` featured image)).

If ACF is enabled a new settings page for custom Title, Description and Image is added to every public post type as well.
