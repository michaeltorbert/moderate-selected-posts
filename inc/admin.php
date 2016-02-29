<?php
/**
 * Part of WordPress Plugin: Moderate Selected Posts
 * Plugin URI: http://sivel.net/wordpress/
 */

// Plugin Version
$msp_version = '1.3';

// Full path and plugin basename of the main plugin file
$msp_plugin_file = dirname ( dirname ( __FILE__ ) ) . '/moderate-selected-posts.php';
$msp_plugin_basename = plugin_basename ( $msp_plugin_file );

load_plugin_textdomain( 'moderate-selected-posts', FALSE, '/moderate-selected-posts/lang' );

/**
 * Check the version in the options table and if less than this version perform update
 */
function msp_ver_check () {
	global $msp_version;
	if ( ( msp_get_opt ( 'version' ) < $msp_version ) || ( ! msp_get_opt ( 'version' ) ) ) :
		$msp_options['version'] = $msp_version;
		$msp_options['posts'] = msp_get_opt ( 'posts' );
		msp_delete ();
		add_option ( 'msp_options' , $msp_options );
	endif;
}

/**
 * Initialize the default options during plugin activation
 */
function msp_init () {
	global $msp_version;
	if ( ! msp_get_opt ( 'version' ) ) :
		$msp_options['version'] = $msp_version;
		$msp_options['posts'] = array();
		add_option ( 'msp_options' , $msp_options ) ;
	else :
		msp_ver_check ();
	endif;
}

/**
 * Delete all options 
 */
function msp_delete () {
	delete_option ( 'msp_options' );
}

/** 
 * Add the options page
 */
function msp_options_page () {
	global $msp_plugin_basename;
	if ( current_user_can ( 'edit_others_posts' ) && function_exists ( 'add_options_page' ) ) :
		add_options_page ( __( 'Moderate Selected Posts' ) , __( 'Moderate Selected Posts' ) , 'publish_posts' , 'moderate-selected-posts' , 'msp_admin_page' );
		add_filter("plugin_action_links_$msp_plugin_basename", 'msp_filter_plugin_actions' );
	endif;
}

function msp_filter_plugin_actions ( $links ) { 
        $settings_link = '<a href="options-general.php?page=moderate-selected-posts">' . __( 'Settings' ) . '</a>';
        array_unshift ( $links, $settings_link );
        return $links;
}

/** 
 * The options page
 */
function msp_admin_page () {
	msp_ver_check ();
	if ( ! empty ( $_POST['action'] ) && $_POST['action'] == 'update' ) :
		$post_ids = $_POST['post_id'];
		if ( ! is_array ( $post_ids ) )
			$post_ids = array ();
		$msp_options['posts'] = $post_ids;
		$msp_options['version'] = msp_get_opt ( 'version' );
		update_option ( 'msp_options' , $msp_options );
		echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved.' ) . '</strong></p></div>';
	else :
		$post_ids = msp_get_opt ( 'posts');
		if ( ! is_array ( $post_ids ) )
			$post_ids = array ();
	endif;
?>
	<div class="wrap">
		<h2><?php _e( 'Moderate Selected Posts Settings' ); ?></h2>
		<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
                        <input type="hidden" name="action" value="update" />
			<h3><?php _e( 'Post List' ); ?></h3>
			<p><?php _e( 'Select the posts that you wish to moderate all comments on.' ); ?></p>
			<table class="form-table">
<?php
	$avail_posts = get_posts ( 'numberposts=-1&post_type=any' );
	foreach ( $avail_posts as $post ) :
?>
				<tr valign="top">
					<th scope="row">
						<?php echo $post->post_title; ?>
					</th>
					<td>
						<input type="checkbox" name="post_id[]" value="<?php echo $post->ID; ?>"<?php checked ( true , in_array ( $post->ID , $post_ids ) ); ?>/>
					</td>
				</tr>
<?php
	endforeach;
?>
			</table>
			<p class="submit">
				<input type="submit" name="submit" class="button-primary" value="<?php _e( 'Save Changes' ); ?>" />
			</p>
		</form>
	</div>
<?php
}

/**
 * The meta box
 */
function post_moderation_status_meta_box ( $post ) {
	$post_ID = $post->ID;
?>
	<p>
		<input name="msp" type="hidden" value="update" />
		<label for="moderate_status" class="selectit">
			<input type="checkbox" name="moderate_status" id="moderate_status"<?php if ( in_array ( $post_ID , msp_get_opt ( 'posts' ) ) ) echo ' checked="checked"'; ?>/>
			<?php _e( 'Moderate Comments' ); ?>
		</label>
	</p>
	<p><?php _e( 'These settings apply to this post only. For a full list of moderation statuses see the'); ?> <a href="options-general.php?page=moderate-selected-posts"><?php _e( 'global settings page' ); ?></a>.</p>
<?php
}

/**
 * Add meta box to create/edit post pages
 */
function msp_meta_box () {
	add_meta_box ( 'postmoderationstatusdiv' , __( 'Moderation' ) , 'post_moderation_status_meta_box' , 'post' , 'normal' , 'high' );
	add_meta_box ( 'postmoderationstatusdiv' , __( 'Moderation' ) , 'post_moderation_status_meta_box' , 'page' , 'normal' , 'high' );
}

/**
 * Get custom POST vars on edit/create post pages and update options accordingly
 */
function msp_meta_save () {
	if ( ! empty ( $_POST['msp'] ) && $_POST['msp'] == 'update' ) :
		$post_ID = $_POST['post_ID'];
		$moderated_posts = msp_get_opt ( 'posts' );
		if ( ! is_array ( $moderated_posts ) )
			$moderated_posts = array ();
		if ( ! empty ( $_POST['moderate_status'] ) && $_POST['moderate_status'] == 'on' ) :
			$moderated_posts[] = $post_ID ;
			$msp_options['posts'] = $moderated_posts;
		else :
			$msp_options['posts'] = array_filter ( $moderated_posts , 'msp_array_delete' );
		endif;
		$msp_options['version'] = msp_get_opt ( 'version' );
		update_option ( 'msp_options' , $msp_options );
	endif;
}

/**
 * Remove item from array
 */
function msp_array_delete ( $item ) {
	return ( $item !== $_POST['post_ID'] );
}

/**
 * Activation hook
 */
register_activation_hook ( dirname ( dirname ( __FILE__ ) ) . '/moderate-selected-posts.php' , 'msp_init' );

/**
 * Tell WordPress what to do.  Action hooks.
 */
add_action ( 'admin_menu' , 'msp_options_page' ) ;
add_action ( 'admin_menu' , 'msp_meta_box' );
add_action ( 'save_post' , 'msp_meta_save' );

