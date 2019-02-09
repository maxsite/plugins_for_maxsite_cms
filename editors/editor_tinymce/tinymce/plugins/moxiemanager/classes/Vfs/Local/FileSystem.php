<?php
/**
 * LocalFileSystem.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Local file system implementation.
 *
 * @package MOXMAN_Vfs_Local
 */
class MOXMAN_Vfs_Local_FileSystem extends MOXMAN_Vfs_FileSystem {
	/**
	 * Constructs a new LocalFileSystem.
	 *
	 * @param string $scheme File scheme.
	 * @param MOXMAN_Util_Config $config Config instance for file system.
	 * @param string $root Root path for file system.
	 */
	public function __construct($scheme, MOXMAN_Util_Config $config, $root) {
		parent::__construct($scheme, $config, $root);

		// Force the root path to an absolute path
		$this->rootPath = MOXMAN_Util_PathUtils::toAbsolute(MOXMAN_ROOT, $this->rootPath);

		$this->setFileConfigProvider(new MOXMAN_Vfs_Local_FileConfigProvider($this, $config));
		$this->setFileUrlProvider(new MOXMAN_Vfs_Local_FileUrlProvider());
		$this->setFileUrlResolver(new MOXMAN_Vfs_Local_FileUrlResolver($this));
	}

	/**
	 * Returns a MOXMAN_Vfs_IFile instance based on the specified path.
	 *
	 * @param string $path Path of the file to retrive.
	 * @return MOXMAN_Vfs_IFile File instance for the specified path.
	 */
	public function getFile($path) {
		// Get file from cache
		if ($this->cache->has($path)) {
			return $this->cache->get($path);
		}

		// Never give access to the mc_access file
		if ($this->getConfig()->get("filesystem.local.access_file_name") === basename($path)) {
			throw new MOXMAN_Exception("Can't access the access_file_name.");
		}

		MOXMAN_Util_PathUtils::verifyPath($path, true);

		// Force the path to an absolute path
		$path = MOXMAN_Util_PathUtils::toAbsolute(MOXMAN_ROOT, $path);

		// If the path is out side the root then return null
		if (!MOXMAN_Util_PathUtils::isChildOf($path, $this->rootPath)) {
			$null = null;
			return $null;
		}

		// Create the file and put it in the cache
		$file = new MOXMAN_Vfs_Local_File($this, $path);
		$this->cache->put($path, $file);

		return $file;
	}
}

?>