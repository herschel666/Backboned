<?php

/** 
 * The model for the Backboned-Theme
 */

class wp_model
{
	/*
	 * Get all entries by page-number, wpautop
	 * the content and return the result as array
	**/
	
	public static function get_index_entries( $page )
	{
		$count_posts = wp_count_posts();
		$posts = query_posts('numberposts=10&paged=' . $page);
		array_walk($posts, 'wpautop_content', 'post_content');

		$output = array(
			'posts' => $posts,
			'count' => $count_posts->publish
		);
		
		return $output;
    }

	/*
	 * Get all entries by category-ID and page-number,
	 * wpautop the content and return the result as array
	**/

	public static function get_category_entries( $slug, $id, $page )
	{
		$cat_args = array(
			'category_name' => $slug,
			'paged' => $page
		);
		$post_count = get_categories('include=' . $id);
		$post_count = $post_count[0]->count;
		$posts = query_posts($cat_args);
		array_walk($posts, 'wpautop_content', 'post_content');
		$output = array(
			'posts' => $posts,
			'count' => $post_count
		);
		
		return $output;		
    }

	/*
	 * Get all entries by post-date and page-number,
	 * wpautop the content and return the result as array
	**/

	public static function get_archive_entries( $month, $year, $page )
	{
		$lang = str_replace('-', '_', get_bloginfo('language'));
		setlocale(LC_TIME, $lang);
				
		for ( $i=1; $i<=12; $i++ )
		{
			$month_edit = preg_match('/ae/', $month) ?
				str_replace('ae', '&auml;', $month) :
				$month;

			if ( str_replace('ä', '&auml;', strtolower( strftime('%B', mktime(0, 0, 0, $i, 1, 0))) ) == $month_edit )
			{
				$month_num = $i;
				break;
			}
		}

		$archive_args = array(
			'numberposts' => 10,
			'paged' => $page,
			'monthnum' => $month_num,
			'year' => $year
		);
		
		global $wpdb;
		
		$post_count = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*)
			FROM $wpdb->posts
			WHERE YEAR(post_date) = '" . $year . "'
			AND MONTH(post_date) = '" . $month_num . "'
			AND post_status = 'publish'
			AND post_type = 'post'
		"));

		$posts = query_posts($archive_args);
		array_walk($posts, 'wpautop_content', 'post_content');
		
		$output = array(
			'posts' => $posts,
			'count' => $post_count
		);
		
		return $output;		
    }

	/*
	 * Get a static page-entry by slug,
	 * wpautop the content and return the result as array
	**/

	public static function get_page_entry( $slug )
	{
		$page = query_posts('pagename=' . $slug);
		array_walk($page, 'wpautop_content', 'post_content');
		
		return $page;
    }

	/*
	 * Get a single post-entry by ID, wpautop the
	 * content and return the result as array
	**/

	public static function get_post_entry( $id )
	{
		$post = query_posts('p=' . $id);
		array_walk($post, 'wpautop_content', 'post_content');
		
		$comments = get_comments('post_id=' . $id);
		array_walk($comments, 'wpautop_content', 'comment_content');
		
		$output = array(
			'post' => $post,
			'cats' => wp_get_object_terms( $id, 'category' ),
			'comments' => $comments
		);
		
		return $output;
	}
	
	/*
	 * Return the site's title, description
	 * and frontpage-slug
	**/
	
	public static function get_site_title()
	{
		return array(
			'name' => get_bloginfo('name'),
			'description' => get_bloginfo('description'),
			'home' => '/index/1/'
		);
	}
	
	/*
	 * Delete the comment and return success-message
	**/
	
	public static function delete_comment ( $id )
	{
		return wp_delete_comment( $id );
	}
	
	/*
	 * Return the static-page-navigation
	**/
	
	public static function get_main_nav()
	{
		$pages = get_pages('parent=0');
		$page_array = array();

		if ( !empty($pages) )
		{
			foreach( $pages as $page )
			{
				array_push($page_array, array(
					'title' => $page->post_title,
					'slug' => '/page/' . $page->post_name . '/'
				));
			}
		}
		
		return $page_array;
	}
	
	/*
	 * Return the category-navigation
	**/
	
	public static function get_category_nav()
	{
		$categories = get_categories('hierarchical=0');
		$category_array = array();

		if ( !empty($categories) )
		{
			foreach( $categories as $category )
			{
				array_push($category_array, array(
					'title' => $category->name,
					'slug' => '/category/' . $category->slug . '/' . $category->term_id . '/1/',
					'cat_id' => $category->term_id,
					'count' => $category->count
				));
			}
		}

		return $category_array;
	}
	
	/*
	 * Return the archive-navigation (by month)
	**/
	
	public static function get_archive_nav()
	{
		$archive = wp_get_archives('format=custom&echo=0&show_post_count=1');
		$archive = preg_replace('/<[^>]*>/', '', $archive);
		$archive = str_replace('&nbsp;', " ", $archive);
		$archive = explode("\n", $archive);
		$archive_array = array();

		array_splice($archive, -1);

		if ( !empty($archive) )
		{
			foreach( $archive as $archive_item )
			{
				$item_parts = explode(' ', $archive_item);
				$month = ltrim($item_parts[0], "\t");
				$year = ltrim($item_parts[1], "\t");
				$count = preg_replace('/(\()(\d+)(\))/', '$2', $item_parts[2]);

				array_push($archive_array, array(
					'title' => $month . ' ' . $year,
					'slug' => '/archive/' . str_replace('ä', 'ae', strtolower($month)) . '/' . $year . '/1/',
					'count' => $count
				));
			}
		}

		return $archive_array;
	}
	
	/*
	 * Return the bookmarks-navigation
	**/
	
	public static function get_bookmarks()
	{
		$bookmarks = get_bookmarks();
		$bookmark_array = array();
		
		if ( !empty($bookmarks) )
		{
			foreach( $bookmarks as $bookmark )
			{
				array_push($bookmark_array, array(
					'title' => $bookmark->link_name,
					'slug' => $bookmark->link_url
				));
			}
		}

		return $bookmark_array;
	}
	
	/*
	 * Return the total post count
	**/
	
	public static function get_post_count()
	{
		$count_posts = wp_count_posts();
		
		return $count_posts->publish;
	}
}