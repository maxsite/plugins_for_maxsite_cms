<?php
/**
 * LocalFile.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

setlocale(LC_CTYPE, 'UTF8', 'en_US.UTF-8'); // Forces Linux to use proper UTF-8 for file names

/**
 * This is the local file system implementation of MOXMAN_Vfs_IFile.
 *
 * @package MOXMAN_Vfs_Local
 */
class MOXMAN_Vfs_Local_File extends MOXMAN_Vfs_BaseFile {
	private $internalPath;

	/**
	 * Creates a new absolute file.
	 *
	 * @param MOXMAN_Vfs_FileSystem $fileSystem MCManager reference.
	 * @param string $path Absolute path to local file.
	 */
	public function __construct($fileSystem, $path) {
		parent::__construct($fileSystem, $path);
		$this->internalPath = $this->fromUtf($path);
		MOXMAN_Util_PathUtils::verifyPath($path, true);
	}

	/**
	 * Returns true if the file exists.
	 *
	 * @return boolean true if the file exists.
	 */
	public function exists() {
		return file_exists($this->internalPath);
	}

	/**
	 * Returns true if the file is a directory.
	 *
	 * @return boolean true if the file is a directory.
	 */
	public function isDirectory() {
		return $this->exists() && is_dir($this->internalPath);
	}

	/**
	 * Returns true if the file is a file.
	 *
	 * @return boolean true if the file is a file.
	 */
	public function isFile() {
		return $this->exists() && is_file($this->internalPath);
	}

	/**
	 * Returns last modification date in ms as an long.
	 *
	 * @return long last modification date in ms as an long.
	 */
	public function getLastModified() {
		return $this->exists() ? filemtime($this->internalPath) : 0;
	}

	/**
	 * Returns true if the files is readable.
	 *
	 * @return boolean true if the files is readable.
	 */
	public function canRead() {
		if (!parent::canRead()) {
			return false;
		}

		// Check parent
		if (!$this->exists()) {
			return $this->getParentFile()->canRead();
		}

		return is_readable($this->internalPath);
	}

	/**
	 * Returns true if the files is writable.
	 *
	 * @return boolean true if the files is writable.
	 */
	public function canWrite() {
		if (!parent::canWrite()) {
			return false;
		}

		// Check parent
		if (!$this->exists()) {
			return $this->getParentFile()->canWrite();
		}

		// Is windows we need to check if we can really write by accessing files
		// @codeCoverageIgnoreStart
		/*if (DIRECTORY_SEPARATOR === "\\") {
			if (is_file($this->internalPath)) {
				$fp = @fopen($this->internalPath, 'ab');

				if ($fp) {
					fclose($fp);
					return true;
				}
			} else if (is_dir($this->internalPath)) {
				$tmpnam = time() . md5(uniqid('iswritable'));

				if (@touch($this->internalPath . '\\' . $tmpnam)) {
					unlink($this->internalPath . '\\' . $tmpnam);
					return true;
				}
			}

			return false;
		}*/

		return is_writeable($this->internalPath);
		// @codeCoverageIgnoreEnd
	}

	/**
	 * Returns file size as an long.
	 *
	 * @return long file size as an long.
	 */
	public function getSize() {
		return $this->exists() ? filesize($this->internalPath) : 0;
	}

	/**
	 * Renames/Moves this file to the specified file instance.
	 *
	 * @param MOXMAN_Vfs_IFile $dest File to rename/move to.
	 */
	public function moveTo(MOXMAN_Vfs_IFile $dest) {
		if (!$this->exists()) {
			throw new Exception("Source file doesn't exist: " . $dest->getPublicPath());
		}

		$isSameFile = strtolower($this->getPath()) != strtolower($dest->getPath()) || $this->getName() == $dest->getName();

		if ($dest->exists()) {
			if ($isSameFile) {
				throw new Exception("Destination file already exists: " . $dest->getPublicPath());
			}
		}

		if ($isSameFile && MOXMAN_Util_PathUtils::isChildOf($dest->getPath(), $this->getPath())) {
			throw new Exception("You can't move the file into it self.");
		}

		$status = rename($this->internalPath, $this->fromUtf($dest->getPath()));
	}

	/**
	 * Copies this file to the specified file instance.
	 *
	 * @param MOXMAN_Vfs_IFile $dest File to copy to.
	 */
	public function copyTo(MOXMAN_Vfs_IFile $dest) {
		if (!$this->exists()) {
			throw new Exception("Source file doesn't exist: " . $dest->getPublicPath());
		}

		if (MOXMAN_Util_PathUtils::isChildOf($dest->getPath(), $this->getPath())) {
			throw new Exception("You can't copy the file into it self.");
		}

		// File copy or dir copy
		if ($this->isFile()) {
			if ($dest instanceof MOXMAN_Vfs_Local_File) {
				copy($this->internalPath, $this->fromUtf($dest->getPath()));
			} else {
				// Copy between file systems
				$in = $this->open(MOXMAN_Vfs_IFileStream::READ);
				$out = $dest->open(MOXMAN_Vfs_IFileStream::WRITE);

				// Stream in file to out file
				while (($data = $in->read()) !== "") {
					$out->write($data);
				}

				$in->close();
				$out->close();
			}
		} else {
			// Copy dir to dir
			$this->copyDir($this, $dest);
		}
	}

	/**
	 * Deletes the file.
	 *
	 * @param boolean $deep If this option is enabled files will be deleted recurive.
	 */
	public function delete($deep = false) {
		if (!$this->exists()) {
			throw new Exception("Could not delete file since it doesn't exist: " . $this->getPublicPath());
		}

		if ($this->isDirectory()) {
			$files = array_reverse($this->getFiles($this->internalPath));

			if ($deep) {
				foreach ($files as $path) {
					if (is_dir($path)) {
						rmdir($path);
					} else {
						unlink($path);
					}
				}
			} else {
				if (count($files) > 1) {
					throw new Exception("Could not delete directory since it's not empty.");
				}

				rmdir($this->internalPath);
			}
		} else {
			unlink($this->internalPath);
		}
	}

	/**
	 * Returns an array of File instances.
	 *
	 * @return array array of File instances.
	 */
	public function listFiles() {
		$files = $this->listFilesFiltered(new MOXMAN_Vfs_BasicFileFilter());

		return $files;
	}

	/**
	 * Returns an array of BaseFile instances based on the specified filter instance.
	 *
	 * @param MOXMAN_Vfs_IFileFilter $filter FileFilter instance to filter files by.
	 * @return array array of File instances based on the specified filter instance.
	 */
	public function listFilesFiltered(MOXMAN_Vfs_IFileFilter $filter) {
	 	$files = array();
	 	$dirs = array();
		$accessFileName = $this->getConfig()->get("filesystem.local.access_file_name");

		if ($this->isFile()) {
			return $files;
		}

		if ($fHnd = opendir($this->internalPath)) {
			while (false !== ($file = readdir($fHnd))) {
				// Ignore current and parent
				if ($file === "." || $file === ".." || $file === $accessFileName) {
					continue;
				}

				// Returns false if safe mode is on and the user/group is not the same as apache
				$path = $this->internalPath . "/" . $file;
				if (file_exists($path)) {
					if (is_file($path)) {
						$files[] = $file;
					} else {
						$dirs[] = $file;
					}
				}
			}

			// Close handle
			closedir($fHnd);

			// Add dirs
			sort($dirs);
			$list = array();
			foreach ($dirs as $dir) {
				$file = $this->internalPath . "/" . $dir;

				// Ignore files that isn't valid
				try {
					MOXMAN_Util_PathUtils::verifyPath($file, true, "dir");
				} catch (Exception $e) {
					continue;
				}

				// Hack
				if (!@json_encode($file)) {
					continue;
				}

				$file = $this->fileSystem->getFile($this->toUtf($file));
				if ($filter->accept($file) === MOXMAN_Vfs_BasicFileFilter::ACCEPTED) {
					$list[] = $file;
				}
			}
			$dirs = $list;

			// Add files
			sort($files);
			$list = array();
			foreach ($files as $file) {
				$file = $this->internalPath . "/" . $file;

				// Ignore files that isn't valid
				try {
					MOXMAN_Util_PathUtils::verifyPath($file, true, "file");
				} catch (Exception $e) {
					continue;
				}

				// Hack
				if (!@json_encode($file)) {
					continue;
				}

				$file = $this->fileSystem->getFile($this->toUtf($file));
				if ($filter->accept($file) === MOXMAN_Vfs_BasicFileFilter::ACCEPTED) {
					$list[] = $file;
				}
			}
			$files = $list;
		}

		$files = array_merge($dirs, $files);

		return $files;
	}

	/**
	 * Creates a new directory.
	 */
	public function mkdir() {
		MOXMAN_Util_PathUtils::verifyPath($this->internalPath, true, "dir");
		mkdir($this->internalPath);
	}

	/**
	 * Opens a file stream by the specified mode. The default mode is rb.
	 *
	 * @param string $mode Mode to open file by, r, rb, w, wb etc.
	 * @return MOXMAN_Vfs_IStream File stream implementation for the file system.
	 */
	public function open($mode = MOXMAN_Vfs_IStream::READ) {
		$stream = new MOXMAN_Vfs_Local_FileStream($this->internalPath, $mode);

		return $stream;
	}

	/**
	 * Exports the file to a local path. This is used by some operations that can be done in memory.
	 *
	 * @param string $localPath Local path to export file to.
	 * @return string Local path that the file was exported to.
	 */
	public function exportTo($localPath) {
		if ($this->internalPath !== $localPath) {
			copy($this->internalPath, $localPath);
		}

		return $localPath;
	}

	/**
	 * Imports a local file into the file system.
	 *
	 * @param string $localPath Local file system path to import.
	 */
	public function importFrom($localPath) {
		if ($this->internalPath !== $localPath) {
			copy($localPath, $this->internalPath);
		}
	}

	public function getInternalPath() {
		return $this->internalPath;
	}

	/** @ignore */
	private function getFiles($path) {
		$files = array();
		$files[] = $path;

		if ($dir = opendir($path)) {
			while (false !== ($file = readdir($dir))) {
				if ($file == "." || $file == "..") {
					continue;
				}

				$file = $path . "/" . $file;

				if (is_dir($file)) {
					$files = array_merge($files, $this->getFiles($file));
				} else {
					$files[] = $file;
				}
			}

			closedir($dir);
		}

		return $files;
	}

	/** @ignore */
	private function copyDir($from, $to) {
		$fromPathRoot = $from->getPath();
		$files = $this->getFiles($fromPathRoot);

		foreach ($files as $fromPath) {
			$toPath = MOXMAN_Util_PathUtils::combine($to->getPath(), substr($fromPath, strlen($fromPathRoot)));

			if (is_file($fromPath)) {
				if ($to instanceof MOXMAN_Vfs_Local_File) {
					copy($fromPath, $toPath);
				} else {
					$to->getFileSystem()->getFile($toPath)->importFrom($fromPath);
				}
			} else {
				if ($to instanceof MOXMAN_Vfs_Local_File) {
					MOXMAN_Util_PathUtils::verifyPath($toPath, true, "dir");
					mkdir($toPath);
				} else {
					$to->getFileSystem()->getFile($toPath)->mkdir();
				}
			}
		}
	}

	private function fromUtf($path) {
		if (DIRECTORY_SEPARATOR == "\\") {
//			$path = mb_convert_encoding($path, "Windows-1252", "UTF-8");
            $path = iconv("Windows-1252", "UTF-8",$path);


			// Detect any characters outside the Win32 filename byte range
			if (strpos($path, '?') !== false) {
				throw new MOXMAN_Exception("PHP doesn't support the specified characters on Windows.", MOXMAN_Exception::INVALID_FILE_NAME);
			}
		}

		return $path;
	}

	private function toUtf($path) {
		if (DIRECTORY_SEPARATOR == "\\") {
			return iconv("UTF-8", "Windows-1252", $path);
		}

		return $path;
	}
}

?>
