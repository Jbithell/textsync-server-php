<?php
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

$textsync = new Textsync\Textsync([
  'instance_locator' => 'your:instance:locator',
  'key' => 'your:key'
]);

if (!isset($_POST['docId'])) die("Document id not specified");

if (true) { //Decide here what permissions this user might have for this doc id
    $auth_data = $textsync->authenticate([ 'docId' => $_POST['docId'], 'permissions' => ['READ','WRITE'], 'tokenExpiry' => 1200]);
}

if ($auth_data['status'] != 200) throw new Exception("Error authorizing");


header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
echo json_encode($auth_data['body']);
