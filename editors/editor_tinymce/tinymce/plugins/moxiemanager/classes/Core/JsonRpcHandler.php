<?php
/**
 * JsonRpcHandler.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Http handler that takes JSON-RPC calls and executed MOXMAN_ICommand instances based in that input.
 *
 * @package MOXMAN_Core
 */
class MOXMAN_Core_JsonRpcHandler implements MOXMAN_Http_IHandler {
	/**
	 * Process a request using the specified context.
	 *
	 * @param MOXMAN_Http_Context $httpContext Context instance to pass to use for the handler.
	 */
	public function processRequest(MOXMAN_Http_Context $httpContext) {
		$request = $httpContext->getRequest();
		$response = $httpContext->getResponse();

		$response->disableCache();
		$response->setHeader('Content-type', 'application/json');

		@set_time_limit(5 * 60); // 5 minutes execution time

		$id = null;

		try {
			$json = MOXMAN_Util_Json::decode($request->get("json"));

			// Check if we should install
			if ($json && $json->method != "install") {
				$config = MOXMAN::getConfig()->getAll();

				if (empty($config)) {
					$exception = new MOXMAN_Exception("Installation needed.", MOXMAN_Exception::NEEDS_INSTALLATION);
					throw $exception;
				}

//				if (!preg_match('/^([0-9A-Z]{4}\-){7}[0-9A-Z]{4}$/', trim($config["general.license"]))) {
//					throw new MOXMAN_Exception("Invalid license: " . $config["general.license"]);
//				}
			}

			// Check if the user is authenticated or not
			if (!MOXMAN::getAuthManager()->isAuthenticated()) {
				if (!isset($json->method) || !preg_match('/^(login|logout|install)$/', $json->method)) {
					$exception = new MOXMAN_Exception("Access denied by authenticator(s).", MOXMAN_Exception::NO_ACCESS);

					$exception->setData(array(
						"login_url" => MOXMAN::getConfig()->get("authenticator.login_page")
					));

					throw $exception;
				}
			}

			if ($json && isset($json->id) && isset($json->method) && isset($json->params)) {
				$id = $json->id;
				$params = $json->params;
				$result = null;

				if (isset($params->access)) {
					MOXMAN::getAuthManager()->setClientAuthData($params->access);
				}

				$plugins = MOXMAN::getPluginManager()->getAll();
				foreach ($plugins as $plugin) {
					if ($plugin instanceof MOXMAN_ICommandHandler) {
						$result = $plugin->execute($json->method, $json->params);
						if ($result !== null) {
							break;
						}
					}
				}

				if ($result === null) {
					throw new Exception("Method not found: " . $json->method, -32601);
				}

				$response->sendJson((object) array(
					"jsonrpc" => "2.0",
					"result" => $result,
					"id" => $id
				));
			} else {
				throw new Exception("Invalid Request.", -32600);
			}

			MOXMAN::dispose();
		} catch (Exception $e) {
			MOXMAN::dispose(); // Closes any open file systems/connections

			$message = $e->getMessage();
			$data = null;

			// Add file and line number when running in debug mode
			if (MOXMAN::getConfig()->get("general.debug")) {
				$message .= " " . $e->getFile() . " (" . $e->getLine() . ")";
			}

			// Grab the data from the exception
			if ($e instanceof MOXMAN_Exception && !$data) {
				$data = $e->getData();
			}

			// Json encode error response
			$response->sendJson((object) array(
				"jsonrpc" => "2.0",
				"error" => array(
					"code" => $e->getCode(),
					"message" => $message,
					"data" => $data
				),
				"id" => $id
			));
		}
	}
}

?>