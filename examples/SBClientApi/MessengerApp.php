<?php
require_once('../../SBClientSDK/SBApp.php');
/**
 * Messenger application
 *
 * Says hello to any user who sends a message to it
 * @author Spotbros <support@spotbros.com>
 */
class MessengerApp extends SBApp
{
	protected function onError($errorType_){}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){}
	protected function onNewContactSubscription(SBUser $sbUser_){}
	protected function onNewContactUnSubscription(SBUser $sbUser_){}
	protected function onNewMessage(SBMessage $message_)
	{
		// destination user's sbcode
		$sbcode = "TESTSBC";
		// send message to user
		if (!$this-> sendTextMessageOrFalse("Hello there!", $sbcode))
		{
			print "Could not send message to the user with sbcode: ".$sbcode;
		}
	}
}

$messengerApp = new MessengerApp($messengerAppSBCode,$messengerAppKey);
$messengerApp->serveRequest($_GET["params"]);
?>