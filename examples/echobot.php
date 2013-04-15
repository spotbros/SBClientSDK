<?php
require_once('../SBClientSDK/SBApp.php');
class EchoBot extends SBApp
{
	protected function onError($errorType_)
	{
		error_log($errorType_);
	}
	protected function onNewVote(SBUser $user_,$newVote_,$oldRating_,$newRating_)
	{
		$this->replyOrFalse("Thanks for voting me with ".$newVote_." stars");
	}
	protected function onNewContactSubscription(SBUser $user_)
	{
		if(($userName = $user_->getSBUserNameOrFalse()))
		{
			$this->replyOrFalse("Hi ".$userName."! Welcome to echobot!");
		}
	}
	protected function onNewContactUnSubscription(SBUser $user_)
	{
		if(($userName = $user_->getSBUserNameOrFalse()))
		{
			error_log($userName." just unsubscribed");
		}
	}
	protected function onNewMessage(SBMessage $msg_)
	{
		if(($messageText = $msg_->getSBMessageTextOrFalse()))
		{
			$this->replyOrFalse("You just told me: ".$messageText);
		}
	}
}
// Create a new SBApp on dev.spotbros.com and copy-paste your SBCode and key
$echoBotSBCode = "[SBCode]";
$echoBotKey = "[key]";
$echoBot=new EchoBot($echoBotSBCode,$echoBotKey);
$echoBot->serveRequest($_GET["params"]);
?>
