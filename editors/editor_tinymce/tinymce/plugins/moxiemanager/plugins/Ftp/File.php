<?php
/**
 * LocalFile.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This is the local file system implementation of MOXMAN_Vfs_IFile.
 */
class MOXMAN_Ftp_File extends MOXMAN_Vfs_BaseFile {
	private $stat;

	public function __construct($fileSystem, $path, $stat = null) {
		$this->fileSystem = $fileSystem;
		$this->path = $path;
	}

	public function isFile() {
		return $this->exists() && !$this->getStatItem("isdir");
	}

	public function exists() {
		return $this->getStatItem("size") !== null;
	}

	public function getSize() {
		return $this->getStatItem("size");
	}

	public function getLastModified() {
		return $this->getStatItem("mdate");
	}

	public function delete($deep = false) {
		$this->fileSystem->getCache()->remove($this->getPath());

		if ($this->isFile()) {
			ftp_delete($this->fileSystem->getConnection(), $this->getInternalPath());
		} else {
			if ($deep) {
				$this->deleteRecursive($this->getInternalPath());
			} else {
				ftp_rmdir($this->fileSystem->getConnection(), $this->getInternalPath());
			}
		}
	}

	public function mkdir() {
		ftp_mkdir($this->fileSystem->getConnection(), $this->getInternalPath());
	}

	public function moveTo(MOXMAN_Vfs_IFile $dest) {
		$this->fileSystem->getCache()->remove($this->getPath());

		ftp_rename($this->fileSystem->getConnection(), $this->getInternalPath(), $dest->getInternalPath());
	}

	public function copyTo(MOXMAN_Vfs_IFile $dest) {
		$this->fileSystem->getCache()->remove($dest->getPath());

		$fromStream = $this->open("rb");
		$toStream = $dest->open("wb");

		while (($buff = $fromStream->read(8192)) !== "") {
			$toStream->write($buff);
		}

		$fromStream->close();
		$toStream->close();
	}

	public function listFilesFiltered(MOXMAN_Vfs_IFileFilter $filter) {
		$files = array();
		$dirs = array();

		$ftpFiles = $this->getFtpList($this->getPath());
		foreach ($ftpFiles as $ftpFile) {
			$path = MOXMAN_Util_PathUtils::combine($this->getPath(), $ftpFile["name"]);
			$file = new MOXMAN_Ftp_File($this->fileSystem, $path, $ftpFile);

			// Check if the file is accepted
			if ($filter->accept($file) === MOXMAN_Vfs_IFileFilter::ACCEPTED) {
				// Add to directory array or file array
				if ($ftpFile["isdir"]) {
					$dirs[] = $file;
				} else {
					$files[] = $file;
				}

				$this->fileSystem->getCache()->put($path, $ftpFile);
			}
		}

		$files = array_merge($dirs, $files);

		return $files;
	}

	public function getInternalPath($path = null) {
		$url = parse_url($path ? $path : $this->path);
		$path = isset($url["path"]) ? $url["path"] : "/";

		return MOXMAN_Util_PathUtils::combine($this->getFileSystem()->getAccountItem("path"), $path);
	}

	public function open($mode = MCFM_Vfs_IStream::READ) {
		$stream = new MOXMAN_Vfs_MemoryFileStream($this, $mode);

		return $stream;
	}

	private function getStat() {
		$parentPath = $this->getParent();

		if ($parentPath) {
			$ftpFiles = $this->getFtpList($parentPath);
			$targetStat = null;

			foreach ($ftpFiles as $stat) {
				$path = MOXMAN_Util_PathUtils::combine($parentPath, $stat["name"]);

				if ($stat["name"] === $this->getName()) {
					$targetStat = $stat;
				}

				$this->fileSystem->getCache()->put($path, $stat);
			}
		} else {
			// Stat info for root directory
			$targetStat = array(
				"name" => $this->fileSystem->getRootName(),
				"isdir" => true,
				"size" => 0,
				"mdate" => time()
			);
		}

		return $targetStat;
	}

	public function exportTo($localPath) {
		if (!file_exists($localPath)) {
			ftp_get($this->getFileSystem()->getConnection(), $localPath, $this->getInternalPath(), FTP_BINARY);
		}
	}

	public function importFrom($localPath) {
		if (file_exists($localPath)) {
			ftp_put($this->getFileSystem()->getConnection(), $this->getInternalPath(), $localPath, FTP_BINARY);
		}
	}

	public function getUrl() {
		$url = "";
		$fileSystem = $this->getFileSystem();
		$wwwroot = $fileSystem->getAccountItem("wwwroot");
		$path = $this->getInternalPath();

		// Resolve ftp path to url
		if ($wwwroot) {
			if (MOXMAN_Util_PathUtils::isChildOf($path, $wwwroot)) {
				// Get config items
				$prefix = $fileSystem->getAccountItem("prefix", "{proto}://{host}");
				$suffix = $fileSystem->getAccountItem("urlsuffix");

				// Replace protocol
				if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") {
					$prefix = str_replace("{proto}", "https", $prefix);
				} else {
					$prefix = str_replace("{proto}", "http", $prefix);
				}

				// Replace host/port
				$prefix = str_replace("{host}", $fileSystem->getAccountItem("host"), $prefix);
				$prefix = str_replace("{port}", $_SERVER['SERVER_PORT'], $prefix);

				// Insert path into URL
				$url = substr($path, strlen($wwwroot));

				// Add prefix to URL
				if ($prefix) {
					$url = MOXMAN_Util_PathUtils::combine($prefix, $url);
				}

				// Add suffix to URL
				if ($suffix) {
					$url .= $suffix;
				}
			} else {
				throw new MOXMAN_Exception("Ftp path is not within wwwroot path.");
			}
		}

		return $url;
	}

	private function getStatItem($name, $defaultVal = null) {
		if (!$this->stat) {
			$this->stat = $this->getStat();
		}

		return isset($this->stat[$name]) ? $this->stat[$name] : $defaultVal;
	}

	private function getFtpList($path) {
		$files = array();
		$listPath = $this->getInternalPath($path);

		// Special treatment for directories with spaces in them
		if (strpos($listPath, ' ') !== false) {
			ftp_chdir($this->fileSystem->getConnection(), $listPath);
			$listPath = ".";
		}

		$lines = ftp_rawlist($this->fileSystem->getConnection(), $listPath);
		foreach ($lines as $line) {
			$matches = null;
			$unixRe = '/^([\-ld])((?:[\-r][\-w][\-xs]){3})\s+(\d+)\s+(\w+)\s+([\-\w]+)\s+(\d+)\s+(\w+\s+\d+\s+[\w:]+)\s+(.+)$/';
			$windowsRe = "/^([^\s]+\s+[^\s]+)\s+((?:<DIR>|[\w]+)?)\s+(.+)$/";

			if ($line) {
				if (preg_match($unixRe, $line, $matches)) {
					// Unix style
					$stat = array(
						"name" => $matches[8],
						"isdir" => $matches[1] === "d",
						"size" => intval($matches[6]),
						"mdate" => strtotime($matches[7])
					);
				} else if (preg_match($windowsRe, $line, $matches)) {
					// Windows style
					$stat = array(
						"name" => $matches[3],
						"isdir" => $matches[2] === "<DIR>",
						"size" => $matches[2] !== "<DIR>" ? intval($matches[2]) : 0,
						"mdate" => strtotime($matches[1])
					);
				} else {
					// Unknown format
					throw new MOXMAN_Exception("Unknown FTP list format: " . $line);
				}

				$path = MOXMAN_Util_PathUtils::combine($path, $stat["name"]);
				$this->fileSystem->getCache()->put($path, $stat);

				$files[] = $stat;
			}
		}

		return $files;
	}

	private function deleteRecursive($path) {
		$handle = $this->fileSystem->getConnection();
		$files = array();
		$dirs = array();

		$lines = ftp_rawlist($handle, $path);
		foreach ($lines as $line) {
			$matches = null;
			$unixRe = '/^([\-ld])((?:[\-r][\-w][\-xs]){3})\s+(\d+)\s+(\w+)\s+([\-\w]+)\s+(\d+)\s+(\w+\s+\d+\s+[\w:]+)\s+(.+)$/';
			$windowsRe = "/^([^\s]+\s+[^\s]+)\s+((?:<DIR>|[\w]+)?)\s+(.+)$/";

			if (preg_match($unixRe, $line, $matches)) {
				$filePath = MOXMAN_Util_PathUtils::combine($path, $matches[8]);

				if ($matches[1] === "d") {
					$dirs[] = $filePath;
				} else {
					$files[] = $filePath;
				}
			} else if (preg_match($windowsRe, $line, $matches)) {
				$filePath = MOXMAN_Util_PathUtils::combine($path, $matches[3]);

				if ($matches[2] === "<DIR>") {
					$dirs[] = $filePath;
				} else {
					$files[] = $filePath;
				}
			} else {
				// Unknown format
				throw new MOXMAN_Exception("Unknown FTP list format: " . $line);
			}
		}

		// Delete files in dir
		foreach ($files as $file) {
			ftp_delete($handle, $file);
		}

		// Delete directories in dir
		foreach ($dirs as $dir) {
			$this->deleteRecursive($dir);
		}

		// Delete dir
		ftp_rmdir($handle, $path);
	}
}

?>