<?php 
require_once('../../SBClientSDK/SBApp.php');
/**
 * Attachment tester application
 *
 * Sends a message with all the possible attachments to the user who writes to the application
 * @author Spotbros <support@spotbros.com>
 */
class AttachmentTesterApp extends SBApp
{
	protected function onError($errorType_){}
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){}
	protected function onNewContactSubscription(SBUser $sbUser_){}
	protected function onNewContactUnSubscription(SBUser $sbUser_){}
	protected function onNewMessage(SBMessage $message_)
	{
		// add title
		$this->_SBAttachments->addTitleOrFalse("Plaza de Cibeles");
		
		// download image so it can be attached
		$cibelesImagePath = $this->_curlMngr->downloadFileOrFalse("http://upload.wikimedia.org/wikipedia/commons/0/03/Cibeles_con_Palacio_de_Linares_al_fondo.jpg");
		// add image attachment, set timeout to 10 seconds and delete after being uploaded
		$this->_SBAttachments->addImageOrFalse($cibelesImagePath,10000,true);
		
		// add paragraph with some information from wikipedia
		$this->_SBAttachments->addParagraphOrFalse("The Plaza de Cibeles is a square with a neo-classical complex of marble sculptures with fountains that has become an iconic symbol for the city of Madrid. It sits at the intersection of Calle de Alcalá (running from east to west), Paseo de Recoletos (to the North) and Paseo del Prado (to the south). Plaza de Cibeles was originally named Plaza de Madrid, but in 1900, the City Council named it Plaza de Castelar, which was eventually replaced by its current name");
		// add map showing where "Plaza de Cibeles" is
		$this->_SBAttachments->addMapOrFalse(40.4192, -3.6931);
		// add link to the Wikipedia article about it
		$this->_SBAttachments->addLinkOrFalse("http://en.wikipedia.org/wiki/Plaza_de_Cibeles");
		// add quote
		$this->_SBAttachments->addQuoteOrFalse("From Wikipedia, the free encyclopedia");
		// send message to recipient A
		if (!$this->replyOrFalse("Plaza de cibeles"))
		{
			error_log ("Could not send message to the user");
		}
	}
}
$attachmentTesterApp = new AttachmentTesterApp($attachmentTesterAppSBCode,$attachmentTesterAppKey);
$attachmentTesterApp->serveRequest($_GET["params"]);
?>