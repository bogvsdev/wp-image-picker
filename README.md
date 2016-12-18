# WP Image Picker

You can download files from this page, or install form WordPress plugin search https://wordpress.org/plugins/wp-image-picker/

Main goal of plugin to display image from post in loop of template. Not all wordpress themes print images from posts as a thumbnail. Almost all themes support only post thumbnails, but not always editor sets post thumbnail. More often we just publish uploaded or external image. WP Image Picker plugin solves this problem.

Plugin by default searches for any kind of image in post (pasted image, uploaded image or thumbnail), but if nothing found, then plugin displays image by default, which you can upload or paste link to by yourself in plugin settings.
Supported formats of images: jpg, jpeg, bmp, png.

In order to use plugin in your template choose appropriate function.

1) `the_picked_image($classes)`
Use this function in loop. Function prints img tag with classes which you can add as an optional parameter of function, example:
``` php
the_picked_image('big-image home');
```

2) `pick_image($post_id, $alt)`
Function returns url to image. $post_id is required parameter, if optional parameter $alt is true, then function returns array where first item is image url, second item is alt text for image. By default $alt set in false, so function returns just image url, example:

``` php
$data = pick_image(get_the_ID(), true);
$image = $data[0];
$alt = $data[1];		
```
or
``` php
$image = pick_image(get_the_ID());
```

If you have got any troubles with plugin, please, write me in twitter [@bogvsdev](http://twitter.com/bogvsdev).