<?php

class Sendlove_Utilities
{

	public static function createPassword($l = 8, $c = 3, $n = 2, $s = 1)
	{
		// get count of all required minimum special chars
		$count = $c + $n + $s;
	
		// sanitize inputs; should be self-explanatory
		if(!is_int($l) || !is_int($c) || !is_int($n) || !is_int($s)) {
			trigger_error('Argument(s) not an integer', E_USER_WARNING);
			return false;
		}
		elseif($l < 0 || $l > 20 || $c < 0 || $n < 0 || $s < 0) {
			trigger_error('Argument(s) out of range', E_USER_WARNING);
			return false;
		}
		elseif($c > $l) {
			trigger_error('Number of password capitals required exceeds password length', E_USER_WARNING);
			return false;
		}
		elseif($n > $l) {
			trigger_error('Number of password numerals exceeds password length', E_USER_WARNING);
			return false;
		}
		elseif($s > $l) {
			trigger_error('Number of password capitals exceeds password length', E_USER_WARNING);
			return false;
		}
		elseif($count > $l) {
			trigger_error('Number of password special characters exceeds specified password length', E_USER_WARNING);
			return false;
		}
	
		// all inputs clean, proceed to build password
	
		// change these strings if you want to include or exclude possible password characters
		$chars = "abcdefghijklmnopqrstuvwxyz";
		$caps = strtoupper($chars);
		$nums = "0123456789";
		$syms = "!@#$%^&*()-+?";
		$out = '';
	
		// build the base password of all lower-case letters
		for($i = 0; $i < $l; $i++) {
			$out .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
	
		// create arrays if special character(s) required
		if($count) {
			// split base password to array; create special chars array
			$tmp1 = str_split($out);
			$tmp2 = array();
	
			// add required special character(s) to second array
			for($i = 0; $i < $c; $i++) {
				array_push($tmp2, substr($caps, mt_rand(0, strlen($caps) - 1), 1));
			}
			for($i = 0; $i < $n; $i++) {
				array_push($tmp2, substr($nums, mt_rand(0, strlen($nums) - 1), 1));
			}
			for($i = 0; $i < $s; $i++) {
				array_push($tmp2, substr($syms, mt_rand(0, strlen($syms) - 1), 1));
			}
	
			// hack off a chunk of the base password array that's as big as the special chars array
			$tmp1 = array_slice($tmp1, 0, $l - $count);
			// merge special character(s) array with base password array
			$tmp1 = array_merge($tmp1, $tmp2);
			// mix the characters up
			shuffle($tmp1);
			// convert to string for output
			$out = implode('', $tmp1);
		}
		
		if ((strlen($out) < $l) || empty($out)) {
			$out = self::createPassword();
		}
		
		return $out;
	}

	public function exportCSV($filename = '', array $data = array(), $cvs_header = '') {
		if(empty($data)) {
			return false;
		}
		$filename =  empty($filename) ?  "No-name" : $filename;  
		$csv = "";
		foreach ($data as $item) {
			$csv .= implode(",", $item)."\n";
		}
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.$filename.'.csv"');
		echo $cvs_header;
		echo $csv;
	}
	

	public static function searchMultiArray($array, $key = '', $value = '')
	{
	    // If @array is empty, return not found
	    if (!is_array($array) || empty($array)) {
	        return false;
	    }

		foreach ($array as $subArray) {
			if ($subArray[$key] == $value) {
			    return true;
			}
		}

		return false;
	}

	public static function getMimeType($filename, $mimePath = '/etc')
	{
		$fileext = substr(strrchr($filename, '.'), 1); 
		if (empty($fileext)) return false; 
		$regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($fileext\s)/i"; 
		$lines = file("$mimePath/mime.types"); 
		foreach($lines as $line) { 
			if (substr($line, 0, 1) == '#') continue; 
			$line = rtrim($line) . " "; 
			if (!preg_match($regex, $line, $matches)) continue;
			return $matches[1]; 
		} 
		return false;
	}

	public static function createUniqueFilename($strExt = 'tmp')
	{
		$strExt = end(explode(".", $strExt));
		// explode the IP of the remote client into four parts
		$arrIp = explode('.', $_SERVER['REMOTE_ADDR']);
		// get both seconds and microseconds parts of the time
		list($usec, $sec) = explode(' ', microtime());
		// fudge the time we just got to create two 16 bit words
		$usec = (integer) ($usec * 65536);
		$sec = ((integer) $sec) & 0xFFFF;
		// fun bit--convert the remote client's IP into a 32 bit
		// hex number then tag on the time.
		// Result of this operation looks like this xxxxxxxx-xxxx-xxxx
		$strUid = sprintf("%08x-%04x-%04x", ($arrIp[0] << 24) | ($arrIp[1] << 16) | ($arrIp[2] << 8) | $arrIp[3], $sec, $usec);
		// tack on the extension and return the filename
		return $strUid . '.' . $strExt;
	}

}
