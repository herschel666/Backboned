<?php

class wp_view
{
	/*
	 * The template path
	**/
	
	private $path = 'wp-content/themes/backboned/tpl';
	private $template = '';
	
	/*
	 * The variable for the template content
	**/
	
    private $_ = array();

	/*
	 * Assign values to the content variable
	**/
	
	public function assign( $key, $value )
	{
		$this->_[$key] = $value;
	}
	
	/*
	 * Assign the template
	**/
	
	public function setTemplate( $template )
	{
		$this->template = $template;
	}
	
	/*
	 * Load the template file, read
	 * and return its content
	**/
	
	public function loadTemplate()
	{
		$tpl = $this->template;
		$file = $this->path . DIRECTORY_SEPARATOR . $tpl . '.tpl.php';
		$exists = file_exists($file);
		
		if ( $exists )
		{
			ob_start();
			include $file;
			$output = ob_get_contents();
			ob_end_clean();
			
			return $output;
		}
		else
		{
			return 'could not find template';
		}
	}
}