<?php 
/*
Plugin Name: WP Image Picker
Plugin URI: https://github.com/bogvsdev/wp-image-picker
Description: Useful plugin for displaying image associated with post whether it's thumbnail, uploaded or just pasted image.
Version: 1.0
Author: bogvsdev
Author URI: http://bdev.it
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

add_action('admin_menu', 'wp_image_picker_menu');

function wp_image_picker_menu(){
	add_options_page('WP Image Picker settings', 'WP Image Picker', 'administrator', basename(__FILE__), 'wp_image_picker_options_page');
}

function wp_image_picker_options_page(){
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
	<div class="wrap"><h2>WP Image Picker settings</h2>
		<?php if($updated) { ?>
		<div id="message" class="updated"><p>Changes are successfully saved.</p></div>
		<?php } ?>
		<form name='form' method='post' action=''>
			<table width='100%' border='0' cellspacing='0' cellpadding='0'>
				<tr>
					<th width='30%' style="text-align: left;"><strong>Default image (if no image in post): </strong></th>
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
		width: 350px;
    	height: auto;
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

if (!function_exists('the_picked_image')) {
	function the_picked_image($classes='') {
		$classes = (string)$classes;
		if (get_the_post_thumbnail() != '') {
			the_post_thumbnail('large', array('class'=>$classes)); 
		}else {
			$data = pick_image(get_the_ID(), true);
			$img = $data[0];
			$alt = $data[1];
			echo '<img class="'.$classes.'" src="'.$img.'" alt="'.$alt.'">';
		}
	}
}

if (!function_exists('pick_image')) {
	function pick_image($post_id, $inside=false){
		$img = null;
		
		if (get_the_post_thumbnail() != '') {
			$img = wp_get_attachment_url(get_post_thumbnail_id($post_id));
		} else {
			$attachmentss = get_posts(array(
	            'post_type' => 'attachment',
				'post_mime_type' => 'image',
				'numberposts' => 1,
				'order' => 'ASC',
				'orderby' => 'ID',
				'post_status' => null,
				'post_parent' => (int)$post_id
	        ));

			if (!empty($attachmentss)) {
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

		$img = ($img == null) ? '' : $img ;

		if($inside)
			return array($img, $alt);
		return $img;
	}
}
?>