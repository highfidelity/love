<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('class','',$app_root_path);
require_once($app_root_path.'class/DataObject.php');
require_once($app_root_path.'class/Utils.class.php');
require_once($app_root_path.'class/Love.class.php');

/**
 * The purpose of this class is to provide general info about the user including
 * user statistics and other data which might be relevant to the given user
 */
class UserInfo extends DataObject {

    protected $id;
    protected $username;
    protected $nickname;
    protected $picture;

    /**
     * Loads user using it's nickname
     *
     * @param $nickname User nickname
     * @param $company_id Company id in which search for the user
     */
    public function loadUserByNickname($nickname, $company_id) {

        $sql = "SELECT * FROM " . USERS . " u
                WHERE u.nickname = '" . mysql_real_escape_string($nickname) . "' AND
                u.company_id = $company_id";
        $this->loadUser($sql);
    }

    /**
     * Loads user using it's username (email)
     *
     * @param $username User username(email)
     * @param $company_id Company id in which search for the user
     */
    public function loadUserByUsername($username, $company_id) {

        $username = str_replace(" ","+",$username);

        $sql = "SELECT * FROM " . USERS . " u
                WHERE u.username = '" . mysql_real_escape_string($username) . "' AND
                u.company_id = $company_id";
        $this->loadUser($sql);
    }

    /**
     * Loads user using supplied query
     * Method is used by loadUserByUsername and loadUserByNickname
     *
     * @param $sql - Sql code to search and load the user
     * @return object instance of UserInfo for method chaining
     */
    private function loadUser($sql) {
        $row = $this->doQuery($sql);

        if(!$row) {
            return false;
        }

        $this->setId($row->id);
        $this->setUsername($row->username);
        $this->setNickname($row->nickname);
        $this->setPicture($row->picture);
        return $this;
    }

    /**
     * Gets path for the user profile image with given dimentions
     *
     * @param $w Width of the image
     * @param $h Height of image
     * @param $zc Crop image
     * @return Path for the image to use in <img src="path" />
     */
    public function getPhoto($w = 100, $h = 100, $zc = 0) {
        return Utils::getUserImageByPicture($this->getPicture(), $w, $h, $zc);
    }

    /**
     * Gets number of loves user has sent a day on average during last week
     * @return Integer number of loves
     */
    public function getWeekAvgSent() {
        return Love::getUserAverageSent($this->getUsername());
    }

    /**
     * Gets total number of loves user has sent
     * @return Integer number of loves
     */
    public function getTotalSent() {
        return Love::getUserTotal($this->getUsername());
    }

    /**
     * Gets total number of loves user has received
     * @return Integer number of loves
     */
    public function getTotalReceived() {
        return Love::getUserTotal($this->getUsername(), Love::RECEIVER);
    }

    /**
     * Gets total number of people user has received love from
     * @return Integer number of loves
     */
    public function getUniqueSenders() {
        return Love::getUserUniqueSenders($this->getUsername());
    }

    /**
     * Performs sql query
     *
     * @param $sql Sql query to run
     * @return query result object
     */
    private function doQuery($sql) {
        $result = mysql_query($sql);
        $ret = mysql_fetch_object($result);
        return $ret;
    }

}
?>
