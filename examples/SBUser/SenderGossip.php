<?php
require_once('../SBClientSDK/SBApp.php');
/** 
 * Sender gossip application
 * 
 * Replies the user with his/her information
 * @author Spotbros <support@spotbros.com> 
 */ 
class SenderGossipApp extends SBApp 
{ 
	protected function onError($errorType_){}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){} 
	protected function onNewContactSubscription(SBUser $sbUser_){} 
	protected function onNewContactUnSubscription(SBUser $sbUser_){} 
	protected function onNewMessage(SBMessage $message_)
	{
		// create new SBUser instance
		$senderSBUser = $message_->getSBMessageFromUserOrFalse();
		
		// create string with user information
		$userInfo = "Here you have your information\n";
		$userInfo .= "\tUser sbcode: ".$senderSBUser->getSBUserSBCodeOrFalse()."\n";
		$userInfo .= "\tUser name: ".$senderSBUser->getSBUserNameOrFalse()."\n";
		$userInfo .= "\tUser last name: ".$senderSBUser->getSBUserLastNameOrFalse()."\n";
		$userInfo .= "\tUser gender: ".$senderSBUser->getSBUserGenderOrFalse()."\n";
		$userInfo .= "\tUser profile picture MD5: ".$senderSBUser->getSBUserProfilePicMD5OrFalse()."\n";
		$userInfo .= "\tUser rating: ".$senderSBUser->getSBUserRatingOrFalse()."\n";
		$userInfo .= "\tUser email: ".$senderSBUser->getSBUserEmailOrFalse()."\n";
		$userInfo .= "\tUser phone key: ".$senderSBUser->getSBUserPhoneKeyOrFalse()."\n";
		$userInfo .= "\tUser location: ".$senderSBUser->getSBUserLocationOrFalse()."\n";
		
		$this->replyOrFalse($userInfo);
	}
}
$senderGossipApp = new SenderGossipApp($senderGossipAppSBCode,$senderGossipAppKey); 
$senderGossipApp->serveRequest($_GET["params"]); 
?>