<?php
/**
 * General functions
 *
 * @category SendLove
 * @package  Core
 * @author   Below92 LLC. <contact@below92.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version  SVN: $Id: Functions.php 6 2010-05-06 17:15:18Z seong $
 * @link     http://www.sendlove.us
 */
/**
 * General functions
 *
 * Based on Functions.php Copyright (c) 2000-2010, Hunstein & Kang GbR
 *
 * @category SendLove
 * @package  Core
 * @author   Below92 LLC. <contact@below92.com>
 * @license  Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link     http://www.sendlove.us
 */
class Functions
{
    /**
     * Returns a random string
     *
     * A-Z, a-z, 0-9:
     * Functions::randomString(10, 48, 122, array(58, 59, 60, 61, 62, 63, 64, 91, 92, 93, 94, 95, 96))
     *
     * Default is any printable ASCII character without whitespaces
     *
     * @param int   $len  The length of the string
     * @param int   $from The ASCII decimal range start
     * @param int   $to   The ASCII decimal range stop
     * @param array $skip ASCII decimals to skip
     *
     * @return string Random string
     */
    public static function randomString($len, $from = 48, $to = 122, $skip = array(58,59,60,61,62,63,64,91,92,93,94,95,96)) 
    {
        $str = '';
        $i = 0;
        while ($i < $len) {
            $dec = rand($from, $to);
            if (in_array($dec, $skip))
                continue;
            $str .= chr($dec);
            $i++;
        }
        return $str;
    }

    /**
     * Encrypts a cleartext password via the crypt() function
     *
     * @param string $clearText Cleartext password
     *
     * @return string Encrypted password
     */
    public static function encryptPassword($clearText)
    {
        switch (true) {
        case (defined('CRYPT_SHA512') && CRYPT_SHA512 == 1):
            $salt = '$6$' . self::randomString(16);
            break;

        case (defined('CRYPT_SHA256') && CRYPT_SHA256 == 1):
            $salt = '$5$' . self::randomString(16);
            break;

        case (defined('CRYPT_MD5') && CRYPT_MD5 == 1):
            $salt = '$1$' . self::randomString(12);
            break;

        case (defined('CRYPT_STD_DES') && CRYPT_STD_DES == 1):
            $salt = self::randomString(
                2,
                48,
                122,
                array(58, 59, 60, 61, 62, 63, 64, 91, 92, 93, 94, 95, 96)
            );
            break;
        }
        return crypt($clearText, $salt);
    }
}
