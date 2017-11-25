# Bulk Save Tool for Wordpress

This tool is for developers and is useful for running tasks that happen when a post is updated. In and of itself it does nothing but run wp_update_post on each existing post with no additional data. Nothing fancy. But why?

This allows for the save_post action to execute. If using a plugin like [Auto Upload Images](https://wordpress.org/plugins/auto-upload-images/), then it's import image actions will be executed for each post without having to go and save each one individually! YAY! 

When using this tool, you are essentially clicking update on every post.

## Using this Tool

Backup your database! 

Access the WP Dashboard, and under tools you will see Bulk Save. Select the Post Types you want to update. 

## How I use it

This plugin was built to specifically bulk run [Auto Upload Images](https://wordpress.org/plugins/auto-upload-images/) on 1800+ posts. Auto Upload Images only works when you save the post, so if you want to use it on every post you either have to bulk edit groups of them(which results in timesouts) or you have to save each one individually(waste of life). So that's why I wrote this script. I'm sure it could be faster but it gets the job done.

## Disclaimer

Use at your own risk! Worked great for me and saved a ton of time and timeouts!
