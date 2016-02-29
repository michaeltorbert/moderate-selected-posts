<?php
/*
Plugin Name: Moderate Selected Posts
Plugin URI: http://sivel.net/wordpress/moderate-selected-posts/
Description: Force comment moderation on selected posts but allow others to remain open.
Author: Michael Torbert, hallsofmontezuma, Matt Martz
Author URI: http://sivel.net/
Version: 1.4

        Copyright (c) 2008 Matt Martz (http://sivel.net)
        Moderate Selected Posts is released under the GNU General Public License (GPL)
	http://www.gnu.org/licenses/gpl-2.0.txt
*/

// if were in the admin load the admin functionality
if ( is_admin () )
        require_once( dirname ( __FILE__ ) . '/inc/admin.php' );

// get specific option
function msp_get_opt ( $option ) {
        $msp_options = get_option ( 'msp_options' );
        return $msp_options[$option];
}

// the comment id has been added, let's check it against criteria
function moderate_selected_posts ( $comment ) {
	$post_ID = $comment['comment_post_ID'];
	if ( false == is_admin_author ( $post_ID ) ) :
		$moderated_posts = msp_get_opt ( 'posts' );
		if ( ! is_array ( $moderated_posts ) )
			$moderated_posts = array ();
		if ( in_array ( $post_ID , $moderated_posts ) )
			add_filter ( 'pre_comment_approved' , create_function ( '$a' , 'return \'0\';' ) , 1 );
	endif;
	return $comment;
}

// conditional to check if the user is author or admin
function is_admin_author ( $post_ID ) {
	global $user_ID, $userdata;
	get_currentuserinfo ();
        if ( $user_ID ) :
                $user = new WP_User ( $user_ID );
		$postdata = get_post ( $post_ID );
		$post_author = $postdata->post_author;
        endif;
        if ( $userdata && ( $user_ID == $post_author || $user->has_cap ( 'moderate_comments' ) ) ) :
		return true;
	else :
		return false;
	endif;
}


// load the action that makes this work
add_action ( 'preprocess_comment' , 'moderate_selected_posts' , 0 );
