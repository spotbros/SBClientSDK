<?php
require_once('../SBClientSDK/SBPersistentApp.php');
/** 
 * Content Cacher App
 * 
 * Sample application which caches the length some external content
 * @author Spotbros <support@spotbros.com> 
 */ 
class ContentCacherApp extends SBPersistentApp
{
	protected function onError($errorType_){} 
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){} 
	protected function onNewContactSubscription(SBUser $user_){}
	protected function onNewContactUnSubscription(SBUser $user_){} 
	protected function onNewMessage(SBMessage $message_) 
	{
		if($msgText=$message_->getSBMessageTextOrFalse() && strtolower($msgText)=="cache")
		{
			if(!($helpMessage = $this->getOrFalse("cached_content")))
			{
				$theContents = file_get_contents("http://www.spotbros.com");
				$theContentsLength = strlen($theContents);
				$this->setOrFalse("cached_content",$theContentsLength);
			}
			if(!($this->replyOrFalse($helpMessage))) 
			{error_log ("Could not reply to the user");}
		}
		else
		{
			error_log ("Could not retrieve message text");
		}
	}
} 
$contentCacherApp = new HelpCacherApp($contentCacherAppSBCode,$contentCacherAppKey); 
$contentCacherApp->serveRequest($_GET["params"]); 
?>