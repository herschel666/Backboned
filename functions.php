<?php

/*
 * Set the content width based on the theme's design and stylesheet.
**/
if ( ! isset( $content_width ) )
{
	$content_width = 500;
}
	
/*
 * Removing annoying stuff from the HTML-head
**/

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

function bb_remove_annoying_stuff()
{
	wp_deregister_script('l10n');
}
add_action('init', 'bb_remove_annoying_stuff');

/*
 * Include and initialize the MVC
**/

include 'mvc/controller.php';
include 'mvc/model.php';
include 'mvc/view.php';

/*
 * Merge GET- and POST-requests, instantiate
 * a new controller and start the MVC-fun
 * (MVC-pattern taken from http://tutorials.lemme.at/mvc-mit-php/)
**/

$request = array_merge($_GET, $_POST);  
$wp_controller = new wp_controller($request);
$wp_controller->display();

/*
 * Register actions for the theme's index.php
**/

function bb_header()
{
	do_action('bb_header');
}

function bb_content_templates()
{
	do_action('bb_content_templates');
}

function bb_template_skeletons()
{
	do_action('bb_template_skeletons');
}

function bb_static_content()
{
	do_action('bb_static_content');
}

/*
 * Returning the Twitter-Infos
 * from the theme's optionpage
 **/

function get_user_twitter_info()
{
	$name = ( get_option('bb_opts_twitter_name') != '' ) ?
		get_option('bb_opts_twitter_name') :
		false;
	$count = ( get_option('bb_opts_twitter_count') != '' ) ?
		(int) get_option('bb_opts_twitter_count') :
		false;	
	$output = ( $name && $count ) ?
		'{name:"' . $name . '",count:"' . $count . '"}' :
		'false';
	
	return $output;
}

/*
 * Create and echo the JSON-object with all
 * relevant site-infos and start the
 * theme-application;
 * (bound to the 'bb_header'-action)
**/

function display_site_object()
{
	$site_object = new wp_model;
	$site_title = $site_object->get_site_title();
	$main_nav = $site_object->get_main_nav();
	$category_nav = $site_object->get_category_nav();
	$archive_nav = $site_object->get_archive_nav();
	$bookmark_nav = $site_object->get_bookmarks();
	$post_count = $site_object->get_post_count();
	$user_logged_in = is_user_logged_in() ? 'true' : 'false';
	
	# $random = ((rand()%9)*(rand()%8)*(rand()%7));
	
	if ( !isset($_GET['_escaped_fragment_']) )
	{
		echo '<script src="' . get_bloginfo('template_url') . '/assets/js/loadscripts.js" type="text/javascript"></script>';
		echo '<script>';
		echo 'var site = site || {';
		echo 'base_url:"' . get_bloginfo('url') . '",';
		echo 'logged_in:' . $user_logged_in . ',';
		echo 'twtr_info: ' . get_user_twitter_info() . ',';
		echo 'pages:' . json_encode($main_nav) . ',';
		echo 'title:' . json_encode($site_title) . ',';
		echo 'categories:' . json_encode($category_nav) . ',';
		echo 'archives:' . json_encode($archive_nav) . ',';
		echo 'bookmarks:' . json_encode($bookmark_nav) . ',';
		echo 'post_count:' . $post_count . ',';
		echo 'scripts:[';
		echo '"https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js",';
		echo '"http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js",';
		echo '"http://ajax.cdnjs.com/ajax/libs/underscore.js/1.1.6/underscore-min.js",';
		echo '"http://ajax.cdnjs.com/ajax/libs/backbone.js/0.3.3/backbone-min.js",';
		echo '"' . get_bloginfo('template_url') . '/assets/js/wpApp.min.js"';
		echo '],';
		echo 'js:{}';
		echo '};';
		echo '$script(site.scripts,function(){';
		echo 'var appController=new site.js.wordpressController();});';
		echo '</script>';
	}
}

/*
 * echo all jQuery-templates for the theme-application;
 * (bound to the 'bb_content_templates'-action)
**/

function bb_jquery_templates()
{
	if ( !isset($_GET['_escaped_fragment_']) )
	{
		echo '<script type="text/x-jquery-tmpl" id="nav-item-template">';
		echo '<li><a href="#!${slug}">${title}</a></li>';
		echo '</script>';
		echo '<script type="text/x-jquery-tmpl" id="title-template">';
		echo '<div>';
		echo '<h1><a href="#!${home}">{{html $item.beautify(name)}}</a></h1>';
		echo '<em>${description}</em>';
		echo '</div>';
		echo '</script>';
		echo '<script type="text/x-jquery-tmpl" id="post-template">';
		echo '<div class="post">';
		echo '<h2><a href="#!/post/${post_name}/${ID}/">${post_title}</a></h2>';
		echo '<p><small>Geschrieben am ${$item.nicedate(post_date)}</small></p>';
		echo '<div>{{html post_content}}</div>';
		echo '<p><small>${$item.commentCount(comment_count)}</small></p>';
		echo '</div>';
		echo '</script>';
		echo '<script type="text/x-jquery-tmpl" id="single-post-template">';
		echo '<div class="post">';
		echo '<h2>${post_title}</h2>';
		echo '<p><small>Geschrieben am ${$item.nicedate(post_date)}</small></p>';
		echo '<div>{{html post_content}}</div>';
		echo '<div class="meta">';
		echo '{{html $item.displayCategories(categories)}}';
		echo '</div>';
		echo '</div>';
		echo '</script>';
		echo '<script type="text/x-jquery-tmpl" id="page-template">';
		echo '<div class="post">';
		echo '<h2>${post_title}</h2>';
		echo '<p><small>Geschrieben am ${$item.nicedate(post_date)}</small></p>';
		echo '<div>{{html post_content}}</div>';
		echo '</div>';
		echo '</script>';
		echo '<script type="text/x-jquery-tmpl" id="error-template">';
		echo '<div class="post">';
		echo '<h2>${title}</h2>';
		echo '<p>{{html content}}</p>';
		echo '</div>';
		echo '</script>';
		echo '<script type="text/x-jquery-tmpl" id="comment-template">';
		echo '<div class="comment clearfix">';
		echo '<h3>{{html $item.linkify(comment_author) }}</h3>';
		echo '<small class="commentdate">Geschrieben am ${$item.nicedate(comment_date)}</small>';
		echo '<div class="commenttext">{{html comment_content}}</div>';
		echo '{{html $item.deleteButton(site.logged_in)}}';
		echo '</div>';
		echo '</script>';
		
		if ( get_option('bb_opts_twitter_name') != '' )
		{
			echo '<script type="text/x-jquery-tmpl" id="twitter-template">';
			echo '<li class="tweet">';
			echo '<p>{{html $item.tweetify(text)}}</p>';
			echo '<p><small>';
			echo '<a href="http://twitter.com/' . get_option('bb_opts_twitter_name') . '/status/${id_str}">';
			echo '${$item.relativeTime(created_at)}';
			echo '</a>';
			echo '</small></p>';
			echo '</script>';
		}
	}
}

/*
 * echo the html-frame for the theme-application;
 * (bound to the 'bb_template_skeletons'-action)
**/

function display_template_skeleton()
{
	if ( !isset($_GET['_escaped_fragment_']) )
	{
		echo '<div id="nav">';
		echo '<ul id="pages" class="clearfix"></ul>';
		echo '</div>';
		echo '<div id="wrap">';
		echo '<div id="title"></div>';
		echo '<div id="content_wrap" class="clearfix">';
		echo '<div id="content">';
		echo '<div id="posts"></div>';
		echo '<div id="pagination" class="clearfix"></div>';
		echo '<div id="comments"></div>';
		echo '<div id="commentform_wrap"></div>';
		echo '</div>';
		echo '<div id="sidebar">';
		if ( get_option('bb_opts_twitter_name') != '' )
		{
			echo '<ul id="twitter_widget"></ul>';
		}
		echo '<ul id="categories"></ul>';
		echo '<ul id="archives"></ul>';
		echo '<ul id="bookmarks"></ul>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '<div id="footer_outer"><div id="footer" class="clearfix"></div></div>';
	}
}

/*
 * echo the static content content
* if a search crawler demands it;
 * (bound to the 'bb_static_content'-action)
**/

function display_static_content()
{
	global $wp_controller;
	
	if ( !empty($_GET) )
	{
		echo $wp_controller->display();
	}
}

/*
 * Add thje previous actions and functions to the theme
 * by registering wp-theme-hooks
**/

add_action('bb_header', 'display_site_object');

add_action('bb_content_templates', 'bb_jquery_templates');

add_action('bb_template_skeletons', 'display_template_skeleton');

add_action('bb_static_content', 'display_static_content');

/*
 * Wrap paragraphs with <p>-tag for some
 * array_walk-action in the model
 * (/mvc/model.php)
**/

function wpautop_content($item, $key, $element)
{	
	$item->$element = wpautop($item->$element);
}

/*
 * Theme-Options to insert a twitter stream in the sidebar
**/

$backboned_options_arr = array(
	array(
		'name' => '<strong>Twitter-Name</strong>',
		'desc' => 'Gib deinen Twitter-Namen ein',
		'id' => 'bb_opts_twitter_name',
		'default' => ''		
	),
	array(
		'name' => '<strong>Anzahl Tweets</strong>',
		'desc' => 'Wieviele Tweets sollen angezeigt werden?',
		'id' => 'bb_opts_twitter_count',
		'default' => '5'
	),
);

function backboned_options()
{
	global $backboned_options_arr;
	
	if ( $_REQUEST['action'] == 'backboned_save' ) 
	{
		foreach ( $backboned_options_arr as $value )
		{
			if( isset( $_REQUEST[ $value['id'] ] ) )
			{
				update_option( $value['id'], stripslashes($_REQUEST[ $value['id']]));
			}
		}
		
		if ( stristr( $_SERVER['REQUEST_URI'], '&saved=true' ) )
		{
			$location = $_SERVER['REQUEST_URI'];
		}
		else
		{
			$location = $_SERVER['REQUEST_URI'] . '&saved=true';
		}
		
		header('Location: ' . $location);
		die;
	}
	
	add_theme_page('Backboned Settings', 'Backboned Setting', 10, 'backboned-settings', 'backboned_admin');
}

function backboned_admin()
{
	global $backboned_options_arr;
	
	?>
	
		<div class="wrap">
			<h2 class="alignleft">Theme Setting</h2>
			<br clear="all" />
			
			<?php if ( $_REQUEST['saved'] ) : ?>
				<div class="updated fade"><p><strong>Setting Saved</strong></p></div>
			<?php endif; ?>
			
			<form method="post" id="myForm">
				<div id="poststuff" class="metabox-holder">
					<div class="stuffbox">
						<h3>Twitter Settings</h3>
						<div class="inside">
							<table class="form-table" style="width: auto">
							<?php
								foreach ( $backboned_options_arr as $value )
								{
									switch ( $value['id'] )
									{
										case "bb_opts_twitter_name" : ?>
										
										<tr>
											<th scope="row">
												<?php echo $value['name']; ?><br />
												<?php echo $value['desc']; ?>
											</th>
											<td>
												<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php get_option($value['id'])?printf(get_option($value['id'])): printf($value['default']) ?>" />
											</td>
										</tr>
										
										<?php break;
										
										case "bb_opts_twitter_count" : ?>
										
										<tr>
											<th scope="row">
												<?php echo $value['name']; ?><br />
												<?php echo $value['desc']; ?>
											</th>
											<td>
												<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php get_option($value['id'])?printf(get_option($value['id'])): printf($value['default']) ?>" />
											</td>
										</tr>
										
										<?php break;
									}
								}
							?>
							</table>
						</div>
					</div>
				</div>
				<input name="backboned_save" type="submit" class="button-primary" value="Save changes" />
				<input type="hidden" name="action" value="backboned_save" />
			</form>
		</div>
		
		<?php
}				

add_action('admin_menu', 'backboned_options');