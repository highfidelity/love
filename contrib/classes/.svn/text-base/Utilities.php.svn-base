<?php

class Utilities
{

	public static function getMimeType($ext)
	{
		if (empty($ext)) {
			return false;
		}
		
		$regex = "/^([\w\+\-\.\/]+)\s+(\w+\s)*($ext\s)/i";
		$lines = file('/etc/mime.types');
		
		foreach($lines as $line) {
			if (substr($line, 0, 1) == '#') {
				continue;
			}
		
			$line = rtrim($line) . " ";
			
			if (!preg_match($regex, $line, $matches)) {
				continue;
			}
		
			return $matches[1];
		}
		
		return false;
	}

}
