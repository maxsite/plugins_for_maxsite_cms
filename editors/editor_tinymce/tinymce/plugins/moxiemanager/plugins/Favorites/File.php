<?php
/**
 * File.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * This is the local file system implementation of MOXMAN_Vfs_IFile.
 */
class MOXMAN_Favorites_File extends MOXMAN_Vfs_BaseFile {
	private $entry;

	public function __construct($fileSystem, $path, $entry = null) {
		parent::__construct($fileSystem, $path);
		$this->entry = $entry;
	}

	public function getLastModified() {
		return $this->entry ? $this->entry->mdate : 0;
	}

	public function getPublicLinkPath() {
		return $this->entry ? $this->entry->path : "";
	}

	public function isFile() {
		return $this->entry ? !$this->entry->isdir : false;
	}

	public function getSize() {
		return $this->entry ? $this->entry->size : 0;
	}

	public function exists() {
		return true;
	}

	public function canWrite() {
		return false;
	}

	public function getMetaData() {
		return parent::getMetaData()->extend(array(
			"ui.icon_16x16" => "favorites"
		));
	}

	public function listFilesFiltered(MOXMAN_Vfs_IFileFilter $filter) {
		$entries = MOXMAN_Util_Json::decode(MOXMAN::getUserStorage()->get("favorites.files", "[]"));
		$files = array();

		foreach ($entries as $entry) {
			$file = new MOXMAN_Favorites_File($this->fileSystem, $entry->path, $entry);

			if ($filter->accept($file) == MOXMAN_Vfs_IFileFilter::ACCEPTED) {
				$files[] = $file;
			}
		}

		return $files;
	}

	public function getParent() {
		return "";
	}
}

?>