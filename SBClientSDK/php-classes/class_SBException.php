<?php

class SBException extends Exception
{
	protected $_errorType;
	protected $_errorMessage;
	
	public function __construct($errorType_, $errorMessage_="")
	{
		$this->_errorType = $errorType_;
		$this->_errorMessage = $errorMessage_;
	}
	
	public function getErrorType()
	{	return $this->_errorType;	}
	
	public function __toString()
	{
		$str = "[ERROR: ".$this->_errorType."]";
		if (!empty($this->_errorMessage))
		{ $str .= " ".$this->_errorMessage; }
		$str .= "stack trace:\n";
		foreach (getStackTrace() as $line)
		{ $str .= "\t$line\n"; }
		return $str;
	}
}