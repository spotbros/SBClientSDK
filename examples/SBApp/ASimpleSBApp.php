<?php
require_once('../SBClientSDK/SBApp.php');
/** 
 * A simple SBApp
 * 
 * Sample application which handles every possible event type
 * @author Spotbros <support@spotbros.com> 
 */ 
class ASimpleSBApp extends SBApp 
{
	protected function onError($errorType_)
	{
		$applicationCreatorSBCode = "APPCREA";
		if(!($this->sendTextMessageOrFalse("Error with code: ".$errorType_,$applicationCreatorSBCode))) 
		{error_log ("Could not reply to the user");}
	} 
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_)
	{
		if(!($this->replyOrFalse("Thanks for your vote!"))) 
		{error_log ("Could not reply to the user");}
	} 
	protected function onNewContactSubscription(SBUser $sbUser_)
	{
		if(!($this->replyOrFalse("Hello! Thanks for subscribing!"))) 
		{error_log ("Could not reply to the user");}
	} 
	protected function onNewContactUnSubscription(SBUser $sbUser_)
	{
		if(!($this->replyOrFalse("Goodbye!"))) 
		{error_log ("Could not reply to the user");}
	} 
	protected function onNewMessage(SBMessage $message_) 
	{
		if(!($this->replyOrFalse("I just received a message from you!"))) 
		{error_log ("Could not reply to the user");}	 
	}
} 
$aSimpleSBApp = new ASimpleSBApp($aSimpleSBAppSBCode,$aSimpleSBAppKey); 
$aSimpleSBApp->serveRequest($_GET["params"]); 
?>