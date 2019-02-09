<?php
/**
 * LocalFileUrlProvider.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Provides urls for local file instances.
 *
 * @package MOXMAN_Vfs_Local
 */
class MOXMAN_Vfs_Local_FileUrlProvider implements MOXMAN_Vfs_IFileUrlProvider {
	/**
	 * Returns an URL for the specified file object.
	 *
	 * @param MOXMAN_Vfs_IFile $file File to get the absolute URL for.
	 * @return String Absolute URL for the specified file.
	 */
	public function getUrl(MOXMAN_Vfs_IFile $file) {
		$config = $file->getConfig();

		// Get config items
		$wwwroot = $config->get("filesystem.local.wwwroot");
		$prefix = $config->get("filesystem.local.urlprefix");
		$suffix = $config->get("filesystem.local.urlsuffix");

		$paths = MOXMAN_Util_PathUtils::getSitePaths();

		// No wwwroot specified try to figure out a wwwroot
		if (!$wwwroot) {
			$wwwroot = $paths["wwwroot"];
		} else {
			// Force the www root to an absolute file system path
			$wwwroot = MOXMAN_Util_PathUtils::toAbsolute(MOXMAN_ROOT, $wwwroot);
		}

		// Add prefix to URL
		if ($prefix == "") {
			$prefix = MOXMAN_Util_PathUtils::combine("{proto}://{host}", $paths["prefix"]);
		}

		// Replace protocol
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
			$prefix = str_replace("{proto}", "https", $prefix);
		} else {
			$prefix = str_replace("{proto}", "http", $prefix);
		}

		// Replace host/port
		$prefix = str_replace("{host}", $_SERVER['HTTP_HOST'], $prefix);
		$prefix = str_replace("{port}", $_SERVER['SERVER_PORT'], $prefix);

		// Insert path into URL
		$url = substr($file->getPath(), strlen($wwwroot));
		$url = MOXMAN_Util_PathUtils::combine($prefix, $url);

		// Add suffix to URL
		if ($suffix) {
			$url .= $suffix;
		}

		return $url;
	}
}

?>