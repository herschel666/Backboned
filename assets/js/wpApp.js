/*
 * jQuery hashchange event - v1.3 - 7/21/2010
 * http://benalman.com/projects/jquery-hashchange-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,e,b){var c="hashchange",h=document,f,g=$.event.special,i=h.documentMode,d="on"+c in e&&(i===b||i>7);function a(j){j=j||location.href;return"#"+j.replace(/^[^#]*#?(.*)$/,"$1")}$.fn[c]=function(j){return j?this.bind(c,j):this.trigger(c)};$.fn[c].delay=50;g[c]=$.extend(g[c],{setup:function(){if(d){return false}$(f.start)},teardown:function(){if(d){return false}$(f.stop)}});f=(function(){var j={},p,m=a(),k=function(q){return q},l=k,o=k;j.start=function(){p||n()};j.stop=function(){p&&clearTimeout(p);p=b};function n(){var r=a(),q=o(m);if(r!==m){l(m=r,q);$(e).trigger(c)}else{if(q!==m){location.href=location.href.replace(/#.*/,"")+q}}p=setTimeout(n,$.fn[c].delay)}$.browser.msie&&!d&&(function(){var q,r;j.start=function(){if(!q){r=$.fn[c].src;r=r&&r+a();q=$('<iframe tabindex="-1" title="empty"/>').hide().one("load",function(){r||l(a());n()}).attr("src",r||"javascript:0").insertAfter("body")[0].contentWindow;h.onpropertychange=function(){try{if(event.propertyName==="title"){q.document.title=h.title}}catch(s){}}}};j.stop=k;o=function(){return a(q.location.href)};l=function(v,s){var u=q.document,t=$.fn[c].domain;if(v!==s){u.title=h.title;u.open();t&&u.write('<script>document.domain="'+t+'"<\/script>');u.close();q.location.hash=v}}})();return j})()})(jQuery,this);

/*
 * Author: Emanuel kluge
**/

(function($){

	/*
	 * The Backbone Model
	********************************************************************/

	window.site.js.wordpress = Backbone.Model.extend();

	/*
	 * The Backbone Collection
	********************************************************************/

	window.site.js.wordpressCollection = Backbone.Collection.extend({

		model : window.site.js.wordpress,
	
		months : {
			'01' : 'Januar',
			'02' : 'Februar',
			'03' : 'M&auml;rz',
			'04' : 'April',
			'05' : 'Mai',
			'06' : 'Juni',
			'07' : 'Juli',
			'08' : 'August',
			'09' : 'September',
			'10' : 'Oktober',
			'11' : 'November',
			'12' : 'Dezember'
		},

		initialize : function ()
		{
			_.bindAll(this, 'check');
	
			this.bind('refresh', this.check);
		},
	
		/*
		 * Function to check, if the collection is empty
		 * and the 404-page has to be displayed
		**/
	
		check : function ()
		{
			var type = this.view.options.type,
				model = type ? this.toJSON()[0][type] : this;
			
			if ( model.length )
			{
				this.view.trigger('refresh');
			}
			else
			{
				if ( type == 'comments' )
				{
					this.view.noComments();
				}
				else
				{
					window.location.hash = '!/404/';
				}
			}
		},

		returnMonth : function ( number )
		{
			return this.months[number];
		},
	
		/*
		 * Converting the post-/commentdate to a nicer form
		**/
	
		nicedate : function ( data )
		{
			var date_item = data.substr(0, data.indexOf(' ')).split('-');
		
			return date_item[2].replace(/^0/, '') + '. ' + this.returnMonth(date_item[1]) + ' '+ date_item[0];
		}

	});

	/*
	 * The Backbone Controller
	********************************************************************/

	window.site.js.wordpressController = Backbone.Controller.extend({

		routes : {
			'' : 'init',
			'!/index/:page/' : 'index',
			'!/post/:slug/:id/' : 'post',
			'!/page/:slug/' : 'page',
			'!/category/:slug/:id/:page/' :	'category',
			'!/archive/:month/:year/:page/' : 'archive',
			'!/404/' : 'error',
			'*misc' : 'redirect404'
		},

		/*
		 * When the visitors leave a single-
		 * post-page, the comments and the
		 * comment form get removed
		**/
	
		removeComments : function ()
		{				
			$(window).hashchange( function()
			{
				$('#comments, #commentform').empty();
				$('h2.comment_headline').remove();
			});
		},

		initialize : function ()
		{
			_.bindAll(this, 'removeComments');
	
			this.bind('route:post', this.removeComments);
		
			/*
			 * Every time a hashchange occurs,
			 * the visitor gets scrolled to top
			 * to pretend a page reload
			**/
			
			var that = this;
			
			this.bind('all', function()
			{
				window.scrollTo(0, 0);
				that.navPagesApp.checkCurrent();
			});
		
			/*
			 * The page-, archive-, category-
			 * navigations and the title
			 * get initialized
			**/
	
			this.navPagesApp = new window.site.js.navView({
				navId : 'pages'
			});
	
			this.navCategoriesApp = new window.site.js.navView({
				navId : 'categories',
				title : 'Kategorien'
			});
	
			this.navArchivesApp = new window.site.js.navView({
				navId : 'archives',
				title : 'Archiv'
			});
			
			this.navBookmarksApp = new window.site.js.navView({
				navId : 'bookmarks',
				title : 'Blogroll'
			});
			
			if ( site.twtr_info )
			{
				this.twitterCollection = new window.site.js.wordpressCollection;

				this.twitterCollection.url = 'http://twitter.com/statuses/user_timeline.json?screen_name=' + site.twtr_info.name + '&count=' + site.twtr_info.count + '&callback=?';
				
				this.twitterWidget = site.twtr_info && new window.site.js.twitterView({
					model : this.twitterCollection.fetch()
				});
			}
	
			this.theTitle = new window.site.js.titleView();
		
			this.theFooter = new window.site.js.footerView();
		
			/*
			 * Start Backbone history to
			 * enable the browser history
			**/
		
			Backbone.history.start();
		},

		init : function ()
		{
			/*
			 * If there's no location hash at all
			 * the index hash is added to start
			 * the "ride"
			**/
		
			window.location.hash = '!/index/1/';
		},
	
		/*
		 * The frontpage content ( posts and
		 * pagination) gets initialized
		**/

		index : function ( page )
		{
			var indexCollection = new window.site.js.wordpressCollection;
	
			indexCollection.url = '?_escaped_fragment_=/index/' + page + '/&output_type=JSON';
	
			var indexView = new window.site.js.postsView({
					model : indexCollection.fetch(),
					controller : this
				}),
				pagination = new window.site.js.paginationView({
					page : page,
					count : site.post_count,
					slug : '#!/index/'
				});
		},
	
		/*
		 * The single post content ( post,
		 * comments and comment form) gets
		 * initialized
		**/

		post : function ( slug, id )
		{				
			var singlePostCollection = new window.site.js.wordpressCollection,
				commentCollection = new window.site.js.wordpressCollection,
				commentFormCollection = new window.site.js.wordpressCollection({
					post_id : id,
					post_url : site.base_url + '#!/post/' + slug + '/' + id + '/'
				});
	
			singlePostCollection.url = '?_escaped_fragment_=/post/' + slug + '/' + id + '/&output_type=JSON';
			commentCollection.url = singlePostCollection.url;
	
			var singleView = new window.site.js.singlePostView({
					model : singlePostCollection.fetch(),
					type : 'post'
				}),
				comments = new window.site.js.commentsView({
					model : commentCollection.fetch(),
					type : 'comments'
				}),
				commentForm = new window.site.js.commentFormView({
					model : commentFormCollection,
					commentView : comments,
					postView : singleView
				});
	
		},
	
		/*
		 * The page content gets initialized
		**/

		page : function ( slug )
		{
			var pageCollection = new window.site.js.wordpressCollection;
	
			pageCollection.url = '?_escaped_fragment_=/page/' + slug + '/&output_type=JSON';

			var page = new window.site.js.pageView({
				model : pageCollection.fetch()
			});
			
			this.navPagesApp.checkCurrent(slug);
		},
	
		/*
		 * The category content ( posts and
		 * pagination) gets initialized
		**/

		category : function ( slug, id, page )
		{
			var categoryCollection = new window.site.js.wordpressCollection;
	
			categoryCollection.url = '?_escaped_fragment_=/category/' + slug + '/' + id + '/' + page + '/&output_type=JSON';
	
			var categoryView = new window.site.js.postsView({
					model : categoryCollection.fetch()
				}),
				pagination = new window.site.js.paginationView({
					page : page,
					category_id : id,
					slug : '#!/category/' + slug + '/' + id + '/'
				});
		},
	
		/*
		 * The archive content ( posts and
		 * pagination) gets initialized
		**/

		archive : function ( month, year, page )
		{
			var archiveCollection = new window.site.js.wordpressCollection;
	
			archiveCollection.url = '?_escaped_fragment_=/archive/' + month + '/' + year + '/' + page + '/&output_type=JSON';
	
			var archiveView = new window.site.js.postsView({
					model : archiveCollection.fetch()
				}),
				pagination = new window.site.js.paginationView({
					page : page,
					month : month,
					year : year,
					slug : '#!/archive/' + month + '/' + year + '/'
				});
		},
	
		/*
		 * The 404 page content gets initialized
		 * and rendered
		**/

		error : function ()
		{
			var errorCollection = new window.site.js.wordpressCollection({
				title : '404',
				content : 'Leider wurde nichts gefunden. Zurück zur <a href="#!/index/1/">Startseite</a>'
			});

			var error = new window.site.js.errorView({
				model : errorCollection
			}).render();
		},
		
		redirect404 : function ()
		{
			window.location.hash = '!/404/';
		}

	});

	/*
	 * The Backbone View - Posts Collection (e.g. on frontpage)
	********************************************************************/

	window.site.js.postsView = Backbone.View.extend({

		el : $('#posts'),
	
		template : $('#post-template'),

		/*
		 * Setting the collection's view to
		 * 'this', so it can check if it contains
		 * content and - if 'true' - trigger the
		 * refresh and starting the rendering-
		 * process
		**/
	
		initialize : function ()
		{
			_.bindAll(this, 'addPost');
			this.bind('refresh', this.addPost);
			this.model.view = this;
		},
	
		/*
		 * Each item - passed by the addPost-function -
		 * gets rendered and append to the container
		 * element;
		 * when all items are appended, the container
		 * element is faded in
		**/

		render : function ( model, count )
		{
			var that = this;
		
			that.el.append( that.template.tmpl(model, {
				nicedate : function ( post_date )
				{
					return that.model.nicedate( post_date );
				},
				commentCount : function ( comment_count )
				{
					return comment_count == 0 ?
						'Keine Kommentare' :
						comment_count == 1 ?
						'Ein Kommentar' :
						comment_count + ' Kommentare';
				}
			}));
			
			if ( count == this.model.length )
			{
				this.el.fadeIn('slow');
			}
		
			return this;
		},
	
		/*
		 * The container element is emptied and set to
		 * 'display: none';
		 * each item of the model and the current
		 * count are passed to the render-function
		**/

		addPost : function ()
		{
			this.el
				.empty()
				.hide();			
	
			_.each( this.model.toJSON(), function ( num, key )
			{
				this.render(num, ++key);
			}, this);
		}

	});

	/*
	 * The Backbone View - Single Post
	********************************************************************/

	window.site.js.singlePostView = Backbone.View.extend({

		el : $('#posts'),

		template : $('#single-post-template'),

		/*
		 * The container elements gets emptied
		 * and is set to 'display: none';
		 * again the view is passed to its
		 * collection, so that the existence
		 * of content can get checked to
		 * start the rendering process
		**/
	
		initialize : function ()
		{
			_.bindAll(this, 'render');
			this.el
				.empty()
				.hide();
			this.model.view = this;
			this.bind('refresh', this.renderCategories);
		},

		/*
		 * As there's no need for a pagination
		 * on a single post view, the pagination
		 * container element gets emptied;
		 * afterwards the post gets rendered
		 * and faded in
		**/
	
		render : function ( model )
		{
			var that = this;
		
			$('#pagination').empty();
		
			that.el
				.append( that.template.tmpl( model, {
					nicedate : function ( post_date )
					{
						return that.model.nicedate( post_date );
					},
					displayCategories : function ( categories )
					{
						var catArr = [
							'<p><small>',
							/,\s/.test(categories) ? 'Kategorien: ' : 'Kategorie: ',
							categories,
							'</small></p>'
						].join('');
						
						return categories && catArr;
					}
				}))
				.fadeIn('slow');
		
			return this;
		},
	
		/*
		 * Before the post gets rendered, the categories
		 * are extracted from the model, put into an anchor-
		 * list and merged with the post in the model.
		 * Afterwards, the actual post gets rendered with
		 * new, merged model.
		**/

		renderCategories : function ()
		{
			var categories = [],
				obj = this.model.toJSON()[0]['cats'],
				merged;
		
			for ( i in obj )
			{
				if ( obj.hasOwnProperty(i) )
				{
					categories.push('<a href="#!/category/' + obj[i].slug + '/' + obj[i].term_id + '/1/">' + obj[i].name + '</a>');
				}
			}
		
			merged = _.extend(this.model.toJSON()[0]['post'][0], {categories:categories.join(', ')});
		
			this.render( merged );
		}

	});

	/*
	 * The Backbone View - Page
	********************************************************************/

	window.site.js.pageView = Backbone.View.extend({

		el : $('#posts'),

		template : $('#page-template'),

		/*
		 * The container elements gets emptied
		 * and is set to 'display: none';
		 * again the view is passed to its
		 * collection, so that the existence
		 * of content can get checked to
		 * start the rendering process
		**/

		initialize : function ()
		{
			_.bindAll(this, 'render');
			this.el
				.empty()
				.hide();
			this.model.view = this;
			this.bind('refresh', this.render);
		},

		/*
		 * As there's no need for a pagination
		 * on a page view, the pagination
		 * container element gets emptied;
		 * afterwards the page gets rendered
		 * and faded in
		**/

		render : function ()
		{
			var that = this;

			$('#pagination').empty();

			that.el
				.append( that.template.tmpl( this.model.toJSON()[0], {
					nicedate : function ( post_date )
					{
						return that.model.nicedate( post_date );
					}
				}))
				.fadeIn('slow');

			return this;
		}

	});

	/*
	 * The Backbone View - Comments for a Single Post
	********************************************************************/

	window.site.js.commentsView = Backbone.View.extend({

		el : $('#comments'),

		template : $('#comment-template'),
		
		events : {
			'click .comment_delete' : 'deleteComment'
		},

		/*
		 * Same checking procedure as before;
		 * if the comments model is empty, there
		 * is - contrary to posts and pages - no
		 * redirect to the 404-page, but a message,
		 * that there aren't any comments to the
		 * current post in the moment (see noComments-
		 * function further below)
		**/
	
		initialize : function ()
		{
			_.bindAll(this, 'addComments');
			this.model.view = this;
			this.bind('refresh', this.addComments);
		},
		
		/*
		 * The comments and a headline are appended
		 * to the DOM, a Delete-Button is added, if the
		 * user is logged in;
		 * when all comments are rendered, the wrapper-
		 * <div> gets faded in
		**/

		render : function ( model, count )
		{
			var that = this,
				len = this.model.toJSON()[0]['comments'].length,
				post_title = this.model.toJSON()[0]['post'][0]['post_title'],
				headline = (len == 1 ? 'Ein Kommentar zu ' : len + ' Kommentare zu "') + post_title + '"';
		
			that.el.append( that.template.tmpl( model, {
				nicedate : function ( comment_date )
				{
					return that.model.nicedate( comment_date );
				},
				linkify : function ( comment_author )
				{
					if ( model.comment_author_url )
					{
						return '<a href="' + model.comment_author_url + '">' + comment_author + '</a>';
					}
					else
					{
						return comment_author;
					}
				},
				deleteButton : function ( logged_in )
				{
					if ( site.logged_in )
					{
						return '<span class="comment_delete ID-' + model.comment_ID + '">Löschen</span>';
					}
					else
					{
						return '';
					}
				}
			}));
		
			if ( count == len )
			{
				if ( this.el.prev()[0].tagName.toLowerCase() == 'h2' )
				{
					this.el
						.prev()
						.text(headline);
				}
				else
				{
					this.el.before('<h2 class="comment_headline">' + headline + '</h2>');
				}
				
				this.el.fadeIn('slow');
			}
		
			return this;
		},
		
		/*
		 * The delete-button is filled with 'life'
		**/
		
		deleteComment : function ( evnt )
		{
			var	that = this,
				Id = evnt.target.className.substr(evnt.target.className.indexOf('-')+1);
			
			if ( site.logged_in )
			{
				$.ajax({
					url : site.base_url,
					data : '?_escaped_fragment_=/comment_delete/' + Id + '/',
					success : function ( resp )
					{
						that.model.fetch();
					}
				});
			}
		},
	
		/*
		 * Each item of the comment model and
		 * the current count get passed to
		 * the render-function
		**/

		addComments : function ()
		{
			this.el
				.empty()
				.hide();
	
			_.each( this.model.toJSON()[0]['comments'], function( num, key )
			{
				this.render(num, ++key);
			}, this);
		},
	
		/*
		 * If the model's check for content
		 * returns 'false', this function is
		 * called to display the message
		**/

		noComments : function ()
		{
			this.el.before('<h2 class="comment_headline">Zu diesem Beitrag gibt es noch keine Kommentare.</h2>');
		}

	});
	
	/*
	 * The Backbone View - Comment Form
	********************************************************************/

	window.site.js.commentFormView = Backbone.View.extend({

		el : $('#commentform_wrap'),

		/*
		 * The comment form template isn't loaded
		 * directly in the static DOM as its
		 * appereance depends on the user status
		**/
		
		template : null,

		/*
		 * The template is loaded asynchronuosly
		 * and a callback-function is executed
		**/
		
		get_template : function ( callback )
		{
			var that = this;
	
			$.ajax({
				url : site.base_url,
				data : '_escaped_fragment_=/commentform/',
				success : function ( resp )
				{
					that.template = $(resp);
					callback.apply(that);
				}
			});
		},

		events : {
			'click #submit' : 'submitForm',
			'click .logout' : 'logout'
		},

		initialize : function ()
		{
			_.bindAll(this, 'submitForm');
			this.get_template(this.render);
		},

		/*
		 * Rendering the comment form
		**/
		
		render : function ()
		{
			this.el
				.hide()
				.html( this.template.tmpl( this.model.toJSON()[0] ) )
				.fadeIn('slow');
				
			return this;
		},
		
		/*
		 * Posting a comment happens asynchronuosly;
		 * if successful, the model fetches the new
		 * content and triggers the comments-view
		 * to re-render
		**/

		submitForm : function ( evnt )
		{			
			evnt.preventDefault();
	
			var that = this,
				$form = $(evnt.target.form),
				$data = $form.serializeArray();
		
			$.post('wp-comments-post.php', $data, function ( resp, status )
			{				
				if ( status === 'success' )
				{
					that.options.commentView.model.fetch();
					evnt.target.form.reset();
				}
			});
		},
	
		/*
		 * AJAX-Logout; if successful, the comment
		 * form and the comments list gets re-rendered,
		 * the WP-Admin-Bar at the top gets removed
		 * and the style- element for Admin-Bar
		 * gets emptied
		**/

		logout : function ( evnt )
		{
			evnt.preventDefault();
	
			var that = this;
	
			$.get(evnt.target.href, function ( resp, status )
			{
				if ( status === 'success' )
				{
					var $adminBar = $('#wpadminbar');
					site.logged_in = false;
				
					that.get_template(that.render);
					that.options.commentView.model.fetch();
				
					if ( $adminBar.length )
					{
						$adminBar.remove();
						$.each($('head style'), function ()
						{
							$(this).html( function ()
							{
								return !!/html\s\{\smargin-top:\s28px\s!important;\s\}/.test($(this).text()) && '';
							});
						});
					}
				}
			});
		}

	});
	
	/*
	 * If a collection is empty and the site
	 * is redirected to #!/404/, the error-view
	 * gets rendered and displays a message
	**/

	window.site.js.errorView = Backbone.View.extend({

		el : $('#posts'),

		template : $('#error-template'),

		initialize : function ()
		{
			$('#pagination').empty();
		},

		render : function ()
		{
			$('#pagination, #commentform_wrap').empty();
			
			this.el
				.hide()
				.html( this.template.tmpl( this.model.toJSON()[0] ) )
				.fadeIn('slow');
			return this;
		}

	});
	
	/*
	 * For all sites with post lists
	 * a pagination gets rendered depending
	 * on the actual post count
	**/

	window.site.js.paginationView = Backbone.View.extend({

		el : $('#pagination'),

		initialize : function ()
		{
			this.el.hide();
			this.render();
		},

		render : function ()
		{
			var count,
				current = parseInt(this.options.page),
				items = [],
				j = 1;
	
			/*
			 * The following mess gets the total
			 * post count and builds a pagination
			 * with prev- and next-links, the two
			 * first/last pages and the current
			 * element with a certain amount of
			 * siblings.
			**/
			
			if ( current > 1 )
			{
				items.push('<span><a href="' + this.options.slug + (current-1) + '/">&laquo;</a></span>');
			}
	
			if ( !_.isUndefined(this.options.count) )
			{
				count = Math.ceil(this.options.count/10);
			}
			else
			{
				if ( !_.isUndefined(this.options.category_id) )
				{
					count = _.detect(site.categories, function( num )
					{
						return num.cat_id == this.options.category_id;
					}, this);
				}
				else if ( !_.isUndefined(this.options.year) && !_.isUndefined(this.options.month) )
				{
					var regDate = new RegExp('.(' + this.options.month + ').(' + this.options.year + ').', 'gi');
			
					count = _.detect(site.archives, function( num )
					{
						return regDate.test(num.slug);
					}, this);
				}
				count = Math.ceil(count.count/10);
			}
	
			for ( var i=0; i<count; i++ )
			{ 
				if (  j == current)
				{		
					items.push('<span class="current">' + j + '</span>');
				}
				else if ( j != current )
				{
					if ( i == 0 || i == 1 || i == count-2 || i == count-1 )
					{
						items.push('<span><a href="' + this.options.slug + j + '/">' + j + '</a></span>');
					}
			
					if ( current > 3 && i == current-2 && i < count-2 || current < count-2 && i == current && i > 1 )
					{
						items.push('<span><a href="' + this.options.slug + j + '/">' + j + '</a></span>');
					}
			
					if ( current > 4 && i == current-3 || i == current+1 && current < count-3 )
					{
						items.push('<span>&hellip;</span>');
					}
				}
				j++;
			}
	
			if ( current < count )
			{
				items.push('<span><a href="' + this.options.slug + (current+1) + '/">&raquo;</a></span>');
			}
	
			this.el
				.html(items.join(''))
				.fadeIn('slow');
			return this;
		},

	});
	
	/*
	 * The template isn't loaded directly in
	 * the static DOM as the appereance depends
	 * on the user status
	**/

	window.site.js.titleView = Backbone.View.extend({

		el : $('#title'),

		template : $('#title-template'),

		initialize : function ()
		{
			this.render();
		},

		/*
		 * The title and description gets rendered;
		 * a horizontal fade from black to grey is
		 * added to the title, because it's possible :-)
		**/
		
		render : function ()
		{
			this.el.html( this.template.tmpl(site.title, {
				beautify : function ( title )
				{
					var title = title.split(''),
						len = title.length,
						steps = Math.floor(95/len), /* don't let them get brighter than rgb(95,95,95) */
						titleArr = [];

					for ( var i=0; i<len; i++ )
					{
						var j = (i+1)*steps,
							rgbCode = j+','+j+','+j,
							item = title[i];
					
						titleArr.push(
							!/\s/.test(item) ?
							'<span style="color:rgb(' + rgbCode + ')">' + item + '</span>' :
							' '
						);
					}
				
					return titleArr.join('');
				}
			}));
			return this;
		}

	});
	
	/*
	 * The view for navigations; which kind
	 * of navigation and if a title has to
	 * be added before the navigation
	 * depends on the navigation's type passed
	 * over by the views options
	**/

	window.site.js.navView = Backbone.View.extend({

		template : $('#nav-item-template'),

		initialize : function ()
		{
			this.el = $('#' + this.options.navId);
			this.addItems();
		},

		render : function ( item )
		{
			
			this.el.append( this.template.tmpl( item ) );
			return this;
		},

		addItems : function ()
		{
			if ('title' in this.options)
			{
				this.el.before($('<h4 />', {text:this.options.title}));
			}
			
			_.each( site[this.options.navId], function( num, key )
			{
				this.render(num);
			}, this);
		},
		
		/*
		 * Highlights the navigation-element
		 * of the current page
		**/
		
		checkCurrent : function ()
		{
			var hashReg = new RegExp(location.hash, 'g'),
				$elem = this.el.find('a');
			
			$.each($elem, function ()
			{
				this.className = hashReg.test(this.href) ? 'current' : '';
			});
		}

	});
	
	/*
	 * The list of the latest tweets gets rendered;
	 * the functions to add hyperlinks to parts of
	 * the tweet and to display the relative time
	 * are taken from http://twitter.com/javascripts/blogger.js
	**/
	
	window.site.js.twitterView = Backbone.View.extend({
		
		el : $('#twitter_widget'),
		
		template : $('#twitter-template'),
		
		initialize : function ()
		{
			_.bindAll(this, 'addTweet');
			this.model.view = this;
			this.bind('refresh', this.addTweet);
		},
		
		render : function ( model, count )
		{
			var that = this;
			
			this.el.append(  that.template.tmpl( model, {
				tweetify : function ( txt )
				{
					var txt = txt.replace(/http:\/\/\S+/g, '<a href="$&" target="_blank">$&</a>'),
						txt = txt.replace(/\s(@)(\w+)/g, ' @<a href="http://twitter.com/$2" target="_blank">$2</a>'),
						txt = txt.replace(/\s(#)(\w+)/g, ' #<a href="http://search.twitter.com/search?q=%23$2" target="_blank">$2</a>');
					
					return txt;
				},
				relativeTime : function ( created_at )
				{
					var values = created_at.split(' '),
						time_value = values[1] + ' ' + values[2] + ', ' + values[5] + ' ' + values[3],
						parsed_date = Date.parse(time_value),
						relative_to = (arguments.length > 1) ? arguments[1] : new Date(),
						delta = parseInt((relative_to.getTime() - parsed_date) / 1000),
						delta = delta + (relative_to.getTimezoneOffset() * 60);
						
					if ( delta < 60 )
					{
						return 'less than a minute ago';
					}
					else if ( delta < 120 )
					{
						return 'about a minute ago';
					}
					else if ( delta < (60*60) )
					{
						return (parseInt(delta / 60)).toString() + ' minutes ago';
					}
					else if ( delta < (120*60) )
					{
						return 'about an hour ago';
					}
					else if ( delta < (24*60*60) )
					{
						return 'about ' + (parseInt(delta / 3600)).toString() + ' hours ago';
					}
					else if ( delta < (48*60*60) )
					{
						return '1 day ago';
					}
					else
					{
						return (parseInt(delta / 86400)).toString() + ' days ago';
					}
				}
			}));
			
			if ( count == this.model.length )
			{
				this.el.fadeIn('slow');
			}
			
			return this;
		},
		
		addTweet : function ()
		{
			var headline = [
				'<h4>',
				'<a href="http://twitter.com/' + site.twtr_info.name + '">',
				'Neueste Tweets von ' + site.twtr_info.name,
				'</a>',
				'</h4>'
			].join('');
			
			this.el
				.hide()
				.before(headline);
			
			_.each(this.model.toJSON(), function ( key, num )
			{
				this.render(key, ++num);
			}, this);
		}
		
	});
	
	/*
	 * The footer gets rendered...
	**/

	window.site.js.footerView = Backbone.View.extend({

		el : $('#footer'),

		initialize : function ()
		{
			this.render();
		},

		render : function ()
		{
			var theTime = new Date(),
				content = [
					'<p class="footer_left">&copy; <a href="#!' + site.title.home + '">' + site.title.name + '</a> &middot; ' + theTime.getFullYear() + '</p>',
					'<p class="footer_right">&quot;<strong>Backboned</strong>&quot;-Theme by <a href="http://www.emanuel-kluge.de/">Emanuel Kluge</a></p>'
				].join('');
		
			this.el.html(content);
			return this;
		}

	});
	
})(jQuery);