<?php
require_once('../../SBClientSDK/SBApp.php');
/** 
 * URL Shortener App using bit.ly API
 * 
 * Sample application which shows how to use CurlMngr
 * @author Spotbros <support@spotbros.com> 
 */ 
class URLShortenerApp extends SBApp 
{
	// you must obtain a user name and key from bit.ly
	private $_bitlyUserName = "";
	private $_billyAPIKey = "";
	
	protected function onError($errorType_){} 
	protected function onNewVote(SBUser $sbUser_, $newVote_, $oldRating_, $newRating_){}
	protected function onNewContactSubscription(SBUser $sbUser_){} 
	protected function onNewContactUnSubscription(SBUser $sbUser_){} 
	protected function onNewMessage(SBMessage $message_) 
	{
		if(($shortURL = $this->getShortURLOrFalse($message_->getSBMessageTextOrFalse())))
		{
			if(!($this->replyOrFalse("Short URL: ".$shortURL))) 
			{error_log ("Could not reply to the user");}
		}
		else
		{
			error_log ("Could not reply to the user");
		}
	}
	/**
	 * URL shortener. Uses bit.ly API
	 * @param string $url_	the URL to be shortened
	 * @return string|false	the shortened URL or false if it could not by shortened
	 */
	private function getShortURLOrFalse($url_)
	{
		$apiURL = 'http://api.bit.ly/v3/shorten?login='.$this->_bitlyUserName.'&apiKey='.$this->_billyAPIKey.'&uri='.urlencode($url_).'&format=txt';
		if(($handlerId = $this->_curlMngr->queryStringThisUrlOrFalse($apiURL))
				&& ($responses = $this->_curlMngr->getResponsesWhenReadyOrFalse(2000)))
		{
			return $responses[$handlerId];
		}
		return false;
	}
} 
$urlShortenerApp = new URLShortenerApp($urlShortenerAppSBCode,$urlShortenerAppKey); 
$urlShortenerApp->serveRequest($_GET["params"]); 
?>