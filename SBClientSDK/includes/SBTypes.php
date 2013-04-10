<?php

	//Used to define how long should a column last in seconds, after that it's automatically deleted	
	final class TTL {
		const FOREVER 		= NULL;
		const ONEMIN 		  = 60;
		const TENMINS 		= 600;
		const HALFHOUR 		= 1800;
		const ONEHOUR 		= 3600;
		const SIXHOURS 		= 21600;
		const ONEDAY 			= 86400;
		const TWODAYS 		= 172800;
		const ONEWEEK 		= 604800;
		const ONEMONTH 		= 2419200;
		const THREEMONTHS = 7257600;
		const SIXMONTHS 	= 14515200;
		const TWELVEMONTHS 	= 29030400;
	}
/**
 * SBErrors: available error codes
 * 
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
final class SBErrors
{
  const WRONG_PARAMS_FORMAT_ERROR   = "100000";
  const WEBSERVICE_TIMEOUT_ERROR    = "100001";
  const UNKNOWN_ERROR               = "100002";
  const UNABLE_TO_LOAD_MESSAGEID    = "100003";
  const UNABLE_TO_LOAD_USER         = "100004";
}
/**	
 * SBAppEventType: available events
 *
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
final class SBAppEventType{
  const NEW_MESSAGE                 = "NEW_MESSAGE";
  const NEW_CONTACT_SUBSCRIPTION    = "NEW_CONTACT_SUBSCRIPTION";
  const NEW_CONTACT_UNSUBSCRIPTION  = "NEW_CONTACT_UNSUBSCRIPTION";
  const NEW_VOTE 										= "NEW_VOTE";
  const NEW_PING										= "NEW_PING";
}
/**
 * SBVars: miscelaneous variables
 *
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
final class SBVars{
  const SB_WEBSERVICE_ADDR = "https://sbmail.me";
}
/**
 * SBAttachmentType: available attachment types
 *
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
final class SBAttachmentType{
  const AVOIDSHAREBAR								= 0;
  const IMAGE 											= 1;
  const AUDIO 											= 2;
  const VIDEO 											= 3;
  const MAP		 											= 4;
  const PARAGRAPH										= 5;
  const QUOTE	 											= 6;
  const TITLE	 											= 7;
  const LINK	 											= 8;
  const EXTENDED_MSG								= 9;
  const YOUTUBE_LINK								= 100;
  const VIMEO_LINK	 								= 101;
  const GOEAR_LINK									= 102;
  const LASTFM_LINK									= 103;
  const GOOGLE_VIDEO_LINK						= 104;
  const VIDDLER_LINK								= 105;
  const BLIP_LINK	 									= 106;
  const VEOH_LINK										= 107;
  const METACAFE_LINK								= 108;
  const SPIKE_LINK									= 109;
  const MYSPACE_VIDEO_LINK					= 110;
  const MYSPACE_AUDIO_LINK					= 111;
  const DAILY_MOTION_LINK	 					= 112;
}
/**
 * StorageLocation: available storage locations
 *
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
final class StorageLocation{
  const AMAZON_S3						= "AS3";
  const CASSANDRA_CLUSTER		= "CAS";
}
/**
 * SBConstants: miscelaneous constants
 *
 * @author Spotbros <support@spotbros.com>
 * @version 0.01
 */
final class SBConstants{
  const MAX_TEXT_SIZE = 500;
}
?> 