<?php
/*
Plugin Name: Anyfeed Slideshow
Plugin URI: http://tixen.net/projects/anyfeed-slideshow/
Description: A quick and easy jQuery based slideshow widget, fed from absolutely any XML/RSS/ATOM feed that contains images!
Version: 1.0.12
Author: Soleil Golden
Author URI: http://tixen.net/
License: GPL 2.0

	Copyright 2010,  Soleil Golden  (email : soleil@tixen.net)
	
	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Produce the locally stored XML when requested...

##########################################################################################
#                                                                                  DEBUG #
##########################################################################################
/*
header ("Content-Type: text/plain");
function customError($errno, $errstr, $errfile, $errline) {
//	if($errno == 2) 		{return false;} // Warning
//	if($errno == 8)		{return false;} // Notice
//	if($errno == 256)	{return false;} // User Error
//	if($errno == 1024)	{return false;} // User Notice
	if($errno == 2048)	{return false;} // Strict
	if($errno == 4096)	{return false;} // Recoverable Error
	if($errno == 8192)	{return false;} // All
	echo "ERROR: [".$errno."] ".$errstr."\r\n\tIn ".$errfile."[".$errline."]\r\n\r\n";
}
set_error_handler("customError");
require_once(dirname(__FILE__).'/../../../wp-load.php');
*/

##########################################################################################
#                                                                    IMPORTANT FUNCTIONS #
##########################################################################################
if(!function_exists('get_option')) { require_once(dirname(__FILE__).'/../../../wp-load.php'); }
/////////////////////////////////////////////////////////////////////////// anyfeed_pathTo
if(!function_exists('anyfeed_pathTo')) {function anyfeed_pathTo($url = true) {
	if($url) {	return(preg_replace('(\/\w?$)s', '', str_replace("\\", "/", WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)))));}
	return(preg_replace('(\/\w?$)s', '', str_replace("\\", "/", WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)))));
	}}


////////////////////////////////////////////////////////////////////// anyfeed_passOptions
if(!function_exists('anyfeed_passOptions')) { function anyfeed_passOptions() {
	if(get_option('anyfeed_show_titlebar')) {$t['titlebar'] =  'true';} 
		else {$t['titlebar'] = 'false';}
	if(get_option('anyfeed_perm_titlebar')){$t['perm_title_bar'] = 'true';} 
		else {$t['perm_title_bar'] = 'false';}	
	if(get_option('anyfeed_show_navigation')) {$t['navigation'] = 'true';} 
		else {$t['navigation'] = 'false';}
	$return = array(
		'pause_rate' =>	 	get_option('anyfeed_pause_rate'),
		'fade_rate' =>	 	get_option('anyfeed_fade_rate'),
		'media_type' =>	 	get_option('anyfeed_media_type'),
		'loading_text' =>	addslashes(get_option('anyfeed_loading_text')),
		'title_bar' =>		$t['titlebar'],
		'perm_title_bar' =>	$t['perm_title_bar'],
		'navigation' =>		$t['navigation'],
		'c_width' =>	 	get_option('anyfeed_width'),
		'c_height' =>	 	get_option('anyfeed_bgcolor'),
		'c_height' =>	 	get_option('anyfeed_height'),
		'bgcolor' =>	 	get_option('anyfeed_bgcolor'),
		'target' =>			get_option('anyfeed_target'),
		'xml_url' =>	 	anyfeed_pathTo().'/anyfeed_slideshow.php?xml',
		'maximages' =>	 	get_option('anyfeed_maximages')
	);
	return(urlencode(serialize($return)));
}}

///////////////////////////////////////////////////////////////////////////////// errorXML
if(!function_exists('errorXML')) {
function errorXML($title, $link='http://wordpress.org/tags/anyfeed-slideshow') {
	return(
		'<?xml version="1.0" encoding="utf-8"?>'."\r\n".
		'<rss version="2.0">'."\r\n".
		'	<channel>'."\r\n".
		'		<title>50 Random APR Listings, 2 Million +, Residential</title>'."\r\n".
		'		<link>http://www.apr.com/</link>'."\r\n".
		'		<generator>APR Datafeed Framework</generator>'."\r\n".
		'		<description />'."\r\n".
		'		<item>'."\r\n".
		'			<title>'.$title.'</title>'."\r\n".
		'			<link>'.$link.'</link>'."\r\n".
		'			<description type="text/plain" />'."\r\n".
		'			<enclosure url="http://tixen.net/files/anyfeed-slideshow/error.gif" type="image/gif" />'."\r\n".
		'		</item>'."\r\n".
		'	</channel>'."\r\n".
		'</rss>'."\r\n"	
	);
}}

////////////////////////////////////////////////////////////////////////// anyfeed_getFile
if(!function_exists('anyfeed_getFile')) {
function anyfeed_getFile($display = false) {
	$urla = explode("://", get_option('anyfeed_feed_url'));
	if(isset($urla[1])) { $url = $urla[1]; }
	$url = 'http://'.$url;
	if(function_exists('curl_init')) {
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $url);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		ob_start();
		curl_exec ($ch);
		curl_close ($ch);
		$content = ob_get_contents();
		ob_end_clean();
	} elseif(ini_get('allow_url_fopen')) { // Check fopen
		$handle = fopen($url, 'r');
		if(!$handle){ die("Failed to open url".get_option('anyfeed_feed_url')); }
		$content = stream_get_contents($handle);
		fclose($handle);
	}  else {
		return(errorxml('No available file transfer tools'));
	}

		// This alters scripts for JS parsing compatability with all browsers.
		$content = preg_replace('/<rss(.*?)xmlns:media(.*?)(<description\/>|<description>.*?<\/description>)/s', '<rss${1}xmlns:media${2}<description>true</description>', $content);
		$content = preg_replace('/(<media:title\/>|<media:title>?.*?<\/media:title>)/s', '', $content);
		$content = preg_replace('/&(amp;)?/', '&amp;', $content);
		
		if(preg_match('/flickr.com/', get_option('anyfeed_feed_url'))) { /* Resize photos from Flickr */ return preg_replace('/(flickr.com\/[\d]*?\/[\d]*?_.*?_)s\.((jpg|gif|png)") height="75" width="75"/', '$1m.$2', $content); }
	elseif(preg_match('/picasaweb.google.com/', get_option('anyfeed_feed_url'))) { /* Resize photos from Google */ return preg_replace('/(url=\'http:\/\/.*?\.ggpht\.com\/.*?\/.*?\/.*?\/.*?\/s)[\d]*?(\/.*?\.(jpg|JPG|jpeg|JPEG|gif|GIF|png|PNG)\' height=\').*?(\' width=\').*?(\')/', "\${1}240\${2}240\${4}240\${5}", $content);}
		else { return $content; }
}}




##########################################################################################
#                                                                             XML PASSER #
##########################################################################################
if(isset($_GET['xml'])) {
	header('HTTP/1.1 200 OK');
//	header ("Content-Type: text/plain");
	header ("Content-Type: text/xml"); 
	print(anyfeed_getFile(true)); 
	die;
}




##########################################################################################
#                                                                   WP PLUGIN DEFINITION #
##########################################################################################
/////////////////////////////////////////////////////////////////////////// Head Additions
add_action('wp_head', 'anyfeed_head' );
function anyfeed_head(){print("\r\n".
	'<link type="text/css" rel="stylesheet" href="'.anyfeed_pathTo().'/style.css" />'."\r\n".
	'<script type="text/javascript" src="'.anyfeed_pathTo().'/anyfeed.php?options='.anyfeed_passOptions().'"></script>'."\r\n"
);}

//////////////////////////////////////////////////////////////////////// Widget Initiation
add_action( 'widgets_init', 'anyfeed_init' );
function anyfeed_init() { register_widget( 'anyfeed_slideshow' ); }

////////////////////////////////////////////////////////////////// Widget Class Definition
class anyfeed_slideshow extends WP_Widget {
		function anyfeed_slideshow() {
		/* Widget settings. */
		$widget_ops = array( 
			'classname' => 'anyfeed', 
			'description' => 'XML/RSS/ATOM slideshow widget engine.'); 
			
		/* Widget control settings. */
		$control_ops = array( 
			'width' => 245, 
			'height' => 200, 
			'id_base' => 'anyfeed');
			
		/* Create the widget. */
		$this->WP_Widget( 'anyfeed', 'Anyfeed Slideshow', $widget_ops, $control_ops );
		wp_enqueue_script('jquery');
	}
//////////////////////////////////////////////////////////////////////// Widget Definition
	function widget( $args, $instance ) {
		extract( $args );
		
		// Title
		echo $before_widget . $before_title . $instance['title'] . $after_title;

		// Drawing Widget
		echo '<div id="anyfeed_slideshow_main"></div>';
		echo $after_widget;
	}
	
///////////////////////////////////////////////////////////////// Settings Update Function		
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );

		$instance['width'] = strip_tags( $new_instance['width'] ); 
			update_option('anyfeed_width', $instance['width']);

		$instance['height'] = strip_tags( $new_instance['height'] );
			update_option('anyfeed_height', $instance['height']);

		$instance['feed_url'] = $new_instance['feed_url']; 
			update_option('anyfeed_feed_url', $instance['feed_url']);

		if($new_instance['show_titlebar'] == 'on') { $new_instance['show_titlebar'] = true; } 
			else { $new_instance['show_titlebar'] = false; }

		$instance['show_titlebar'] = $new_instance['show_titlebar']; 
			update_option('anyfeed_show_titlebar', $instance['show_titlebar']);

		if($new_instance['perm_titlebar'] == 'on') { $new_instance['perm_titlebar'] = true; } 
			else { $new_instance['perm_titlebar'] = false; }

		$instance['perm_titlebar'] = $new_instance['perm_titlebar']; 
			update_option('anyfeed_perm_titlebar', $instance['perm_titlebar']);

		if($new_instance['show_navigation'] == 'on') { $new_instance['show_navigation'] = true; } 
			else { $new_instance['show_navigation'] = false; }

		$instance['show_navigation'] = $new_instance['show_navigation']; 
			update_option('anyfeed_show_navigation', $instance['show_navigation']);

		$instance['pause_rate'] = $new_instance['pause_rate']; 
			update_option('anyfeed_pause_rate', $instance['pause_rate']);

		$instance['fade_rate'] = $new_instance['fade_rate']; 
			update_option('anyfeed_fade_rate', $instance['fade_rate']);

		$instance['loading_text'] = $new_instance['loading_text']; 
			update_option('anyfeed_loading_text', $instance['loading_text']);

		$instance['media_type'] = $new_instance['media_type']; 
			update_option('anyfeed_media_type', $instance['media_type']);

		$instance['bgcolor'] = $new_instance['bgcolor']; 
			update_option('anyfeed_bgcolor', $instance['bgcolor']);

		$instance['maximages'] = $new_instance['maximages']; 
			update_option('anyfeed_maximages', $instance['maximages']);

		$instance['target'] = $new_instance['target']; 
			update_option('anyfeed_target', $instance['target']);

		if($new_instance['cache'] == 'on') { $new_instance['cache'] = true; } 
			else { $new_instance['cache'] = false; }

		$instance['cache'] = $new_instance['cache']; 
			update_option('anyfeed_cache', $instance['cache']);

		return $instance;
	}


//////////////////////////////////////////////////////////////////// Widget Options Window
	function form( $instance ) {
		print(
			'<a href="http://tixen.net/projects/anyfeed-slideshow" target="_blank"><img src="'.anyfeed_pathTo().'/logo.jpg" alt="" style="float: right; border: none;"></a>'."\r\n".
			'<p style="line-height: 105%; text-align: center;">
				<small>
					(Hover your mouse over the label text for a short description 
					of the corresponding field)
				</small>
			</p>'."\r\n");
		/* Default Widget Settings. */
		$defaults = array( 
			'title' 	=> 'Anyfeed Slideshow', 
			'width' 	=> '100%',
			'height'	=> '200px',
			'feed_url'	=> 'http://api.flickr.com/services/feeds/photos_public.gne?format=rss_200',
			'show_titlebar' => true,
			'perm_titlebar' => false,
			'show_navigation' => true,
			'pause_rate' => 5000,
			'fade_rate'	=> 1000,
			'maximages' => 50,
			'loading_text' => 'Loading...',
			'media_type' => 'thumbnail',
			'bgcolor' => 'transparent',
			'target' => '_blank',
			'cache' => false
			
		);
		$instance = wp_parse_args( $instance, $defaults ); 
		 ?>
		 

		 <!-- BASIC OPTIONS -->
		 <div id="afss_basic_button" onclick="jQuery('.afss_options').slideUp('fast',function(){ jQuery('div#afss_basic').slideDown(function(){jQuery(this).stop();});});" class="button" style="text-align: center; margin: 5px auto 0; clear: right;">Basic Options</div>
		 <div id="afss_basic" class="afss_options">
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>" title="The text that will appear above the slideshow in the sidebar.">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" style="width:90%;" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'feed_url' ); ?>" title="The URL to the feed to get photos from.">Feed URL:</label>
			<input id="<?php echo $this->get_field_id( 'feed_url' ); ?>" name="<?php echo $this->get_field_name( 'feed_url' ); ?>" value="<?php echo $instance['feed_url']; ?>" style="width:90%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'target' ); ?>" title="Where the picture URL is opened.">Feed links open in:</label>
			<select id="<?php echo $this->get_field_id( 'target' ); ?>" name="<?php echo $this->get_field_name( 'target' ); ?>" class="widefat" style="width:90%;">
				<option value="_blank" <?php if ( '_blank' == $instance['target'] ) echo 'selected="selected"'; ?>>New Window</option>
				<option value="_self" <?php if ( '_self' == $instance['target'] ) echo 'selected="selected"'; ?>>Same Window</option>
			</select>
		</p>
		
		</div>
		
		<!-- DISPLAY OPTIONS -->
		<div id="afss_display_button" onclick="jQuery('.afss_options').slideUp('fast',function(){ jQuery('div#afss_display').slideDown(function(){jQuery(this).stop();});});" class="button" style="text-align: center; margin: 5px auto 0;">Display Options</div>
		<div id="afss_display" class="afss_options" style="display: none;">
		<table width="95%"><tr><td width="50%">
			<p>
				<label for="<?php echo $this->get_field_name( 'height' ); ?>" title="The height of the slideshow window.">Height:</label>
				<input id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo $instance['height']; ?>" style="width:90%;" />
			</p>
		</td><td width="5%"></td><td>
			<p>
				<label for="<?php echo $this->get_field_id( 'width' ); ?>" title="The width of the slideshow window.">Width:</label>
				<input id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" style="width:90%;" />
			</p>
		</td></tr></table>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'loading_text' ); ?>" title="The text that pulses while the images are being cached.">Loading Text:</label>
			<input id="<?php echo $this->get_field_id( 'loading_text' ); ?>" name="<?php echo $this->get_field_name( 'loading_text' ); ?>" value="<?php echo $instance['loading_text']; ?>" style="width:90%;" />
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_navigation'], true ); ?> id="<?php echo $this->get_field_id( 'show_navigation' ); ?>" name="<?php echo $this->get_field_name( 'show_navigation' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_navigation' ); ?>" title="Whether or not to show the back and forward arrows when mousing over the slideshow.">Show Navigation?</label>
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show_titlebar'], true ); ?> id="<?php echo $this->get_field_id( 'show_titlebar' ); ?>" name="<?php echo $this->get_field_name( 'show_titlebar' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'show_titlebar' ); ?>"title="Whether or not to show the title of the image when mousing over the slideshow.">Show Titlebar?</label>
		</p>
		
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['perm_titlebar'], true ); ?> id="<?php echo $this->get_field_id( 'perm_titlebar' ); ?>" name="<?php echo $this->get_field_name( 'perm_titlebar' ); ?>" />
			<label for="<?php echo $this->get_field_id( 'perm_titlebar' ); ?>"title="Whether or not to leave the Titlebar up whether or not the mouse enters the slideshow window.">Permanent Titlebar?</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'bgcolor' ); ?>" title="The background color of slideshow.">Background Color:</label>
			<input id="<?php echo $this->get_field_id( 'bgcolor' ); ?>" name="<?php echo $this->get_field_name( 'bgcolor' ); ?>" value="<?php echo $instance['bgcolor']; ?>" style="width:90%;" />
		</p>
		<p>
		</div>
		
		
		<!-- FEED OPTIONS -->
		<div id="afss_feed_button" onclick="jQuery('.afss_options').slideUp('fast',function(){ jQuery('div#afss_feed').slideDown(function(){jQuery(this).stop();});});" class="button" style="text-align: center; margin: 5px auto 0;">Feed Options</div>
		<div id="afss_feed" class="afss_options" style="display: none;">
		
		<p>
			<label for="<?php echo $this->get_field_id( 'media_type' ); ?>" title="If the feed URL is of a media type, select whether to display thumbnail (recommended) or content (the full sized original file).  For Flickr and Picasa feeds, leave this set to thumbnail.">Feed Media Type:</label>
			<select id="<?php echo $this->get_field_id( 'media_type' ); ?>" name="<?php echo $this->get_field_name( 'media_type' ); ?>" class="widefat" style="width:90%;">
				<option <?php if ( 'content' == $instance['media_type'] ) echo 'selected="selected"'; ?>>content</option>
				<option <?php if ( 'thumbnail' == $instance['media_type'] ) echo 'selected="selected"'; ?>>thumbnail</option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_name( 'maximages' ); ?>" title="The maximum number of images to pull from the feed.">Max Images:</label>
			<input id="<?php echo $this->get_field_id( 'maximages' ); ?>" name="<?php echo $this->get_field_name( 'maximages' ); ?>" value="<?php echo $instance['maximages']; ?>" style="width:90%;" />
		</p>
		</div>
		
		<!-- TIMING OPTIONS -->
		<div id="afss_timing_button" onclick="jQuery('.afss_options').slideUp('fast',function(){ jQuery('div#afss_timing').slideDown(function(){jQuery(this).stop();});});" class="button" style="text-align: center; margin: 5px auto 0;">Timing Options</div>
		<div id="afss_timing" class="afss_options" style="display: none;">
		<table width="95%"><tr><td width="50%">
			<p>
				<label for="<?php echo $this->get_field_id( 'pause_rate' ); ?>" title="The length of time the slideshow will pause on a photo.">Pause delay (milliseconds):</label>
				<input id="<?php echo $this->get_field_id( 'pause_rate' ); ?>" name="<?php echo $this->get_field_name( 'pause_rate' ); ?>" value="<?php echo $instance['pause_rate']; ?>" style="width:90%;" />
			</p>
		</td><td width="5%"></td><td>	
			<p>
				<label for="<?php echo $this->get_field_id( 'fade_rate' ); ?>" title="The length of time of the fade effect.">Fade time (milliseconds):</label>
				<input id="<?php echo $this->get_field_id( 'fade_rate' ); ?>" name="<?php echo $this->get_field_name( 'fade_rate' ); ?>" value="<?php echo $instance['fade_rate']; ?>" style="width:100%;" />
			</p>
		</td></tr></table>
		</div>
		<?PHP
	} // End Form
} // End class

//////////////////////////////////////////////////////////// Add Deactivation Cleanup Hook
if ( function_exists('register_deactivation_hook') ) {
	register_deactivation_hook(__FILE__, 'anyfeed_clear_hook');
}

///////////////////////////////////////////////////////////////////////////// Cleanup Hook
function anyfeed_clear_hook() {
	$tempfile = get_option('anyfeed_tempfile');
	if(	!empty($tempfile) AND file_exists($tempfile) ) @unlink($tempfile);
	
	delete_option('anyfeed_width');
	delete_option('anyfeed_height');
	delete_option('anyfeed_target');
	delete_option('anyfeed_feed_url');
	delete_option('anyfeed_show_titlebar');
	delete_option('anyfeed_perm_titlebar');
	delete_option('anyfeed_show_navigation');
	delete_option('anyfeed_pause_rate');
	delete_option('anyfeed_fade_rate');
	delete_option('anyfeed_loading_text');
	delete_option('anyfeed_media_type');
	delete_option('anyfeed_bgcolor');
	delete_option('anyfeed_cache');
	delete_option('anyfeed_tempfile');
	delete_option('widget_anyfeed');
}
?>