<?php



/**

 * @since      1.0.0
 * @package    LnF
 * @author     Philip Mwaniki <mwanikphili@gmail.com>
 */
class Streets_messenger {
	

	 private static $MESSENGER_URL = 'https://graph.facebook.com/v8.0/me/messages?access_token=EAAjiN73eA90BAMaClyQ1V4v6OI3MYZBfx0RxiZBxqhcduSZB3plTbu29ohSkOV1g0c9ogjYduuLoMvcfBK55n76LKZCQmkCL24kThhhUTu2fmtVwC3ZB2VdNLmCe7ZAXEDzul7LZCtLhRncypInfA7JZBzJLRuRqf8IArPJswDuFiQZDZD';
	
	public static function streetbot($messagedata){
		error_log(json_encode($messagedata),0);
		for($x= 0; $x<count($messagedata['entry']); ++$x){
			$action = self::idMessagetype($messagedata['entry'][$x]['messaging']);
		
			$resmessage=self::preparefbmessage($messagedata['entry'][$x]['messaging'],$action, $messagedata['entry'][$x]['messaging'][0]['sender']['id']);
		self::sendmessage($resmessage);
		}
		die;
	}
	
	public static function idMessagetype($message){
		
		if (!empty($message[0]['postback'])) {
               
$action = trim($message[0]['postback']['payload']);
            // When bot receive button click from user
            } elseif (!empty($message['message'])) {
                 $action= $message[0]['message']['text'];
               
              } elseif(!empty($message[0]['optin'])){
			$action="NOTIFY_PAYLOAD";
		}
		return $action;
	}
	
	
	 
	 
	 public static function preparefbmessage($message, $action, $pSid){
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'Lnf-Db.php';
		$response=self::preparepostbackmessage($action, $pSid);
		if(!$response){
			
			return self::preparemessagereplysession($pSid,$message,$action);
		}
		return $response;
	
	}
	
		public static function claimItem($pSid,$record,$contact){
		
		$claim =['recipient' => ['one_time_notif_token' => $record],'message' => ['attachment' =>['type' =>'template', 'payload' => ['template_type'=>'generic', 'elements' =>[['title' => 'Someone claimed the Item you Found', 'subtitle' =>'Contact: '.$contact.' then update us if the Item was  Claimed', 'buttons' => [['type'=>'postback', 'title' => 'Claimed', 'payload'=>'DELETE<>'.$record.''],['type'=>'postback', 'title' => 'Not Claimed', 'payload'=>'NOTIFY_PAYLOAD<>'.$record.'']]]]]]]];
		self::sendmessage($claim);
		
		return $resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' =>['template_type'=> 'generic',
			'elements' => [['title' => 'Done', 'image_url' => 'https://streets.co.ke/wp-content/uploads/2020/08/Connect.png', 'subtitle' => 'The Person with your item has been messaged.', 
			'default_action'=>['type'=>'web_url','url' => 'https://streets.co.ke','webview_height_ratio'=> 'tall'],
	'buttons' =>[['type' => 'postback', 'title' => 'Start Chatting','payload' => 'CHAT_PAYLOAD'],
				 ['type' => 'postback', 'title' => 'About Us','payload' => 'CHAT_PAYLOAD'],
			['type' => 'postback', 'title' => 'Features','payload' => 'FEATURES_PAYLOAD']]]]]]
					]];
	}
	
	
	  public static function preparepostbackmessage($action, $pSid){
		  
switch($action){
			 
			 case "CANCEL_PAYLOAD":
				$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' =>['template_type'=> 'generic',
			'elements' => [['title' => 'Welcome to Streets  Lost N Found Service', 'image_url' => 'https://streets.co.ke/wp-content/uploads/2020/09/LnF.png', 'subtitle' => 'Did you Lose or Find something? (IMPORTANT: You have to be Logged in to use this Service)', 
			'default_action'=>['type'=>'web_url','url' => 'https://streets.co.ke','webview_height_ratio'=> 'tall'],
			'buttons' =>[
			['type' => 'postback', 'title' => 'LOST','payload' => 'LOST_PAYLOAD'],
			['type' => 'postback', 'title' => 'FOUND','payload' => 'FOUND_PAYLOAD'],['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]]
					]];
				 LnF_Db::updatemessenger($pSid,'','-update');
				LnF_Db::Deletemessenger($pSid);
				 return $resposnse;
				 break;
			
			case "FOUND_PAYLOAD":
			
			$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' =>['type' => 'template', 'payload'=>['template_type' => 'button', 'text' => 'What did you find?', 'buttons' =>[['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]];
				 if(!LnF_Db::checkMessenger($pSid)){
					LnF_Db::newmessenger($pSid,'found','-nupdate');
				}else if(LnF_Db::checkMessenger($pSid)){
					LnF_Db::updatemessenger($pSid,'found','-update');
				}
			return $resposnse;
			
			break;
		
			 case "LOST_PAYLOAD":
			$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' =>['type' => 'template', 'payload'=>['template_type' => 'button', 'text' => 'What did you lose?', 'buttons' =>[['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]];
				 if(!LnF_Db::checkMessenger($pSid)){
					LnF_Db::newmessenger($pSid,'lost','-nupdate');
				}else{
					LnF_Db::updatemessenger($pSid,'lost','-update');
				}
			return $resposnse;
			
			
			break;
			
		 case "NOTIFY_PAYLOAD":
				 	 $resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' =>['template_type'=> 'generic',
			'elements' => [['title' => 'Welcome to Streets  Lost N Found Service', 'image_url' => 'https://streets.co.ke/wp-content/uploads/2020/09/LnF.png', 'subtitle' => 'Did you Lose or Find something? (IMPORTANT: You have to be Logged in to use this Service)', 
			'default_action'=>['type'=>'web_url','url' => 'https://streets.co.ke','webview_height_ratio'=> 'tall'],
			'buttons' =>[
			['type' => 'postback', 'title' => 'LOST','payload' => 'LOST_PAYLOAD'],
			['type' => 'postback', 'title' => 'FOUND','payload' => 'FOUND_PAYLOAD'],['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]]
					]];
				
  LnF_Db::updatemessenger($pSid,'','-update');
				LnF_Db::addLnfFbNotifyEntry($message[0]['optin']['one_time_notif_token'],$pSid);
				 
				 return $resposnse;
				 break;

		default: 
		return false;
		}
	}

public static function preparemessagereplysession($pSid,$message,$action){
	
	$state=LnF_Db::checkfbState($pSid);
				 $state2=explode("<>",$action);
				 $state3=explode("<>",$state);
			if($state=='found'){
				$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' =>['type' => 'template', 'payload'=>['template_type' => 'button', 'text' => 'Awesome, where did you find the '.$message[0]['message']['text'].'?(Name of the location)', 'buttons' =>[['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]];
				LnF_Db::addLnfFbstateEntry('Found',$message[0]['message']['text'],$pSid);
				LnF_Db::updatemessenger($pSid,'location-f','-update');
			return $resposnse;
			
			
				
				
			}elseif($state=='lost'){
				$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' =>['type' => 'template', 'payload'=>['template_type' => 'button', 'text' => 'Sorry, where did you lose the '.$message[0]['message']['text'].'? (name of the location)', 'buttons' =>[['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]];
				LnF_Db::addLnfFbstateEntry('Lost',$message[0]['message']['text'],$pSid);
				LnF_Db::updatemessenger($pSid,'location-l','-update');
				
			return $resposnse;
				
				
			}elseif($state=='location-f'){
				$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' => ['template_type' =>'one_time_notif_req', 'title'=>'Would you like to be notified when the item is claimed?', 'payload' =>'NOTIFY_PAYLOAD']]]];
				LnF_Db::addLnfFblocationEntry($message[0]['message']['text'],$pSid);
			return $resposnse;
				
				
			}elseif($state=='location-l'){
				LnF_Db::addLnfFblocationEntry($message[0]['message']['text'],$pSid);
				LnF_Db::updatemessenger($pSid,'itemfound-l','-update');
				return LnF_Db::findLnfItem($pSid,0,'Found');
				
			}elseif($state=='itemfound-l'){
				$action=explode("<>",$action);
				if($action[0]=="CLAIM"){
					
						$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' =>['type' => 'template', 'payload'=>['template_type' => 'button', 'text' => 'Please enter a Contact you can be reached with(Phone or Email)', 'buttons' =>[['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]];
					LnF_Db::updatemessenger($pSid,'CONTACT<>'.$action[1].'','-update');
					
					return $resposnse;
				}elseif($action[0]=="MORE"){
					return LnF_Db::findLnfItem($pSid,$action[1],'Found');
				}
				
				
			}elseif($state3[0]=="CONTACT"){
					LnF_Db::updatemessenger($pSid,'','-update');
			return	self::claimItem($pSid,$state3[1],$message[0]['message']['text']);
		
			
				
			}elseif($state2[0]=="DELETE"){
					LnF_Db::DeleteItem($state2[1]);
		$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' =>['template_type'=> 'generic',
			'elements' => [['title' => 'Streets has been Updated', 'image_url' => 'https://streets.co.ke/wp-content/uploads/2020/08/Connect.png', 'subtitle' => 'Hope let us know if you have any Issues', 
			'default_action'=>['type'=>'web_url','url' => 'https://streets.co.ke','webview_height_ratio'=> 'tall'],
	'buttons' =>[['type' => 'postback', 'title' => 'Start Chatting','payload' => 'CHAT_PAYLOAD'],
				 ['type' => 'postback', 'title' => 'About Us','payload' => 'ABOUT_PAYLOAD'],
			['type' => 'postback', 'title' => 'Lost & Found','payload' => 'LNF_PAYLOAD']]]]]]
					]];
				return $resposnse;
			
}elseif($state2[0]=="NOTIFY_PAYLOAD"){
	$resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' => ['template_type' =>'one_time_notif_req', 'title'=>'Would you like to be notified when the item is claimed, again?', 'payload' =>'NOTIFY_PAYLOAD']]]];
				Streets_Db::addLnfFblocationEntry($pSid,$action[1]);
			return $resposnse;				
		
			
}else{
				
			 $resposnse =[
			'recipient' => ['id' => $pSid],
			'message' => ['attachment' => ['type' => 'template', 'payload' =>['template_type'=> 'generic',
			'elements' => [['title' => 'Welcome to Streets  Lost N Found Service', 'image_url' => 'https://streets.co.ke/wp-content/uploads/2020/09/LnF.png', 'subtitle' => 'Did you Lose or Find something? (IMPORTANT: You have to be Logged in to use this Service)', 
			'default_action'=>['type'=>'web_url','url' => 'https://streets.co.ke','webview_height_ratio'=> 'tall'],
			'buttons' =>[
			['type' => 'postback', 'title' => 'LOST','payload' => 'LOST_PAYLOAD'],
			['type' => 'postback', 'title' => 'FOUND','payload' => 'FOUND_PAYLOAD'],['type'=>'postback','title' => 'Cancel','payload' => 'CANCEL_PAYLOAD']]]]]]
					]];
				return $resposnse;
			}
	
	
}



	public static function sendmessage($resmessage){
			error_log(json_encode($resmessage),0);
		$ch =curl_init(self::$MESSENGER_URL);
	curl_setopt($ch, CURLOPT_POST,1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($resmessage));
	curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
	curl_exec($ch);
	curl_close($ch);
		}

	
}