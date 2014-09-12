<?php

// Convinient helper to get the current user picture
// Called mainly on the application header load
class Zend_View_Helper_UserHelper extends Zend_View_Helper_Abstract 
{
	public function userHelper()
	{
		return $this;
	}

    public function getUserPicture() 
    {
        $dn = new Zend_Session_Namespace();
        $userid = $dn->userid;
        
        if (!empty($userid) && ($userid != 0)) {
            $userMapper = new Admin_Model_UserMapper();
            $user = $userMapper->find($userid);
            return $user->getPicture();
        } else { // If we couldn't get the user, fallback to a default picture
            return "images/no_picture.png";
        }
    }

	public function getUserNickname()
	{
		$dn = new Zend_Session_Namespace();
		$nick = '';
		if ($dn->nickname) {
			$nick = ' ' . $dn->nickname;
		}
		return $nick;
	}

	public function isLoggedIn()
	{
		$dn = new Zend_Session_Namespace();
		return $dn->logged_in;
	}

}
