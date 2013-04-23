<?php
require_once(dirname(__FILE__).'/../includes/SBFunctions.php');
/**
 * SBUser
 *
 * Describes a Spotbros user. When this class is instantiated,
 * you will have a complete user profile with the user's publicly available information.
 *
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 *
 */
class SBUser
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
	 * User's sbcode
	 * @var string
	 */
	private $_userSBCode;
	/**
	 * User's first name
	 * @var string
	 */
	private $_userName;
	/**
	 * User's last name
	 * @var string
	 */
	private $_userLastName;
	/**
	 * User's gender
	 * @var string
	 */
	private $_userGender;
	/**
	 * User's profile picture MD5
	 * @var string
	 */
	private $_userProfilePicMD5;
	/**
	 * User's rating
	 * @var string
	 */
	private $_userRating;
	/**
	 * User's email
	 * @var string
	 */
	private $_userEmail;
	/**
	 * User's phonekey (phone number)
	 * @var unknown_type
	 */
	private $_userPhoneKey;
	/**
	 * User's latitude
	 * @var unknown_types
	 */
	private $_userLatitude;
	/**
	 * User's longitude
	 * @var unknown_type
	 */
	private $_userLongitude;
	/**
	 * User's language
	 * @var unknown_type
	 */
	private $_userLanguage;
	/**
	 * User initialization status
	 * @var boolean
	 */
	private $_userInitialized;
	/**
	 * Creates an instance of SBUser, keeping it uninitialized until the user is loaded
	 * @param unknown_type $appSBCode_			the SBApp's sbcode
	 * @param unknown_type $appKey_					the SBApp's key
	 */
	public function __construct($appSBCode_,$appKey_)
	{
		$this->_appSBCode=$appSBCode_;
		$this->_appKey=$appKey_;
		$this->_curlMngr=CurlMngr::getInstance();
		$this->_userInitialized=false;
	}
	/**
	 * Initializes a new user given all its attributes
	 * @param string $userSBCode_					the user's sbcode
	 * @param string $userName_						the user's first name
	 * @param string $userLastName_				the user's last name
	 * @param string $userGender_					the user's gender
	 * @param string $userProfilePicMD5_	the user's profile pic MD5
	 * @param string $userRating_					the user's rating
	 * @param string $userEmail_					the user's email
	 * @param unknown_type $userPhoneKey_				the user's phone key
	 * @param unknown_type $userLatitude_				the user's latitude
	 * @param unknown_type $userLongitude_			the user's longitude
	 * @param unknown_type $userLanguage_				the user's language
	 */
	public function initUser(
			$userSBCode_,
			$userName_,
			$userLastName_,
			$userGender_,
			$userProfilePicMD5_,
			$userRating_,
			$userEmail_="",
			$userPhoneKey_="",
			$userLatitude_="",
			$userLongitude_="",
			$userLanguage_=""
			)
	{
		$this->_userSBCode=$userSBCode_;
		$this->_userName=$userName_;
		$this->_userLastName=$userLastName_;
		$this->_userGender=$userGender_;
		$this->_userProfilePicMD5=$userProfilePicMD5_;
		$this->_userRating=$userRating_;
		$this->_userEmail=$userEmail_;
		$this->_userPhoneKey=$userPhoneKey_;
		$this->_userLatitude=$userLatitude_;
		$this->_userLongitude=$userLongitude_;
		$this->_userInitialized=true;
	}
	/**
	 * Sets all user's attributes
	 * @param string $SBUserData_		json encoded string containing all the user's attributes
	 */
	private function loadSBUserDataOrFalse($SBUserData_)
	{
		if(($SBUserDataArray=json_decode($SBUserData_,true))!=null)
		{
			if(
					isset($SBUserDataArray["CID"]) &&
					$SBUserDataArray["CID"] == "SBUser" &&
					isset($SBUserDataArray["V1"]) &&
					isset($SBUserDataArray["V1"]["userSBCode"]) &&
					isset($SBUserDataArray["V1"]["userProfilePicMD5"]) &&
					isset($SBUserDataArray["V1"]["userName"]) &&
					isset($SBUserDataArray["V1"]["userLastName"]) &&
					isset($SBUserDataArray["V1"]["userGender"]) &&
					isset($SBUserDataArray["V1"]["userRating"])
			)
			{
				$this->_userSBCode=$SBUserDataArray["V1"]["userSBCode"];
				$this->_userProfilePicMD5=$SBUserDataArray["V1"]["userProfilePicMD5"];
				$this->_userName=$SBUserDataArray["V1"]["userName"];
				$this->_userLastName=$SBUserDataArray["V1"]["userLastName"];
				$this->_userGender=$SBUserDataArray["V1"]["userGender"];
				$this->_userRating=$SBUserDataArray["V1"]["userRating"];
				return ($this->_userInitialized=true);
			}
		}
		return false;
	}
	/**
	 * Loads an user by his/her sbcode, setting all its attributes
	 * @param string $userSBCode_		the user's sbcode
	 * @return boolean true if user gets initialized. False if not
	 */
	public function loadUserBySBCodeOrFalse($userSBCode_)
	{
		$params=array(
				"appSBCode"=>$this->_appSBCode,
				"appKey"=>$this->_appKey,
				"userSBCode"=>$userSBCode_
		);
		$handlerId=$this->_curlMngr->queryStringThisUrlOrFalse(SBVars::SB_WEBSERVICE_ADDR."/public-api/getSBUser.php",$params,30000);
		if(($responses=$this->_curlMngr->getResponsesWhenReadyOrFalse(30000))!=false)
		{
			return (isset($responses[$handlerId]) && $responses[$handlerId]!=false)?$this->loadSBUserDataOrFalse($responses[$handlerId]):false;
		}
		return false;
	}
	/**
	 * Checks whether user data is loaded (i.e. user is initialized)
	 * @return boolean true if user is initialized. False if it is not
	 */
	public function isDataLoaded()
	{
		return $this->_userInitialized;
	}
	/**
	 * Get user's sbcode
	 * @return string|false the user's sbcode or false if user data is not loaded
	 */
	public function getSBUserSBCodeOrFalse()
	{
		return $this->isDataLoaded()?$this->_userSBCode:false;
	}
	/**
	 * Gets user's first name
	 * @return string|false the user's first name or false if user data is not loaded
	 */
	public function getSBUserNameOrFalse()
	{
		return $this->isDataLoaded()?$this->_userName:false;
	}
	/**
	 * Gets user's last name
	 * @return string|false the user's last name or false if user data is not loaded
	 */
	public function getSBUserLastNameOrFalse()
	{
		return $this->isDataLoaded()?$this->_userLastName:false;
	}
	/**
	 * Gets user's gender
	 * @return string|false the user's gender or false if user data is not loaded
	 */
	public function getSBUserGenderOrFalse()
	{
		return $this->isDataLoaded()?$this->_userGender:false;
	}
	/**
	 * Gets user's profile picture md5
	 * @return string|false the user's profile picture md5 or false if user data is not loaded
	 */
	public function getSBUserProfilePicMD5OrFalse()
	{
		return $this->isDataLoaded()?$this->_userProfilePicMD5:false;
	}
	/**
	 * Gets user's rating
	 * @return string|false the user's rating or false if user data is not loaded
	 */
	public function getSBUserRatingOrFalse()
	{
		return $this->isDataLoaded()?$this->_userRating:false;
	}
	/**
	 * Gets user's email
	 * @return string|false the user's email or false if user data is not loaded
	 */
	public function getSBUserEmailOrFalse()
	{
		return $this->isDataLoaded()?$this->_userEmail:false;
	}
	/**
	 * Gets user's phonekey
	 * @return	the user's phone key or false if user data is not loaded
	 */
	public function getSBUserPhoneKeyOrFalse()
	{
		return $this->isDataLoaded()?$this->_userPhoneKey:false;
	}
	/**
	 * Gets user's latitude
	 * @return	the user's latitude or false if user data is not loaded
	 */
	public function getSBUserLatitudeOrFalse()
	{
		return $this->isDataLoaded()?$this->_userLatitude:false;
	}
	/**
	 * Gets user's longitude
	 * @return	the user's longitude or false if user data is not loaded
	 */
	public function getSBUserLongitudeOrFalse()
	{
		return $this->isDataLoaded()?$this->_userLongitude:false;
	}
	/**
	 * Gets user's location
	 * @return	the user's location as latitude, longitude or false if user data is not loaded
	 */
	public function getSBUserLocationOrFalse()
	{
		return $this->isDataLoaded()?($this->getSBUserLatitudeOrFalse().",".$this->getSBUserLongitudeOrFalse()):false;
	}
	/**
	 * Gets user's language
	 * @return string	the user's language
	 */
	public function getSBUserLanguageOrFalse()
	{
		return $this->isDataLoaded()?$this->_userLanguage:false;
	}
	/**
	 * Sets user's email
	 * @param string $userEmail_	the user's new email
	 */
	public function setSBUserEmail($userEmail_)
	{
		$this->_userEmail=$userEmail_;
	}
	/**
	 * Sets user's phone key
	 * @param unknown_type $phoneKey_	the user's new phone key
	 */
	public function setSBUserPhoneKey($phoneKey_)
	{
		$this->_userPhoneKey=$phoneKey_;
	}
	/**
	 * Set user's location
	 * @param float $latitude_	the user's new latitude
	 * @param float $longitude_	the user's new longitude
	 */
	public function setSBUserLocation($latitude_,$longitude_)
	{
		$this->_userLatitude=$latitude_;
		$this->_userLongitude=$longitude_;
	}
	/**
	 * Sets user's language_
	 * @param string $language_	the new user's language
	 */
	public function setSBUserLanguage($language_)
	{
		$this->_userLanguage=$language_;
	}
}
?>