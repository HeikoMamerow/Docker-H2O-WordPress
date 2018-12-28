<?php 
/*
Plugin Name: LH http2 Server Push
Description: HTTP/2 Server Push Optimization for JavaScript and CSS resources enqueued in the page.
Version:     1.02
Author:      Peter Shaw
Author URI:  https://shawfactor.com
*/

class LH_Http2_server_push_plugin {
  

 
private function IsResourceLocal($url){
    if( empty( $url ) ){ return false; }
    $urlParsed = parse_url( $url );
  
  
  if (isset($urlParsed['host'])){ $host = $urlParsed['host']; }

    if(!isset($host) or empty($host) ){ 
    /* maybe we have a relative link like: /wp-content/uploads/image.jpg */
    /* add absolute path to begin and check if file exists */
    $doc_root = $_SERVER['DOCUMENT_ROOT'];
    $maybefile = $doc_root.$url;
    /* Check if file exists */
    $fileexists = file_exists ( $maybefile );
    if( $fileexists ){
        /* maybe you want to convert to full url? */
        return true;        
	} else {
	  
	 return false;   
	  
	}
	} else {
    /* strip www. if exists */
    $host = str_replace('www.','',$host);
    $thishost = $_SERVER['HTTP_HOST'];
    /* strip www. if exists */
    $thishost = str_replace('www.','',$thishost);
    if( $host == $thishost ){
        return true;
	} else {
    return false;
	  
	}
	  
	}
}
  
  
  
/**
* Convert an URL to a relative path
 *
 * @param string $src URL
 *
 * @return string mixed relative path
*/

private function url_to_relative_path($src) {
  
  if ($this->IsResourceLocal($src)){
		    return '//' === substr($src, 0, 2) ? preg_replace('/^\/\/([^\/]*)\//', '/', $src) : preg_replace('/^http(s)?:\/\/[^\/]*/', '', $src);
	
  } else {
	
	return false;
	
  }
		}


private function url_to_relative_path_simple($src) {
  

return '//' === substr($src, 0, 2) ? preg_replace('/^\/\/([^\/]*)\//', '/', $src) : preg_replace('/^http(s)?:\/\/[^\/]*/', '', $src);
	
  
		}

/**
* Create and array of scripts in head
 *
 *
 * @return array of urls
*/

private function return_header_scripts() {

global $wp_scripts;
  

global $wp_version;
  
$scripts = array();

$wp_scripts->all_deps($wp_scripts->queue);


foreach( $wp_scripts->to_do as $handle) {

if ($this->url_to_relative_path($wp_scripts->registered[$handle]->src) and !empty($this->url_to_relative_path($wp_scripts->registered[$handle]->src)) and ($wp_scripts->groups[$handle] == 0)){
  
  if (isset($wp_scripts->registered[$handle]->ver) and !empty($wp_scripts->registered[$handle]->ver)){
	
$src = add_query_arg( 'ver', $wp_scripts->registered[$handle]->ver, $wp_scripts->registered[$handle]->src );
	
  } else {
	
	
$src = add_query_arg( 'ver', $wp_version, $wp_scripts->registered[$handle]->src );
  
  }
  
  

$scripts[] = $src;

}

}

return $scripts;

}

/**
* Create and array of styles in head
 *
 *
 * @return array of urls
*/

private function return_header_styles() {


global $wp_styles;
  
global $wp_version;

$styles = array();

$wp_styles->all_deps($wp_styles->queue);

foreach( $wp_styles->to_do as $handle) {

if (!empty($this->url_to_relative_path($wp_styles->registered[$handle]->src))){
  
  if (isset($wp_styles->registered[$handle]->ver) and !empty($wp_styles->registered[$handle]->ver)){
	
$src = add_query_arg( 'ver', $wp_styles->registered[$handle]->ver, $wp_styles->registered[$handle]->src );
	
  } else {
	
	
$src = add_query_arg( 'ver', $wp_version, $wp_styles->registered[$handle]->src );
  
  }
  
  
  

$styles[] = $src;

}

}

return $styles;


}

/**
* Do the work of sending the headers
 *
 * @params string $src URL, and $as type of file
 *
 * @return string mixed relative path
*/

private function prepare_and_send_header($src, $as){
  
$src = $this->url_to_relative_path_simple($src);
  
if (strlen(trim($src)) > 1){


$set_header = sprintf('Link: <%s>; rel=preload; as=%s', esc_url($src), sanitize_html_class( $as ));

header( $set_header, false );
	
}

}




public function add_header(){
  
  if (!is_admin()){

do_action('wp_enqueue_scripts');



$scripts = $this->return_header_scripts();
  
foreach( $scripts as $script) {

$send[$script] = 'script';

}

$styles = $this->return_header_styles();

foreach( $styles as $style) {

$send[$style] = 'style';

}
  
$send = apply_filters('lh_htt2_server_push_urls', $send);
  

foreach ($send as $src => $as){
	
	
$this->prepare_and_send_header($src, $as);
	
}
	
  }
  
}


public function __construct() {

add_action( 'send_headers', array($this, 'add_header' ),100);


}


}

$lh_http2_server_push_instance = new LH_Http2_server_push_plugin();