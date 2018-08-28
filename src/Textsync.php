<?php

namespace Textsync;

use \Firebase\JWT\JWT;

class Textsync
{
    protected $settings = array(
        'scheme'       => 'https',
        'port'         => 80,
        'timeout'      => 30,
        'debug'        => false,
    );
    protected $logger = null;

    protected $api_settings = array();
    protected $authorizer_settings = array();

    /**
     *
     * Initializes a new Textsync instance.
     *
     *
     * @param array $options   Options to configure the Textsync instance.
     *                         instance_locator - your Textsync instance locator
     *                         key - your Textsync instance's key
     */
    public function __construct($options)
    {
        $this->checkCompatibility();

        if (!isset($options['instance_locator'])) {
            throw new Exception('You must provide an instance_locator');
        }
        if (!isset($options['key'])) {
            throw new Exception('You must provide a key');
        }

        $this->settings['instance_locator'] = $options['instance_locator'];
        $this->settings['key'] = $options['key'];
        $this->authorizer_settings['service_name'] = $this->api_settings['service_name'] = "textsync";
        $this->authorizer_settings['service_version'] = $this->api_settings['service_version'] = "v1";

        foreach ($options as $key => $value) {
            // only set if valid setting/option
            if (isset($this->settings[$key])) {
                $this->settings[$key] = $value;
            }
        }
    }

    public function authenticate($auth_options)
    {
        if (!isset($auth_options['docId'])) {
            throw new Exception('You must provide a document ID');
        }
        if (!isset($auth_options['read'])) {
            $auth_options['read'] = false;
        }
        if (!isset($auth_options['write'])) {
            $auth_options['write'] = false;
        }
        if (!isset($auth_options['tokenExpiry']) || $auth_options['tokenExpiry'] > 12 * 60 * 60) { //12 hours max expiry
            $auth_options['tokenExpiry'] = 20 * 60; //Default 20 mins
        }

        $access_token = $this->generateAccessToken($auth_options);

        return [
            'status' => 200,
            'headers' => array(),
            'body' => [
                'access_token' => $access_token,
                'token_type' => 'bearer',
                'expires_in' => $auth_options['tokenExpiry']
            ]
        ];
    }

    public function generateAccessToken($auth_options)
    {
        return $this->generateToken($auth_options);
    }

    public function generateToken($auth_options)
    {
        $split_instance_locator = explode(":", $this->settings['instance_locator']);
        $split_key = explode(":", $this->settings['key']);


        $claims = array(
            "instance" => $split_instance_locator[2],
            "iss" => "api_keys/".$split_key[0],
            "iat" => time()
        );

        if (isset($auth_options['docId'])) {
            if (gettype($auth_options['docId']) != 'string') {
                throw new Exception('docId must be a string');
            }
            $claims['textsync'] = ["docId" => $auth_options['docId']];
        } else {
            throw new Exception('docId must be passed');
        }

        if (isset($auth_options['permissions'])) {
            if (gettype($auth_options['permissions']) != 'array') {
                throw new Exception('docId must be an array');
            }
            $claims['textsync']["permissions"] = $auth_options['permissions'];
        }


        $claims['exp'] = time() + $auth_options['tokenExpiry'];

        return JWT::encode($claims, $split_key[1]);
    }

    /**
     * Set a logger to be informed of internal log messages.
     *
     * @return void
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * Log a string.
     *
     * @param string $msg The message to log
     *
     * @return void
     */
    protected function log($msg)
    {
        if (is_null($this->logger) === false) {
            $this->logger->log('TextSync: '.$msg);
        }
    }

    /**
     * Check if the current PHP setup is sufficient to run this class.
     *
     * @throws TextSyncException if any required dependencies are missing
     *
     * @return void
     */
    protected function checkCompatibility()
    {
        if (!extension_loaded('json')) {
            throw new Exception('The Textsync library requires the PHP JSON module. Please ensure it is installed');
        }
    }
}
