<?php
/**
 * Request
 *
 * Parts are taken from Zend Framework, released under the new BSD license.
 * See THIRDPARTY.txt for license info.
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @version    SVN: $Id: Request.php 47 2010-04-17 09:36:03Z seong $
 * @edited     26-MAY-2010 <Yani>
 * @link       http://www.lovemachineinc.com
 */
/**
 * Request object
 *
 * @category   LoveMachine
 * @package    Core
 * @author     LoveMachine Inc. <all@lovemachineinc.com>
 * @license    Copyright (c) 2009-2010, LoveMachine Inc. All Rights Reserved
 * @link       http://www.lovemachineinc.com
 */
class Request
{
    /**
     * Scheme for http
     */
    const SCHEME_HTTP  = 'http';

    /**
     * Scheme for https
     */
    const SCHEME_HTTPS = 'https';

    /**
     * The requested URI
     *
     * @var string
     */
    protected $requestUri;
    /**
     * Base url
     *
     * @var string
     */
    protected $baseUrl;
    /**
     * The path info
     *
     * @var string
     */
    protected $pathInfo;
    /**
     * The success page
     *
     * @var string
     */
    protected $repost_page;
    /**
     * Constructor
     */
    public function __construct($requestUri = null)
    {
        if ($requestUri instanceof Request) {
            $this->setRequestUri($requestUri->getRequestUri())
            ->setBaseUrl($requestUri->getBaseUrl())
            ->setPathInfo($requestUri->getPathInfo());
        } else {
            $this->setRequestUri($requestUri);
        }
    }

    /**
     * Sets the request URI
     *
     * @param string $requestUri The request URI
     *
     * @return Request
     */
    public function setRequestUri($requestUri = null)
    {
        if ($requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) { // check this first so IIS will catch
                $requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            } elseif (
            // IIS7 with URL Rewrite: make sure we get the unencoded url (double slash problem)
            isset($_SERVER['IIS_WasUrlRewritten'])
            && $_SERVER['IIS_WasUrlRewritten'] == '1'
            && isset($_SERVER['UNENCODED_URL'])
            && $_SERVER['UNENCODED_URL'] != ''
            ) {
                $requestUri = $_SERVER['UNENCODED_URL'];
            } elseif (isset($_SERVER['REQUEST_URI'])) {
                $requestUri = $_SERVER['REQUEST_URI'];
                // Http proxy reqs setup request uri with scheme and host [and port] + the url path, only use url path
                $schemeAndHttpHost = $this->getScheme() . '://' . $this->getHttpHost();
                if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                    $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
                }
            } elseif (isset($_SERVER['ORIG_PATH_INFO'])) { // IIS 5.0, PHP as CGI
                $requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING'])) {
                    $requestUri .= '?' . $_SERVER['QUERY_STRING'];
                }
            } else {
                return $this;
            }
        } elseif (!is_string($requestUri)) {
            return $this;
        } else {
            // Set GET items, if available
            if (false !== ($pos = strpos($requestUri, '?'))) {
                // Get key => value pairs and set $_GET
                $query = substr($requestUri, $pos + 1);
                parse_str($query, $vars);
                $this->setQuery($vars);
            }
        }

        $this->requestUri = $requestUri;
        $this->setBaseUrl($this->getBaseUrl());
        $this->setPathInfo($this->getPathInfo());
        if(isset($_REQUEST["repost_page"])){
            $this->setRepostPage($_REQUEST["repost_page"]);
        }
        return $this;
    }

    /**
     * Returns the REQUEST_URI taking into account
     * platform differences between Apache and IIS
     *
     * @return string
     */
    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            $this->setRequestUri();
        }

        return $this->requestUri;
    }

    /**
     * Set the base URL of the request; i.e., the segment leading to the script name
     *
     * E.g.:
     * - /admin
     * - /myapp
     * - /subdir/index.php
     *
     * Do not use the full URI when providing the base. The following are
     * examples of what not to use:
     * - http://example.com/admin (should be just /admin)
     * - http://example.com/subdir/index.php (should be just /subdir/index.php)
     *
     * If no $baseUrl is provided, attempts to determine the base URL from the
     * environment, using SCRIPT_FILENAME, SCRIPT_NAME, PHP_SELF, and
     * ORIG_SCRIPT_NAME in its determination.
     *
     * @param mixed $baseUrl The base url
     *
     * @return Request
     */
    public function setBaseUrl($baseUrl = null)
    {
        if ((null !== $baseUrl) && !is_string($baseUrl)) {
            return $this;
        }

        if ($baseUrl === null) {
            $filename = (isset($_SERVER['SCRIPT_FILENAME'])) ? basename($_SERVER['SCRIPT_FILENAME']) : '';

            if (isset($_SERVER['SCRIPT_NAME']) && basename($_SERVER['SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['SCRIPT_NAME'];
            } elseif (isset($_SERVER['PHP_SELF']) && basename($_SERVER['PHP_SELF']) === $filename) {
                $baseUrl = $_SERVER['PHP_SELF'];
            } elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
                $baseUrl = $_SERVER['ORIG_SCRIPT_NAME']; // 1and1 shared hosting compatibility
            } else {
                // Backtrack up the script_filename to find the portion matching
                // php_self
                $path    = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : '';
                $file    = isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : '';
                $segs    = explode('/', trim($file, '/'));
                $segs    = array_reverse($segs);
                $index   = 0;
                $last    = count($segs);
                $baseUrl = '';
                do {
                    $seg     = $segs[$index];
                    $baseUrl = '/' . $seg . $baseUrl;
                    ++$index;
                } while (($last > $index) && (false !== ($pos = strpos($path, $baseUrl))) && (0 != $pos));
            }

            // Does the baseUrl have anything in common with the request_uri?
            $requestUri = $this->getRequestUri();

            if (0 === strpos($requestUri, $baseUrl)) {
                // full $baseUrl matches
                $this->baseUrl = $baseUrl;
                return $this;
            }

            if (0 === strpos($requestUri, dirname($baseUrl))) {
                // directory portion of $baseUrl matches
                $this->baseUrl = rtrim(dirname($baseUrl), '/');
                return $this;
            }

            $truncatedRequestUri = $requestUri;
            if (($pos = strpos($requestUri, '?')) !== false) {
                $truncatedRequestUri = substr($requestUri, 0, $pos);
            }

            $basename = basename($baseUrl);
            if (empty($basename) || !strpos($truncatedRequestUri, $basename)) {
                // no match whatsoever; set it blank
                $this->baseUrl = '';
                return $this;
            }

            // If using mod_rewrite or ISAPI_Rewrite strip the script filename
            // out of baseUrl. $pos !== 0 makes sure it is not matching a value
            // from PATH_INFO or QUERY_STRING
            $isSubset = (strlen($requestUri) >= strlen($baseUrl));
            $isSubset = $isSubset && (false !== ($pos = strpos($requestUri, $baseUrl))) && ($pos !== 0);
            if ($isSubset) {
                $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
            }
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        return $this;
    }

    /**
     * Everything in REQUEST_URI before PATH_INFO
     * <form action="<?=$baseUrl?>/news/submit" method="POST"/>
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (null === $this->baseUrl) {
            $this->setBaseUrl();
        }

        return $this->baseUrl;
    }

    /**
     * Set the PATH_INFO string
     *
     * @param string|null $pathInfo The path info to set
     *
     * @return Request
     */
    public function setPathInfo($pathInfo = null)
    {
        if ($pathInfo === null) {
            $baseUrl = $this->getBaseUrl();

            if (null === ($requestUri = $this->getRequestUri())) {
                return $this;
            }

            // Remove the query string from REQUEST_URI
            if ($pos = strpos($requestUri, '?')) {
                $requestUri = substr($requestUri, 0, $pos);
            }

            if (null !== $baseUrl
            && ((!empty($baseUrl) && 0 === strpos($requestUri, $baseUrl))
            || empty($baseUrl))
            && false === ($pathInfo = substr($requestUri, strlen($baseUrl)))
            ) {
                // If substr() returns false then PATH_INFO is set to an empty string
                $pathInfo = '';
            } elseif (null === $baseUrl
            || (!empty($baseUrl) && false === strpos($requestUri, $baseUrl))
            ) {
                $pathInfo = $requestUri;
            }
        }

        $this->pathInfo = (string) $pathInfo;
        return $this;
    }

    /**
     * Returns everything between the BaseUrl and QueryString.
     * This value is calculated instead of reading PATH_INFO
     * directly from $_SERVER due to cross-platform differences.
     *
     * @return string
     */
    public function getPathInfo()
    {
        if (empty($this->pathInfo)) {
            $this->setPathInfo();
        }

        return $this->pathInfo;
    }

    /**
     * Returns an array of path info parts
     *
     * @return array Path info
     */
    public function getPathArray()
    {
        return explode('/', $this->getPathInfo());
    }

    /**
     * Get the HTTP host.
     *
     * "Host" ":" host [ ":" port ] ; Section 3.2.2
     * Note the HTTP Host header is not the same as the URI host.
     * It includes the port while the URI host doesn't.
     *
     * @return string
     */
    public function getHttpHost()
    {
        $host = $this->getServer('HTTP_HOST');
        if (!empty($host)) {
            return $host;
        }

        $scheme = $this->getScheme();
        $name   = $this->getServer('SERVER_NAME');
        $port   = $this->getServer('SERVER_PORT');

        if (($scheme == self::SCHEME_HTTP && $port == 80) || ($scheme == self::SCHEME_HTTPS && $port == 443)) {
            return $name;
        } else {
            return $name . ':' . $port;
        }
    }

    /**
     * Get the request URI scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return ($this->getServer('HTTPS') == 'on') ? self::SCHEME_HTTPS : self::SCHEME_HTTP;
    }

    /**
     * Return the method by which the request was made
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getServer('REQUEST_METHOD');
    }

    /**
     * Retrieve a member of the $_SERVER superglobal
     *
     * If no $key is passed, returns the entire $_SERVER array.
     *
     * @param string $key     The key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getServer($key = null, $default = null)
    {
        if (null === $key) {
            return $_SERVER;
        }

        return (isset($_SERVER[$key])) ? $_SERVER[$key] : $default;
    }
    
    /**
     * Retrieve a member of the $_GET superglobal
     *
     * If no $key is passed, returns the entire $_GET array.
     *
     * @param string $key     The key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getGet($key = null, $default = null)
    {
        if (null === $key) {
            return $_GET;
        }

        return (isset($_GET[$key])) ? $_GET[$key] : $default;
    }
    
    /**
     * Retrieve a member of the $_POST superglobal
     *
     * If no $key is passed, returns the entire $_POST array.
     *
     * @param string $key     The key
     * @param mixed  $default Default value to use if key not found
     *
     * @return mixed Returns null if key does not exist
     */
    public function getPost($key = null, $default = null)
    {
        if (null === $key) {
            return $_POST;
        }

        return (isset($_POST[$key])) ? $_POST[$key] : $default;
    }
    
    /**
     * Set GET values
     *
     * @param string|array $spec  The spec, can also be an array with key/value pairs
     * @param null|mixed   $value The (optional) value
     *
     * @return Request
     */
    public function setQuery($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            throw new Request_Exception('Invalid value passed to setQuery(); must be either array of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setQuery($key, $value);
            }
            return $this;
        }
        $_GET[(string) $spec] = $value;
        return $this;
    }

    /**
     * Set POST values
     *
     * @param string|array $spec  The spec, can also be an array with key/value pairs
     * @param null|mixed   $value The (optional) value
     *
     * @return Request
     */
    public function setPost($spec, $value = null)
    {
        if ((null === $value) && !is_array($spec)) {
            throw new Request_Exception('Invalid value passed to setPost(); must be either array of values or key/value pair');
        }
        if ((null === $value) && is_array($spec)) {
            foreach ($spec as $key => $value) {
                $this->setPost($key, $value);
            }
            return $this;
        }
        $_POST[(string) $spec] = $value;
        return $this;
    }

    /**
     * Was the request made by GET?
     *
     * @return boolean
     */
    public function isGet()
    {
        if ('GET' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by PUT?
     *
     * @return boolean
     */
    public function isPut()
    {
        if ('PUT' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by DELETE?
     *
     * @return boolean
     */
    public function isDelete()
    {
        if ('DELETE' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by HEAD?
     *
     * @return boolean
     */
    public function isHead()
    {
        if ('HEAD' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Was the request made by OPTIONS?
     *
     * @return boolean
     */
    public function isOptions()
    {
        if ('OPTIONS' == $this->getMethod()) {
            return true;
        }

        return false;
    }

    /**
     * Is the request a Javascript XMLHttpRequest?
     *
     * Should work with Prototype/Script.aculo.us, possibly others.
     *
     * @return boolean
     */
    public function isXmlHttpRequest()
    {
        if(isset($_SERVER["HTTP_X_REQUESTED_WITH"]) &&
           $_SERVER["HTTP_X_REQUESTED_WITH"] == 'XMLHttpRequest'){
           return true;
        } else {
           return false;
        }
    }
    
    /**
     * Sets repost page
     * @param string repost page to be assigned
     */
    public function setRepostPage($page){
        $this->repost_page = $page;
    }
    
    /**
     * Gets repost page
     * @return string repost page to be assigned
     */
    public function getRepostPage(){
        return $this->repost_page;
    }
}
