<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
$app = 'love';
$class = "class";
if (isset($_GET['app']) ) {
    $app = $_GET['app'];
    if ($app == "review") {
        $class = "classes";
    }
}
if (!defined('APP_PATH'))     define('APP_PATH', realpath(dirname(__FILE__) . '/' . '../' . $app ));
require_once('../' . $app . '/' . $class . '/CompressedFiles.class.php');
require_once('../' . $app . '/' . $class . '/Compressor.class.php');

$compressedFiles = new CompressedFiles();


function compile($compressedFiles,$app,$nameInclude,$compile,$delete) {
    $compressor = new Compressor();
    $compressor->setCompressorType('js')
               ->setPath(realpath(APP_PATH . '/' . $compressor->getFilesDir($nameInclude,$compressedFiles) ))
               ->setFiles($compressedFiles->files[$nameInclude])
               ->setFilename($nameInclude);
    if ($delete == true) {
        echo "Deleted: " . $compressor->getCombinedFilePath() . "<br/>\n";
        unlink($compressor->getCombinedFilePath());
        echo "Deleted: " . $compressor->getCompiledFilePath() . "<br/>\n";
        unlink($compressor->getCompiledFilePath());
    }
    if ($compile == true) {
        $compressor->compile(true);
        echo "Compiled: " . $nameInclude . "<br/>\n";
        
    }
}

function compileAll($compressedFiles,$app,$nameInclude,$compile,$delete) {
    if ($nameInclude == "ALL") {
        foreach ($compressedFiles->files as $key => $fileNameInclude) {
            compile($compressedFiles,$app,$key,$compile,$delete);
        }
        
    } else {
        compile($compressedFiles,$app,$nameInclude,$compile,$delete);
    }
}

if (isset($_GET['detail']) ) {
    $detail = $_GET['who'];
}

if (isset($_GET['delete']) ) {
//die("dom");
    compileAll($compressedFiles,$app,$_GET['who'],false,true);
}

if (isset($_GET['compile']) ) {
    compileAll($compressedFiles,$app,$_GET['who'],true,true);
}

echo  "Applications available : <a href='?app=love' title='click to get details'>love</a>
        - <a href='?app=review' title='click to get details'>review</a>
        <br/>\n";

echo  "Current Application : ".$app . "
         - <a href='?app=".$app."&compile=y&who=ALL' >compile</a>
         - <a href='?app=".$app."&delete=y&who=ALL' >delete</a>
        <br/>\n";


foreach ($compressedFiles->files as $key => $fileNameInclude) {
    echo  "<a href='?app=".$app."&detail=y&who=" . $key ."' title='click to get details'>" .$key . "</a>
             - <a href='?app=".$app."&compile=y&who=" . $key ."' >compile</a>
             - <a href='?app=".$app."&delete=y&who=" . $key ."' >delete</a>
            <br/>\n";
    if (isset($detail) && $detail ==$key) {
        foreach ($fileNameInclude as $fileName) {
            echo  "...." . $fileName . " <br/>\n";
        }
    }
}

?>