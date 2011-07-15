<?php

/*
 * Return content as JSON-object and exit
**/

if ( $this->_['output_type'] == 'JSON' )
{
	header('Content-type: application/json');
	echo json_encode($this->_['content']['posts']);
	exit;
}

?>

<?php include 'components/header.inc.php'; ?>
	<div id="content_wrap" class="clearfix">
		<div id="content">
			<div id="posts">
				<?php if ( $this->_['content']['posts'] ) : foreach ( $this->_['content']['posts'] as $post ) : ?>
					<div class="post">
						<h2>
							<a href="#!/post/<?php echo $post->post_name; ?>/<?php echo $post->ID; ?>/">
								<?php echo $post->post_title; ?>
							</a>
						</h2>
						<p>
							<small>
								<?php echo date('j. F Y', strtotime($post->post_date)); ?>
							</small>
						</p>
						<div>
							<?php echo $post->post_content; ?>
						</div>
					</div>
				<?php endforeach; else : ?>
					<div class="post">
						<h2>Es wurde nichts gefunden</h2>
						<p>Zurück zur <a href="#!/index/1/">Startseite</a></p>
					</div>
				<?php endif; ?>
			</div>
			<?php if ( $this->_['content']['posts'] ) : ?>
				<div id="pagination" class="clearfix">
				<?php
					$page = $this->_['meta']['page'];
					$slug = $this->_['meta']['slug'];
					$id = $this->_['meta']['id'];
					$max = ceil($this->_['content']['count']/10);
				
					if ( $page > 1 )
					{
						echo '<a class="prev_page" href="#!/category/' . $slug . '/' . $id . '/' . ($page-1) . '/">Vorherige Seite</a>';
					}
				
					if ( $max > $page )
					{
						echo '<a class="next_page" href="#!/category/' . $slug . '/' . $id . '/' . ($page+1) . '/">N&auml;chste Seite</a>';
					}
				?>
				</div>
			<?php endif; ?>
		</div>
		<?php include 'components/sidebar.inc.php'; ?>
	</div>
</div>
<?php include 'components/footer.inc.php'; ?>