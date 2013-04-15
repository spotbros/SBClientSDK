<?php
require_once(dirname(__FILE__).'/SBClientApi.php');
require_once(dirname(__FILE__).'/php-classes/class_SBMessage.php');
require_once(dirname(__FILE__).'/php-classes/class_SBAttachments.php');
/**
 * SBApp
 *
 * @author Spotbros <support@spotbros.com>
 *
 */
abstract class SBApp extends SBClientApi
{
	
	private $_fromUserSBCode = false;
	
	
	/**
	 * Creates a new instance of SBApp
	 * @param string $appSBCode_	the SBApp's sbcode
	 * @param string $appKey_	the SBApp's key
	 */
	public function __construct($appSBCode_,$appKey_)
	{
		parent::__construct($appSBCode_, $appKey_);
	}

  /**
   * Validate if all parameters are present and the request looks as it should
   * @param array $params_	the parameters to be validated
   * @return boolean true if every parameter is set and false if not
   */
  private function isValidParams(Array $params_)
  {
    if(isset($params_["eventType"]))
    {
      switch($params_["eventType"])
      {
        case SBAppEventType::NEW_MESSAGE:
          {
            return (
                    isset($params_["SBMessageId"]) &&
                    isset($params_["userEmail"]) &&
                    isset($params_["userPhoneKey"]) &&
                    isset($params_["userLatitude"]) &&
                    isset($params_["userLongitude"]) &&
                    isset($params_["userLanguage"])
                    );
          }
        case SBAppEventType::NEW_CONTACT_SUBSCRIPTION:
        case SBAppEventType::NEW_CONTACT_UNSUBSCRIPTION:
          {
            return (
                    isset($params_["userName"]) &&
                    isset($params_["userLastName"]) &&
                    isset($params_["userSBCode"]) &&
                    isset($params_["userEmail"]) &&
                    isset($params_["userPhoneKey"]) &&
                    isset($params_["userLatitude"]) &&
                    isset($params_["userLongitude"]) &&
                    isset($params_["userLanguage"]) 
                   );
          }
        case SBAppEventType::NEW_VOTE:
        	{
        		return (
                    isset($params_["userName"]) &&
                    isset($params_["userLastName"]) &&
                    isset($params_["userSBCode"]) &&
                    isset($params_["userEmail"]) &&
                    isset($params_["userPhoneKey"]) &&
                    isset($params_["userLatitude"]) &&
                    isset($params_["userLongitude"]) &&
                    isset($params_["userLanguage"]) &&
        						isset($params_["rating"]) &&
        						isset($params_["vote"])
        					);
        	}
      }
    }
    return true;
  }
  
  /**
   * This function receives $_GET["params"], parses it and invokes the appropriate callBacks on the target SBApp
   * @param  Name of the return + N parameters
   * @return void
   */
  public function serveRequest($params_)
  {
    if((($requestData=json_decode($params_,true))!=null) && $this->isValidParams($requestData))
    {
     switch($requestData["eventType"])
      {
        case SBAppEventType::NEW_MESSAGE:
          {
            if($this->_SBMessage->loadSBMessageBySBMessageIdOrFalse($requestData["SBMessageId"]))
            {
              $fromUser=$this->_SBMessage->getSBMessageFromUserOrFalse();
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              $this->_fromUserSBCode = $fromUser->getSBUserSBCodeOrFalse();
              $this->onNewMessage($this->_SBMessage);
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_MESSAGEID);
            }
            break;
          }
        case SBAppEventType::NEW_CONTACT_SUBSCRIPTION:
          { 
            $fromUser=new SBUser($this->_appSBCode,$this->_appKey);
            if($fromUser->loadUserBySBCodeOrFalse($requestData["userSBCode"]))
            {
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              $this->_fromUserSBCode = $fromUser->getSBUserSBCodeOrFalse();
              $this->onNewContactSubscription($fromUser);
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_USER);
            }
            break;
          }
        case SBAppEventType::NEW_CONTACT_UNSUBSCRIPTION:
          {
            $fromUser=new SBUser($this->_appSBCode,$this->_appKey);
            if($fromUser->loadUserBySBCodeOrFalse($requestData["userSBCode"]))
            {
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              $this->_fromUserSBCode = $fromUser->getSBUserSBCodeOrFalse();
              $this->onNewContactUnSubscription($fromUser);
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_USER);
            }
            break;
          }
        case SBAppEventType::NEW_VOTE:
          {
            $fromUser=new SBUser($this->_appSBCode,$this->_appKey);
            if($fromUser->loadUserBySBCodeOrFalse($requestData["userSBCode"]))
            {
              $fromUser->setSBUserEmail($requestData["userEmail"]);
              $fromUser->setSBUserLocation($requestData["userLatitude"], $requestData["userLongitude"]);
              $fromUser->setSBUserPhoneKey($requestData["userPhoneKey"]);
              $fromUser->setSBUserLanguage($requestData["userLanguage"]);
              if(is_array($rating=$requestData["rating"]))
              {
              	$this->_fromUserSBCode = $fromUser->getSBUserSBCodeOrFalse();
                $this->onNewVote($fromUser,$requestData["vote"],$rating["oldRating"],$rating["newRating"]);
              }
              else
              {$this->onError(SBErrors::WRONG_PARAMS_FORMAT_ERROR);}
            }
            else
            {
              $this->onError(SBErrors::UNABLE_TO_LOAD_USER);
            }
            break;            
          }
          case SBAppEventType::NEW_PING:
          {
          		printJson("PingOk");
          }
      }
    }
    else
    {$this->onError(SBErrors::WRONG_PARAMS_FORMAT_ERROR);}
  }
  /**
   * Sends a message to the user who generated the last event (either SBAppEventType::NEW_MESSAGE, SBAppEventType::NEW_CONTACT_SUBSCRIPTION,
   * SBAppEventType::NEW_CONTACT_UNSUBSCRIPTION or SBAppEventType::NEW_VOTE)
   * @param string	$msgText_ the text of the response message
   * @return array|false the result of the reply or false on error
   */
  public function replyOrFalse($msgText_)
  {
    if($this->_fromUserSBCode)
    {
      return $this->sendTextMessageOrFalse($msgText_, $this->_fromUserSBCode);
    }
    return false;
  }
  protected abstract function onNewVote(SBUser $user_,$newVote_,$oldRating_,$newRating_);
  protected abstract function onNewContactSubscription(SBUser $user_);
  protected abstract function onNewContactUnSubscription(SBUser $user_);
  protected abstract function onNewMessage(SBMessage $msg_);
  protected abstract function onError($errorType_);
}
?> 