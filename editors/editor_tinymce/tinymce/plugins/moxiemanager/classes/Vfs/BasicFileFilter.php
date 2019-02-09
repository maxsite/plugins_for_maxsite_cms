<?php
/**
 * BasicFileFilter.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This class provides basic file filtering logic.
 *
 * @package MOXMAN_Vfs
 */
class MOXMAN_Vfs_BasicFileFilter implements MOXMAN_Vfs_IFileFilter {
	/** @ignore */
	private $excludeFolders, $includeFolders, $excludeFiles, $includeFiles;

	/** @ignore */
	private $includeFilePattern, $excludeFilePattern, $includeDirectoryPattern;

	/** @ignore */
	private $excludeDirectoryPattern, $filesOnly, $dirsOnly, $logFunction;

	/** @ignore */
	private $includeWildcardPattern, $excludeWildcardPattern, $extensions;

	/**
	 * Invalid file extension.
	 */
	const INVALID_EXTENSION = -1;

	/**
	 * Invalid file name.
	 */
	const INVALID_NAME = -2;

	/**
	 * Invalid file type file/directory.
	 */
	const INVALID_TYPE = -3;

	/**
	 * Sets the log function to be called.
	 *
	 * @param mixed $func Name of function or array with instance and method name.
	 */
	public function setLogFunction($func) {
		$this->logFunction = $func;
	}

	/**
	 * Sets if only files are to be accepted in result.
	 *
	 * @param boolean $filesOnly True if only files are to be accepted.
	 */
	public function setOnlyFiles($filesOnly) {
		$this->filesOnly = $filesOnly;
	}

	/**
	 * Sets if only dirs are to be accepted in result.
	 *
	 * @param boolean $dirsOnly True if only dirs are to be accepted.
	 */
	public function setOnlyDirs($dirsOnly) {
		$this->dirsOnly = $dirsOnly;
	}

	/**
	 * Sets a comma separated list of valid file extensions.
	 *
	 * @param string $extensions Comma separated list of valid file extensions.
	 */
	public function setIncludeExtensions($extensions) {
		if ($extensions === "*" || !$extensions) {
			$this->extensions = "";
			return;
		}

		$this->extensions = explode(',', strtolower($extensions));
	}

	/**
	 * Sets comma separated string list of filenames to exclude.
	 *
	 * @param string $files separated string list of filenames to exclude.
	 */
	public function setExcludeFiles($files) {
		if ($files) {
			$this->excludeFiles = explode(',', $files);
		}
	}

	/**
	 * Sets comma separated string list of filenames to include.
	 *
	 * @param string $files separated string list of filenames to include.
	 */
	public function setIncludeFiles($files) {
		if ($files) {
			$this->includeFiles = explode(',', $files);
		}
	}

	/**
	 * Sets comma separated string list of foldernames to exclude.
	 *
	 * @param string $folders separated string list of foldernames to exclude.
	 */
	public function setExcludeFolders($folders) {
		if ($folders) {
			$this->excludeFolders = explode(',', $folders);
		}
	}

	/**
	 * Sets comma separated string list of foldernames to include.
	 *
	 * @param string $folders separated string list of foldernames to include.
	 */
	public function setIncludeFolders($folders) {
		if ($folders) {
			$this->includeFolders = explode(',', $folders);
		}
	}

	/**
	 * Sets a regexp pattern that is used to accept files path parts.
	 *
	 * @param string $pattern regexp pattern that is used to accept files path parts.
	 */
	public function setIncludeFilePattern($pattern) {
		$this->includeFilePattern = $pattern;
	}

	/**
	 * Sets a regexp pattern that is used to deny files path parts.
	 *
	 * @param string $pattern regexp pattern that is used to deny files path parts.
	 */
	public function setExcludeFilePattern($pattern) {
		$this->excludeFilePattern = $pattern;
	}

	/**
	 * Sets a regexp pattern that is used to accept directory path parts.
	 *
	 * @param string $pattern regexp pattern that is used to accept directory path parts.
	 */
	public function setIncludeDirectoryPattern($pattern) {
		$this->includeDirectoryPattern = $pattern;
	}

	/**
	 * Sets a regexp pattern that is used to deny directory path parts.
	 *
	 * @param string $pattern regexp pattern that is used to deny directory path parts.
	 */
	public function setExcludeDirectoryPattern($pattern) {
		$this->excludeDirectoryPattern = $pattern;
	}

	/**
	 * Sets a wildcard pattern that is used to accept files path parts.
	 *
	 * @param string $pattern wildcard pattern that is used to accept files path parts.
	 */
	public function setIncludeWildcardPattern($pattern) {
		$this->includeWildcardPattern = $pattern;
	}

	/**
	 * Sets a wildcard pattern that is used to deny files path parts.
	 *
	 * @param string $pattern wildcard pattern that is used to deny files path parts.
	 */
	public function setExcludeWildcardPattern($pattern) {
		$this->excludeWildcardPattern = $pattern;
	}

	/**
	 * Returns true or false if the file is accepted or not.
	 *
	 * @param MOXMAN_Vfs_IFile $file File to grant or deny.
	 * @param Boolean $isFile Default state if the filter is on an non existing file.
	 * @return int Accepted or the reson why it failed.
	 */
	public function accept(MOXMAN_Vfs_IFile $file, $isFile = true) {
		$name = $file->getName();
		$absPath = $file->getPath();
		$isFile = $file->exists() ? $file->isFile() : $isFile;

		// Handle file patterns
		if ($isFile) {
			if ($this->dirsOnly) {
				if ($this->logFunction) {
					$this->log("File denied \"" . $absPath . "\" by \"dirsOnly\".");
				}

				return self::INVALID_TYPE;
			}

			// Handle exclude files
			if (is_array($this->excludeFiles) && $isFile) {
				foreach ($this->excludeFiles as $fileName) {
					if ($name == $fileName) {
						if ($this->logFunction) {
							$this->log("File \"" . $absPath . "\" denied by \"excludeFiles\".");
						}

						return self::INVALID_NAME;
					}
				}
			}

			// Handle include files
			if (is_array($this->includeFiles) && $isFile) {
				$state = false;

				foreach ($this->includeFiles as $fileName) {
					if ($name == $fileName) {
						$state = true;
						break;
					}
				}

				if (!$state) {
					if ($this->logFunction) {
						$this->log("File \"" . $absPath . "\" denied by \"includeFiles\".");
					}

					return self::INVALID_NAME;
				}
			}

			// Handle exclude pattern
			if ($this->excludeFilePattern && preg_match($this->excludeFilePattern, $name)) {
				if ($this->logFunction) {
					$this->log("File \"" . $absPath . "\" denied by \"excludeFilePattern\".");
				}

				return self::INVALID_NAME;
			}

			// Handle include pattern
			if ($this->includeFilePattern && !preg_match($this->includeFilePattern, $name)) {
				if ($this->logFunction) {
					$this->log("File \"" . $absPath . "\" denied by \"includeFilePattern\".");
				}

				return self::INVALID_NAME;
			}

			// Handle file extension pattern
			if (is_array($this->extensions)) {
				$ext = MOXMAN_Util_PathUtils::getExtension($absPath);
				$valid = false;

				foreach ($this->extensions as $extension) {
					if ($extension == $ext) {
						$valid = true;
						break;
					}
				}

				if (!$valid) {
					if ($this->logFunction) {
						$this->log("File \"" . $absPath . "\" denied by \"extensions\".");
					}

					return self::INVALID_EXTENSION;
				}
			}
		} else {
			if ($this->filesOnly) {
				if ($this->logFunction) {
					$this->log("Dir denied \"" . $absPath . "\" by \"filesOnly\".");
				}

				return self::INVALID_TYPE;
			}

			// Handle exclude folders
			if (is_array($this->excludeFolders)) {
				foreach ($this->excludeFolders as $folder) {
					if (strpos($absPath, $folder) !== false) {
						if ($this->logFunction) {
							$this->log('File denied "' . $absPath . '" by "excludeFolders".');
						}

						return self::INVALID_NAME;
					}
				}
			}

			// Handle include folders
			if (is_array($this->includeFolders)) {
				$state = false;

				foreach ($this->includeFolders as $folder) {
					if (strpos($absPath, $folder) !== false) {
						$state = true;
						break;
					}
				}

				if (!$state) {
					if ($this->logFunction) {
						$this->log("File \"" . $absPath . "\" denied by \"includeFolders\".");
					}

					return self::INVALID_NAME;
				}
			}

			// Handle exclude pattern
			if ($this->excludeDirectoryPattern && preg_match($this->excludeDirectoryPattern, $name)) {
				if ($this->logFunction) {
					$this->log("File \"" . $absPath . "\" denied by \"excludeDirectoryPattern\".");
				}

				return self::INVALID_NAME;
			}

			// Handle include pattern
			if ($this->includeDirectoryPattern && !preg_match($this->includeDirectoryPattern, $name)) {
				if ($this->logFunction) {
					$this->log("File \"" . $absPath . "\" denied by \"includeDirectoryPattern\".");
				}

				return self::INVALID_NAME;
			}
		}

		// Handle include wildcard pattern
		if ($this->includeWildcardPattern && !$this->matchWildCard($this->includeWildcardPattern, $name)) {
			if ($this->logFunction) {
				$this->log("File \"" . $absPath . "\" denied by \"includeWildcardPattern\".");
			}

			return self::INVALID_NAME;
		}

		// Handle exclude wildcard pattern
		if ($this->excludeWildcardPattern && $this->matchWildCard($this->excludeWildcardPattern, $name)) {
			if ($this->logFunction) {
				$this->log("File \"" . $absPath . "\" denied by \"excludeWildcardPattern\".");
			}

			return self::INVALID_NAME;
		}

		return self::ACCEPTED;
	}

	/**
	 * Creates a config instance from the specified config. It will use various config options
	 * for setting up a filter instance. This is a helper function.
	 *
	 * @param MOXMAN_Util_Config $config Config instance to get settings from.
	 * @return MOXMAN_Vfs_BasicFileFilter Basic file filter instance based on config.
	 */
	public static function createFromConfig(MOXMAN_Util_Config $config) {
		$filter = new MOXMAN_Vfs_BasicFileFilter();

		$filter->setIncludeDirectoryPattern($config->get('filesystem.include_directory_pattern'));
		$filter->setExcludeDirectoryPattern($config->get('filesystem.exclude_directory_pattern'));
		$filter->setIncludeFilePattern($config->get('filesystem.include_file_pattern'));
		$filter->setExcludeFilePattern($config->get('filesystem.exclude_file_pattern'));
		$filter->setIncludeExtensions($config->get('filesystem.extensions'));
		$filter->setExcludeFiles($config->get('filesystem.local.access_file_name'));

		return $filter;
	}

	/** @ignore */
	private function matchWildCard($pattern, $name) {
		// Convert whildcard pattern to regexp
		$pattern = preg_quote($pattern);
		$pattern = str_replace("\\*", ".*", $pattern);
		$pattern = str_replace("\\?", ".", $pattern);

		return preg_match("/" . $pattern . "/i", $name) === 1;
	}

	/** @ignore */
	private function log($str) {
		if (is_array($this->logFunction)) {
			// Call user function in class reference
			$class = $this->logFunction[0];
			$name = $this->logFunction[1];
			$func = $class->$name($str);
		} else {
			$func = $this->logFunction;
			$func($str);
		}
	}
}
?>