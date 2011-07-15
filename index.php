<?php

	/*
	 * Call the official wp-theme's header
	**/
	
	get_header();
	
	/*
	 * Call the wp-theme-hooks to insert
	 * the requested content
	**/
	
	bb_header();
	
	bb_jquery_templates();
	
	bb_template_skeletons();
	
	bb_static_content();
	
	/*
	 * Call the official wp-theme's footer
	**/
	
	get_footer();