<?php
ini_set('display_errors', 'On');

error_reporting(E_ALL | E_STRICT);

require_once 'config.php';
require_once 'functions.php';
require_once 'lib/FolderParser.php';

if(defined('PHPUNIT_DIR')) {
    set_include_path(get_include_path() . PATH_SEPARATOR . PHPUNIT_DIR);
}

// Definitions:
define('RUN_SELECTED_LABEL', 'Run selected');
define('SELECT_ALL_LABEL', 'Select All');
define('CLEAR_ALL_LABEL', 'Clear All');
define('RESET_TESTER_LABEL', 'Reload Tests');
define('RUNNER_INFO_LABEL', 'Tests Info');
define('CLOSE_LABEL', 'Close');


// start the session
session_start();

// reset tester
if(isset($_REQUEST['reset']) && ('true' === $_REQUEST['reset'])) {
    session_destroy();
    session_start();
    // we will not run any suites in this case
    unset($_REQUEST['runSuites']);
}

// create the folderParser object
if(!isset($_SESSION['folderParser'])) {
    $_SESSION['folderParser'] = new FolderParser(ROOT_DIR, $exclude);
}
$folderParser = $_SESSION['folderParser'];

// Process the request:

$runSuites = array();
$testCase = null;

if (isset($_REQUEST['runSuites']) && is_array($_REQUEST['runSuites'])) {
    $runSuites = $_REQUEST['runSuites'];
}

if (isset($_REQUEST['keepOpen'])) {
    $keepOpen = trim($_REQUEST['keepOpen']);
} else {
    $keepOpen = '';
}
$keepOpen = explode(' ', $keepOpen);

// if is set code coverage
if (isset($_REQUEST['coverage']) && 'true' === $_REQUEST['coverage']) {
    // get the group name and the file name
    $suite = $runSuites[0];

    // set the file name
    $file = $folderParser->getSuitePath($suite);

    // set the class name
    $className = getClassName($file);

    // set the output dir for the code coverage
    $relativePath       = str_replace(ROOT_DIR . '/', '', $file);
    $codeCoveragePath   = str_replace(TEST_SUFFIX . '.php', '', $relativePath);
    $codeCoverageTarget = str_replace(TEST_SUFFIX, '', $className) . '.php.html';

    $codeCoverageDir = CODE_COVERAGE_DIR . '/' . $codeCoveragePath;

    $command = PHPUNIT . " --coverage-html $codeCoverageDir $className $file";
    //echo $command; die();

    // run the code coverage
    exec($command);

    // redirect to the code coverage dir
	if (isAjax()){
    	echo file_get_contents(basename(CODE_COVERAGE_DIR) . "/$codeCoveragePath/" . PRECEDE_TESTS.$codeCoverageTarget);
    }else{
    	header(sprintf("Location: " . basename(CODE_COVERAGE_DIR) . "/$codeCoveragePath/" . PRECEDE_TESTS.$codeCoverageTarget));
    }
    
//    echo('Loading...');
    exit(0);
}

if (isAjax()) {
    // action can be: 'START', 'CONTINUE' or 'STOP'
    if (isset($_GET['action'])) {
        switch($_GET['action'])
        {
            case 'START':
                if(0 == initializeSuites()) {
                    printf('0'); // no tests to run
                }
            break;

            case 'CONTINUE':
                list($testClass, $testName, $testResult) = runCurrentTestCase();
                incrementSuite();
                printStatus($testClass, $testName, $testResult);
            break;

            case 'STOP':
                stopTestsAndDisplay();
            break;
        }
    }
} else {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Test Suites</title>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />

<link href="css/container.css" rel="stylesheet" type="text/css" />
<link href="css/coveragestyle.css" rel="stylesheet" type="text/css" />

<link href="css/style.css" rel="stylesheet" type="text/css" />
<link href="css/contextMenu.css" rel="stylesheet" type="text/css" />
<script src="js/jquery-1.2.6.js" type="text/javascript"></script>
<script src="js/jquery.contextMenu.js" type="text/javascript"></script>
<script src="js/functions.js" type="text/javascript"></script>

<script src="js/yahoo-dom-event.js" type="text/javascript"></script>
<script src="js/container-min.js" type="text/javascript"></script>
<script>
    var useAjax = <?php echo USE_AJAX ? 'true' : 'false' ?>;
    $(document).ready(onDocumentLoad);
</script>
</head>
<body>
<!-- Context menu -->
<ul id="myContextMenu" class="contextMenu">
    <li class="menuRun">
        <a href="#menuRun"><?php echo RUN_SELECTED_LABEL; ?></a>
    </li>
    <li class="menuReload">
        <a href="#menuReload"><?php echo RESET_TESTER_LABEL; ?></a>
    </li>
</ul>
<!-- Main menu and form -->
<div id="menu">
<form id="menuForm" method="post" action="./">
<?php
    if($folderParser->getRootFolderId() !== 0) {
        displayMenu($folderParser, $folderParser->getRootFolderId());
    } else {
        echo 'No tests found. Please check out your config file.<br /><br />';
    }
?>
<input type="hidden" name="keepOpen" id="keepOpen" value="<?php echo $keepOpen ?>"/>
<input type="hidden" name="coverage" id="coverage" value="false" />
<input type="hidden" name="reset" id="reset" value="false" />
<div>
    <input class="button" style="width: 80px;" type="button" name="action" id="submitForm" value="<?php echo RUN_SELECTED_LABEL ?>" onclick="runTests()" />
</div>
<p>
    <input class="button" style="width: 80px;" type="button" name="action" id="resetButton" value="<?php echo RESET_TESTER_LABEL ?>" onclick="resetTester()" />
</p>
</form>
	<input class="button" style="width: 80px;" type="button" name="action" id="runnerButton" value="<?php echo RUNNER_INFO_LABEL ?>" onclick="runnerInfo()" />
</div>
<!-- Test Results -->
<div id="status">
 <div id="loaderContainer"><div id="loader"></div></div>
 <table><tr><td><div id="runnedTest"></div></td><td><div id="testResult"></div></td></tr></table>
 <input class="button" style="width: 80px;" type="button" name="action" value="Stop" onclick="forceStop()" />
 <div id="buffer" style="display: none;"></div>
</div>
<div id="suites">

<?php
    flush();
    if (0 !== initializeSuites()) {
        while($_SESSION['runnedTests'] < $_SESSION['testCount']) {
            list($testClass, $testName) = runCurrentTestCase();
            incrementSuite();
        }
        stopTestsAndDisplay();
    }
?>
</div>
	<div id="runnerInfo">
		<input class="button" style="width: 80px; float: right;" type="button" name="action" id="runnerButton" value="<?php echo CLOSE_LABEL ?>" onclick="hideEl('runnerInfo')" />
		
		<h3>CurlHandlerTest</h3>
		<p><strong>Description:</strong> CurlHandler is a class that is used to make POST and GET requests.</p>
		<p><strong>Affects:</strong> If this class doesn't work any communication that goes out of the Login app is broken.</p>
		
		<hr />
			
		<h3>DataObjectTest</h3>
		<p>
			<strong>Description:</strong> 
			DataObject is a class that is used to benefit developers. Lets say that a developer creates a class named Car. 
			The Car class will have members like licensePlate, speed. When the developer uses that class, he/she will need to 
			set the licensePLate and the speed. This has to be defined in the code, the so-called setters and getters. DataObject saves 
			the day by eliminating the need of writing these methods. By extending that class developers are able to use setters and getters 
			without coding them.
		</p>
		<p><strong>Affects:</strong> If this class fails everything else will fail, this is the core class and all classes extend it</p>
		
		<hr />
		
		<h3>FunctionsTest</h3>
		<p><strong>Description:</strong> This class holds common functions that could be used at different places in other classes.</p>
		<p><strong>Affects:</strong> If this class fails, authenticating the user and creating a new user will fail.</p>
		
		<hr />
		
		<h3>LoaderTest</h3>
		<p><strong>Description:</strong> This is a core class. It loads all classes.</p>
		<p><strong>Affects:</strong> If this class fails, the whole application will fail as this is part of the core classes</p>
		
		<hr />
		
		<h3>UserTest</h3>
		<p><strong>Description:</strong> User class, as the name suggests, is responsible for working with users. Adding users, modifying users, authenticating users.</p>
		<p>
			<strong>Affects:</strong> 
			Depending on what exactly fails, some of the following functionality will be broken:
			<ol class="infoList">
				<li>Creating new users</li>
				<li>Authenticating users</li>
				<li>Updating users</li>
				<li>Confirming users</li>
			</ol>
		</p>
		
	</div>
</html>
<?php
}
/*EOF*/
