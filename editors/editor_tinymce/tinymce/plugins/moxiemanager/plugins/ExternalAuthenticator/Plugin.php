<?php
/**
 * Plugin.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

@session_start();

/**
 * This class handles MoxieManager SessionAuthenticator stuff.
 */
class MOXMAN_ExternalAuthenticator_Plugin implements MOXMAN_Auth_IAuthenticator {
	public function authenticate(MOXMAN_Auth_User $user) {
		$config = MOXMAN::getConfig();

		$secretKey = $config->get("ExternalAuthenticator.secret_key");
		$authUrl = $config->get("ExternalAuthenticator.external_auth_url");

		if (!$secretKey || !$authUrl) {
			throw new MOXMAN_Exception("No key/url set for ExternalAuthenticator, check config.");
		}

		// Build url
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
			$url = "https://";
		} else {
			$url = "http://";
		}

		$url .= $_SERVER['HTTP_HOST'];

		if ($_SERVER['SERVER_PORT'] != 80) {
			$url .= ':' . $_SERVER['SERVER_PORT'];
		}

		$httpClient = new MOXMAN_Http_HttpClient($url);

		$authUrl = MOXMAN_Util_PathUtils::toAbsolute(dirname($_SERVER["REQUEST_URI"]) . '/plugins/ExternalAuthenticator', $authUrl);
		$request = $httpClient->createRequest($url . $authUrl);

		$cookie = '';
		foreach ($_COOKIE as $name => $value) {
			$cookie .= ($cookie ? '; ' : '') . $name . '=' . $value;
		}

		$request->setHeader('cookie', $cookie);

		$seed = $cookie . uniqid() . time();
		$hash = hash_hmac('sha256', $seed, $secretKey);

		$response = $request->send(array(
			"seed" => $seed,
			"hash" => $hash
		));

		$json = json_decode($response->getBody());


		if (!$json) {
			throw new MOXMAN_Exception("Did not get a proper JSON response from Auth url.");
		}

		if (isset($json->result)) {
			foreach ($json->result as $key => $value) {
				$key = str_replace('_', '.', $key);
				$config->put($key, $value);
			}

			return true;
		} else if (isset($json->error)) {
			throw new MOXMAN_Exception($json->error->message . " - ". $json->error->code);
		} else {
			throw new MOXMAN_Exception("Generic unknown error, did not get a proper JSON response from Auth url.");
		}
	}
}

MOXMAN::getAuthManager()->add("ExternalAuthenticator", new MOXMAN_ExternalAuthenticator_Plugin());

?>
