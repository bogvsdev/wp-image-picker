<?php 
/*
Plugin Name: WP Get Post Image
Plugin URI: http://github/url
Description: Plugin displays featured image or thumbnail from post in template. Prints img tag or returns url to image
Version: 1.0
Author: Bogdan Dever
Author URI: http://www.twitter.com/bogvsdev
*/
add_action('admin_menu', 'wp_get_post_image_menu');


function wp_get_post_image_menu(){
	add_options_page('WP Get Post Image settings', 'WP Get Post Image', 8, basename(__FILE__), 'wp_get_post_image_options_page');
}

function wp_get_post_image_options_page(){
	$updated = false;
	if ($_POST) {
		update_option('wp_pi_defsrc', $_POST['wp_pi_defsrc']);
		$updated = true;
	}
	$src = get_option('wp_pi_defsrc');
	$arr = explode('.', substr($src, strlen($src)-5, strlen($src)));
	$format = $arr[1];
	$formats = array('jpg', 'jpeg', 'bmp', 'png');

	if(!in_array($format, $formats) or (strlen(get_option('wp_pi_defsrc'))==0)) {
		update_option('wp_pi_defsrc', plugin_dir_url( __FILE__ ).'default-image.png');
	}

	$wp_pi_defsrc = get_option('wp_pi_defsrc');


	wp_enqueue_script('jquery');
	wp_enqueue_media();
	?>
	<div class="wrap"><h2>WP Get Post Image settings</h2>
		<?php if($updated) { ?>
		<div id="message" class="updated"><p>Changes successfully saved.</p></div>
		<?php } ?>
		<div class="info">
			<p>
				Plugin by default searching for any kind of image in post (pasted image, uploaded image or thumbnail), but if nothing found, then plugin prints image by default, which you can upload or paste link to it by yourself.
				Supported formats of images:jpg, jpeg, bmp, png.
			</p>
			<p>
				In order to use plugin in your template pick appropriate function.
			</p>
			<p>
				1) <code>the_post_image($classes)</code> <br>
				Use this function in loop. Function prints img tag with classes which you can add as an optional parameter of function, example:
				<p></p>
				<code>
					the_post_image('big-image home');
				</code>
			</p>
			<p>
				2) <code>get_post_image($post_id, $alt)</code> <br>
				Function returns url to image. $post_id is required parameter, if optional parameter $alt is true, then function returns array where first item is image url, second item is alt text for image. By default $alt set in false, so function returns just image url, example:
				<p></p>
				<code>
					$data = get_post_image(get_the_ID(), true);
					$image = $data[0];
					$alt = $data[1];		
				</code>
				<p>or</p>
				<code>
					$image = get_post_image(get_the_ID());	
				</code>
			</p>
			<p>
				If you have got any troubles with plugin, please, write me in twitter <a href="http://twitter.com/bogvsdev" target="_blank">@bogvsdev</a> .
			</p>
			
		</div>
		<form name='form' method='post' action=''>
			<table width='100%' border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<th width='30%'><strong>Default image (if image doesn't exist in post): </strong></th>
					<td width='70%'>
						<input type="text" name="wp_pi_defsrc" id="wp_pi_defsrc" class="regular-text" value='<?php echo $wp_pi_defsrc; ?>'>
    					<input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
					</td>
				</tr>
				<tr>
					<th width='30%'></th>
					<td width='70%'>
						<img src="<?php echo $wp_pi_defsrc; ?> " alt="test" width="352px" height="270px" id="imgrw">
					</td>
				</tr>				
			</table>
			<input name='Submit' type='submit' value='Save' class='button' style="margin:10px 0 20px 0;"> 
		</form>
	</div>
	<style>
	#imgrw {
		margin: 15px 0px;
		box-shadow: 0 0 40px rgba(0,0,0,.1);
	}
	</style>
	<script type="text/javascript">
	jQuery(document).ready(function($){
		function checkImg() {
			var val = $('#wp_pi_defsrc').val(), arr;
			arr = val.slice(val.length-5, val.length);
			arr = arr.split('.');
			if((arr[1]=='jpg')||(arr[1]=='jpeg')||(arr[1]=='png')||(arr[1]=='bmp')) {
				$('#imgrw').fadeIn('fast');
				$('#imgrw').attr('src', val);
			}else{
				$('#imgrw').fadeOut('fast');
			}
		}
		checkImg();
		$('#wp_pi_defsrc').on('focus', checkImg);
		$('#wp_pi_defsrc').on('blur', checkImg);

		$('#upload-btn').click(function(e) {
	        e.preventDefault();
	        var image = wp.media({ 
	            title: 'Upload Image',
	            multiple: false
	        }).open()
	        .on('select', function(e){
	            var uploaded_image = image.state().get('selection').first();
	            var wp_pi_defsrc = uploaded_image.toJSON().url;
	            $('#wp_pi_defsrc').val(wp_pi_defsrc);
	            checkImg();
	        });
	    });
	});
	</script>
	<?php
}

function the_post_image($classes='') {
	$classes = (string)$classes;
	if (get_the_post_thumbnail() != '') {
		$attrs = array('class'=>$classes);
		the_post_thumbnail('large', $attrs); 
	}else {
		$data = get_post_image(get_the_ID(), true);
		$img = $data[0];
		$alt = $data[1];
		echo '<img class="'.$classes.'" src="'.$img.'" alt="'.$alt.'">';
	}
}

function get_post_image($post_id, $inside=false){
	if (get_the_post_thumbnail() != '') {
		$img = wp_get_attachment_url(get_post_thumbnail_id($post_id));
	} else {
		 $p = array(
                   'post_type' => 'attachment',
		 		  'post_mime_type' => 'image',
		 		  'numberposts' => 1,
		 		  'order' => 'ASC',
		 		  'orderby' => 'ID',
		 		  'post_status' => null,
		 		  'post_parent' => $post_id
                 );
		 $attachmentss = get_posts($p);

		 if ($attachmentss) {
		 	$imgsrc = wp_get_attachment_image_src($attachmentss[0]->ID, 'full');
		 	$img = $imgsrc[0];
			$alt = $attachmentss[0]->post_name;
		 }

		 if($img==''){
		 	$post = get_posts('p='.$post_id);
			$match_count = preg_match_all("/<img[^']*?src=\"([^']*?)\"[^']*?>/", $post[0]->post_content, $match_array, PREG_PATTERN_ORDER);		
		 	$img = $match_array[1][0];
			$alt = $post[0]->post_date;
		 }

		 if ($img=='') {
			$img = get_option('wp_pi_defsrc');
			$alt = time();
		}
	 }

	if($inside)
		return array($img, $alt);
	return $img;
}
?>