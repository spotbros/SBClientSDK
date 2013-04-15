<?php
require_once(dirname(__FILE__).'/../includes/SBFunctions.php');
/**
 * CurlMngr
 * 
 * Provides connectivity functions using cURL
 * 
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
class CurlMngr
{
	/**
	 * CurlMngr instance to handle http queries
	 * @var CurlMngr
	 */
  private static $_CurlMngrInstance;
  /**
   * Curl multi handle resource
   * @var resource
   */
  private $_MCHandler;
  /**
   * Array containing Curl handle resources
   * @var array
   */
  private $_cHandlers;
  /**
   * Initializes the multi-handler resource and the array of handlers
   */
  private function __construct()
  {
  	$this->_MCHandler = curl_multi_init();  
  	$this->_cHandlers=array();
  }
  /**
   * Destroys current CurlMngr instance
   */
  public function __destruct()
  {}
  /**
   * Gets current CurlMngr instance if it exists. If not, creates one and returns it
   * @return CurlMngr the CurlMngr instance
   */
  public static function getInstance() 
  { 
   	if (!self::$_CurlMngrInstance) 
    {self::$_CurlMngrInstance = new CurlMngr();} 
    return self::$_CurlMngrInstance; 
  }
  /**
   * Queries an URL using cURL
   * @param string $url_	the url to query
   * @param array $params_	the url parameters as ("param0" => "value0", "param1" => "value1"...,"paramN" => "valueN")
   * @param integer $timeoutMS_	the maximum number of seconds to allow cURL functions to execute
   * @param string $userAgent_	the contents of the "User Agent" header for http requests
   * @return string|false	the handler id or bool false if any error ocurred
   */
  public function queryStringThisUrlOrFalse($url_, Array $params_=null, $timeoutMS_=1000,$userAgent_="")
  {
  	$timeoutMS_=(is_numeric($timeoutMS_)&&$timeoutMS_>1000)?$timeoutMS_:1000;
  	$handlerId=md5(round((microtime(1)*1000),0).rand(0,1000000));
  	if($params_!=null)
  	{
  		$url_ = $url_."?".http_build_query($params_);
  	}
  	if(($this->_cHandlers[$handlerId]=curl_init($url_))!=false)
  	{
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_RETURNTRANSFER, true);  
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HEADER, false); //not include header
  		if($userAgent_!="")
  		{
  		  curl_setopt($this->_cHandlers[$handlerId], CURLOPT_USERAGENT,$userAgent_);
  		}
  		//curl_setopt($this->_cHandlers[$handlerId], CURLOPT_CONNECTTIMEOUT_MS, 75); //100ms max timeout connecting
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_TIMEOUT_MS, $timeoutMS_); //full execution process
  		if(curl_multi_add_handle($this->_MCHandler,$this->_cHandlers[$handlerId])==0)
  		{
  			curl_multi_exec($this->_MCHandler, $active);
    		return $handlerId;
  		}
  	}
		unset($this->_cHandlers[$handlerId]);
		return false;
  }
  /**
   * Performs a HTTP POST request using cURL
   * @param string $url_	the url to perform the HTTP POST request
   * @param integer $timeoutMS_	the maximum number of seconds to allow cURL functions to execute
   * @param string $json_	json encoded string, which will be the body of the POST request
   * @return string|false	the handler id or bool false if any error ocurred
   */
  public function postJSONToThisURLOrFalse($url_,$timeoutMS_=1000, $json_)
  {
  	$timeoutMS_=(is_numeric($timeoutMS_)&&$timeoutMS_>1000)?$timeoutMS_:1000;
  	$handlerId=md5(round((microtime(1)*1000),0).rand(0,1000000));
  	if(($this->_cHandlers[$handlerId]=curl_init($url_))!=false)
  	{
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_RETURNTRANSFER, true);  
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HEADER, false); //not include header
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_CUSTOMREQUEST, "POST");
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_POSTFIELDS, $json_);  
  		//curl_setopt($this->_cHandlers[$handlerId], CURLOPT_CONNECTTIMEOUT_MS, 75); //100ms max timeout connecting
  		curl_setopt($this->_cHandlers[$handlerId], CURLOPT_TIMEOUT_MS, $timeoutMS_); //full execution process
  		if(curl_multi_add_handle($this->_MCHandler,$this->_cHandlers[$handlerId])==0)
  		{
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($json_))); 
  			curl_multi_exec($this->_MCHandler, $active);
    		return $handlerId;
  		}
  	}
		unset($this->_cHandlers[$handlerId]);
		return false;
  }
  /**
   * Uploads a file using a HTTP POST request
   * @param string $url_	the URL to perform the HTTP POST request to
   * @param string $filePath_	the path to the file that we want to upload
   * @param array $params_ the data to post in the HTTP POST request
   * @param integer $timeoutMS_	the maximum number of seconds to allow cURL functions to execute
   * @param string $userAgent_	the contents of the "User Agent" header for http requests
   * @return string|false	the handler id or bool false if any error ocurred
   */
  public function postFileToUrlOrFalse($url_,$filePath_,Array $params_,$timeoutMS_=10000,$userAgent_="")
  {
  	if(file_exists($filePath_))
  	{
  		if(
  				isset($params_["appSBCode"]) && 
  				isset($params_["appKey"]) && 
  				isset($params_["attachmentType"]) && 
  				isValidAttachmentType($params_["attachmentType"])
  			)
  		{
  			$timeoutMS_=(is_numeric($timeoutMS_)&&$timeoutMS_>1000)?$timeoutMS_:1000;
  			$handlerId=md5(round((microtime(1)*1000),0).rand(0,1000000));
  			$params_["attachmentPayload"]="@".$filePath_;
  			$this->_cHandlers[$handlerId] =  curl_init($url_);
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_POSTFIELDS, $params_);
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HEADER, false);
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_RETURNTRANSFER, true);
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
  			curl_setopt($this->_cHandlers[$handlerId], CURLOPT_TIMEOUT_MS, $timeoutMS_); 
  			if(curl_multi_add_handle($this->_MCHandler,$this->_cHandlers[$handlerId])==0)
  			{
  				curl_multi_exec($this->_MCHandler, $active);
  				return $handlerId;
  			}
  		}
  	}
  	return false;	
  }
  /**
   * Gets cURL responses when they are ready
   * @param integer $timeoutMS_
   * @return array|false responses from the curl_multi_exec call or bool false if any error ocurred
   */
  public function getResponsesWhenReadyOrFalse($timeoutMS_=1000)
  {
  	$startTime=microtime(true);
  	do
  	{  
  		curl_multi_exec($this->_MCHandler, $active);
  		if(microtime(true) < ($startTime+$timeoutMS_))
  		{usleep(1000); /*1 ms*/}
  		else
  		{return false;}
  		
  	}
  	while($active > 0);
  	$replies=array();
  	foreach($this->_cHandlers as $handlerId => $handler)
  	{
  		if(curl_error($handler)!='')
  		{
  			error_log("CURL_ERROR: ".curl_error($handler));  			
  		}
  		$replies[$handlerId] = curl_multi_getcontent($handler); 
  	}
  	return $replies;
  }
  /**
   * Downloads file from URL to the specified filepath or /tmp/[FILE_NAME]. If the file already exists, it is not downloaded
   * @param string $url_	the URL to the file
   * @param string $filePath_	the path where the file will be downloaded to
   * @return string|boolean	the path to the downloaded file or false if any error occurs
   */
  public function downloadFileOrFalse($url_,$filePath_="")
  {
    if($filePath_=="")
    {$filePath_ = "/tmp/".array_pop(explode("/", $url_));}
    if(!file_exists($filePath_))
    {
      $handlerId=$this->queryStringThisUrlOrFalse($url_,array(),100000);
      if(($responses=$this->getResponsesWhenReadyOrFalse(10000))!=false)
      {
        if(isset($responses[$handlerId]) && $responses[$handlerId]!=false)
        {
          $fh = fopen($filePath_,'x');
          fwrite($fh, $responses[$handlerId]);
          fclose($fh);
          return $filePath_;
        }
      }
      return false;
    }
    return $filePath_;
  }
  /**
   * Clears all the CURL handlers
   */
  public function clearHandlers()
  {
  	$this->_cHandlers=array();
  }
}
?> 