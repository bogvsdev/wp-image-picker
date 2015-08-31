# WP Post Image

Main goal of plugin to print image from post in loop of template. Not all wordpress themes print images from posts as a thumbnail in loop. Almost all the time themes support only post thumbnails, but not always in post editor sets this thumbnail. More often we just publish uploaded or external image. WP Post Image plugin solves this problem.

Plugin by default searching for any kind of image in post (pasted image, uploaded image or thumbnail), but if nothing found, then plugin prints image by default, which you can upload or paste link to it by yourself.
Supported formats of images:jpg, jpeg, bmp, png.


In order to use plugin in your template pick appropriate function.


1) `the_post_image($classes)`
Use this function in loop. Function prints img tag with classes which you can add as an optional parameter of function, example:

`the_post_image('big-image home');`



2) `get_post_image($post_id, $alt)`
Function returns url to image. $post_id is required parameter, if optional parameter $alt is true, then function returns array where first item is image url, second item is alt text for image. By default $alt set in false, so function returns just image url, example:

```
$data = get_post_image(get_the_ID(), true);
$image = $data[0];
$alt = $data[1];		
```
or

`$image = get_post_image(get_the_ID());`



If you have got any troubles with plugin, please, write me in twitter [@bogvsdev](http://twitter.com/bogvsdev).
			
