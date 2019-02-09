<?php
/**
 * ListFiles.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Command for listing files for a specific path in a file system.
 *
 * @package MOXMAN_Core
 */
class MOXMAN_Core_ListFilesCommand extends MOXMAN_Core_BaseCommand {
	/**
	 * Executes the command logic with the specified RPC parameters.
	 *
	 * @param Object $params Command parameters sent from client.
	 * @return Object Result object to be passed back to client.
	 */
	public function execute($params) {
		$url = isset($params->url) ? $params->url : '';
		$path = isset($params->path) ? $params->path : '{default}';

		// Result URL to closest file
		$file = null;
		if ($url) {
			try {
				$file = MOXMAN::getFile($url);
			} catch (MOXMAN_Exception $e) {
				// Might throw exception ignore it
				$file = null;
			}

			if ($file) {
				if ($file->exists()) {
					$urlFile = $file;
				}

				while (!$file->exists() || !$file->isDirectory()) {
					$file = $file->getParentFile();
				}
			}
		}

		$file = $file ? $file : MOXMAN::getFile($path);

		if (!$file->isDirectory()) {
			throw new MOXMAN_Exception(
				"Path isn't a directory: " . $file->getPublicPath(),
				MOXMAN_Exception::INVALID_FILE_TYPE
			);
		}

		$config = $file->getConfig();

		// Setup input file filter
		$paramsFileFilter = new MOXMAN_Vfs_BasicFileFilter();

		if (isset($params->include_directory_pattern) && $params->include_directory_pattern) {
			$paramsFileFilter->setIncludeDirectoryPattern($params->include_directory_pattern);
		}

		if (isset($params->exclude_directory_pattern) && $params->exclude_directory_pattern) {
			$paramsFileFilter->setExcludeDirectoryPattern($params->exclude_directory_pattern);
		}

		if (isset($params->include_file_pattern) && $params->include_file_pattern) {
			$paramsFileFilter->setIncludeFilePattern($params->include_file_pattern);
		}

		if (isset($params->exclude_file_pattern) && $params->exclude_file_pattern) {
			$paramsFileFilter->setExcludeFilePattern($params->exclude_file_pattern);
		}

		if (isset($params->extensions) && $params->extensions) {
			$paramsFileFilter->setIncludeExtensions($params->extensions);
		}

		if (isset($params->filter) && $params->filter != null) {
			$paramsFileFilter->setIncludeWildcardPattern($params->filter);
		}

		if (isset($params->only_dirs) && $params->only_dirs === true) {
			$paramsFileFilter->setOnlyDirs(true);
		}

		if (isset($params->only_files) && $params->only_files === true) {
			$paramsFileFilter->setOnlyFiles(true);
		}

		// Setup file filter
		$configuredFilter = new MOXMAN_Vfs_BasicFileFilter();
		$configuredFilter->setIncludeDirectoryPattern($config->get('filesystem.include_directory_pattern'));
		$configuredFilter->setExcludeDirectoryPattern($config->get('filesystem.exclude_directory_pattern'));
		$configuredFilter->setIncludeFilePattern($config->get('filesystem.include_file_pattern'));
		$configuredFilter->setExcludeFilePattern($config->get('filesystem.exclude_file_pattern'));
		$configuredFilter->setIncludeExtensions($config->get('filesystem.extensions'));

		// Setup combined filter
		$combinedFilter = new MOXMAN_Vfs_CombinedFileFilter();
		$combinedFilter->addFilter($paramsFileFilter);
		$combinedFilter->addFilter($configuredFilter);

		$files = $file->listFilesFiltered($combinedFilter);
		$args = $this->fireFilesAction(MOXMAN_Core_FileActionEventArgs::LIST_FILES, $file, $files);
		$files = $args->getFileList();

		$renameFilter = MOXMAN_Vfs_CombinedFileFilter::createFromConfig($file->getConfig(), "rename");
		$editFilter = MOXMAN_Vfs_CombinedFileFilter::createFromConfig($file->getConfig(), "edit");
		$viewFilter = MOXMAN_Vfs_CombinedFileFilter::createFromConfig($file->getConfig(), "view");

		$result = (object) array(
			"columns" => array("name", "size", "modified", "attrs", "info"),
			"config" => $this->getPublicConfig($file),
			"file" => $this->fileToJson($file, true),
			"urlFile" => isset($urlFile) ? $this->fileToJson($urlFile, true) : null,
			"data" => array()
		);

		foreach ($files as $subFile) {
			$attrs = $subFile->isDirectory() ? "d" : "-";
			$attrs .= $subFile->canRead() ? "r" : "-";
			$attrs .= $subFile->canWrite() ? "w" : "-";
			$attrs .= $renameFilter->accept($subFile) === MOXMAN_Vfs_CombinedFileFilter::ACCEPTED ? "r" : "-";
			$attrs .= $subFile->isFile() && $editFilter->accept($subFile) === MOXMAN_Vfs_CombinedFileFilter::ACCEPTED ? "e" : "-";
			$attrs .= $subFile->isFile() && $viewFilter->accept($subFile) === MOXMAN_Vfs_CombinedFileFilter::ACCEPTED ? "v" : "-";
			$attrs .= $subFile->isFile() && MOXMAN_Media_ImageAlter::canEdit($subFile) ? "p" : "-";

			$args = $this->fireCustomInfo(MOXMAN_Core_CustomInfoEventArgs::LIST_TYPE, $subFile);
			$custom = (object) $args->getInfo();

			if ($subFile->getPublicLinkPath()) {
				$custom->link = $subFile->getPublicLinkPath();
			}

			$result->data[] = array(
				$subFile->getName(),
				$subFile->isDirectory() ? 0 : $subFile->getSize(),
				$subFile->getLastModified(),
				$attrs,
				$custom
			);
		}

		return $result;
	}
}

?>