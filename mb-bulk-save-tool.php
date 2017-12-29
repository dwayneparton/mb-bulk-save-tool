<?php /*

**************************************************************************

Plugin Name:  MB Bulk Save Tool
Plugin URI:   https://github.com/dwayneparton/mb-bulk-save-tool/
Description:  This tool is for developers and is useful for running tasks that happen when a post is updated. In and of itself it does nothing but update each post with no additional data. However if using a plugin like Import External Images is installed. It's actions will be executed on each post. When using this tool, you are essentially clicking update on every post.
Version:      1.0.0
Author:       Dwayne Parton
Author URI:   https://www.dwayneparton.com/
Text Domain:  mb-bulk-save-tool

**************************************************************************
*/

class MbBulkSaveTool {
	public $menu_id;
	
	// Initiate
	public function __construct() {
		// Load up the localization file if we're using WordPress in a different language
		load_plugin_textdomain( 'mb-bulk-save-tool' );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'wp_ajax_mb_bst_process', array( $this, 'ajax_update_post' ) );
		add_action( 'mb_bulk_save_actions', array( $this, 'update_posts' ));
		// Allow people to change what capability is required to use this plugin
		$this->capability = apply_filters( 'tools_update_posts_cap', 'manage_options' );
	}

	// Register the management page
	public function add_admin_menu() {
		$this->menu_id = add_management_page( __( 'Bulk Save Tool', 'bulk-save-tool' ), __( 'Bulk Save Tool', 'mb-bulk-save-tool' ), $this->capability, 'mb-bulk-save-tool', array($this, 'admin_tools_view') );
	}

	// UI
	public function admin_tools_view(){
		?>
		<div class="wrap mb-bulk-save-tool">
			<h1><?php _e('Bulk Save Post Action Runner'); ?></h1>
			<div id="about"><p>This tool is for developers and is useful for running tasks that happen when a post is updated. In and of itself it does nothing but update each post with no additional data. However if using a plugin like Import External Images is installed. It's actions will be executed on each post.</p><p>When using this tool, you are essentially clicking update on every post.</p></div>
			<?php 
				if(isset($_POST['post_type']) && count($_POST['post_type']) > 0){
					$post_types = $_POST['post_type'];
				}else{
					$post_types = array('post','page');
				}
				$json = $this->ajax_posts_object($post_types); 
				$j_decoded = json_decode($json);
			?>
			<form method="post" action="" class="mb-bulk-save-tool__form">
				<h2>Included Post Types</h2>
				<ul>
				<?php
				//print_r(get_post_types( '', 'objects' ));
				foreach ( get_post_types( '', 'objects' ) as $post_type => $object) {
					if(in_array($post_type, $post_types)){
						$checked = 'checked=checked';
					}else{
						$checked = '';
					}
					if($object->public == 1){
						$class = '';
					}else{
						$class = 'class="advanced" style="display:none;"';
					}
					echo '<li '.$class.'><input type="checkbox" name="post_type[]" value="' . $post_type . '" '.$checked .'>' . $object->label . '</li>';
				}
				?>
				</ul>
				<button class="button" type="submit">Save</button>
				<p><input id="mb-bulk-save-tool-show-advanced" type="checkbox" name="show_advanced" value="true"><small>Advanced: Show All Post Types</small></p>
			</form>
			<hr />
			<h2>Process</h2>
			<p>The button below will update <?php echo $j_decoded->count; ?> of the follow post types: <?php echo implode(', ',$post_types); ?>.</p>
			<button id="mb_bst_start" class="button primary">Run</button>
			<p>Leaving this page will terminate the process</p>
			<div class="mb-bulk-save-tool__progress">
				<div class="mb-bulk-save-tool__progress-status">0 of <?php echo $j_decoded->count; ?></div>
				<div class="mb-bulk-save-tool__progress-fill"></div>
			</div>
			<div>

			</div>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					var json = <?php echo $json; ?>;
					var posts = json.posts;
					//Run Update Posts when Clicked					
					$('#mb_bst_start').click(function(event){
						mb_bst_start();
						$(this).text('Processing');
						$(this).attr('disabled', 'disabled');
					});
					
					// Update Posts
					function mb_bst_start(){
						console.log('Processing posts...');
						$('#mb_bst_log').prepend('<li class="info">Processing posts...</li>');
						mb_bst_progress_bar();
					};

					//Update the post, log success or log error
					function mb_bst_update_post(id){
						$.ajax({
							type: 'POST',
							url: ajaxurl,
							data: { action: "mb_bst_process", id: id },
							success: function( response ) {
								console.log(id);
								console.log(response);
								$('#mb_bst_log').prepend('<li class="'+response.type+'">'+response.message+'</li>');
							},
							error: function( response ) {
								console.log(id);
								console.log(response);
								$('#mb_bst_log').prepend('<li class="error">'+response.error+'</li>');
							},
							complete: function( response ) {
								mb_bst_progress_bar();
							}
						});
					}

					// Update Progress bar
					var current = 0;
					function mb_bst_progress_bar(){
						$('.mb-bulk-save-tool__progress-status').text(current+' of '+posts.length);
						$('.mb-bulk-save-tool__progress-fill').css('width' , ((current/posts.length)*100)+'%');
						if(current < posts.length){
							mb_bst_update_post(posts[current]);
						}else{
							console.log('All done...');
							$('#mb_bst_log').prepend('<li class="info">All Done :)</li>');
							$('#mb_bst_start').text('Finished');
						}
						current++;
					}
					// Show Advanced
					$('#mb-bulk-save-tool-show-advanced').change(function(){
						if(this.checked){
							$(".advanced").show()
						}else{
							$(".advanced").hide();
						}
					});
				});
			</script>
			<div class="mb_update_posts__log">
				<ul id="mb_bst_log">
					<li class="info">Ready?</li>
					<li class="info"><?php echo $j_decoded->count; ?> combined posts will be processed...</li>
					<?php foreach($post_types as $post_type): ?>
						<li class="info">Ready to update <?php print_r(get_post_type_object( $post_type )->label); ?>...</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<style type="text/css">
				.mb-bulk-save-tool {}
				.mb-bulk-save-tool__form {}
				.mb-bulk-save-tool__progress {
					margin: 0.5rem 0;
					position: relative;
					display: block;
					height: 20px;
					background-color: #fefefe;
					border: 1px solid #e0e0e0;
				}
				.mb-bulk-save-tool__progress-status {
					position: absolute;
					margin: 0 auto;
					height: 20px;
					width: 100%;
					text-align: center;
				}
				.mb-bulk-save-tool__progress-fill {
					justify-content: flex-start;
					height: 20px;
					width: 0%;
					background-color: #999;
				}
				.mb_update_posts__log {
					height: 250px;
					background-color: #f9f9f9;
					border: 1px solid #e0e0e0;
					overflow-y: auto;
					padding: 1rem;
				}
				.mb_update_posts__log .error {
					color: #ff0000;
				}

			</style>
		</div>
		<hr />
		<div>
			<h2>Functions that will run:</h2>
			<?php $this::list_attached_actions(); ?>
		</div>
		<?php
	}

	// Just for Debugging..show what will run when update_post happens
	private static function list_attached_actions(){
		global $wp_filter;
		$filters = array();
		foreach ($wp_filter as $key => $value) {
			if ($key == 'mb_bulk_save_actions'){
				foreach ($value->callbacks as $priority => $actions) {
					foreach ($actions as $action_key => $action_value) {
						$message = "";
						if(is_object($action_value['function'][0])){
							$message .= 'Class: '. get_class($action_value['function'][0]).' | ';
						}
							$message .= 'Function: '.$action_value['function'][1];
						$filters[$key][] = $message;
					}
				}
			}
		}
		?>
		<ul>
		<?php foreach ($filters as $key => $value): ?>	
			<li><strong><?php echo $key; ?></strong>
				<ol>
				<?php foreach ($value as $string): ?>	
					<li><?php echo $string; ?></li>
				<?php endforeach; ?>
				</ol>
			</li>
		<?php endforeach; ?>
		</ul>
		<?php
	}

	// Create Post Object
	public function ajax_posts_object($post_types = array('post')){
		$args = array(
			'post_type' => $post_types, 
			'posts_per_page' => -1,
			'post_status' => 'any',
		);
		$posts = get_posts($args);
		$json = array();
		$json['types'] = $args['post_type'];
		$json['count'] = count($posts);
		foreach ($posts as $post) {
			$json['posts'][] = $post->ID;
		}
		return json_encode ($json);
	}

	// Action for updating posts
	public function update_posts($post){
		// Args to update
		$update_args = array(
			'ID' => $post->ID,
		);
		// Update the Post
		$updated_post = wp_update_post( apply_filters('mb_bulk_save_update_post_args', $update_args), true );						  
		// If this fails, then it just means that nothing was changed (old value == new value)
		if (is_wp_error($updated_post)) {
			$errors = $updated_post->get_error_messages();
			die( json_encode( array( 'type' => 'error', 'message' => implode("|",$errors))));
		}
	}

	// Process a single post ID (this is an AJAX handler)
	public function ajax_update_post() {
		@error_reporting( 0 ); // Don't break the JSON result
		header( 'Content-type: application/json' );
		$post_id = (int) $_REQUEST['id'];
		$post = get_post( $post_id );
		// Make sure user has permission
		if ( ! current_user_can( $this->capability ) ){
			die( json_encode( array( 'type' => 'error', 'message' =>  __( "Your user account doesn't have permission to update posts", 'mb-bulk-save-tool' ))));
		}
		// 5 minutes per post should be PLENTY
		@set_time_limit( 900 );
		// Actions to run
		do_action('mb_bulk_save_actions', $post);
		// Success Messages
		die( json_encode( array( 'type' => 'success', 'message' => sprintf( __( '&quot;%1$s&quot; (ID %2$s) was successfully updated in %3$s seconds.', 'regenerate-thumbnails' ), esc_html( get_the_title( $post->ID ) ), $post->ID, timer_stop() ) ) ) );
	}
}

// Start up this plugin
add_action( 'init', 'MbBulkSaveTool' );
function MbBulkSaveTool() {
	global $MbBulkSaveTool;
	$MbBulkSaveTool = new MbBulkSaveTool();
}
