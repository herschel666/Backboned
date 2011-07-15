<div id="sidebar">
	<h4>Kategorien</h4>
	<ul id="categories">
		<?php foreach ( $this->_['category_nav'] as $nav_item ) : ?>
			<li>
				<a href="#!<?php echo $nav_item['slug']; ?>">
					<?php echo $nav_item['title']; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<h4>Archiv</h4>
	<ul id="archives">
		<?php foreach ( $this->_['archive_nav'] as $nav_item ) : ?>
			<li>
				<a href="#!<?php echo $nav_item['slug']; ?>">
					<?php echo $nav_item['title']; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<h4>Blogroll</h4>
	<ul id="bookmarks">
		<?php foreach ( $this->_['bookmarks'] as $nav_item ) : ?>
			<li>
				<a href="#!<?php echo $nav_item['slug']; ?>">
					<?php echo $nav_item['title']; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>