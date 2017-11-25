# Bulk Save Tool for Wordpress

This tool is for developers and is useful for running tasks that happen when a post is updated. In and of itself it does nothing but update each post with no additional data. However if using a plugin like Import External Images is installed. It's actions will be executed on each post. 

When using this tool, you are essentially clicking update on every post.

# Using this Tool

Access the WP Dashboard, and under tools you will see Bulk Save. 

# Why and How I use it.

This plugin was bulk to specifically bulk run [Auto Upload Images](https://wordpress.org/plugins/auto-upload-images/) on 1800+ posts. Auto Upload Images only works when you save the post, so if you want to use it on every post you have to edit them all which results in timesouts or you have to save each one individually(Waste of life). So that why I wrote this script to save every post via ajax one after another. Just click process and let it work. I'm sure it could be faster but it gets the job done.

## Disclaimer

Use at your own risk! Worked great for me and saved a ton of time and timeouts!
