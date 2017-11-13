<?php
 
require 'Slim/Slim.php';
require_once 'dbHandler.php';

 
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

//Enable logging
$app->log->setEnabled(true);
$app->log->setLevel(\Slim\Log::DEBUG);

// User id from db - Global Variable
$user_id = NULL;

require_once 'utils.php';
require_once 'document_management.php';
 

$app->get('/', function () {
    echo "Hello! Welcome to Document Search";
});


/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields,$request_params) {
    $error = false;
    $error_fields = "";
    foreach ($required_fields as $field) {
        if (!isset($request_params->$field) || strlen(trim($request_params->$field)) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["status"] = "error";
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(200, $response);
        $app->stop();
    }
}


function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}
 
$app->run();
 
?>