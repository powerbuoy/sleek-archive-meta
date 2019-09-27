<?php
namespace Sleek\ArchiveMeta;

############################
# Modify the_archive_title()
add_filter('get_the_archive_title', function ($title) {
	global $wp_query;
	global $post;

	# Blog page should show blog page's the_title()
	if (is_home() and get_option('page_for_posts')) {
		$title = get_the_title(get_option('page_for_posts'));
	}

	# Search should show something nice too
	elseif (is_search()) {
		# With results
		if (have_posts()) {
			# Non-empty search
			if (strlen(trim(get_search_query())) > 0) {
				$title = sprintf(__('Search results (%s) for: <strong>"%s"</strong>', 'sleek'), $wp_query->found_posts, get_search_query());
			}
			# An empty search
			else {
				$title = sprintf(__('Empty search', 'sleek'), $wp_query->found_posts, get_search_query());
			}
		}
		# No search results
		else {
			$title = sprintf(__('No search results for: <strong>"%s"</strong>', 'sleek'), get_search_query());
		}
	}

	# CPT archive should show custom title if set
	elseif (is_post_type_archive() and function_exists('get_field') and $customTitle = get_field('title', $wp_query->query['post_type'] . '_archive_meta')) {
		$title = $customTitle;
	}

	# Default (remove PREFIX:)
	else {
		$title = preg_replace('/^(.*?): /', '', $title);
	}

	return $title;
});

##################################
# Modify the_archive_description()
add_filter('get_the_archive_description', function ($description) {
	global $wp_query;
	global $post;

	# Blog page should show blog page's the_content()
	if (is_home() and get_option('page_for_posts')) {
		$description = apply_filters('the_content', get_post_field('post_content', get_option('page_for_posts')));
	}

	# Search should show something nice too
	elseif (is_search()) {
		# With results
		if (have_posts()) {
			# Non-empty search
			if (strlen(trim(get_search_query())) > 0) {
				$total = $wp_query->found_posts;
				$currPage = $wp_query->query_vars['paged'] ? $wp_query->query_vars['paged'] : 1;
				$numPerPage = $wp_query->query_vars['posts_per_page'];
				$resFrom = ($currPage * $numPerPage - $numPerPage) + 1;
				$resTo = ($resFrom + $numPerPage) - 1;
				$resTo = $resTo > $total ? $total : $resTo;

				$description = '<p>' . sprintf(__('Displaying results %d through %d', 'sleek'), $resFrom, $resTo) . '</p>';
			}
			# An empty search
			else {
				$description = '<p>' . __("You didn't search for anything in particular so I'm showing you everything", 'sleek') . '</p>';
			}
		}
		# No search results
		else {
			$description = '<p>' . __("We couldn't find any matching search results for your query.", 'sleek') . '</p>';
		}
	}

	# CPT archive should show custom description if set
	elseif (is_post_type_archive() and function_exists('get_field') and $customDescription = get_field('description', $wp_query->query['post_type'] . '_archive_meta')) {
		$description = $customDescription;
	}

	# Author: WP doesn't wrap this in a <p>
	elseif (is_author()) {
		$description = wpautop(get_the_author_meta('description'));
	}

	return $description;
});

###############
# Archive Image
function the_archive_image ($size) {
	echo get_the_archive_image($size);
}

function the_archive_image_url ($size) {
	echo get_the_archive_image_url($size);
}

function get_the_archive_image_url ($size = 'large') {
	return get_the_archive_image($size, true);
}

function get_the_archive_image ($size = 'large', $urlOnly = false) {
	global $_wp_additional_image_sizes;
	global $wp_query;
	global $post;

	$image = false;

	# Blog pages (category, date, tag etc)
	if ((is_home() or is_category() or is_tag() or is_year() or is_month() or is_day()) and get_option('page_for_posts')) {
		if (has_post_thumbnail(get_option('page_for_posts'))) {
			if ($urlOnly) {
				$image = get_the_post_thumbnail_url(get_option('page_for_posts'), $size);
			}
			else {
				$image = get_the_post_thumbnail(get_option('page_for_posts'), $size);
			}
		}
	}

	# CPT archive
	elseif (is_post_type_archive() and function_exists('get_field') and $imageId = get_field('image', $wp_query->query['post_type'] . '_archive_meta')) {
		if ($urlOnly) {
			$image = wp_get_attachment_image_src($imageId, $size)[0];
		}
		else {
			$image = wp_get_attachment_image($imageId, $size);
		}
	}

	# Custom taxonomy (TODO: Should use Sleek\Utils\get_current_post_type())
	elseif (is_tax() and function_exists('get_field') and $imageId = get_field('image', get_post_type() . '_archive_meta')) {
		if ($urlOnly) {
			$image = wp_get_attachment_image_src($imageId, $size)[0];
		}
		else {
			$image = wp_get_attachment_image($imageId, $size);
		}
	}

	# Author
	elseif (is_author()) {
		$user = get_queried_object();

		if (isset($_wp_additional_image_sizes[$size])) {
			$size = $_wp_additional_image_sizes[$size]['width'];
		}
		else {
			$size = 640;
		}

		if ($urlOnly) {
			$image = get_avatar_url($user->ID, ['size' => $size]);
		}
		else {
			$image = get_avatar($user->ID, ['size' => $size]);
		}
	}

	return $image;
}

# TODO: Add {postType}_archive_meta options pages with archive_title, archive_description and archive_image fields
