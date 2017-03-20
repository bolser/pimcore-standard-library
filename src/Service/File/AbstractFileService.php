<?php

/**
 * AbstractFileService
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Service\File;

use Bolser\Pimcore\Exception\InvalidAssetTypeException;
use Bolser\Pimcore\Util\HttpResponseCode;
use Exception;
use Pimcore\File;
use Pimcore\Logger;
use Pimcore\Model\Asset;
use Pimcore\Model\Asset\Archive;
use Pimcore\Model\Asset\Audio;
use Pimcore\Model\Asset\Document;
use Pimcore\Model\Asset\Image;
use Pimcore\Model\Asset\Video;
use Zend_Controller_Action_Exception;
use Zend_File_Transfer_Adapter_Http;
use Zend_File_Transfer_Exception;
use Zend_Controller_Response_Http;
use SplFileInfo;

/**
 * Class AbstractFileService
 *
 * @package Bolser\Pimcore\Service\File
 */
abstract class AbstractFileService
{
    /**
     * Receive an uploaded file from the Zend File Transfer Adapter and move it to the required folder
     *
     * @param string $filename        The filename of the uploaded file to be retrieved from the adapter
     * @param string $destinationPath The destination for the uploaded file
     *
     * @return bool|SplFileInfo
     */
    public function receiveFileUpload(string $filename, string $destinationPath)
    {
        if (!file_exists($destinationPath)) {
            $message = sprintf('The destination: %s does not exist. Abandoning file upload', $destinationPath);
            Logger::error($message);

            return false;
        }

        $adapter = new Zend_File_Transfer_Adapter_Http();

        // Make sure the upload exists and it matches the filename we give
        if (!$adapter->getFilename() && strcasecmp($adapter->getFileName(), $filename) === 0) {
            $message = sprintf('Filename "%s" in the filebag doesn\'t match the filename provided', $filename);
            Logger::error($message);

            return false;
        }

        // Remove the cache directory path from the filename
        $formattedFilename = basename($filename);

        // Get a filename without spaces or special characters
        $key = File::getValidFilename($formattedFilename);

        // Destination file
        $destinationFile = $destinationPath . DIRECTORY_SEPARATOR . $key;

        // Check if there is already a file with the same key
        // If so, put the first 6 characters of the md5 hash of the filename at the beginning of the key/filename
        // Then update the $destinationFile variable with the new key
        if (file_exists($destinationFile)) {
            $key = File::getValidFilename(substr(md5($filename), 0, 6) . '-' . $formattedFilename);
            $destinationFile = $destinationPath . DIRECTORY_SEPARATOR . $key;
        }

        // Receive the file
        try {
            $adapter->receive($filename);
        } catch (Zend_File_Transfer_Exception $e) {
            Logger::error($e->getMessage());
        }

        // Copy the file to the destination
        if (!copy($adapter->getFileName(), $destinationFile)) {
            $message = sprintf('Copy of file: %s to destination folder: %s failed', [$filename, $destinationPath]);
            Logger::error($message);

            return false;
        }

        // Remove the file from the cache
        unlink($adapter->getFileName());

        // One last sanity check
        if (!file_exists($destinationFile)) {
            $message = sprintf('Copy of file: %s to destination folder: %s failed', [$filename, $destinationPath]);
            Logger::error($message);

            return false;
        }

        // Make sure the uploaded file was removed from the cache dir
        if (file_exists($adapter->getFileName())) {
            $message = sprintf('Unlink did not remove the file "%s" from the cache folder', [$filename]);
            Logger::error($message);

            return false;
        }

        // Our new file
        return new SplFileInfo($destinationFile);
    }

    /**
     * Downloads a given file in the user's browser
     *
     * @param Zend_Controller_Response_Http $response Zend response object for directing the user's browser
     * @param SplFileInfo                   $file     The file to download
     *
     * @throws Zend_Controller_Action_Exception  thrown when chosen file was not found
     */
    public function downloadFile(Zend_Controller_Response_Http $response, SplFileInfo $file): void
    {
        // Full path to the file
        $fullPath = $file->getRealPath();

        // The filename
        $filename = $file->getFilename();

        // Sanity check to make sure the file we're trying to download exists on the system
        if (file_exists($fullPath)) {
            $response
                ->setHeader('Content-Description', 'File Transfer', true)
                ->setHeader('Content-Type', 'application/octet-stream', true)
                ->setHeader('Content-Disposition', 'attachment; filename=' . $filename, true)
                ->setHeader('Content-Transfer-Encoding', 'binary', true)
                ->setHeader('Expires', '0', true)
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
                ->setHeader('Pragma', 'public', true)
                ->setHeader('Content-Length: ', filesize($fullPath), true);

            $response->setBody(file_get_contents($fullPath));
        } else {
            $message = sprintf('File with filename: "%s" not found', $filename);
            throw new Zend_Controller_Action_Exception($message, HttpResponseCode::HTTP_NOT_FOUND);
        }
    }

    /**
     * Downloads a given Asset file in the user's browser
     *
     * @param Zend_Controller_Response_Http $response Zend response object for directing the user's browser
     * @param Asset                         $file     The asset to download
     */
    public function downloadAssetFile(Zend_Controller_Response_Http $response, Asset $file): void
    {
        $this->downloadFile($response, new SplFileInfo($file->getRealPath()));
    }

    /**
     * Create an Asset type from a file.
     *
     * Optional: Add a folder name to place the asset in a folder. This will be created if it does not already exist
     *
     * @param SplFileInfo $file       The file to create
     * @param string      $type       The type of file to create
     * @param string      $folderName Optional foldername to place the asset inside
     *
     * @return Asset The created asset
     * @throws Exception Thrown if an incorrect type is given
     */
    public function createAssetFromFile(SplFileInfo $file, string $type, string $folderName = ""): Asset
    {
        // Data required to create the asset
        $data = [
            'fileName'         => $file->getFilename(),
            'data'             => file_get_contents($file->getRealPath()),
            'userOwner'        => 1,
            'userModification' => 1,
        ];

        // Instantiate the folder variable
        $folder = null;

        // Create or get a folder if name is provided
        if (!empty($folderName)) {
            // Attempt to get a folder asset by name
            // Returns null if folder asset doesn't exist
            $folder = Asset::getByPath(PIMCORE_ASSET_DIRECTORY . DIRECTORY_SEPARATOR . $folderName);

            // If there is no folder with the provided name, create one
            if (is_null($folder)) {
                $folder = Asset::create(1, // Base parent ID of 1
                    [
                        'fileName'         => $folderName,
                        'type'             => 'folder',
                        'userOwner'        => 1,
                        'userModification' => 1,
                    ]);

                $folder->save();
            }
        }

        // Set the parent ID to either 1 or the folder ID if available
        $parentId = is_null($folder) ? 1 : $folder->getId();

        // Create the appropriate type
        switch ($type) {
            case FileType::FILE_TYPE_ARCHIVE:
                $document = Archive::create($parentId, $data);
                break;
            case FileType::FILE_TYPE_AUDIO:
                $document = Audio::create($parentId, $data);
                break;
            case FileType::FILE_TYPE_DOCUMENT:
                $document = Document::create($parentId, $data);
                break;
            case FileType::FILE_TYPE_IMAGE:
                $document = Image::create($parentId, $data);
                break;
            case FileType::FILE_TYPE_VIDEO:
                $document = Video::create($parentId, $data);
                break;
            default:
                $message = sprintf('The asset type: "%s" is not valid', $type);
                throw new InvalidAssetTypeException($message, HttpResponseCode::HTTP_BAD_REQUEST);
                break;
        }

        // Save the new asset and return it
        return $document->save();
    }
}

class FileType
{
    const FILE_TYPE_ARCHIVE = "archive";
    const FILE_TYPE_AUDIO = "audio";
    const FILE_TYPE_DOCUMENT = "document";
    const FILE_TYPE_IMAGE = "image";
    const FILE_TYPE_VIDEO = "video";
}
