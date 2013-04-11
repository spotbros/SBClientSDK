<?php
require_once('../../SBClientSDK/SBApp.php');
/**
 * Tell me followers application
 * 
 * Reports the number of followers it has to the application creator when a 
 * new user subscribes to the application
 * @author Spotbros <support@spotbros.com>
 */
class TellMeFollowersApp extends SBApp
{
	protected function onError($errorType_){}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){}
	protected function onNewContactSubscription(SBUser $sbUser_)
	{
		$applicationCreatorSBCode = "APPCREA";
		if(!($nFollowers = $this->getFollowerNumOrFalse()))
		{error_log ("Could not get follower's number");}
		if (!($this-> sendTextMessageOrFalse("New follower!Total is: ".$nFollowers, $applicationCreatorSBCode)))
		{error_log ("Could not send message");}
	}
	protected function onNewContactUnSubscription(SBUser $sbUser_){}
	protected function onNewMessage(SBMessage $message_){}
}

$tellMeFollowersApp = new TellMeFollowersApp($groupMessengerAppSBCode,$groupMessengerAppKey);
$tellMeFollowersApp->serveRequest($_GET["params"]);
?>