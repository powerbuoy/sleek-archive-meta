<?php
namespace Sleek\ArchiveMeta;

############################
# Modify the_archive_title()
add_filter('get_the_archive_title', function ($title) {
	global $wp_query;
	global $post;

	# Blog page should show blog settings title field
	if (is_home() and function_exists('get_field') and $customTitle = get_field('title', 'post_settings')) {
		$title = $customTitle;
	}

	# Search should show something nice too
	elseif (is_search()) {
		# With results
		if (have_posts()) {
			# Non-empty search
			if (strlen(trim(get_search_query())) > 0) {
				# Translators: This is the search results description when there are more than zero search results
				$title = sprintf(__('Search results (%s) for: %s', 'sleek'), $wp_query->found_posts, '<strong>"' . get_search_query() . '"</strong>');
			}
			# An empty search
			else {
				# Translators: This is the search results description when the user didn't enter a search term
				$title = sprintf(__('Empty search', 'sleek'), $wp_query->found_posts, get_search_query());
			}
		}
		# No search results
		else {
			# Translators: This is the search results description when there are zero search results
			$title = sprintf(__('No search results for: <strong>"%s"</strong>', 'sleek'), get_search_query());
		}
	}

	# CPT archive should show custom title if set
	elseif (is_post_type_archive() and function_exists('get_field') and $customTitle = get_field('title', $wp_query->query['post_type'] . '_settings')) {
		$title = $customTitle;
	}

	# Tax archive
	elseif (is_tax() and function_exists('get_field') and $customTitle = get_field('title', get_queried_object())) {
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

	# Blog page should show blog settings description field
	if (is_home() and function_exists('get_field') and $customDescription = get_field('description', 'post_settings')) {
		$description = $customDescription;
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

				$description = '<p>' . sprintf(__('Displaying results %d through %d.', 'sleek'), $resFrom, $resTo) . '</p>';
			}
			# An empty search
			else {
				$description = '<p>' . __("You didn't search for anything in particular so I'm showing you everything.", 'sleek') . '</p>';
			}
		}
		# No search results
		else {
			$description = '<p>' . __("We couldn't find any matching search results for your query.", 'sleek') . '</p>';
		}
	}

	# CPT archive should show custom description if set
	elseif (is_post_type_archive() and function_exists('get_field') and $customDescription = get_field('description', $wp_query->query['post_type'] . '_settings')) {
		$description = $customDescription;
	}

	# Tax archive should show custom description if set
	elseif (is_tax() and function_exists('get_field') and $customDescription = get_field('description', get_queried_object())) {
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
function the_archive_image ($size = 'large') {
	echo get_the_archive_image($size);
}

function the_archive_image_url ($size = 'large') {
	echo get_the_archive_image_url($size);
}

function get_the_archive_image_url ($size = 'large') {
	return get_the_archive_image($size, true);
}

function get_the_archive_image ($size = 'large', $urlOnly = false) {
	global $_wp_additional_image_sizes;

	$imageId = get_the_archive_image_id();
	$image = false;

	# Special image
	if (is_array($imageId)) {
		if ($imageId['type'] === 'user') {
			if (isset($_wp_additional_image_sizes[$size])) {
				$size = $_wp_additional_image_sizes[$size]['width'];
			}
			else {
				$size = 640;
			}

			if ($urlOnly) {
				$image = get_avatar_url($imageId['id'], ['size' => $size]);
			}
			else {
				$image = get_avatar($imageId['id'], $size);
			}
		}
	}
	# Normal attachment image
	elseif ($imageId) {
		if ($urlOnly) {
			$image = wp_get_attachment_image_src($imageId, $size)[0];
		}
		else {
			$image = wp_get_attachment_image($imageId, $size);
		}
	}

	return $image;
}

function get_the_archive_image_id () {
	global $wp_query;
	global $post;

	$id = false;

	# Category or custom taxonomies
	if ((is_tag() or is_category() or is_tax()) and function_exists('get_field') and $imageId = get_field('image', get_queried_object())) {
		$id = $imageId;
	}

	# Blog pages (category, date, tag etc)
	elseif ((is_home() or is_category() or is_tag() or is_date()) and function_exists('get_field') and $imageId = get_field('image', 'post_settings')) {
		$id = $imageId;
	}

	# CPT archive
	elseif (is_post_type_archive() and function_exists('get_field') and $imageId = get_field('image', $wp_query->query['post_type'] . '_settings')) {
		$id = $imageId;
	}

	# Custom taxonomy - fallback to settings page
	elseif (is_tax() and function_exists('get_field') and $imageId = get_field('image', \Sleek\Utils\get_current_post_type() . '_settings')) {
		$id = $imageId;
	}

	# Author
	elseif (is_author()) {
		$user = get_queried_object();

		$id = [
			'id' => $user->ID,
			'type' => 'user'
		];
	}

	return $id;
}

###############################
# Add archive image as OG image
add_filter('wpseo_opengraph_image', __NAMESPACE__ . '\\add_archive_og_image');
add_filter('wpseo_twitter_image', __NAMESPACE__ . '\\add_archive_og_image');

function add_archive_og_image ($image) {
	$archiveImage = get_the_archive_image_url('large');

	if ($archiveImage) {
		return $archiveImage;
	}

	return $image;
}
