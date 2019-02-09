<?php
/**
 * CorePlugin.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * Core plugin contains core commands and logic.
 *
 * @package MOXMAN_Core
 */
class MOXMAN_Core_Plugin implements MOXMAN_IPlugin, MOXMAN_ICommandHandler, MOXMAN_Http_IHandler {
	/** @ignore */
	private $dispatcher, $commands;

	// @codeCoverageIgnoreStart

	/** @ignore */
	public function __construct() {
		$this->dispatcher = new MOXMAN_Util_EventDispatcher();

		// Listen for add/remove/stream events to generate thumbnails
		$this->bind("FileAction", "onFileAction", $this);
	}

	/**
	 * Initializes the core plugin.
	 */
	public function init() {
		$this->commands = new MOXMAN_CommandCollection();

		// Map commands to classes
		$this->commands->addClasses(array(
			"Install" => "MOXMAN_Core_InstallCommand",
			"AlterImage" => "MOXMAN_Core_AlterImageCommand",
			"CopyTo" => "MOXMAN_Core_CopyToCommand",
			"CreateDirectory" => "MOXMAN_Core_CreateDirectoryCommand",
			"CreateDocument" => "MOXMAN_Core_CreateDocumentCommand",
			"PutFileContents" => "MOXMAN_Core_PutFileContentsCommand",
			"Delete" => "MOXMAN_Core_DeleteCommand",
			"FileInfo" => "MOXMAN_Core_FileInfoCommand",
			"ListFiles" => "MOXMAN_Core_ListFilesCommand",
			"ListRoots" => "MOXMAN_Core_ListRootsCommand",
			"Loopback" => "MOXMAN_Core_LoopbackCommand",
			"MoveTo" => "MOXMAN_Core_MoveToCommand",
			"Zip" => "MOXMAN_Core_ZipCommand",
			"UnZip" => "MOXMAN_Core_UnZipCommand",
			"GetAppKeys" => "MOXMAN_Core_GetAppKeysCommand",
			"GetFileContents" => "MOXMAN_Core_GetFileContentsCommand",
			"GetConfig" => "MOXMAN_Core_GetConfigCommand",
			"ImportFromUrl" => "MOXMAN_Core_ImportFromUrlCommand",
			"Login" => "MOXMAN_Core_LoginCommand",
			"Logout" => "MOXMAN_Core_LogoutCommand"
		));
	}

	// @codeCoverageIgnoreEnd

	/**
	 * Gets executed when a RPC call is made.
	 *
	 * @param string $name Name of RPC command to execute.
	 * @param Object $params Object passed in from RPC handler.
	 * @return Object Return object that gets passed back to client.
	 */
	public function execute($name, $params) {
		return $this->commands->execute($name, $params);
	}

	/**
	 * Process a request using the specified context.
	 *
	 * @param MOXMAN_Http_Context $httpContext Context instance to pass to use for the handler.
	 */
	public function processRequest(MOXMAN_Http_Context $httpContext) {
		$request = $httpContext->getRequest();

		if ($request->get("json")) {
			$instance = new MOXMAN_Core_JsonRpcHandler();
			$instance->processRequest($httpContext);
		}

		// TODO: Make this nicer, switch?
		$action = strtolower($request->get("action", ""));

		if ($action == "download") {
			$instance = new MOXMAN_Core_DownloadHandler();
			$instance->processRequest($httpContext);
		}

		if ($action == "upload") {
			$instance = new MOXMAN_Core_UploadHandler();
			$instance->processRequest($httpContext);
		}

		if ($action == "streamfile") {
			$instance = new MOXMAN_Core_StreamFileHandler($this);
			$instance->processRequest($httpContext);
		}

		if ($action == "language") {
			$instance = new MOXMAN_Core_LanguageHandler($this);
			$instance->processRequest($httpContext);
		}

		// @codeCoverageIgnoreStart
		if ($action == "pluginjs") {
			$instance = new MOXMAN_Core_PluginJsHandler($this);
			$instance->processRequest($httpContext);
		}
		// @codeCoverageIgnoreEnd
	}

	/**
	 * This method will fire a specific event by name with the specified event args instance.
	 *
	 * @param string $name Name of the event to fire for example custom info.
	 * @param MOXMAN_Util_EventArgs $args Event args to pass to all event listeners.
	 * @return MOXMAN_PluginManager PluginManager instance to enable chainablity.
	 */
	public function fire($name, MOXMAN_Util_EventArgs $args) {
		return $this->dispatcher->dispatch($this, $name, $args);
	}

	/**
	 * Binds a specific event by name for a specific plugin instance.
	 *
	 * @param string $name Event name to bind.
	 * @param string $func String name of the function to call.
	 * @param MOXMAN_Plugin $plugin Plugin instance to call event method on.
	 * @return MOXMAN_PluginManager PluginManager instance to enable chainablity.
	 */
	public function bind($name, $func, $plugin) {
		return $this->dispatcher->add($name, $func, $plugin);
	}

	/**
	 * Unbinds a specific event by name from a specific plugin instance.
	 *
	 * @param string $name Event name to unbind.
	 * @param string $func String name of the function not to call.
	 * @param MOXMAN_IPlugin $plugin Plugin instance to not call event method on.
	 * @return MOXMAN_PluginManager PluginManager instance to enable chainablity.
	 */
	public function unbind($name, $func, $plugin) {
		return $this->dispatcher->remove($name, $func, $plugin);
	}

	/**
	 * Event handler function. Gets executed when a file action event occurs.
	 *
	 * @param MOXMAN_Core_FileActionEventArgs $args File action event arguments.
	 */
	public function onFileAction(MOXMAN_Core_FileActionEventArgs $args) {
		if ($args->getAction() == MOXMAN_Core_FileActionEventArgs::DELETE) {
			if (!isset($args->getData()->thumb)) {
				$this->deleteThumbnail($args->getFile());
			}
		}
	}

	public function getThumbnail(MOXMAN_Vfs_IFile $file) {
		$config = $file->getConfig();

		if ($config->get('thumbnail.enabled') !== true) {
			return $file;
		}

		$thumbnailFolderPath = MOXMAN_Util_PathUtils::combine($file->getParent(), $config->get('thumbnail.folder'));
		$thumbnailFile = MOXMAN::getFile($thumbnailFolderPath, $config->get('thumbnail.prefix') . $file->getName());

		return $thumbnailFile;
	}

	/**
	 * Creates a thumbnail for the specified file and returns that file object
	 * or the input file if thumbnails are disabled or not supported.
	 *
	 * @param MOXMAN_Vfs_IFile $file File to generate thumbnail for.
	 * @return MOXMAN_Vfs_IFile File instance that got generated or input file.
	 */
	public function createThumbnail(MOXMAN_Vfs_IFile $file, $localTempFile = null) {
		$config = $file->getConfig();

		// Thumbnails disabled in config
		if (!$config->get('thumbnail.enabled')) {
			return $file;
		}

		// File is not an image
		if (!MOXMAN_Media_ImageAlter::canEdit($file)) {
			return $file;
		}

		// No write access to parent path
		$dirFile = $file->getParentFile();
		if (!$dirFile->canWrite()) {
			return $file;
		}

		$thumbnailFolderPath = MOXMAN_Util_PathUtils::combine($file->getParent(), $config->get('thumbnail.folder'));
		$thumbnailFile = MOXMAN::getFile($thumbnailFolderPath, $config->get('thumbnail.prefix') . $file->getName());

		// Never generate thumbs in thumbs dirs
		if (basename($file->getParent()) == $config->get('thumbnail.folder')) {
			return $file;
		}

		$thumbnailFolderFile = $thumbnailFile->getParentFile();
		if ($thumbnailFile->exists()) {
			if ($file->isDirectory()) {
				return $file;
			}

			return $thumbnailFile;
		}

		if (!$thumbnailFolderFile->exists()) {
			$thumbnailFolderFile->mkdir();
			$this->fireFileAction(MOXMAN_Core_FileActionEventArgs::ADD, $thumbnailFolderFile);
		}

		// TODO: Maybe implement this inside MOXMAN_Media_ImageAlter
		if ($file instanceof MOXMAN_Vfs_Local_File) {
			if ($config->get('thumbnail.use_exif') && function_exists("exif_thumbnail") && preg_match('/jpe?g/i', MOXMAN_Util_PathUtils::getExtension($file->getName()))) {
				$imageType = null;
				$width = 0;
				$height = 0;

				$exifImage = exif_thumbnail(
					$localTempFile ? $localTempFile : $file->getInternalPath(),
					$width,
					$height,
					$imageType
				);

				if ($exifImage) {
					$stream = $thumbnailFile->open(MOXMAN_Vfs_IFileStream::WRITE);
					$stream->write($exifImage);
					$stream->close();

					$this->fireFileAction(MOXMAN_Core_FileActionEventArgs::ADD, $thumbnailFile);
					return $thumbnailFile;
				}
			}
		}

		$imageAlter = new MOXMAN_Media_ImageAlter();

		if ($localTempFile) {
			$imageAlter->load($localTempFile);
		} else {
			$imageAlter->loadFromFile($file);
		}

		$imageAlter->createThumbnail($config->get('thumbnail.width'), $config->get('thumbnail.height'));
		$imageAlter->saveToFile($thumbnailFile, $config->get('thumbnail.jpeg_quality'));

		$this->fireFileAction(MOXMAN_Core_FileActionEventArgs::ADD, $thumbnailFile);

		return $thumbnailFile;
	}

	/**
	 * Deletes any existing thumbnail for the specified file.
	 *
	 * @param MOXMAN_Vfs_IFile $file File to remove thumbnail for.
	 */
	public function deleteThumbnail(MOXMAN_Vfs_IFile $file) {
		if ($file->isDirectory() || !MOXMAN_Media_ImageAlter::canEdit($file)) {
			return false;
		}

		$config = $file->getConfig();

		if (!$config->get('thumbnail.delete')) {
			return false;
		}

		// Delete thumbnail file
		$thumbnailFolderPath = MOXMAN_Util_PathUtils::combine($file->getParent(), $config->get('thumbnail.folder'));
		$thumbnailFile = MOXMAN::getFile($thumbnailFolderPath, $config->get('thumbnail.prefix') . $file->getName());

		if ($thumbnailFile->exists()) {
			$thumbnailFile->delete();
			$this->fireFileAction(MOXMAN_Core_FileActionEventArgs::DELETE, $thumbnailFile);
			return true;
		}

		return false;
	}

	/**
	 * Converts a file instance to a JSON serializable object.
	 *
	 * @param MOXMAN_Vfs_IFile $file File to convert into JSON format.
	 * @param Boolean $meta State if the meta data should be returned or not.
	 * @return stdClass JSON serializable object.
	 */
	public static function fileToJson($file, $meta = false) {
		$config = $file->getConfig();

		$renameFilter = MOXMAN_Vfs_CombinedFileFilter::createFromConfig($config, "rename");
		$editFilter = MOXMAN_Vfs_CombinedFileFilter::createFromConfig($config, "edit");
		$viewFilter = MOXMAN_Vfs_CombinedFileFilter::createFromConfig($config, "view");

		$result = (object) array(
			"path" => $file->getPublicPath(),
			"size" => $file->getSize(),
			"lastModified" => $file->getLastModified(),
			"isFile" => $file->isFile(),
			"canRead" => $file->canRead(),
			"canWrite" => $file->canWrite(),
			"canEdit" => $file->isFile() && $editFilter->accept($file) === MOXMAN_Vfs_IFileFilter::ACCEPTED,
			"canRename" => $renameFilter->accept($file) === MOXMAN_Vfs_IFileFilter::ACCEPTED,
			"canView" => $file->isFile() && $viewFilter->accept($file) === MOXMAN_Vfs_IFileFilter::ACCEPTED,
			"canPreview" => $file->isFile() && MOXMAN_Media_ImageAlter::canEdit($file),
			"exists" => $file->exists()
		);

		if ($meta) {
			$metaData = $file->getMetaData();
			//$args = $this->fireCustomInfo(MOXMAN_Core_CustomInfoEventArgs::INSERT_TYPE, $file);
			$metaData = (object) $metaData->getAll();

			if ($file instanceof MOXMAN_Vfs_Local_File && MOXMAN_Media_ImageAlter::canEdit($file)) {
				$thumbnailFolderPath = MOXMAN_Util_PathUtils::combine($file->getParent(), $config->get('thumbnail.folder'));
				$thumbnailFile = MOXMAN::getFile($thumbnailFolderPath, $config->get('thumbnail.prefix') . $file->getName());

				// TODO: Implement stat info cache layer here
				$info = MOXMAN_Media_MediaInfo::getInfo($file);
				$metaData->width = $info["width"];
				$metaData->height = $info["height"];

				if ($thumbnailFile->exists()) {
					$metaData->thumb_url = $thumbnailFile->getUrl();

					$info = MOXMAN_Media_MediaInfo::getInfo($thumbnailFile);
					$metaData->thumb_width = $info["width"];
					$metaData->thumb_height = $info["height"];
				}
			}

			$metaData->url = $file->getUrl();
			$result->meta = $metaData;
		}

		return $result;
	}

	/** @ignore */
	private function fireFileAction($action, $file, $data = array()) {
		$args = new MOXMAN_Core_FileActionEventArgs($action, $file);
		$args->getData()->thumb = true;

		return MOXMAN::getPluginManager()->get("core")->fire("FileAction", $args);
	}
}

MOXMAN::getPluginManager()->add("core", new MOXMAN_Core_Plugin());

?>