<?php
require_once('classes/DataObject.php');
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

	public function __construct($id)
	{
		parent::__construct();
		$this->loadCompanyById($id);
	}
	
	public function loadCompanyById($id)
	{
		$sql = 'SELECT * FROM `' . LOVE_COMPANIES . '` WHERE `id` = ' . (int)$id . ';';
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);
		$this->setId($row['id']);
		$this->setName($row['name']);
		$this->setFeatures($row['features']);
		$this->setLogo($row['logo']);
		$this->setLove_multiplier($row['love_multiplier']);
		$this->setWeekly_updates($row['weekly_updates']);
	}
}
