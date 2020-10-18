<?php
/*
Plugin Name: LnF
Plugin URI: https://streets.co.ke
Description: Website manifest
Version: 1.0.0
Author: Philip Mwaniki
Author URI: https://streets.co.ke	
*/

if( !defined( 'WPCMN_VER' ) )
	define( 'WPCMN_VER', '1.0.0' );

// Start up the engine
class LnF
{

	/**
	 * Static property to hold our singleton instance
	 *
	 */
	static $instance = false;

	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	private function __construct() {
		// Registering the method that will handle Messenger requests from Facebook under the wordpress "init" hook
	
		add_action		( 'init',array( $this, 'LnFmessengerBot')			);
		
	}

	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return WP_Comment_Notes
	 */

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

public function fbmessaging_Webhook(){
//Your verfication token as defined	
$VERIFY_TOKEN ='qwertyuiop';
	
	if(isset($_GET['hub_challenge']) &&
        isset($_GET['hub_mode'])&&
        isset($_GET['hub_verify_token'])){
			
			$challenge=$_GET['hub_challenge'];
			$mode =$_GET['hub_mode'];
			$token_fb=$_GET['hub_verify_token'];
			if ($mode === 'subscribe' && $token_fb === $VERIFY_TOKEN) {
				
				echo $challenge;
				die;
		} 
		}
		$postData = file_get_contents('php://input');

	 $postdata_dec = json_decode($postData,true);
	if(isset($postdata_dec['entry'][0]['id'])){
	require_once plugin_dir_path(dirname( __FILE__ ) ) . 'LnFMessengerBot.php';
	LnFMessengerBot::LNFbot($postdata_dec);
	}

	
	
	
		}

/// end class
}


// Instantiate our class
$LnF = LnF::getInstance();

