<?php
/**
 * @package Crisp
 * @version 0.6
Plugin Name: Crisp
Plugin URI: http://wordpress.org/plugins/crisp/
Description: Crisp is a Livechat plugin
Author: Crisp Communications
Version: 0.6
Author URI: https://crisp.im
*/


add_action('admin_menu', 'crisp_create_menu');

function crisp_create_menu() {
  add_menu_page('Crisp Settings', 'Crisp Settings', 'administrator', __FILE__, 'crisp_plugin_settings_page' , 'https://crisp.im/favicon.png');
  add_action( 'admin_init', 'register_crisp_plugin_settings' );
}


function register_crisp_plugin_settings() {
  register_setting( 'crisp-plugin-settings-group', 'website_id' );
}

function crisp_plugin_settings_page() {
  if (isset($_GET["crisp_website_id"]) && !empty($_GET["crisp_website_id"])) {
    update_option("website_id", $_GET["crisp_website_id"]);
  }

  $website_id = get_option('website_id');
  $is_crisp_working = isset($website_id) && !empty($website_id);
  $http_callback = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $add_to_crisp_link = "https://app.crisp.im/initiate/plugin/aca0046c-356c-428f-8eeb-063014c6a278?payload=$http_callback";
?>

<link rel="stylesheet" href="<?php echo plugins_url("assets/style.css", __FILE__ );?>">
  <?php
  if ($is_crisp_working) {
  ?>
  <div class="wrap crisp-wrap">
    <div class="crisp-modal">
      <span class="crisp-span">Crisp is working. Click on retry to reconfigure</span>
      <img class="crisp-check" src="<?php echo plugins_url("assets/check.png", __FILE__ );?>">
      <a class="crisp-retry" href="<?php echo $add_to_crisp_link; ?>">Retry</a>
    </div>
  </div>

  <?php
  } else {
  ?>
  <div class="wrap crisp-wrap">
    <div class="crisp-modal">
      <span class="crisp-span">To get started, please click on "link with Crisp"</span>
      <a href="<?php echo $add_to_crisp_link; ?>"><img class="crisp-sign" src="<?php echo plugins_url("assets/link-with-crisp.png", __FILE__ );?>" /></a>
    </div>
  </div>
  <?php
  }
}

add_action('wp_head', 'crisp_hook_head');

function crisp_hook_head() {

  $website_id = get_option('website_id');
  $output="<script type='text/javascript'>
    CRISP_WEBSITE_ID = '$website_id';
    (function(){
      d=document;s=d.createElement('script');
      s.src='https://client.crisp.im/l.js';
      s.async=1;d.getElementsByTagName('head')[0].appendChild(s);
    })();
  </script>";

  if (isset($website_id) && !empty($website_id)) {
    echo $output;
  }

  if ( is_user_logged_in() ) {
    $current_user = wp_get_current_user();
    $email = $current_user->user_email;

  	$output='<script type="text/javascript">
  	if (jQuery) {
  		jQuery(function($){
  		  window.CRISP_READY_TRIGGER = function() {
  		    $crisp.push(["set", "user:email", "' . $email . '"]);
  		  };
  		});
  	}
  	</script>';

  	echo $output;
  }

}
?>
