# Bulk Save Tool for Wordpress

This tool is for developers and is useful for running tasks that happen when a post is updated. In and of itself it does nothing but run wp_update_post on each existing post with no additional data. Nothing fancy. But why?

This allows for the save_post action to execute. If using a plugin like [Auto Upload Images](https://wordpress.org/plugins/auto-upload-images/), then it's import image actions will be executed for each post without having to go and save each one individually! YAY! 

When using this tool, you are essentially clicking update on every post.

## Using this Tool

Backup your database! 

Access the WP Dashboard, and under tools you will see Bulk Save. Select the Post Types you want to update. 

## How I use it

mb_bulk_save_actions passes the post variable.

This plugin was built to specifically bulk run [Auto Upload Images](https://wordpress.org/plugins/auto-upload-images/) on 1800+ posts. Auto Upload Images only works when you save the post, so if you want to use it on every post you either have to bulk edit groups of them(which results in timesouts) or you have to save each one individually(waste of life). So that's why I wrote this script. I'm sure it could be faster but it gets the job done.

### A couplt examples

```
add_action( 'mb_bulk_save_actions', 'my_custom_action');
function my_custom_action($post){
  //This would update custom_meta_key for post 1 to have a value of "new_value1"
  update_post_meta( $post->ID, 'custom_meta_key', 'new_value'.$post->ID);
}
```

This would regenerate all post slugs, but there the update posts will be built in, and have the $update_args can be filtered.
```
add_action( 'mb_bulk_save_actions', 'mb_update_post_slugs');
function mb_update_post_slugs(){
	// Args to update
	$update_args = array(
		'ID' => $post->ID,
		'post_slug' => ''
	);
	// Update the Post
	$updated_post = wp_update_post( apply_filters('mb_bulk_save_update_post_args', $update_args), true );						  
	// If this fails, then it just means that nothing was changed (old value == new value)
	if (is_wp_error($updated_post)) {
		$errors = $updated_post->get_error_messages();
		die( json_encode( array( 'type' => 'error', 'message' => implode("|",$errors))));
	}
}
```

## Disclaimer

Use at your own risk! Worked great for me and saved a ton of time and timeouts!
