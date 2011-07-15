<?php

/*
 * The controller for the Backboned-Theme
**/

class wp_controller
{
	private $request = null;
	private $template = '';

	/*
	 * Constructing the controller and defining the routes
	**/

	public function __construct($request)
	{
		$this->request = $request;
			
		$this->route = !empty($request['_escaped_fragment_']) ?
			preg_replace('/^\/|\/$/', '', $request['_escaped_fragment_']) :
			'index/1';
			
		$this->output_type = isset($request['output_type']) ?
			$request['output_type'] :
			false;
			
		$this->route_fragments = explode('/', $this->route);
		
		$this->arguments_length = count($this->route_fragments);
		
        $this->template = $this->route_fragments[0];
	}
	
	/* Instantiate a new view and assign content
	 * depending on the route
	**/

	public function display()
	{
        $view = new wp_view();
		
		switch ( $this->template )
		{
			case 'category' :
			
				if ( $this->arguments_length == 4 )
				{
					$view->setTemplate('category');
					$meta = array(
						'slug' => $this->route_fragments[1],
						'id' => $this->route_fragments[2],
						'page' => $this->route_fragments[3]
					);
	                $output = wp_model::get_category_entries( $meta['slug'], $meta['id'], $meta['page'] );
					$view->assign('meta', $meta);
				}
				else
				{
					$view->setTemplate('redirect');
					$output = '#!/404/';
				}
				break;
				
			case 'archive' :
				
				if ( $this->arguments_length == 4 )
				{
					$view->setTemplate('archive');
					$meta = array(
						'month' => $this->route_fragments[1],
						'year' => $this->route_fragments[2],
						'page' => $this->route_fragments[3]
					);
	                $output = wp_model::get_archive_entries( $meta['month'], $meta['year'], $meta['page'] );
					$view->assign('meta', $meta);
				}
				else
				{
					$view->setTemplate('redirect');
					$output = '#!/404/';
				}
				break;
				
			case 'post' :
				
				if ( $this->arguments_length == 3 )
				{
					$view->setTemplate('post');
					$id = $this->route_fragments[2];
					$output = wp_model::get_post_entry( $id );
				}
				else
				{
					$view->setTemplate('redirect');
					$output = '#!/404/';
				}
				break;
				
			case 'commentform' :

				$view->setTemplate('commentform');
				$output = '';
				break;
				
			case 'comment_delete' :

				$view->setTemplate('comment_delete');
				$id = $this->route_fragments[1];
				$output = wp_model::delete_comment( $id );
				break;
				
			case 'page' :
				
				if ( $this->arguments_length == 2 )
				{
					$view->setTemplate('page');
					$slug = $this->route_fragments[1];
	                $output = wp_model::get_page_entry( $slug );
				}
				else
				{
					$view->setTemplate('redirect');
					$output = '#!/404/';
				}
				break;
				
			case 'index' :
			
				if ( $this->arguments_length == 2 )
				{
					$view->setTemplate('index');
					$page = $this->route_fragments[1];
	                $output = wp_model::get_index_entries( $page );
					$view->assign('page', $page);
				}
				else
				{
					$view->setTemplate('redirect');
					$output = '#!/404/';
				}
				break;
				
			case '404' :
				$view->setTemplate('404');
				$output = '';
				break;
				
			default :
				$view->setTemplate('redirect');
				$output = '#!/404/';
				break;
		}
		
		$title = wp_model::get_site_title();
		$main_nav = wp_model::get_main_nav();
		$category_nav = wp_model::get_category_nav();
		$archive_nav = wp_model::get_archive_nav();
		$bookmarks = wp_model::get_bookmarks();
		
		$view->assign('title', $title);
		$view->assign('main_nav', $main_nav);
		$view->assign('category_nav', $category_nav);
		$view->assign('archive_nav', $archive_nav);
		$view->assign('bookmarks', $bookmarks);
		$view->assign('content', $output);
		$view->assign('output_type', $this->output_type);
		
		return $view->loadTemplate();
    }  
}