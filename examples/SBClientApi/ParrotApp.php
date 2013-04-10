<?php
require_once('../SBClientSDK/SBApp.php');
/**
 * Parrot application
 * 
 * Replies to every message with “Hello!”
 * @author Spotbros <support@spotbros.com>
 */
class ParrotApp extends SBApp
{
	protected function onError($errorType_){}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){}
	protected function onNewContactSubscription(SBUser $sbUser_){}
	protected function onNewContactUnSubscription(SBUser $sbUser_){}
	protected function onNewMessage(SBMessage $message_)
	{
		if(!($this->replyOrFalse("Hello!")))
		{print ("Could not reply to the user");}	
	}
}

$parrotApp = new ParrotApp($parrotAppSBCode,$parrotAppKey);
$parrotApp->serveRequest($_GET["params"]);
?>