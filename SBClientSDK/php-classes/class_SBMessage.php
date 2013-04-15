<?php
require_once(dirname(__FILE__).'/class_CurlMngr.php');
require_once(dirname(__FILE__).'/class_SBUser.php');
require_once(dirname(__FILE__).'/../includes/SBFunctions.php');
/**
 * SBMessage
 * 
 * Describes a Spotbros message
 * 
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
class SBMessage
{
	/**
	 * App's sbcode
	 * @var string
	 */
  private $_appSBCode;
  /**
   * App's key
   * @var string
   */
  private $_appKey;
	/**
	 * CurlMngr instance to handle outgoing web requests and incoming responses
	 * @var CurlMngr
	 */
  private $_curlMngr;
  /**
   * Message's text (not the text included in the attachment)
   * @var string
   */
  private $_SBMessageText;
  /**
   * Message's attachment references
   * @var array
   */
  private $_SBMessageAttachmentRefs;
  /**
   * Message's attachments
   * @var array
   */
  private $_SBMessageAttachments;
  /**
   * Message's id
   * @var string
   */
  private $_SBMessageId;
  /**
   * Message's date
   * @var string
   */
  private $_SBMessageDate;
  /**
   * Message's sender
   * @var SBUser
   */
  private $_fromUser;
  /**
   * Message's initialization status
   * @var boolean
   */
  private $_SBMessageInitialized;
  /**
   * Creates a new instance of SBMessage, keeping it uninitialized until the message is loaded
   * @param string $appSBCode_	the app's sbcode
   * @param string $appKey_			the app's key
   */
  public function __construct($appSBCode_,$appKey_)
  {
    $this->_appSBCode=$appSBCode_;
    $this->_appKey=$appKey_;
    $this->_curlMngr=CurlMngr::getInstance();
    $this->_SBMessageInitialized=false;
    $this->_SBMessageAttachments=array();
  }
  /**
   * Sets all message's attributes
   * @param string $SBMessageData_	json encoded string containing all the message's attributes
   * @return boolean true if message data could be set or false if it could not be set
   */
  private function loadSBMessageDataOrFalse($SBMessageData_)
  {
    if(($SBMessageDataArray=json_decode($SBMessageData_,true))!=null)
    {
      if(
          isset($SBMessageDataArray["CID"]) &&
          $SBMessageDataArray["CID"] == "SBMessage" &&
          isset($SBMessageDataArray["V1"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageId"]) &&
          isset($SBMessageDataArray["V1"]["fromSBCode"]) &&
          isset($SBMessageDataArray["V1"]["userName"]) &&
          isset($SBMessageDataArray["V1"]["userLastName"]) &&
          isset($SBMessageDataArray["V1"]["userGender"]) &&
          isset($SBMessageDataArray["V1"]["userProfilePicMD5"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageDate"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageType"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageText"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageAttachments"]) &&
          isset($SBMessageDataArray["V1"]["GSBCode"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageUniqueId"]) &&
          isset($SBMessageDataArray["V1"]["SBMessageFwd"]) &&
          isset($SBMessageDataArray["V1"]["userRating"]) &&
          isset($SBMessageDataArray["V1"]["SBMMetadata"]) 
        )
      {
        $this->_SBMessageText=$SBMessageDataArray["V1"]["SBMessageText"];
        $this->_SBMessageAttachmentRefs=json_decode($SBMessageDataArray["V1"]["SBMessageAttachments"],true);
        $this->_SBMessageId=$SBMessageDataArray["V1"]["SBMessageId"];
        $this->_SBMessageDate=$SBMessageDataArray["V1"]["SBMessageDate"];
        $this->_fromUser=new SBUser($this->_appSBCode,$this->_appKey);
        $this->_fromUser->initUser(
                                    $SBMessageDataArray["V1"]["fromSBCode"], 
                                    $SBMessageDataArray["V1"]["userName"], 
                                    $SBMessageDataArray["V1"]["userLastName"], 
                                    $SBMessageDataArray["V1"]["userGender"], 
                                    $SBMessageDataArray["V1"]["userProfilePicMD5"],
                                    $SBMessageDataArray["V1"]["userRating"]
                                   );
        $this->loadAttachmentRefsOrFalse();
        return ($this->_SBMessageInitialized=true);
      }
    }
    return false;
  }
  /**
   * Loads message's attachment references
   * @return void|false loads attachment refs in _SBMessageAttachments or false if there was any error
   */
  private function loadAttachmentRefsOrFalse()
  {
    if($this->_SBMessageAttachmentRefs!=null && is_array($this->_SBMessageAttachmentRefs))
    {
      $params=array(
                    "appSBCode"=>$this->_appSBCode,
                    "appKey"=>$this->_appKey,
                    "attachments"=>json_encode($this->_SBMessageAttachmentRefs)
                    );
      $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/getAttachmentInfo.php",$params,30000);
      if((($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(30000))!=false) && isset($responses[$handlerId]) && $responses[$handlerId]!=false)
      {
        if(($attachmentInfoArray=json_decode($responses[$handlerId],true))!=null)
        {
          if(
              isset($attachmentInfoArray["CID"]) &&
              $attachmentInfoArray["CID"] == "FileInfo" &&
              isset($attachmentInfoArray["V1"]) 
          )
          {
            foreach ($attachmentInfoArray["V1"] as $attachmentInfo)
            {
              if(
                      isset($attachmentInfo["attachmentDate"]) &&
                      isset($attachmentInfo["attachmentMD5"]) &&
                      isset($attachmentInfo["attachmentSize"]) &&
                      isset($attachmentInfo["attachmentType"]) &&
                      isset($attachmentInfo["storageLocation"])
              )
              {
                switch($attachmentInfo["storageLocation"])
                {
                  case StorageLocation::AMAZON_S3:
                  {
                    $tmpAttachment=array(
                                        "attachmentPayload" => "http://sbbkt.s3.amazonaws.com/".md5(substr($attachmentInfo["attachmentMD5"], 0,16)) . md5(substr($attachmentInfo["attachmentMD5"], 16,16)),
                                        "attachmentMD5"     => $attachmentInfo["attachmentMD5"],
                                        "attachmentDate"    => $attachmentInfo["attachmentDate"],
                                        "attachmentSize"    => $attachmentInfo["attachmentSize"],
                                        "attachmentType"    => $attachmentInfo["attachmentType"]
                                        );
                    $this->_SBMessageAttachments[]=$tmpAttachment;
                    break;
                  }
                  case StorageLocation::CASSANDRA_CLUSTER:
                  {
                    $tmpAttachment=array(
                                        "attachmentPayload" => $attachmentInfo["attachmentPayload"],
                                        "attachmentMD5"     => $attachmentInfo["attachmentMD5"],
                                        "attachmentDate"    => $attachmentInfo["attachmentDate"],
                                        "attachmentSize"    => $attachmentInfo["attachmentSize"],
                                        "attachmentType"    => $attachmentInfo["attachmentType"]
                                        );
                    $this->_SBMessageAttachments[]=$tmpAttachment;
                    break;
                  }
                  default:{return false;}
                }
              }
            } 
          }
        }
      }
    }
    return false;
  }
  /**
   * Loads a message by its message id
   * @param string $SBMessageId_	the message's id
   * @return boolean true if the message could be loaded or false if it could not be loaded
   */
  public function loadSBMessageBySBMessageIdOrFalse($SBMessageId_)
  {
    $params=array(
                  "appSBCode"=>$this->_appSBCode,
                  "appKey"=>$this->_appKey,
                  "SBMessageId"=>$SBMessageId_
                 );
    $handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/getSBMessage.php",$params,30000);
    if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(30000))!=false)
    {
      return (isset($responses[$handlerId]) && $responses[$handlerId]!=false)?$this->loadSBMessageDataOrFalse($responses[$handlerId]):false;
    }
    return false;
  }
  /**
   * Checks whether message data is loaded (i.e. message is initialized)
   * @return boolean true if the message is initialized or false if it is not
   */
  public function isDataLoaded()
  {return $this->_SBMessageInitialized;}
  /**
   * Gets message's text
   * @return string|false the message's text or false if message data was not loaded
   */
  public function getSBMessageTextOrFalse()
  {
    return $this->isDataLoaded()?$this->_SBMessageText:false;
  }
  /**
   * Gets message's Id
   * @return string|false the message's id or false if message data was not loaded
   */
  public function getSBMessageIdOrFalse()
  {
    return $this->isDataLoaded()?$this->_SBMessageId:false;
  }
  /**
   * Gets message's attachments
   * @return array|false the message's attachments or false if message data was not loaded
   */
  public function getSBMessageAttachmentsOrFalse()
  {
    return $this->isDataLoaded()?$this->_SBMessageAttachments:false;
  }
  /**
   * Gets message's attachment references
   * @return array|false the message's attachment references or false if data was not loaded 
   */
  public function getSBMessageAttachmentRefsOrFalse()
  {
    return $this->isDataLoaded()?$this->_SBMessageAttachmentRefs:false;
  }
  /**
   * Gets message's date
   * @return string|false the message's date or false if message data was not loaded
   */
  public function getSBMessageDateOrFalse()
  {
    return $this->isDataLoaded()?$this->_SBMessageDate:false;
  }
  /**
   * Gets message's creator
   * @return SBUser an instance of SBUser or false if message data was not loaded
   */
  public function getSBMessageFromUserOrFalse()
  {
    return $this->isDataLoaded()?$this->_fromUser:false;  
  }
}
?> 