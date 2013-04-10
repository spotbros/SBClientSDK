<?php
require_once('../SBClientSDK/SBClientApi.php');
/** 
 * Daily Winner notificator
 * 
 * Proactive SBApp to notify a random user that he/she has won a prize
 * @author Spotbros <support@spotbros.com> 
 */ 
class DailyWinnerNotificator extends SBClientApi 
{ 
	private function getTodaysWinnerOrFalse()
	{
		if(($followers = $this->getFollowerSBCodesOrFalse()))
		{
			return $followers[array_rand($followers)];
		}
		return false;
	}
	public function notifyTodaysWinnerOrFalse()
	{
		if($todaysWinnerSBCode = $this->getTodaysWinnerOrFalse())
		{
			$todaysWinner = new SBUser($this->_appSBCode, $this->_appKey);
			$todaysWinner->loadUserBySBCodeOrFalse($todaysWinnerSBCode);
			$todaysWinnerName = $todaysWinner->getSBUserNameOrFalse();
			$todaysWinnerRedeemCode = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 5);
			$this->sendTextMessageOrFalse("Congrats ".$todaysWinnerName."! You are today's winner! Write the code ".$todaysWinnerRedeemCode." to know about your prize",$todaysWinnerSBCode);
		}
	}
}
$winnerNotificator = new DailyWinnerNotificator($winnerNotificatorAppSBCode,$winnerNotificatorAppKey); 
$winnerNotificator->notifyTodaysWinnerOrFalse(); 
?>