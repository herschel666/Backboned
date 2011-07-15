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
					$post = $this->_['content']['post'][0];
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
					<div class="meta">
						<p>
							<small>
								<?php
									$categories = $this->_['content']['cats'];
									$length = count($categories);
									$iteration = 1;
									echo $length == 1 ? 'Kategorie: ' : 'Kategorien: ';
									foreach ( $categories as $categorie )
									{
										echo '<a href="#!/category/' . $categorie->slug . '/' . $categorie->term_id . '/1/">' . $categorie->name . '</a>';
										echo $iteration < $length ? ', ' : '';
										$iteration++;
									}
								?>
							</small>
						</p>
					</div>
				</div>
				<?php else : ?>
					<div class="post">
						<h2>Es wurde nichts gefunden</h2>
						<p>Zurück zur <a href="#!/index/1/">Startseite</a></p>
					</div>
				<?php endif; ?>
			</div>
			<?php
				if ( $post )
				{
					$comments = $this->_['content']['comments'];
					$length = count($comments);
					
					if ( $length )
					{				
						echo '<h2 class="comment_headline">' . ( $length == 1 ? 'Ein Kommentar ' : $length . ' Kommentare ' ) . ' zu "' . $post->post_title . '"</h2>';
				
						echo '<div id="comments">';
						foreach ( $comments as $comment )
						{
							echo '<div class="comment clearfix">';
							echo '<h3>';
							echo !empty($comment->comment_author_url) ? '<a href="' . $comment->comment_author_url . '" rel="nofollow">' : '';
							echo $comment->comment_author;
							echo !empty($comment->comment_author_url) ? '</a>' : '';
							echo '</h3>';
							echo '<small class="commentdate">Geschrieben am ' . date('j. F Y', strtotime($comment->comment_date)). '</small>';
							echo '<div class="commenttext">';
							echo wpautop($comment->comment_content);
							echo '</div>';
							echo '</div>';
						}
						echo '</div>';
					}
					else
					{
						echo '<h2 class="comment_headline">Es wurden noch keine Kommentare geschrieben</h2>';
					}
				}
			?>
		</div>
		<?php include 'components/sidebar.inc.php'; ?>
	</div>
</div>
<?php include 'components/footer.inc.php'; ?>