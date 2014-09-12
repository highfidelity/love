<?php
$app_root_path = dirname(__FILE__);
$app_root_path = str_replace('class','',$app_root_path);

require_once ($app_root_path . 'class/DataObject.php');
require_once ($app_root_path . 'class/Database.class.php');

//Fix for fetch image from admin database 
if ($_SERVER['SERVER_NAME'] == 'dev.sendlove.us' || $_SERVER['SERVER_NAME'] == 'www.sendlove.us' || $_SERVER['SERVER_NAME'] == 'sendlove.us') {
    if (isset($_REQUEST['app']) && $_REQUEST['app'] == 'admin') {
        define("APP_DB",DB_NAME_ADMIN);
    } else {
        define("APP_DB",DB_NAME);
    }
} else {
    define("APP_DB",DB_NAME);
}

class Assets extends DataObject {
    /**
     * Image id
     * @var int
     */
    protected $id;
    
    /**
     * Application name
     * @var string 
     */
    protected $app;
    
    /**
     * The type of the content (image, html etc)
     * @var string
     */
    protected $content_type;
    
    /**
     * Blob representation of the content
     * @var string
     */
    protected $content;
    
    /**
     * Size of the asset - for image the size of the image
     * @var int
     */
    protected $size;
    
    /**
     * Filename of the asset
     * @var string
     */
    protected $filename;
    
    /**
     * Original filename of the asset
     * @var string
     */
    protected $original_filename;
    
    /**
     * Width of the asset - used usually when the stored asset is an image
     * @var int
     */
    protected $width;
    
    /**
     * Height of the asset - used usually when the stored asset is an image
     * @var int
     */
    protected $height;
    
    /**
     * Date when the asset was created
     * @var string
     */
    protected $created;
    
    /**
     * Date when the asset was updated
     * @var string
     */
    protected $updated;
    
    /**
     * This is the database object used to query the database
     * @var $db OBJECT
     */
    protected $db;
    
    /**
     * The requested image
     * @var string
     */
    protected $requested_image;
    
    /**
     * Constructor that calles the parent class constructor
     */
    public function __construct() {
        parent::__construct();
        $this->db = new Database();
    }
    
    /**
     * Checks to see if image exists in the database
     * @param string $imageName
     * @return bool
     */
    public function imageExists($imageName, $id = false) {
        if (strrchr($imageName,"/")) {
            $imageName = strtolower(substr(strrchr($imageName,"/"),1,strlen(strrchr($imageName,"/"))));
        }
        if ($id === true) {
            $sql = "SELECT * FROM " . APP_DB . "." . ALL_ASSETS . " WHERE id = " . ( int ) $imageName;
        } else {
            $sql = "SELECT * FROM " . APP_DB . "." . ALL_ASSETS . " WHERE filename = '" . mysql_real_escape_string($imageName,$this->db->getLink()) . "'";
        }
        if (($res = $this->db->query($sql)) && (mysql_num_rows($res) > 0)) {
            $this->assignImageProperties(mysql_fetch_assoc($res));
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Populates class data members
     * @param array $properties
     */
    protected function assignImageProperties($properties) {
        foreach ( $properties as $property => $value ) {
            if (! isset($this->$property) || is_null($this->$property)) {
                $this->$property = $value;
            }
        }
    }
}