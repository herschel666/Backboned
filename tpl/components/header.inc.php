<div id="nav">
	<ul id="pages" class="clearfix">
		<?php foreach ( $this->_['main_nav'] as $nav_item ) : ?>
			<li>
				<a href="#!<?php echo $nav_item['slug']; ?>">
					<?php echo $nav_item['title']; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<div id="wrap">
	<div id="title">
		<div>
			<h1>
				<a href="#!<?php echo $this->_['title']['home']; ?>">
					<?php echo $this->_['title']['name']; ?>
				</a>
			</h1>
			<em>
				<?php echo $this->_['title']['description']; ?>
			</em>
		</div>
	</div>