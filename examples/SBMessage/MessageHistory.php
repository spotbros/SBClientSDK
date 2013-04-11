<?php
require_once('../../SBClientSDK/SBClientApi.php');
/**
 * Message Sender History
 *
 * Prints the message's sender for each messageId stored in sbmessageHistory
 * @author Spotbros <support@spotbros.com>
 */

function fileReader($filePath_)
{
	$fileContents = array();
	$fh = fopen($filePath_, "r");
	while (!feof($fh)) {
		$lineChunks = explode(",",fgets($fh));
		$fileContents[] = array("sbMessageId"=>$lineChunks[0], "sbMessageDate"=>$lineChunks[1]);
	}
	fclose($fh);
	return $fileContents;
}

$sbMessage = new $SBMessage($yourAppSBCode,$yourAppKey);
$sbMessageIdsDates = fileReader("./sbmessageHistory.txt");
foreach($sbMessageIdsDates as $item)
{
	$sbMessageId = $item["sbMessageId"];
	$sbMessageDate = $item["sbMessageDate"];
	if($sbMessage->loadSBMessageBySBMessageIdOrFalse())
	{
		if($sbMessageSender = $sbMessage->getSBMessageFromUserOrFalse())
		{
			$sbMessageSenderFullName = $sbMessageSender->getSBUserNameOrFalse()." ".$sbMessageSender->getSBUserLastNameOrFalse();
			print ("\tThe message was sent by: ".$sbMessageSenderFullName."\n");
		}
		else
		{
			print ("Could not load the message sender\n");
		}
	}
	else
	{
		print ("Could not load the message\n");
	}
}
?>