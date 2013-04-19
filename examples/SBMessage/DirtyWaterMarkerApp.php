<?php 
require_once('../../SBClientSDK/SBApp.php');
/**
 * Water marker application
 *
 * The application draws a 30x30px square on the upper-left corner of the user's image and sends it back to the user
 * @author Spotbros <support@spotbros.com>
 */
class DirtyWaterMarkerApp extends SBApp
{
	protected function onError($errorType_){
	}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){
	}
	protected function onNewContactSubscription(SBUser $sbUser_){
	}
	protected function onNewContactUnSubscription(SBUser $sbUser_){
	}
	protected function onNewMessage(SBMessage $message_)
	{
		$attachments = $message_->getSBMessageAttachmentsOrFalse();
		$imageURL = '';
		foreach($attachments as $attachment)
		{
			if($attachment["attachmentType"]==SBAttachmentType::IMAGE)
			{
				$imageURL = $attachment["attachmentPayload"];
				break;
			}
		}
		if($imageURL!=='' && ($imagePath = $this->_curlMngr->downloadFileOrFalse($imageURL)))
		{
			if($waterMarkedImagePath = $this->drawWaterMark($imagePath))
			{
				$this->_SBAttachments->addImageOrFalse($waterMarkedImagePath);
				$this->replyOrFalse("This is your watermarked image");
			}
			else
			{
				$this->replyOrFalse("Your image could not be watermarked");
			}
		}
		else
		{
			$this->replyOrFalse("Your image could not be retrieved. You must send an image");
		}
	}
	private function drawWaterMark($originalImagePath_)
	{
		$waterMarkedImagePath = substr(MD5(microtime()), 0, 8).".jpg";
		$originalImage = imagecreatefromjpeg($originalImagePath_);
		$waterMark = imagecreatetruecolor(30, 30);
		imagecopyresampled($originalImage, $waterMark, 30, 30, 0, 0, imagesx($waterMark), imagesy($waterMark), imagesx($originalImage), imagesy($originalImage));
		if(imagejpeg($originalImage,$waterMarkedImagePath))
		{
			return $waterMarkedImagePath;
		}
		return false;
	}
}
$dirtyWaterMarkerApp = new DirtyWaterMarkerApp($dirtyWaterMarkerApp,$dirtyWaterMarkerApp);
$dirtyWaterMarkerApp->serveRequest($_GET["params"]);
?>