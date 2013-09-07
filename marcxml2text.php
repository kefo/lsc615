<?php

//ini_set('display_errors', true);
//error_reporting(E_ALL);

$uri = "";

if ( isset($_GET["uri"]) ) {
    $uri = $_GET["uri"];
} else {
    exit;
}

include("classes/class.Record.php");

$r = new Record;
if ( $r->getRecord($uri) ) {
    $txt = $r->recordToTxt();
    
    header('Content-type: text/plain');
    echo $txt;
    exit;
}

exit;

?>