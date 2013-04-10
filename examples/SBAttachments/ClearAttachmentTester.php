<?php 
require_once('../SBClientSDK/SBApp.php');
/**
 * Clear attachment tester application
 *
 * Sends messages with different attachments to different users
 * @author Spotbros <support@spotbros.com>
 */
class ClearAttachmentTesterApp extends SBApp
{
	protected function onError($errorType_)
	{
		error_log($errorType_);
	}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){
	}
	protected function onNewContactSubscription(SBUser $sbUser_){
	}
	protected function onNewContactUnSubscription(SBUser $sbUser_){
	}
	protected function onNewMessage(SBMessage $message_)
	{
		// destination users' sbcodes
		$sbcodeA = "TESTSB1";
		$sbcodeB = "TESTSB2";
		// set attachments for recipient A
		$this->_SBAttachments->addTitleOrFalse("This is a title");
		$this->_SBAttachments->addParagraphOrFalse("And this is a paragraph");
		// send message to recipient A
		if (!$this-> sendTextMessageOrFalse("Hello A!", $sbcodeA))
		{
			error_log ("Could not send message to the user with sbcode: ".$sbcodeA);
		}
		$this->_SBAttachments->clearAttachments();
		// set attachments for recipient B
		$this->_SBAttachments->addTitleOrFalse("This is another title");
		$this->_SBAttachments->addParagraphOrFalse("And this is another paragraph");
		// send message
		if (!$this-> sendTextMessageOrFalse("Hello B!", $sbcodeB))
		{
			error_log ("Could not send message to the user with sbcode: ".$sbcodeB);
		}
	}
}
$clearAttachmentTesterApp = new ClearAttachmentTesterApp($clearAttachmentTesterAppSBCode,$clearAttachmentTesterAppKey);
$clearAttachmentTesterApp->serveRequest($_GET["params"]);
?>