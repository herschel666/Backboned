<?php

/*
 * Return the comment-form-template depending on the user status;
 * ideally only accessible by xhrequest
**/

if ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
{
	$current_user = wp_get_current_user();

	echo '<script type="text/x-jquery-tmpl" id="commentform-template">';
	echo '<form action="" method="post" id="commentform">';
	echo '<fieldset class="clearfix">';

	if ( is_user_logged_in() )
	{
		echo '<p id="user_logged_in">Eingeloggt als ' . $current_user->display_name . ' (<a href="' . wp_logout_url() . '" class="logout">Ausloggen</a>)</p>';
	}
	else
	{
		echo '<div class="comment_form_row clearfix">';
		echo '<label for="author">Name <small>(wird ben&ouml;tigt)</small></label>';
		echo '<input type="text" name="author" id="author" value="" size="" tabindex="1" aria-required="true" />';
		echo '</div>';
		echo '<div class="comment_form_row clearfix">';
		echo '<label for="email">Mail <small>(wird nicht ver&ouml;ffentlicht) (wird ben&ouml;tigt)</small></label>';
		echo '<input type="text" name="email" id="email" value="" size="" tabindex="2" aria-required="true" />';
		echo '</div>';
		echo '<div class="comment_form_row clearfix">';
		echo '<label for="url">Website</label>';
		echo '<input type="text" name="url" id="url" value="" size="" tabindex="3" />';
		echo '</div>';
	}
	echo '<textarea name="comment" id="comment" cols="50" rows="10" tabindex="4"></textarea><br />';
	echo '<input name="submit" type="submit" id="submit" tabindex="5" value="Absenden" /><br />';
	echo '<input type="hidden" name="comment_post_ID" value="${ post_id }" id="comment_post_ID" />';
	echo '<input type="hidden" name="comment_parent" id="comment_parent" value="0" />';
	echo '<input type="hidden" name="redirect_to" id="redirect_to" value="${ post_url }" />';
	echo '</fieldset>';
	echo '</form>';
	echo '</script>';
}
exit;