<!doctype html>
<html lang="de-DE">

	<head>
		<meta charset="utf-8">
		
		<!-- make sure google et al. opts in to the hashbang-party -->
		<meta name="fragment" content="!" />
		
		<?php if ( is_home() && !is_paged() ) : ?>
			<!-- avoid duplicate content, because /?_escaped_fragment_ and /?_escaped_fragment_=/index/1/ displays the same content -->
			<meta rel="canonical" href="<?php bloginfo('url'); ?>/#!/index/1/" />
		<?php endif; ?>
		
		<base href="<?php bloginfo('url'); ?>/" />
		<link href="<?php bloginfo('stylesheet_url'); ?>" media="screen" rel="stylesheet" type="text/css" />
		
		<title><?php bloginfo('name'); ?><?php wp_title(' | ')?></title>
		<?php wp_head(); ?>
	</head>
	<body>