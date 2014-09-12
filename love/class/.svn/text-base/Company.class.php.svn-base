<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('class','',$app_root_path);
require_once($app_root_path.'class/DataObject.php');
class Company extends DataObject
{
	/**
	 * @var int Company id
	 */
	protected $id;
	/**
	 * @var string Company name
	 */
	protected $name;
	/**
	 * @var int Company features
	 */
	protected $features;
	/**
	 * @var string Company logo
	 */
	protected $logo;
	/**
	 * @var float Company love_multiplier
	 */
	protected $love_multiplier;
	/**
	 * @var int Weekly updates
	 */
	protected $weekly_updates;
	/**
	 * @var string Review done graph color
	 */
	protected $review_done_color;
	/**
	 * @var string Review not done graph color
	 */
	protected $review_not_done_color;

	public function __construct($id)
	{
		parent::__construct();
		$this->loadCompanyById($id);
	}
	
	public function loadCompanyById($id)
	{ 
		$sql = 'SELECT * FROM `' . COMPANY . "` WHERE `id` = '" . (int)$id . "';";
		$result = mysql_query($sql) or error_log("load company by id: ".mysql_error()."\n".$sql);
		$row = mysql_fetch_assoc($result);
		$this->setId($row['id']);
		$this->setName($row['name']);
		$this->setFeatures($row['features']);
		$this->setLogo($row['logo']);
		$this->setLove_multiplier($row['love_multiplier']);
		$this->setReview_done_color($row['review_done_color']);
		$this->setReview_not_done_color($row['review_not_done_color']);
	}
}
