<?php
/*
Plugin Name: AskApache Search Engine Verify
Plugin URI: http://www.askapache.com/seo/wp-plugin-search-engine-verify.html
Description: Adds the verification meta tags to home page provided by Google and Yahoo <a href="options-general.php?page=search-engine-verify.php">Options configuration panel</a>
Version: 3.5
Author: AskApache
Author URI: http://www.askapache.com
*/


/*
== Installation ==

1. Upload and unzip and activate the plugin: /wp-content/plugins/search-engine-verify/
2. Go to your Options Panel and open the "AS Search Engine Verify" submenu. /wp-admin/options-general.php?page=search-engine-verify.php
3. Enter in the meta verification tags and hit the "Update Values" Button.
*/



/*
/--------------------------------------------------------------------\
|                                                                    |
| License: GPL                                                       |
|                                                                    |
| AskApache Search Engine Verify - Adds HTTP Basic Authentication |
| Copyright (C) 2008, AskApache, www.askapache.com                   |
| All rights reserved.                                               |
|                                                                    |
| This program is free software; you can redistribute it and/or      |
| modify it under the terms of the GNU General Public License        |
| as published by the Free Software Foundation; either version 2     |
| of the License, or (at your option) any later version.             |
|                                                                    |
| This program is distributed in the hope that it will be useful,    |
| but WITHOUT ANY WARRANTY; without even the implied warranty of     |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the      |
| GNU General Public License for more details.                       |
|                                                                    |
| You should have received a copy of the GNU General Public License  |
| along with this program; if not, write to the                      |
| Free Software Foundation, Inc.                                     |
| 51 Franklin Street, Fifth Floor                                    |
| Boston, MA  02110-1301, USA                                        |   
|                                                                    |
\--------------------------------------------------------------------/
*/
?>
<?php
function aa_sev_options_setup() {
	$aa_SEV=get_plugin_data(__FILE__);
    add_options_page($aa_SEV['Name'], 'AA Search Engine Verify', 7, basename(__FILE__), 'aa_sev_main_page');
}
add_action('admin_menu', 'aa_sev_options_setup');





//---------------------------
function aa_sev_main_page() {
    if ( function_exists('current_user_can') && !current_user_can( 7 ) )  wp_die( __('You do not have sufficient permissions to access this page.') );
	

	$mymess=' ';
	$myerr=' ';
	$sevgoogle = stripslashes(base64_decode(get_option('aa_sev_google')));
	$sevyahoo = stripslashes(base64_decode(get_option('aa_sev_yahoo')));


	if($_SERVER['REQUEST_METHOD']==='POST'){
        check_admin_referer('askapache-search-engine-verify-update_modify');
		

		if(isset($_POST['sevgoogle']) && $_POST['sevgoogle']!='')	{
			update_option('aa_sev_google',base64_encode($_POST['sevgoogle']));
			$sevgoogle = stripslashes(base64_decode(get_option('aa_sev_google')));
		}
		
		if(isset($_POST['sevyahoo']))	{
			update_option('aa_sev_yahoo',base64_encode($_POST['sevyahoo']));
			$sevyahoo = stripslashes(base64_decode(get_option('aa_sev_yahoo')));
		}
		

		if(isset($_POST['sevgoogleon'])){
			update_option('sevgoogleon','1');
			aa_sev_is_wp_cache();			
			$sev_ok=aa_sev_fetch_meta($sevgoogle);
			if(is_array($sev_ok))$myerr.='<p><strong>Did not find the Google Authorization key!</strong></p>';
			else $mymess.='<p><strong>Google Authorization key found!</strong></p>';
		}
		else update_option('sevgoogleon','0');
		
		if(isset($_POST['sevyahooon'])){
			update_option('sevyahooon','1');
			aa_sev_is_wp_cache();			
			$yah_ok=aa_sev_fetch_meta($sevyahoo);
			if(is_array($yah_ok))$myerr.='<p><strong>Did not find the Yahoo Authorization key.</strong></p>';
			else $mymess.='<p><strong>Yahoo Authorization key found!</strong></p>';
		}
		else update_option('sevyahooon','0');
		


		if(strlen($mymess)>6)$mymess='<br /><div id="message" class="updated fade">'.$mymess.'</div>';
		if(strlen($myerr)>6)$myerr='<br /><div id="message" class="error fade"'.$myerr.'</div>';
	} 


?>
	
<div class="wrap">
<h2><?php $aa_SEV=get_plugin_data(__FILE__); echo $aa_SEV['Name']; ?></h2>
<?php echo $mymess.$myerr; ?>
<form method="post" action="<?php echo attribute_escape($_SERVER["REQUEST_URI"]); ?>">
<?php wp_nonce_field('askapache-search-engine-verify-update_modify'); ?>
<p class="desc"><?php _e('It is strongly encouraged to use both Yahoo and Google.'); ?></p>
<table class="form-table">
<tr valign="top">
<th scope="row"><?php _e('Google Webmaster Tools') ?></th>
<td>
<input name="sevgoogle" type="text" id="sevgoogle" value="<?php echo htmlentities($sevgoogle); ?>" size="100" /><br />
<label for="sevgoogleon">
<input type="checkbox" name="sevgoogleon" id="sevgoogleon" value="1" <?php if(get_option('sevgoogleon')=='1')echo 'checked="checked" ';?>/>
<?php _e('Enabled') ?></label>
</td>
</tr>
<tr valign="top">
<th scope="row"><?php _e('Yahoo Site Explorer') ?></th>
<td>
<input name="sevyahoo" type="text" id="sevyahoo" value="<?php echo htmlentities($sevyahoo); ?>" size="100" /><br />
<label for="sevyahooon">
<input type="checkbox" name="sevyahooon" id="sevyahooon" value="1" <?php if(get_option('sevyahooon')=='1')echo 'checked="checked" ';?>/>
<?php _e('Enabled') ?></label>
</td>
</tr>
</table>
<p class="submit"><input name="aasubmitconfiguration" id="aasubmitconfiguration" value="<?php _e('Save Settings &raquo;'); ?>" type="submit" class="button valinp" /></p>
</form>
</div>
<hr style="visibility:hidden;clear:both;" />
<hr style="visibility:hidden;clear:both;" /></form></div>

    <div class="wrap">
		<p style="text-align:center;">&laquo;&laquo; <a href="https://siteexplorer.search.yahoo.com/mysites">Get Yahoo Code</a> - <a href="https://www.google.com/webmasters/tools/siteoverview">Get Google Code</a> &raquo;&raquo;</p>
		<hr style="visibility:hidden;" />
		<p style="text-align:center;"><a href="http://www.askapache.com/seo/404-google-wordpress-plugin.html">Best 404 Error Page with Google SEO - WordPress Plugin for 404.php</a></p>
    </div>
<?php
}


// aa_sev_fetch_meta
function aa_sev_fetch_meta($key){
	$ref=parse_url(get_option('siteurl'));
	$timeout=10;
	$path = ($r=parse_url(get_option('home')) && isset($r['path'])) ? $r['path'] : '/';
	$host =	($_SERVER['HTTP_HOST']==$ref['host']) ? $_SERVER['HTTP_HOST'] : $ref['host'];
	$port =	(isset($_SERVER['SERVER_PORT'])) ? $_SERVER['SERVER_PORT'] : $ref['port'];
	$ipad =	(isset($_SERVER["SERVER_ADDR"])) ? $_SERVER["SERVER_ADDR"] : gethostbyname($_SERVER['HTTP_HOST']);
	$sche =	((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS'])=='on')||$_SERVER['SERVER_PORT']=='443') ? 'ssl://' : '';
	$resp='';
	$out=array();
	
    if(!$fp = fsockopen($sche.$ipad, $port, $errno, $errstr, $timeout)) return false;
    if(!fputs($fp, "GET $path HTTP/1.1\r\nHost: $host\r\nUser-Agent: AskApache Search Engine Verify\r\nReferer: $ref\r\nAccept: */*\r\nConnection: Close\r\n\r\n")) return false;
	while (!feof($fp) && (strpos($resp,'</head')===false) &&  (strpos($resp,'</HEAD')===false) && (strpos($resp,$key)===false) ){
		$out[]=($resp=fread($fp, 1028)) ? htmlentities($resp) : 'ERROR';
		if( (strpos($resp,$key)!==false) ) $out=$key;
	} if(fclose($fp))return $out;
}

function aa_sev_is_wp_cache(){
	global $cache_path, $file_prefix;

	if( !@include(ABSPATH . 'wp-content/wp-cache-config.php') )	$aa_cache=false;
	else {
		if(!$cache_enabled)$aa_cache=false;
		else {
			if(wp_cache_phase2_clean_cache($file_prefix))$ok=NULL;//echo '<div id="message" class="updated fade"><p>Deleted site cache.</p></div>';
			//else echo "<p>Error Deleting: files from: $cache_path</p>";
		}
	}
	return true;
}


function askapache_sev(){
	if(is_home() && !is_paged()){
		if(get_option('sevgoogleon')=='1'){
			$sevgoogle = stripslashes(base64_decode(get_option('aa_sev_google')));
			echo $sevgoogle."\n";
		}
		if(get_option('sevyahooon')=='1'){
			$sevyahoo = stripslashes(base64_decode(get_option('aa_sev_yahoo')));
			echo '	'.$sevyahoo."\n";
		}
	}
}
add_action('wp_head', 'askapache_sev');



function askapache_sev_activate(){
	delete_option('aa_sev_version');

	if(strlen(get_option('aa_sev_yahoo'))>4)update_option('sevyahooon','1');
	else update_option('sevyahooon','0');
	
	if(strlen(get_option('aa_sev_google'))>4)update_option('sevgoogleon','1');
	else update_option('sevgoogleon','0');
}


register_activation_hook(__FILE__, 'askapache_sev_activate');
?>