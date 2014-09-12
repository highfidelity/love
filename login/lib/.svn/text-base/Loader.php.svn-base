<?php
/**
 * Autoloader
 *
 * This is an autoloading utility class. @see loadlib.php for a concrete
 * example of usage.
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */
class Loader
{
    /**
     * @var Loader
     */
    private static $_instance;

    protected $classes = array();

    // @codeCoverageIgnoreStart
    /**
    * Constructor
    */
    protected function __construct()
    {
    }
    // @codeCoverageIgnoreEnd

    /**
     * Singleton getter
     *
     * @return Loader
     */
    public static function getInstance()
    {
        // @codeCoverageIgnoreStart
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        // @codeCoverageIgnoreEnd
        return self::$_instance;
    }

    // @codeCoverageIgnoreStart
    /**
    * Register loader with spl
    *
    * @return void
    */
    public static function splRegister()
    {
        // check if __autoload() function is in use
        if (function_exists('__autoload')) {
            // check if the __autoload function is registered
            $autoloadStack = spl_autoload_functions();
            if (!in_array('__autoload', $autoloadStack)) {
                // add __autoload function to the stack
                spl_autoload_register('__autoload');
            }
        }
        // register Loader callback
        spl_autoload_register(array(__CLASS__, 'load'));
    }
    // @codeCoverageIgnoreEnd

    /**
     * Register a class
     *
     * @param string $className The class name
     * @param string $fileName  The file where the class resides
     *
     * @return void
     * @throws Loader_Exception
     */
    public static function registerClass($className, $fileName)
    {
        $instance = self::getInstance();
        if (isset($instance->classes[$className])) {
            throw new Loader_Exception('Class "' . $className . '" already registered.');
        }
        if (!file_exists($fileName) || !is_readable($fileName)) {
            throw new Loader_Exception('File "' . $fileName . '" is not accessible.');
        }
        $instance->classes[$className] = $fileName;
    }

    /**
     * Load a registered class
     *
     * @param string $className The class name to load
     *
     * @return void
     * @throws Loader_Exception
     */
    public static function load($className)
    {
        $instance = self::getInstance();
        if (!isset($instance->classes[$className])) {
            throw new Loader_Exception('Class "' . $className . '" not found.');
        }
        $fileName = $instance->classes[$className];
        // @codeCoverageIgnoreStart
        if (!file_exists($fileName)) {
            throw new Loader_Exception('File "' . $fileName . '" not found.');
        }
        // @codeCoverageIgnoreEnd
        include_once $fileName;
        if (!class_exists($className, false) && !interface_exists($className, false)) {
            throw new Loader_Exception('Requested class "' . $className . '" was not found in registered location "' . $fileName . '".');
        }
    }
}
