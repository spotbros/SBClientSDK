<?php
require_once(dirname(__FILE__).'/php-classes/class_CurlMngr.php');
require_once(dirname(__FILE__).'/includes/SBTypes.php');
require_once(dirname(__FILE__).'/php-classes/class_SBMessage.php');
require_once(dirname(__FILE__).'/php-classes/class_SBAttachments.php');
require_once(dirname(__FILE__).'/includes/SBFunctions.php');
/**
 * SBClientApi
 * 
 * Parent class of any SBApp
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
class SBClientApi
{
	/**
	 * App's sbcode
	 * @var string
	 */
  protected $_appSBCode;
  /**
   * App's key
   * @var string
   */
  protected $_appKey;
	/**
	 * CurlMngr instance to handle outgoing web requests and incoming responses
	 * @var CurlMngr
	 */
  public  $_curlMngr;
  /**
   * Instance of SBMessage
   * @var SBMessage
   */
  protected $_SBMessage;
  /**
   * Instance of SBAttachments
   * @var SBAttachments
   */
  protected  $_SBAttachments;
  /**
   * Creates a new instance of SBClientApi
   * @param string $appSBCode_
   * @param string $appKey_
   */
  public function __construct($appSBCode_,$appKey_)
  {
    $this->_appKey=$appKey_;
    $this->_appSBCode=$appSBCode_;
    $this->_curlMngr=CurlMngr::getInstance();
    $this->_SBMessage=new SBMessage($appSBCode_,$appKey_);
    $this->_SBAttachments=new SBAttachments($appSBCode_, $appKey_);
  }
    
  /**
   * Send a text message to a group of App followers. If attachments are set, then they will be automatically embedded
   * into the message as a SBMail.
   * @param string $msgText_	The text of the message to be sent
   * @param string $toSBCode_	The SBCode of the App follower who will receive the message
   * @return array|false with values (V1=date in ms,V2=message Id,V3=true if app received message, false if just server,V4=Msg unique Id)
   */
  public function sendTextMessageOrFalse($msgText_,$toSBCode_)
  {
    return $this->sendTextMessageToGroupOrFalse($msgText_, array($toSBCode_));
  }
  
  /**
   * Send a text message to a group of App followers. If attachments are set, then they will be automatically embedded
   * into the message as a SBMail.
   * @param string $msgText_				The text of the message to be sent
   * @param array $toSBCodes_				The SBCodes of the SBApp followers who will receive the message
   * @return Array of DeliveryStatus (as per sendTextMessageOrFalse) or False
   */
  public function sendTextMessageToGroupOrFalse($msgText_,Array $toSBCodes_)
  {
  	if(mb_strlen($msgText_,'UTF-8')>SBConstants::MAX_TEXT_SIZE)
  	{
  		$this->_SBAttachments->addExtendedText(mb_substr($msgText_, SBConstants::MAX_TEXT_SIZE));
  		$msgText_=mb_substr($msgText_, 0,SBConstants::MAX_TEXT_SIZE);
  	}
  	$params=array(
  			"appSBCode"=>$this->_appSBCode,
  			"appKey"=>$this->_appKey,
  			"toSBCodes"=>json_encode($toSBCodes_),
  			"msgText"=>$msgText_,
  			"msgUniqueId"=>md5(rand(0,100000000).rand(0,100000000).rand(0,100000000).rand(0,100000000).microtime(1))
  	);
  	if(count($attachmentRefs = $this->_SBAttachments->getAttachmentRefs())>0)
  	{
  		$params["attachments"]=json_encode($attachmentRefs,true);
  	}
  	
  	$handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/sendSBMessage.php",$params,30000);
  	if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(30000))!=false)
  	{
  		if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
  		{
  			return (($responseData=json_decode($responses[$handlerId],true))!=null)&&(isset($responseData["CID"]) && $responseData["CID"]=="SBMailSentOk")?$responseData:false;
  		}
  	}
  	return false;
  }
  /**
   * Forwards a message to a group of SBApp followers.
   * @param string $SBMessageId_	the id of the message to be forwarded
   * @param array $toSBCodes_		the SBCodes of the SBApp followers who will receive the message
   * @return Array of DeliveryStatus (as per sendTextMessageOrFalse) or False
   */
  public function forwardSBMessageOrFalse($SBMessageId_,Array $toSBCodes_)
  {
  	$msgUniqueIds = array();
  	foreach($toSBCodes_ as $sbcode)
  	{
  		$msgUniqueIds[] = md5(rand(0,100000000).rand(0,100000000).rand(0,100000000).rand(0,100000000).microtime(1));
  	}
  	 
  	$params=array(
  			"appSBCode"=>$this->_appSBCode,
  			"appKey"=>$this->_appKey,
  			"messageId"=>$SBMessageId_,
  			"toSBCodes"=>json_encode($toSBCodes_),
  			"msgUniqueIds"=>json_encode($msgUniqueIds),
  	);
  	$handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/forwardSBMessage.php",$params,1000);
  	if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(1000))!=false)
  	{
  		if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
  		{
  			return (($responseData=json_decode($responses[$handlerId],true))!=null)&&(isset($responseData["CID"]) && $responseData["CID"]=="ForwardOk")?$responseData:false;
  		}
  	}
  	return false;
  }
  /**
   * Gets this app's followers' sbcodes from firstSBCode_ (if informed)
   * @return Ambigous <boolean, mixed>|boolean	An array with the followers' sbcodes or false if there was any error getting it
   */
  public function getFollowerSBCodesOrFalse($firstSBCode_='')
  {
    $params=array(
                  "appSBCode"=>$this->_appSBCode,
                  "appKey"=>$this->_appKey,
    							"firstSBCode"=>$firstSBCode_
                 );
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/getFollowerSBCodes.php",$params,30000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(30000))!=false)
    {
      if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
      {
        if(($responseData=json_decode($responses[$handlerId],true))!=null)
        {
          if(
              isset($responseData["CID"]) &&
              $responseData["CID"]=="FollowerSBCodes" &&
              isset($responseData["V1"])
            )
          {
            return is_array($responseData["V1"])?$responseData["V1"]:false;
          }
        }
      }
    }
    return false;
  }
  /**
   * Gets the number of followers for the current SBApp
   * @return integer|false The number of followers or false if any error occurs
   */
  public function getFollowerNumOrFalse()
  {
    $params=array(
			            "appSBCode"=>$this->_appSBCode,
			            "appKey"=>$this->_appKey
			  				  );
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/getFollowerSBCodes.php",$params,1000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(1000))!=false)
    {
      if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
      {
        if(($responseData=json_decode($responses[$handlerId],true))!=null)
        {
          if(
	            isset($responseData["CID"]) &&
              $responseData["CID"]=="FollowerSBCodes" &&
              isset($responseData["V2"])
          	)
          {
            return is_numeric($responseData["V2"])?$responseData["V2"]:false;
          }
        }
      }
    }
    return false;
  }
}
?> 