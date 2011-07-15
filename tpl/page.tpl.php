<?php

/*
 * Return content as JSON-object and exit
**/

if ( $this->_['output_type'] == 'JSON' )
{
	header('Content-type: application/json');
	echo json_encode($this->_['content']);
	exit;
}

?>

<?php include 'components/header.inc.php'; ?>
	<div id="content_wrap" class="clearfix">
		<div id="content">
			<div id="posts">
				<?php
					$post = $this->_['content'][0];
					if ( $post ) :
				?>
				<div class="post">
					<h2><?php echo $post->post_title; ?></h2>
					<p>
						<small>
							<?php echo date('j. F Y', strtotime($post->post_date)); ?>
						</small>
					</p>
					<div>
						<?php echo $post->post_content; ?>
					</div>
				</div>
				<?php else : ?>
					<div class="post">
						<h2>Es wurde nichts gefunden</h2>
						<p>Zurück zur <a href="#!/index/1/">Startseite</a></p>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php include 'components/sidebar.inc.php'; ?>
	</div>
</div>
<?php include 'components/footer.inc.php'; ?>