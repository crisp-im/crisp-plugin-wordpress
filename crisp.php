<?php
/**
 * @package Crisp
 * @version 0.11
Plugin Name: Crisp
Plugin URI: http://wordpress.org/plugins/crisp/
Description: Crisp is a Livechat plugin
Author: Crisp IM
Version: 0.11
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
      <h2 class="crisp-title">Connected with Crisp.</h2>
      <p class="crisp-subtitle">You can now use Crisp from your homepage.</p>
      <a class="crisp-button crisp-neutral" href="https://app.crisp.im/settings/website/<?php echo $website_id ?>">Go to my Crisp settings</a>

      <a class="crisp-button crisp" href="<?php echo $add_to_crisp_link; ?>">Reconfigure</a>

      
    </div>

    <p class="crisp-notice">Loving Crisp <b style='color:red'>♥</b> ? Rate us on the <a target="_blank" href="https://wordpress.org/support/plugin/crisp/reviews/?filter=5">Wordpress Plugin Directory</a></p>
  </div>

  <?php
  } else {
  ?>
  <div class="wrap crisp-wrap">
    <div class="crisp-modal">
      <h2 class="crisp-title">Connect with Crisp.</h2>
      <p class="crisp-subtitle">This link will redirect you to Crisp and configure your Wordpress. Magic</p>
      <a class="crisp-button crisp" href="<?php echo $add_to_crisp_link; ?>">Connect with Crisp</a>
    </div>
  </div>
  <?php
  }
}

add_action('wp_head', 'crisp_hook_head');

function crisp_hook_head() {

  $website_id = get_option('website_id');

  $output="<script data-cfasync='false'>
    window.$crisp=[];
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
    $nickname = $current_user->display_name;

  	$output='<script type="text/javascript">
  	if (typeof jQuery === "function") {
  		jQuery(function($){
  		  window.CRISP_READY_TRIGGER = function() {
  		    $crisp.push(["set", "user:email", "' . $email . '"]);
          $crisp.push(["set", "user:nickname", "' . $nickname . '"]);
  		  };
  		});
  	}
  	</script>';

  	echo $output;
  }

}
?>
