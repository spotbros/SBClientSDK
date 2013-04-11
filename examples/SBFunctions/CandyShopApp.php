<?php
require_once('../../SBClientSDK/SBApp.php');
/** 
 * Candy Shop App
 * 
 * Sample application which shows how to use the function findMostSimilarWord
 * Whatever thing the user writes, the application has an answer
 * @author Spotbros <support@spotbros.com> 
 */ 
class CandyShopApp extends SBApp 
{
	private $_availableFlavours = array(
			"strawberry" => 0.05,
			"banana" => 0.03,
			"orange" => 0.04,
			"pear" => 0.02,
			"watermelon" => 0.02,
			"melon" => 0.05,
			"peach" => 0.06,
			"kiwi" => 0.03);
	protected function onError($errorType_){} 
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){} 
	protected function onNewContactSubscription(SBUser $sbUser_)
	{
		if(!($this->replyOrFalse("Hello! Welcome to the candy shop! Tell me your favourite flavour and I will tell you what's the price for that candy"))) 
		{error_log ("Could not reply to the user");}
	} 
	protected function onNewContactUnSubscription(SBUser $sbUser_){} 
	protected function onNewMessage(SBMessage $message_) 
	{
		// find the most similar flavour from the user input
		$bestGuess = findMostSimilarWord($message_->getSBMessageTextOrFalse(), array_keys($this->_availableFlavours));
		if(!($this->replyOrFalse("Did you mean ".$bestGuess[1]." candy (".$bestGuess[2]."% similar)? In that case, its price tag is $".$this->_availableFlavours[$bestGuess[1]]))) 
		{error_log ("Could not reply to the user");}	 
	}
}
$candyShopApp = new CandyShopApp($candyShopAppSBCode,$candyShopAppKey); 
$candyShopApp->serveRequest($_GET["params"]); 
?>