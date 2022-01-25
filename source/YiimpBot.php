<?php
require_once 'db.php';
$botToken = "0000000000:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
$botID = '000000000';
$url = "https://api.telegram.org/bot".$botToken;

$update = file_get_contents("php://input");
$update = json_decode($update, TRUE);

	#LOGGING OF SENT TO TELEGRAM
	$singleVarToShow = 'START';	
    #checkJSON($singleVarToShow,$update);
	
##############################################################
$isCPUtalkBotId = $update['callback_query']['message']['from']['id'];

if ($isCPUtalkBotId == $botID){ #this is my bot replying.
	$userMessage = $update['callback_query']['data'];
	$message = $update['callback_query']['data'];
	$chat_id = $update['callback_query']['message']['chat']['id'];
	$from_id = $update['callback_query']['from']['id'];
	$message_id = $update['callback_query']['message']['message_id'];
}else{
	$chat_id = $update['message']['chat']['id'];
	$from_id = $update['message']['from']['id'];
	$userMessage = $update['message']['text'];
	$message = $update['message']['text'];
	$message_id = $update['message']['message_id'];
	
	$inReplyTo = $update['message']['reply_to_message']['text'];
	$from_reply_id = $update['message']['reply_to_message']['from']['id'];
}
	#$userMessage = strtolower($userMessage);

#hash value one way to 'somewhat' mask the users telegram id.
$from_id_hashed = sha1($botToken.$from_id);


#test($url, $chat_id, $message_id,$emoji.">");	

##############################################################	
# START switch has been called
##############################################################
if ($userMessage == '/start') {

$reply = "<b>Welcome</b> I am the Yiimp Bot!

I can connect you to multiple yiimp based pools you are mining to and show some handy features.

To get started please use /api

You can also /donate if you wish to add more than (<b>3</b>) wallets.

For help and announcements
visit the official @yiimp chat.

Interested in CPU mining? 
Check out @CPUtalk";
	
#$reply='<code>inline fixed-width code</code>
#<pre>pre-formatted fixed-width code block</pre>';	
	
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);
	
executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&disable_web_page_preview=TRUE&parse_mode=HTML&");
}	
##############################################################	
# DONATE switch has been called
##############################################################
if ($userMessage == '/donate') {

$reply = "<b>Donate!</b>
If you would like to add more than (<b>3</b>) wallets to your account or just want to say thanks! then check out the donation addresses below.";

#$reply='<code>inline fixed-width code</code>
#<pre>pre-formatted fixed-width code block</pre>';	

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => 'DYN (Dynamic)',
				'callback_data' => 'donate_to-DYN'
			)
		),		
		array(
			array(
				'text' => 'ELI (Elicoin)',
				'callback_data' => 'donate_to-ELI'
			)
		),		
		array(
			array(
				'text' => 'CRDS (Credits)',
				'callback_data' => 'donate_to-CRDS'
			)
		),		
		array(
			array(
				'text' => 'HTML (HTMLcoin)',
				'callback_data' => 'donate_to-HTML'
			)
		),		
		array(
			array(
				'text' => 'BTCP (Bitcoin Private)',
				'callback_data' => 'donate_to-BTCP'
			)
		),		
		array(
			array(
				'text' => 'ZCL (ZCLassic)',
				'callback_data' => 'donate_to-ZCL'
			)
		)
	)); 
		
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);
	
	if ($isCPUtalkBotId == $botID){ #this is my bot replying.	
		executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	
	}else{
		executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");
	}		
}			
##############################################################	
# donate_to has been called
##############################################################
if (strpos($userMessage, "donate_to") !== false && $isCPUtalkBotId == $botID){

$donateTo = explode("-", $userMessage);

switch ($donateTo[1]) {
    case "DYN":
        $reply = "<b>DYN (Dynamic)</b>
		XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        break;
    case "ELI":
        $reply = "<b>ELI (Elicoin)</b>
		XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        break;
    case "CRDS":
        $reply = "<b>CRDS (Credits)</b>
		XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        break;
    case "HTML":
       $reply = "<b>HTML (HTMLcoin)</b>
	   XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        break;
    case "BTCP":
        $reply = "<b>BTCP (Bitcoin Private)</b>
		XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        break;		
    case "ZCL":
        $reply = "<b>ZCL (ZCLassic)</b>
		XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
        break;			
    default:
        $reply = "this shouldn't happen";
}
	
#$reply='<code>inline fixed-width code</code>
#<pre>pre-formatted fixed-width code block</pre>';	

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => '< BACK',
				'callback_data' => '/donate'
			)
		)		
	)); 
		
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);
	
executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&disable_web_page_preview=TRUE&parse_mode=HTML&");
}			
##############################################################	
# API switch has been called
##############################################################
if ($userMessage == '/api') {

#could check for user saved db if not then show default

/*
		array(
			array(
				'text' => 'VIEW POOL INFO',
				'callback_data' => 'view_api'
			)
		)
*/		

	$linkAwallet = array(
			array(
				'text' => "LINK A NEW WALLET",
				'callback_data' => 'start_api'
			)
		);
	$walletSettings = array(
			array(
				'text' => "\xE2\x9A\x99 SETTINGS \xE2\x9A\x99",
				'callback_data' => 'wallet_settings'
			)
		);

$query = "SELECT walletAddress,unique_id,poolAddress,currency FROM poolWallets WHERE from_id='$from_id_hashed'";
$result = mysqli_query($conn, $query);
$totalRowCount = mysqli_num_rows($result);
$numRow = 0;

#if donated = 0 then
$userAllowed = 3;
#else userAllowed = 50.

if ($totalRowCount > 0){



	while($row = mysqli_fetch_array($result)) {
	#$walletAddressEncoded = $row['walletAddress'];
	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID']))), "\0");
	
		if ($numRow < $userAllowed){		
		
			$buttonToWallet[$numRow] = array(
					array(
						'text' => '['.$row["currency"].'] '.substr($row["poolAddress"], 0, 5).' - '.substr($walletAddressDecoded, 0, 8).'..',
						'callback_data' => 'show_userwallet-'.$row['unique_id']
					)
				);	
			$numRow++;		

		} 

	}//end while
	
	$reply = 'Showing (<b>'.$numRow.'</b> of <b>'.$totalRowCount.'</b>) wallets saved.';	

	#add on extras# back,settings
	array_push($buttonToWallet, $linkAwallet, $walletSettings);
	$keyboard = array('inline_keyboard' =>  $buttonToWallet);
	
	if ($totalRowCount > $userAllowed){
	$reply .= ' Sorry you are only allowed to save a maximum of 3 wallets. You can always /donate to add more.';
	}
	
	
}else{
	
	#show default
		
	$reply = 'Please link a new wallet to get started.';

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => 'LINK A WALLET',
				'callback_data' => 'start_api'
			)
		)
	)); 

} //end if user not found

mysqli_free_result($result);
	
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);

	
	if ($isCPUtalkBotId == $botID){ #this is my bot replying.	
		executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	
	}else{
		executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");
	}		

} //end api call
##############################################################	
# REMOVE THIS WALLET
##############################################################
if (strpos($userMessage, "yes_remove_this_wallet") !== false && $isCPUtalkBotId == $botID){

#split by '-' and search db for that value.
$unique_id = explode("-", $userMessage);

#have to make sure this is not a star otherwise it will remove all.

	#remove from db where unique id is and check if deleted.
	$sql = "DELETE FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	if (!($conn->query($sql))) {
		displayError($url, $chat_id, $message_id, 'ERROR 26: Could not remove address, please contact @KramWell');
	}
	
	#check if deleted.
	$query = "SELECT unique_id FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == true){
		displayError($url, $chat_id, $message_id, "ERROR 27: Could not remove address, please contact @KramWell");
	}
	
	$reply='Wallet removed from your profile.';
	
	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => '< BACK',
				'callback_data' => 'wallet_settings'
			)
		)
	)); 
		
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);

	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	

}
##############################################################	
# REMOVE THIS WALLET
##############################################################
if (strpos($userMessage, "remove_this_wallet") !== false && $isCPUtalkBotId == $botID){

#split by '-' and search db for that value.
$unique_id = explode("-", $userMessage);




	
#show are you sure you want to remove xx yes-no.	

$reply = 'Are you sure you want to remove this wallet?';

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => 'YES - Please remove',
				'callback_data' => 'yes_remove_this_wallet-'.$unique_id[1]
			)
		),		
		array(
			array(
				'text' => 'NO - Go back!',
				'callback_data' => 'remove_wallet'
			)
		)
	)); 
		
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);

	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	

}
##############################################################	
# DELETE WALLET
##############################################################
if ($userMessage == 'remove_wallet' && $isCPUtalkBotId == $botID){

#could check for user saved db if not then show default

	$back = array(
		array(
			'text' => '< BACK',
			'callback_data' => 'wallet_settings'
		)
	);

	$query = "SELECT walletAddress,unique_id,poolAddress FROM poolWallets WHERE from_id='$from_id_hashed'";
	$result = mysqli_query($conn, $query);
	$totalRowCount = mysqli_num_rows($result);
	$numRow = 0;

	if ($totalRowCount > 0){

		while($row = mysqli_fetch_array($result)) {	

		#$walletAddressEncoded = $row['walletAddress'];
		$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
		$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($botID))), "\0");	
		
				$remove_this_wallet[$numRow] = array(
						array(
							'text' => '['.$row["poolAddress"].'] '.substr($walletAddressDecoded, 0, 10).'..',
							'callback_data' => 'remove_this_wallet-'.$row['unique_id']
						)
					);	
				$numRow++;		

		}//end while
		
		$reply = 'Please choose a wallet to remove.';	

		#add on extras# back,settings
		array_push($remove_this_wallet, $back);
		$keyboard = array('inline_keyboard' =>  $remove_this_wallet);

	}else{
		
		#show default
			
		$reply = 'Sorry no wallets found.';

		$keyboard = array('inline_keyboard' =>  $back);

	} //end if user not found

	mysqli_free_result($result);
	
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);

	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	


} //end api call
##############################################################	
#WALLET SETTINGS
##############################################################
if ($userMessage == 'wallet_settings' && $isCPUtalkBotId == $botID){

$reply = "### SETTINGS ###";

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => 'DELETE A WALLET',
				'callback_data' => 'remove_wallet'
			)
		),
		array(
			array(
				'text' => '< BACK',
				'callback_data' => '/api'
			)
		)		
	)); 
		
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($keyboard)
	);

executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	

}
##############################################################	
#START BUTTON PRESSED
##############################################################
if ($userMessage == 'start_api' && $isCPUtalkBotId == $botID){

$reply = "Thanks: To get started please reply with the pool you are mining to for example 'pool.kramwell.com'";
		
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);

executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&", FALSE);

#POP UP REPLY
$reply = "pool-address";

	$replyMarkup = array(
	  'force_reply' => true,
	  'selective' => true
	);

	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply",
		'reply_markup' => json_encode($replyMarkup)
	);

executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");	

}

##############################################################	
#REPLY FROM 'POOL ADDRESS'
##############################################################
if ($inReplyTo == "pool-address" && $from_reply_id == $botID){
	
	$previousMessageID = $message_id - 1;	
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$previousMessageID"
	);		
	#deleteMessage($postfields, $url."/deleteMessage?");
		
	$reply = "Checking validity of pool..";
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);	
executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&", FALSE);	

#HERE WE NEED TO CHECK IF POOL ADDRESS RETURNS CORRECTLY THEN UPDATE

$checkPoolAddressResult = checkPoolAddress($userMessage, $from_id_hashed);

#test($url, $chat_id, $message_id, $userMessage);
#test($url, $chat_id, $message_id, $unique_id[1]);

if ($checkPoolAddressResult !== TRUE){
	
	#send back responce to say error
	displayError($url, $chat_id, $message_id, 'Sorry, there was an error: '.$checkPoolAddressResult);
	
}else{

	

	$reply = "Thanks, this has been added now.\n\nPlease enter your wallet address."; #.$checkPoolAddressResult;
	
	$NextMessageID = $message_id + 1;	
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$NextMessageID",
		'text' => "$reply"
	);

	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&", FALSE);

	#POP UP REPLY
	$reply = "wallet-address";

		$replyMarkup = array(
		  'force_reply' => true,
		  'selective' => true
		);

		$postfields = array(
			'chat_id' => "$chat_id",
			'message_id' => "$message_id",
			'text' => "$reply",
			'reply_markup' => json_encode($replyMarkup)
		);

	executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");	
}

}

##############################################################	
# SHOW API RESULTS / BALANCE ETC.
##############################################################
#if ($userMessage == '/test'){
#	$userMessage = 'showStats_nosave';
if (strpos($userMessage, "showStats_") !== false && $isCPUtalkBotId == $botID) {
	
#here we save the results to database and call showStats	

	#grab row details and remove from temp DB
	$query = "SELECT * FROM tempchatPool WHERE from_id='$from_id_hashed'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, 'ERROR 12 user not found, please contact @KramWell');
	}
	mysqli_free_result($result);

	$sql = "DELETE FROM tempchatPool WHERE from_id='$from_id_hashed'";
	if (!($conn->query($sql))) {
		displayError($url, $chat_id, $message_id, 'ERROR 11: please contact @KramWell');
	}

	#get info from row
	$scheme = $row['scheme'];
	$poolAddress = $row['poolAddress'];
	$from_id_hashed = $row['from_id'];
	$currency = $row['currency'];

	$walletAddressEncoded = $row['walletAddress'];
	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($botID))), "\0");
	
	# NO SAVE
	if ($userMessage == 'showStats_nosave'){
		
		$walletinfo = showBalance($scheme, $poolAddress, $walletAddressDecoded);
		#$m1 = $scheme . $poolAddress . $walletAddressDecoded;
		#test($url, $chat_id, $message_id, $m1);
		
		if (strpos($walletinfo, "ERROR") !== false){
			displayError($url, $chat_id, $message_id, $walletinfo);
		}
		$reply = "Showing (".$currency.") Balance\nFrom pool: ".$poolAddress."\nFrom wallet: ".substr($walletAddressDecoded, 0, 10)."...\n\n".$walletinfo;

		$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
		);
		executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	

		
	} #end showStats_nosave



		# SAVE TO DB
	if ($userMessage == 'showStats_save'){
		#save row details / show messages / 


		$query = "SELECT unique_id FROM poolWallets WHERE from_id='$from_id_hashed' AND poolAddress='$poolAddress' AND walletAddress='$walletAddressEncoded'";
		$result = mysqli_query($conn, $query);
		$row = mysqli_fetch_assoc($result);
		if ($row == false){
			#record not present-insert
			$timeNow = time();
			$sql = "INSERT INTO poolWallets (poolAddress, from_id, scheme, timeNow, walletAddress, currency) VALUES ('$poolAddress', '$from_id_hashed', '$scheme', '$timeNow', '$walletAddressEncoded', '$currency')";
			if (!($conn->query($sql))) {
				#already added.	
				displayError($url, $chat_id, $message_id, "ERROR 16: can't insert record, please contact @KramWell");
			}
		}
		
		viewWalletInfo($poolAddress, $walletAddressEncoded, $from_id_hashed);
		
	} #end showStats_save

}
##############################################################	
# SHOW USER WALLET
##############################################################
if (strpos($userMessage, "show_userwallet") !== false && $isCPUtalkBotId == $botID){

#split by '-' and search db for that value.
$unique_id = explode("-", $userMessage);

#could do more error checking to make sure its a number value only.
	#grab tabke details form unique_id
	$query = "SELECT * FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "ERROR 10: can't get record, please contact @KramWell");
	}
	mysqli_free_result($result);
	
	viewWalletInfo($row['poolAddress'], $row['walletAddress'], $row['from_id']);		
	
}
##############################################################	
# SHOW WALLET ON POOL - INFO
##############################################################
function viewWalletInfo($poolAddress, $walletAddressEncoded, $from_id_hashed){
#global $conn;

#the reason this is here is because it will need to be called a few times in different senarous

	#grab unique_id from table to send over to quick find.
	$query = "SELECT unique_id,scheme,walletAddress,currency FROM poolWallets WHERE from_id='$from_id_hashed' AND poolAddress='$poolAddress' AND walletAddress='$walletAddressEncoded'";
	$result = mysqli_query($GLOBALS['conn'], $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($GLOBALS['url'], $GLOBALS['chat_id'], $GLOBALS['message_id'], "ERROR 17: can't find record, please contact @KramWell");
	}
	mysqli_free_result($result);

	$unique_id = $row['unique_id'];
	$scheme = $row['scheme'];
	$currency = $row['currency'];
	
	#$walletAddressEncoded = $row['walletAddress'];
	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID']))), "\0");
	
	#here we need to show 
	#VIEW BALANCE
	#24HR PAYOUTS
	#LAST 50 BLOCKS
	#SHOW MY MINERS
	#BACK

	$reply = "Viewing Wallet: ".substr($walletAddressDecoded, 0, 10)."...\nFrom pool: ".$poolAddress."\nWith currency: ".$currency;
	
	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => 'VIEW BALANCE',
				'callback_data' => 'show_balance-'.$unique_id
			)
		),		
		array(
			array(
				'text' => 'BLOCKS FOUND',
				'callback_data' => 'show_blocks-'.$unique_id
			)
		),		
		array(
			array(
				'text' => '24HR PAYOUTS',
				'callback_data' => 'show_payouts-'.$unique_id
			)
		),
		array(
			array(
				'text' => 'SHOW MY MINERS',
				'callback_data' => 'show_miners-'.$unique_id
			)
		),
		array(
			array(
				'text' => 'OPEN WEBSITE',
				'url' => $scheme.$poolAddress.'/?address='.$walletAddressDecoded
			)
		),
		array(
			array(
				'text' => '< BACK TO WALLETS',
				'callback_data' => '/api'
			)
		)
	)); 
		
	$postfields = array(
		'chat_id' => $GLOBALS['chat_id'],
		'message_id' => $GLOBALS['message_id'],
		'text' => $reply,
		'reply_markup' => json_encode($keyboard)
	);

	executeQuery($postfields, $GLOBALS['url']."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	
}
##############################################################	
# SHOW MINERS
##############################################################
if (strpos($userMessage, "show_miners") !== false && $isCPUtalkBotId == $botID){

	#split by '-' and search db for that value.
	$unique_id = explode("-", $userMessage);

	#could do more error checking to make sure its a number value only.
	#grab tabke details form unique_id
	$query = "SELECT * FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "ERROR 15: can't get record, please contact @KramWell");
	}
	mysqli_free_result($result);

	#$walletAddressEncoded = $row['walletAddress'];
	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID']))), "\0");	
	
	$payoutinfo = showMinerInfo($row['scheme'], $row['poolAddress'], $walletAddressDecoded);
	
	if (strpos($payoutinfo, "ERROR") !== false){
		displayError($url, $chat_id, $message_id, $payoutinfo);
	}
	
	$reply = "<b>MINERS</b>\nWallet: ".substr($walletAddressDecoded, 0, 10)."...\nPool: ".$row['poolAddress']."\n\n".$payoutinfo;

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => '< BACK',
				'callback_data' => 'show_userwallet-'.$unique_id[1]
			)
		)
	)); 	
	
	$postfields = array(
	'chat_id' => "$chat_id",
	'message_id' => "$message_id",
	'text' => "$reply",
	'reply_markup' => json_encode($keyboard)
	);
	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");
}
function showMinerInfo($scheme, $poolAddress, $walletAddressDecoded){

	$textContentOut = '';
	$count = 0;

		$getWalletInfo = getApiData($scheme.$poolAddress."/site/wallet_miners_results?address=".$walletAddressDecoded);	
		if($getWalletInfo === FALSE) {
			RETURN 'ERROR 27: please contact @KramWell';
		}

		if (isset($getWalletInfo)){

			# Create a DOM parser object
			$dom = new DOMDocument();
			@$dom->loadHTML($getWalletInfo);

			$count = 0;
			$AmountOfMiners = 1;
				
			foreach($dom->getElementsByTagName('td') as $tdElement) {
				
			$textContent = $tdElement->textContent;
						
					if ($AmountOfMiners < 10){	#show only 10.
						
						$count++;
						
						if ($count == 2){
							$textContentOut .= "###########\n<b>Overall Totals</b>:\n<b>Miners:</b> ".$textContent."\n";
						}elseif ($count == 3){
							$textContentOut .= '<b>Shares:</b> '.$textContent."\n";
						}elseif ($count == 4){
							$textContentOut .= '<b>Hashrate:</b> '.$textContent."\n";
						}elseif ($count == 5){
							$textContentOut .= '<b>Reject:</b> '.$textContent."\n###########\n";
							$count++;
							
						}elseif ($count == 7){
							$textContentOut.= "\n".'<b>Version:</b> '.$textContent."\n";
						}elseif ($count == 8){
							$textContentOut .= '<b>Extra:</b> '.$textContent."\n";
						}elseif ($count == 9){
							$textContentOut .= '<b>Algo:</b> '.$textContent."\n";
						}elseif ($count == 10){
							$textContentOut .= '<b>Diff:</b> '.$textContent."\n";							
						}elseif ($count == 12){
							$textContentOut .= '<b>Hashrate:</b> '.$textContent."\n";
						}elseif ($count == 13){
							$textContentOut .= '<b>Reject:</b> '.$textContent."\n";	
							$count = 6;
							$AmountOfMiners++;
						}
					}
			}
		
		RETURN $textContentOut;
		
		}else{
			RETURN "ERROR 23: could not get wallet result, contact @KramWell";
		}
	
}
##############################################################	
# SHOW LAST 50 BLOCKS ON POOL
##############################################################
if (strpos($userMessage, "show_blocks") !== false && $isCPUtalkBotId == $botID){

#split by '-' and search db for that value.
$unique_id = explode("-", $userMessage);

#could do more error checking to make sure its a number value only.
	#grab tabke details form unique_id
	$query = "SELECT * FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "ERROR 20: can't get record, please contact @KramWell");
	}
	mysqli_free_result($result);

	#$walletAddressEncoded = $row['walletAddress'];
	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID']))), "\0");
	
	$payoutinfo = showBlocks($row['scheme'], $row['poolAddress'], $walletAddressDecoded);
	
	if (strpos($payoutinfo, "ERROR") !== false){
		displayError($url, $chat_id, $message_id, $payoutinfo);
	}
	
	$reply = "Viewing Wallet: ".substr($walletAddressDecoded, 0, 10)."...\nFrom pool: ".$row['poolAddress']."...\n\n".$payoutinfo;

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => '< BACK',
				'callback_data' => 'show_userwallet-'.$unique_id[1]
			)
		)
	)); 	
	
	$postfields = array(
	'chat_id' => "$chat_id",
	'message_id' => "$message_id",
	'text' => "$reply",
	'reply_markup' => json_encode($keyboard)
	);
	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");
}
##############################################################	
# SHOW LAST 50 BLOCKS ON POOL
##############################################################
function showBlocks($scheme, $poolAddress, $walletAddressDecoded){

/*
This will show the following-



*/

$textContentOut = '';
$count = 0;
$blockCount = 0;

	#here we will show the wallet info

		$getBlockInfo = getApiData($scheme.$poolAddress."/site/user_earning_results?address=".$walletAddressDecoded);	
		if($getBlockInfo === FALSE) {
			RETURN 'ERROR 18: please contact @KramWell';
		}

		
		if (isset($getBlockInfo)){

			# Create a DOM parser object
			$dom = new DOMDocument();
			@$dom->loadHTML($getBlockInfo);			
				
			foreach($dom->getElementsByTagName('td') as $tdElement) {
				
			$textContent = $tdElement->textContent;
		
					if ($textContent){
						
						$count++;
						
						if ($blockCount < 10){
						
							if ($count == 7){
								$textContentOut .= "\n";
								$count = 1;
								$blockCount++;
							}
							
							#if ($count !== 1 && $count !== 4){
							#	$textContentOut .= $textContent ."\n";
							#}

							if ($count == 2){
								$textContentOut .= "<b>Amount:</b> " . $textContent ."\n";
							}
							if ($count == 3){
								$textContentOut .= "<b>Percent:</b> " . $textContent ."\n";
							}
							if ($count == 5){
								$textContentOut .= "<b>Found:</b> " . $textContent ."\n";
							}							
							if ($count == 6){
								$textContentOut .= "<b>Status:</b> " . $textContent ."\n";
							}	
							
						}
					}
				
			}
		
		RETURN $textContentOut;
		
		}else{
			RETURN "ERROR 22: could not get result, contact @KramWell";
		}
	
}
##############################################################	
# SHOW LAST 24HRS PAYOUTS FROM POOL
##############################################################
if (strpos($userMessage, "show_payouts") !== false && $isCPUtalkBotId == $botID){

#split by '-' and search db for that value.
$unique_id = explode("-", $userMessage);

#could do more error checking to make sure its a number value only.
	#grab tabke details form unique_id
	$query = "SELECT * FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "ERROR 21: can't get record, please contact @KramWell");
	}
	mysqli_free_result($result);

	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID']))), "\0");	
	
	$payoutinfo = showPayouts($row['scheme'], $row['poolAddress'], $walletAddressDecoded);
	
	if (strpos($payoutinfo, "ERROR") !== false){
		displayError($url, $chat_id, $message_id, $payoutinfo);
	}
	
	$reply = "Viewing Wallet: ".substr($walletAddressDecoded, 0, 10)."...\nFrom pool: ".$row['poolAddress']."...\n\n".$payoutinfo;

	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => '< BACK',
				'callback_data' => 'show_userwallet-'.$unique_id[1]
			)
		)
	)); 	
	
	$postfields = array(
	'chat_id' => "$chat_id",
	'message_id' => "$message_id",
	'text' => "$reply",
	'reply_markup' => json_encode($keyboard)
	);
	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");		
	
}
##############################################################	
# SHOW LAST 24HRS PAYOUT ON POOL
##############################################################
function showPayouts($scheme, $poolAddress, $walletAddressDecoded){

/*
This will show the following-



*/

$textContentOut = '';
$notShow = 0;
$count = 0;

	#here we will show the wallet info

		$getWalletInfo = getApiData($scheme.$poolAddress."/site/wallet_results?address=".$walletAddressDecoded);	
		if($getWalletInfo === FALSE) {
			RETURN 'ERROR 18: please contact @KramWell';
		}

		
		if (isset($getWalletInfo)){

			# Create a DOM parser object
			$dom = new DOMDocument();
			@$dom->loadHTML($getWalletInfo);			
				
			foreach($dom->getElementsByTagName('td') as $tdElement) {
				
			$textContent = $tdElement->textContent;
							
				if (strpos($textContent, "Show Details") === false) {
					
					if ($textContent){
						
						if (strlen($textContent) < 25){

							if ($textContent == 'Balance'){
								$textContent = "<b>" . $textContent ."</b>";
								$count = 1;
							}
									
							if ($count > 0){
									
								if (strpos($textContent, 'ago') !== false) {
									$notShow = $count;
									$textContent = "\n<b>" . $textContent ."</b>";									
								}

								if ($textContent == 'Total:'){
									$textContent = "\n<b>(24hrs) " . $textContent ."</b>";	
								}
									
								if ($count = $notShow){ #if count <> NotShow
									$textContentOut .= $textContent ."\n";
								}
								$count++;									
							}
						}
					}
				}
			}
		
		RETURN $textContentOut;
		
		}else{
			RETURN "ERROR 19: could not get result, contact @KramWell";
		}
	
}
##############################################################	
# SHOW SAVED BALANCE FROM POOL
##############################################################
if (strpos($userMessage, "show_balance") !== false && $isCPUtalkBotId == $botID){

#split by '-' and search db for that value.
$unique_id = explode("-", $userMessage);

#could do more error checking to make sure its a number value only.
	#grab tabke details form unique_id
	$query = "SELECT * FROM poolWallets WHERE from_id='$from_id_hashed' AND unique_id='$unique_id[1]'";
	$result = mysqli_query($conn, $query);
	$row = mysqli_fetch_assoc($result);
	if ($row == false){
		displayError($url, $chat_id, $message_id, "ERROR 18: can't get record, please contact @KramWell");
	}
	mysqli_free_result($result);

	#get info from row
	$scheme = $row['scheme'];
	$poolAddress = $row['poolAddress'];
	$from_id_hashed = $row['from_id'];

	#$walletAddressEncoded = $row['walletAddress'];
	$walletAddressDecoded = strrev( $row['walletAddress'] ) . '==';
	$walletAddressDecoded = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), base64_decode($walletAddressDecoded), MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID']))), "\0");	
	
	#test($url, $chat_id, $message_id, $userMessage);


	$walletinfo = showBalance($scheme, $poolAddress, $walletAddressDecoded);
	
	if (strpos($walletinfo, "ERROR") !== false){
		displayError($url, $chat_id, $message_id, $walletinfo);
	}

	$reply = "Viewing Wallet: ".substr($walletAddressDecoded, 0, 10)."...\nFrom pool: ".$row['poolAddress']."\n\n".$walletinfo;	
	
	$keyboard = array(
	'inline_keyboard' => array(
		array(
			array(
				'text' => '< BACK',
				'callback_data' => 'show_userwallet-'.$unique_id[1]
			)
		)
	)); 	
	
	$postfields = array(
	'chat_id' => "$chat_id",
	'message_id' => "$message_id",
	'text' => "$reply",
	'reply_markup' => json_encode($keyboard)
	);
	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	
	
}
##############################################################	
# SHOW BALANCE ON POOL
##############################################################
function showBalance($scheme, $poolAddress, $walletAddressDecoded){

/*
This will show the following-

<b>Balance</b>
0.00000000 ELI

<b>Total Unpaid</b>
31.12097153 ELI

<b>Total Paid</b>
1557.00091501 ELI

<b>Total Earned</b>
1588.12188654 ELI

<b>Total:</b>
308.95024503

*/

$textContentOut = '';
$notShow = 0;
$count = 0;

	#here we will show the wallet info

		$getWalletInfo = getApiData($scheme.$poolAddress."/site/wallet_results?address=".$walletAddressDecoded);	
		if($getWalletInfo === FALSE) {
			RETURN 'ERROR 13: please contact @KramWell';
		}

		
		if (isset($getWalletInfo)){

			# Create a DOM parser object
			$dom = new DOMDocument();
			@$dom->loadHTML($getWalletInfo);			
				
			foreach($dom->getElementsByTagName('td') as $tdElement) {
				
			$textContent = $tdElement->textContent;
							
				if (strpos($textContent, "Show Details") === false) {
					
					if ($textContent){
						
						if (strlen($textContent) < 25){

							if ($textContent == 'Balance'){
								$textContent = "<b>" . $textContent ."</b>";
								$count = 1;
							}
									
							if ($count > 0){

								if ($textContent == 'Total Unpaid'){
									$textContent = "\n<b>" . $textContent ."</b>";	
								}

								if ($textContent == 'Total Paid'){
									$textContent = "\n<b>" . $textContent ."</b>";	
								}

								if ($textContent == 'Total Earned'){
									$textContent = "\n<b>" . $textContent ."</b>";	
								}
									
								if (strpos($textContent, 'ago') !== false) {
									$notShow = $count +1;
								}

								if ($textContent == 'Total:'){
									$textContent = "\n<b>(24hrs) " . $textContent ."</b>";	
								}
									
								if ($count > $notShow){ #if count <> NotShow
									$textContentOut .= $textContent ."\n";
								}
								$count++;									
							}
						}
					}
				}
			}
		
		RETURN $textContentOut;
		
		}else{
			RETURN "ERROR 14: could not get wallet result, contact @KramWell";
		}
	
}
##############################################################	
#REPLY FROM 'WALLET ADDRESS'
##############################################################

if ($inReplyTo == "wallet-address" && $from_reply_id == $botID){
	
	$previousMessageID = $message_id - 1;	
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$previousMessageID"
	);		
	#deleteMessage($postfields, $url."/deleteMessage?");

	$reply = "Checking for wallet address on pool..";
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);	
executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&", FALSE);	


#HERE WE NEED TO CHECK IF WALLET ADDRESS RETURNS CORRECTLY THEN UPDATE

$checkWalletAddressResult = checkWalletAddress($userMessage, $from_id_hashed);

	if ($checkWalletAddressResult !== TRUE){

		#	if ($checkWalletAddressResult !== FALSE){

/*
			$reply = 'Sorry, there was an error: '.$checkWalletAddressResult;

				$keyboard = array(
				'inline_keyboard' => array(
				array(
					array(
						'text' => '### RETRY ###',
						'callback_data' => 'showStats_nosave'
					)
				)	
				)); 
			
			#$NextMessageID = $message_id + 1;	
			$postfields = array(
				'chat_id' => "$chat_id",
				'message_id' => "$message_id",
				'text' => "$reply",
				'reply_markup' => json_encode($keyboard)
			);

			executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");
			
		}
	
*/	
		#send back responce to say error
		displayError($url, $chat_id, $message_id, 'Sorry, there was an error: '.$checkWalletAddressResult);		
		
	}else{


		$reply = "Success! Would you like to save your settings for quicker access next time and to unlock more features?";

			$keyboard = array(
			'inline_keyboard' => array(
			array(
				array(
					'text' => 'YES, Save these settings',
					'callback_data' => 'showStats_save'
				)
			),
			array(
				array(
					'text' => 'NO, Just show me stats',
					'callback_data' => 'showStats_nosave'
				)
			)	
			)); 
		
		$NextMessageID = $message_id + 1;	
		$postfields = array(
			'chat_id' => "$chat_id",
			'message_id' => "$NextMessageID",
			'text' => "$reply",
			'reply_markup' => json_encode($keyboard)
		);
		
		executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");
	}
}	
##############################################################
#REPLACE OR DISPLAY
##############################################################
#if ($isCPUtalkBotId == $botID){ #this is my bot replying.	
#	executeQuery($postfields, $url."/editMessageText?disable_notification=TRUE&parse_mode=HTML&");	
#}else{
#	executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");
#}
	
	
##############################################################
#EXECUTE QUERY - SEND TO CURL
##############################################################

function executeQuery($postfields, $urlToSend, $exit = TRUE){

	if (!$curld = curl_init()) {
	exit;
	}

	curl_setopt($curld, CURLOPT_POST, true);
	curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($curld, CURLOPT_URL,$urlToSend);
	curl_setopt($curld, CURLOPT_RETURNTRANSFER, true); #enable to view results

	$output = curl_exec($curld);

	#outputResponce("TELEGRAM RESULTS: ",$output);
	
	curl_close ($curld);

	if ($exit){
		exit;
	}	
}

##############################################################
#DELETE MESSAGE
##############################################################
function deleteMessage($postfields, $urlToSend){

	if (!$curld = curl_init()) {
	exit;
	}

	curl_setopt($curld, CURLOPT_POST, true);
	curl_setopt($curld, CURLOPT_POSTFIELDS, $postfields);
	curl_setopt($curld, CURLOPT_URL,$urlToSend);
	#curl_setopt($curld, CURLOPT_RETURNTRANSFER, true); #enable to view results

	$output = curl_exec($curld);

	curl_close ($curld);
}
##############################################################
# output responce from sending commands to telegram.
##############################################################
function outputResponce($singleVarToShow,$output){

	$myFile = "outputResponce.txt";
	$updateArray = print_r($output,TRUE);
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh,"ID ".$isCPUtalkBotId ."\n\n");
	
	fwrite($fh, $updateArray."\n\n");
	fclose($fh);
}
##############################################################
# output all results if dumpResult() is called.
##############################################################
function checkJSON($singleVarToShow,$output){

	$myFile = "log_dev.txt";
	$updateArray = print_r($output,TRUE);
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh,"ID ".$isCPUtalkBotId ."\n\n");
	
	fwrite($fh, $updateArray."\n\n");
	fclose($fh);
}
##############################################################
# place to dump any info from varible
##############################################################
function checkOutput($id,$output){

	$myFile = "checkOutput.txt";
	$updateArray = print_r($output,TRUE);
	$fh = fopen($myFile, 'a') or die("can't open file");
	fwrite($fh,"ID ".$id ."\n\n");
	
	fwrite($fh, $updateArray."\n\n");
	fclose($fh);
}
##############################################################
# FOR SENDING ANY OUTPUT TO USER
##############################################################
function test($url, $chat_id, $message_id, $reply = 'blank'){
#$reply = 'test';
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);
executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");	
}
##############################################################
# FOR SENDING ERROR OUTPUT TO USER
##############################################################
function displayError($url, $chat_id, $message_id, $reply = 'ERROR'){
	$postfields = array(
		'chat_id' => "$chat_id",
		'message_id' => "$message_id",
		'text' => "$reply"
	);
executeQuery($postfields, $url."/sendMessage?disable_notification=TRUE&parse_mode=HTML&");	
}
##############################################################
# CHECK IF POOL ADDRESS IS VALID
##############################################################
function checkPoolAddress($poolAddress, $from_id_hashed){
#global $conn;

	#check for https or http
	$url = parse_url($poolAddress);

	$scheme = 'https://';


	if (isset($url['host'])){
		$poolAddress = $url['host'];
		
		if (isset($url['scheme'])){
			switch ($url['scheme']) {
				case "http":
					$scheme = 'http://';
					break;
				default:
					#default to https
					$scheme = 'https://';
			}	
		}
		
	}

	#here we check if host is already in db. if so no need to scrape.

	
	
	$getApiInfo = getApiData($scheme.$poolAddress."/api/currencies");

	if($getApiInfo === FALSE) {
		
		#error try with https?
		if ($scheme == 'https://'){
			$scheme = 'http://';
		}else{
			$scheme = 'https://';
		}
		
		$getApiInfo = getApiData($scheme.$poolAddress."/api/currencies");	
		

		if($getApiInfo === FALSE) {
			RETURN 'error with url '.$poolAddress;
		}
	}

	#$getApiInfo need to see this
	checkOutput("HOST",$poolAddress);
	
	$getApiInfo = json_decode($getApiInfo, TRUE);

	if (isset($getApiInfo)){

		if (isset(array_keys($getApiInfo)[0])){

			$timeNow = time();
			
			$sql = "INSERT INTO tempchatPool (poolAddress, from_id, scheme, timeNow) VALUES ('$poolAddress', '$from_id_hashed', '$scheme', '$timeNow')";
			if (!($GLOBALS['conn']->query($sql))) {
			
				$sql = "UPDATE tempchatPool SET poolAddress='$poolAddress', scheme='$scheme', timeNow='$timeNow' WHERE from_id='$from_id_hashed'";
				if (!($GLOBALS['conn']->query($sql))) {
					RETURN 'pool name already present';
				}else{
				RETURN TRUE;
				}
			}else{
				RETURN TRUE;
			}
	
		}else{
			RETURN 'pool not valid';
		}
	}else{
		RETURN 'not a valid pool';
	}
}
##############################################################
# CHECK IF WALLET ADDRESS IS VALID
##############################################################
function checkWalletAddress($walletAddress, $from_id_hashed){
#global $conn;
#$GLOBALS['conn']

$query = "SELECT poolAddress,scheme FROM tempchatPool WHERE from_id = '$from_id_hashed'";
$result = mysqli_query($GLOBALS['conn'], $query);
$row = mysqli_fetch_assoc($result);
if ($row == false){
  RETURN 'user not found';
}
mysqli_free_result($result);

# $row['poolAddress']

	if (isset($row['poolAddress'])){
		#now we call curl and check link if wallet is valid
		
		$getWalletInfo = getApiData($row['scheme'].$row['poolAddress']."/api/wallet?address=".$walletAddress);	
		if($getWalletInfo === FALSE) {
			RETURN 'error with wallet url '.$poolAddress;
		}
		
		$getWalletInfo = json_decode($getWalletInfo, TRUE);
		
		if (isset($getWalletInfo)){

			if (isset($getWalletInfo['balance'])){
				#valid pool add to db and continue
				
				#encrypt the poolAddress
				$walletAddressEncoded = strrev( base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($from_id_hashed), $walletAddress, MCRYPT_MODE_CBC, md5(sha1($GLOBALS['botID'])))) );

				if (substr($walletAddressEncoded,0,2) == '=='){
					$walletAddressEncoded = substr($walletAddressEncoded,2);
				}

				#save coin currency
				$currency = $getWalletInfo['currency'];
				
				#test($GLOBALS['url'], $GLOBALS['chat_id'], $GLOBALS['message_id'],$currency);	
				
				$sql = "UPDATE tempchatPool SET walletAddress='$walletAddressEncoded',currency='$currency' WHERE from_id='$from_id_hashed'";
				if (!($GLOBALS['conn']->query($sql))) {
				RETURN 'cant update wallet';
				}else{
				RETURN TRUE;
				}
							
			}else{
				RETURN "wallet not ok";
			}
		}else{
			RETURN "no wallet found";
		}

	}else{
		RETURN "can't get wallet.";
	}

}
##############################################################
# GET API INFO AND SEND BACK RESULTS
##############################################################
function getApiData($urlToSend){
	
	if (!$curld = curl_init()) {
	exit;
	}
	
	curl_setopt($curld, CURLOPT_POST, FALSE);
	curl_setopt($curld, CURLOPT_URL,$urlToSend);
	curl_setopt($curld, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curld, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curld, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($curld, CURLOPT_TIMEOUT, 5);
	
	$output = curl_exec($curld);
	curl_close ($curld);
	return $output;
}
?>