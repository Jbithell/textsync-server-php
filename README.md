# Textsync PHP Server SDK

[![Read the docs](https://img.shields.io/badge/read_the-docs-92A8D1.svg)](https://docs.pusher.com/textsync)
[![Twitter](https://img.shields.io/badge/twitter-@Pusher-blue.svg?style=flat)](http://twitter.com/Pusher)
[![GitHub license](https://img.shields.io/badge/license-MIT-lightgrey.svg)](https://github.com/pusher/chatkit-server-php/blob/master/LICENSE.md)
[![Packagist](https://img.shields.io/packagist/v/jbithell/pusher-textsync-server.svg)](https://packagist.org/packages/jbithell/pusher-textsync-server)


Find out more about Textsync [here](https://pusher.com/textsync).

## Installation

You can get the Textsync PHP SDK via a composer package called `pusher-textsync-server`. See <https://packagist.org/packages/jbithell/pusher-textsync-server>

```bash
$ composer require jbithell/pusher-textsync-server
```

Or add to `composer.json`:

```json
"require": {
    "jbithell/pusher-textsync-server": "^1.0.0"
}
```

and then run `composer update`.

Or you can clone or download the library files.

**We recommend you [use composer](http://getcomposer.org/).**

This library depends on PHP modules for JSON. See [JSON module installation instructions](http://php.net/manual/en/json.installation.php).

## Usage Example
Require the package through composer's autoload file or directly as above.
In the textsync client editor set the auth endpoint as the php file with the contents as below and fill out the keys and locator details from the pusher dash. 

```php
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
```
