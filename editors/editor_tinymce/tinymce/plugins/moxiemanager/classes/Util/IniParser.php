<?php
/**
 * IniParser.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class is used to parse ini files either from a string or a file.
 *
 * @package MOXMAN_Util
 */
class MOXMAN_Util_IniParser {
	/** @ignore */
	private $items, $useInternal;

	/**
	 * Constructs a new IniParser instance.
	 */
	public function __construct() {
		$this->useInternal = true;
	}

	/**
	 * Enables you to disabe the PHP built in parser for unit test purposes.
	 *
	 * @param Boolean $state True/false state to use internal or not.
	 */
	public function setUseInternal($state) {
		$this->useInternal = $state;
	}

	/**
	 * Loads and parses the specified file by path.
	 *
	 * @param string $path File path to ini file to parse.
	 */
	public function load($path) {
		return $this->parse(file_get_contents($path));
	}

	/**
	 * Parses the specified ini file string.
	 *
	 * @param string $str String to parse.
	 */
	public function parse($str) {
		if (!function_exists('parse_ini_string') || !$this->useInternal) {
			$this->items = array();
			$section = "";
			$lines = preg_split('/\r\n|\n/', $str);

			foreach ($lines as $line) {
				$matches = array();
				if (preg_match('/^(?:(?:\\s*[#;].+)|(?:\\[([^\\]]+)\\])|([^=]+)=\\s*(?:"([^"]+)"|\'([^\']+)\'|(.*)))$/', trim($line), $matches)) {
					// No section, no key/value then it's a comment
					if (!isset($matches[1]) && !isset($matches[2])) {
						continue;
					}

					if ($matches[1]) {
						// Handle section
						$section = trim($matches[1]);
						$this->items[$section] = array();
					} else {
						// Handle item
						$name = trim($matches[2]);

						// Get value
						if (isset($matches[3]) && $matches[3] !== "") {
							$value = $matches[3];
						} else if (isset($matches[4]) && $matches[4] !== "") {
							$value = $matches[4];
						} else if (isset($matches[5]) && $matches[5] !== "") {
							$value = $matches[5];
							$lvalue = strtolower($value);

							if ($lvalue === "true" || $lvalue === "on") {
								$value = "1";
							}

							if ($lvalue === "null" || $lvalue === "false" || $lvalue === "off") {
								$value = "";
							}
						}

						$value = trim($value);

						// Add it to a section or the root
						if ($section) {
							$this->items[$section][$name] = $value;
						} else {
							$this->items[$name] = $value;
						}
					}
				}
			}
		} else {
			$this->items = parse_ini_string($str, true);

			// Trim the values
			foreach ($this->items as $key => $value) {
				if (is_array($this->items[$key])) {
					foreach ($this->items[$key] as $key2 => $value2) {
						$this->items[$key][$key2] = $value2;
					}
				} else {
					$this->items[$key] = $value;
				}
			}
		}

		return $this->items;
	}

	/**
	 * Returns the ini items where some item values will be arrays if they where declared in sections.
	 *
	 * @return Array Name/value array of config items.
	 */
	public function getItems() {
		return $this->items;
	}
}

?>
