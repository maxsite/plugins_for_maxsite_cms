<?php
/**
 * GetFile.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Command that returns meta data for the specified file.
 *
 * @package MOXMAN_Core
 */
class MOXMAN_Core_FileInfoCommand extends MOXMAN_Core_BaseCommand {
	/**
	 * Executes the command logic with the specified RPC parameters.
	 *
	 * @param Object $params Command parameters sent from client.
	 * @return Object Result object to be passed back to client.
	 */
	public function execute($params) {
		if (isset($params->paths)) {
			$result = array();

			foreach ($params->paths as $path) {
				$file = MOXMAN::getFile($path);
				$fileInfo = $this->fileToJson($file, true);

				$args = $this->fireCustomInfo(MOXMAN_Core_CustomInfoEventArgs::INSERT_TYPE, $file);
				$fileInfo->info = (object) $args->getInfo();

				if (isset($params->insert) && $params->insert) {
					$this->fireFileAction(MOXMAN_Core_FileActionEventArgs::INSERT, $file);
				}

				$result[] = $fileInfo;
			}
		} else {
			$file = MOXMAN::getFile($params->path);
			$fileInfo = $this->fileToJson($file, true);

			$args = $this->fireCustomInfo(MOXMAN_Core_CustomInfoEventArgs::INSERT_TYPE, $file);
			$fileInfo->info = (object) $args->getInfo();

			if (isset($params->insert) && $params->insert) {
				$this->fireFileAction(MOXMAN_Core_FileActionEventArgs::INSERT, $file);
			}

			$result = $fileInfo;
		}

		return $result;
	}
}

?>