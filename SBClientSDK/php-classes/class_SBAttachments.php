<?php
require_once(dirname(__FILE__).'/class_CurlMngr.php');
require_once(dirname(__FILE__).'/../includes/SBTypes.php');
require_once(dirname(__FILE__).'/../includes/SBFunctions.php');
/**
 * SBAttachments
 *
 * Describes a Spotbros message's attachment
 *
 * @author Spotbros
 * @version 0.01
 */
class SBAttachments
{
	/**
	 * App's key
	 * @var string
	 */
	private $_appKey;
	/**
	 * App's sbcode
	 * @var string
	 */
	private $_appSBCode;
	/**
	 * CurlMngr instance to handle outgoing web requests and incoming responses
	 * @var CurlMngr
	 */
	private $_curlMngr;
	/**
	 * IDs of curl handlers used for uploading attachments
	 * @var array
	 */
	private $_handlerIds;
	/**
	 * Array of file paths to unlink after finishing the upload (used for images)
	 * @var array
	 */
	private $_filePathsToUnlink;
	/**
	 * Attachment references
	 * @var array
	 */
	public $_attachmentRefs;
	/**
	 * Reference to extended text attachment
	 * @var string
	 */
	private $_extendedTextRef;
	/**
	 * Creates a new instance of SBAttachments
	 * @param string $appSBCode_
	 * @param string $appKey_
	 */
	public function __construct($appSBCode_,$appKey_)
	{
		$this->_appKey=$appKey_;
		$this->_appSBCode=$appSBCode_;
		$this->_curlMngr=CurlMngr::getInstance();
		$this->_attachmentRefs=array();
		$this->_extendedTextRef="";
	}
	/**
	 * Uploads an attachment to Spotbros' remote storage
	 *
	 * @param SBAttachmentType $attachmentType_	the type of attachment
	 * @param string $payload_						the attachment's payload
	 * @return string|false the attachment reference or false if there was any problem while processing the attachment
	 */
	private function  uploadAttachmentOrFalse($attachmentType_,$payload_)
	{
		if(isValidAttachmentType($attachmentType_))
		{
			$params=array(
					"appSBCode"=>$this->_appSBCode,
					"appKey"=>$this->_appKey,
					"attachmentType"=>$attachmentType_,
					"attachmentPayload"=>$payload_
			);
			if(($handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/uploadAttachment.php",$params,	20000))!=false)
			{
				$this->_handlerIds[] = array("id"=>$handlerId,"attachmentType"=>$attachmentType_);
				return true;
			}
		}
		return false;
	}
	/**
	 * Uploads a file to Spotbros' remote storage
	 *
	 * @param SBAttachmentType $attachmentType_	the type of attachment
	 * @param string $payload_	the file's path
	 * @return string|false the attachment reference or false if there was any problem while processing the attachment
	 */
	private function uploadFileOrFalse($attachmentType_,$filePath_,$timeout_=10000)
	{
		$params=array(
				"appSBCode"=>$this->_appSBCode,
				"appKey"=>$this->_appKey,
				"attachmentType"=>$attachmentType_
		);
		if(($handlerId=$this->_curlMngr->postFileToUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/uploadAttachment.php", $filePath_, $params,$timeout_))!=false)
		{
			$this->_handlerIds[] = array("id"=>$handlerId,"attachmentType"=>$attachmentType_,);
			return true;
		}
		return false;
	}
	/**
	 * Gets all the attachment references
	 *
	 * @return array this attachment's references
	 */
	public function getAttachmentRefs()
	{
		if((($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(100000))!=false))
		{
			if(isset($this->_handlerIds))
			{
			foreach($this->_handlerIds as $handler)
			{
				$handlerId = $handler["id"];
				if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
				{
					if(($attachmentInfoArray=json_decode($responses[$handlerId],true))!=null)
					{
						if(
								isset($attachmentInfoArray["CID"]) &&
								$attachmentInfoArray["CID"] == "attachmentUploadedOk" &&
								isset($attachmentInfoArray["V1"])
						)
						{
							if($handler["attachmentType"]==SBAttachmentType::EXTENDED_MSG)
							{
								array_unshift($this->_attachmentRefs, $attachmentInfoArray["V1"]);
								$this->_extendedTextRef=$attachmentInfoArray["V1"];
							}
							else
							{
									$this->_attachmentRefs[]=$attachmentInfoArray["V1"];
							}
						}
					}
				}
			}
			}
			if(count($this->_filePathsToUnlink)>0)
			{
				foreach($this->_filePathsToUnlink as $filePath)
				{
					if(file_exists($filePath))
					{unlink($filePath);}
				}
			}
			return $this->_attachmentRefs;
		}
	}
	/**
	 * Adds a title to the SBMessage attachments
	 *
	 * @param string $title_			the title to be added
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addTitleOrFalse($title_)
	{
		return $this->uploadAttachmentOrFalse(SBAttachmentType::TITLE, $title_);
	}
	/**
	 * Adds a paragraph to the SBMessage attachments
	 *
	 * @param string $paragraph_	the paragraph to be added
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addParagraphOrFalse($paragraph_)
	{
		return $this->uploadAttachmentOrFalse(SBAttachmentType::PARAGRAPH, $paragraph_);
	}
	/**
	 * Adds a quote to the SBMessage attachments
	 *
	 * @param string $quote_				the quote to be added
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addQuoteOrFalse($quote_)
	{
		return $this->uploadAttachmentOrFalse(SBAttachmentType::QUOTE, $quote_);
	}
	/**
	 * Adds a quote to the SBMessage attachments
	 *
	 * @param string $quote_				the quote to be added
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addLinkOrFalse($link_)
	{
		return $this->uploadAttachmentOrFalse(SBAttachmentType::LINK, $link_);
	}
	/**
	 * Adds extended text to the SBMessage attachments. Please note that if you send a message
	 * with a text with more than SBConstants::MAX_TEXT_SIZE, this text will override whatever
	 * text you want to add using this method.
	 *
	 * @param string $text_				the text to be added
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addExtendedText($text_)
	{
		if($pos=array_search($this->_extendedTextRef, $this->_attachmentRefs))
		{
			unset($this->_attachmentRefs[$pos]);
		}
		return $this->uploadAttachmentOrFalse(SBAttachmentType::EXTENDED_MSG, $text_);
	}
	/**
	 * Adds a map to the SBMessage attachments.
	 *
	 * @param string $text_				the text to be added
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addMapOrFalse($latitude_,$longitude_)
	{
		if(is_numeric($latitude_) && is_numeric($longitude_))
		{
			return $this->uploadAttachmentOrFalse(SBAttachmentType::MAP, $latitude_.",".$longitude_);
		}
		return false;
	}
	/**
	 * Adds an image to the SBMessage attachments
	 *
	 * @param string $text_				the path to the image locally
	 * @return string|false the attachment reference or false there was any error processing the attachment
	 */
	public function addImageOrFalse($imagePath_,$timeout_=10000,$unlink_ = false)
	{
		if($unlink_)
		{
			$this->_filePathsToUnlink[] = $imagePath_;
		}
		return $this->uploadFileOrFalse(SBAttachmentType::IMAGE, $imagePath_,$timeout_);
	}
	public function addAudioFalse($audioPath_,$timeout_=10000,$unlink_ = false)
	{
		if($unlink_)
		{
			$this->_filePathsToUnlink[] = $audioPath_;
		}
		return $this->uploadFileOrFalse(SBAttachmentType::AUDIO, $audioPath_,$timeout_);
	}
	public function addVideoOrFalse($videoPath_,$timeout_=10000,$unlink_ = false)
	{
		if($unlink_)
		{
			$this->_filePathsToUnlink[] = $videoPath_;
		}
		return $this->uploadFileOrFalse(SBAttachmentType::VIDEO, $videoPath_,$timeout_);
	}
	public function addYoutubeLinkOrFalse($link_)
	{
		return $this->uploadAttachmentOrFalse(SBAttachmentType::YOUTUBE_LINK, $link_);
	}

	/**
	 * Clears all the SBMessage attachments. Use this method if you want to include
	 * different attachments to different messages sent sequentially.
	 * @return void
	 */
	public function clearAttachments()
	{
		$this->_attachmentRefs=array();
		$this->_curlMngr->clearHandlers();
	}
}
?>