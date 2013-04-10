<?php
require_once('../SBClientSDK/SBClientApi.php');
/**
 * Parrot application
 *
 * Replies to every message with “Hello!”
 * @author Spotbros <support@spotbros.com>
 */
class InitUser extends SBClientApi
{
	public function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){
	}
	public function onNewContactSubscription(SBUser $sbUser_){
	}
	public function onNewContactUnSubscription(SBUser $sbUser_){
	}
	public function onNewMessage(SBMessage $message_)
	{
		// create an empty SBUser
		$theUser = new User();
		// initialize the SBUser with all its attributes
		$userSBCode_ = "TESTSBC";
		$userName_ = "Foo";
		$userLastName_ = "Bar";
		$userGender_ = "M";
		$userProfilePicMD5_ = "10cfc15610623928bd3a36ce44d7b87c";
		$userRating_ = "5.0";
		$userEmail_ = "foobar@bardomain.com";
		$userPhoneKey_ = "00017737887999";
		$userLatitude_ = 80;
		$userLongitude_ = 80;
		$userLanguage_ = "EN";
		$theUser->initUser(
				$userSBCode_,
				$userName_,
				$userLastName_,
				$userGender_,
				$userProfilePicMD5_,
				$userRating_,
				$userEmail_,
				$userPhoneKey_,
				$userLatitude_,
				$userLongitude_,
				$userLanguage_
		);
	}
}
	$InitUser = new ParrotApp($parrotAppSBCode,$parrotAppKey);
	$InitUser->serveRequest($_GET["params"]);
	?>