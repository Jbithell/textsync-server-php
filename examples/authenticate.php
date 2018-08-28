<?php
require_once(dirname(__FILE__) . '/../vendor/autoload.php');

$textsync = new Textsync\Textsync([
  'instance_locator' => 'your:instance:locator',
  'key' => 'your:key'
]);

$payload = json_decode(file_get_contents('php://input'), true); //We can't use post as we might normally because the client passes content type application/json

if (!isset($payload['docId'])) die("Please pass a document id"); //Generic response to a request to this url

if (true) { //Decide here what permissions this user might have for this doc id
    $auth_data = $textsync->authenticate([ 'docId' => $payload['docId'], 'permissions' => ['READ','WRITE'], 'tokenExpiry' => 1200]);
}
if ($auth_data['status'] != 200) throw new Exception("Error authorizing");
header("Access-Control-Allow-Origin: *");
header('Content-type: application/json');
echo json_encode($auth_data['body']);
