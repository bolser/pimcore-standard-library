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

use Pimcore\File;
use Pimcore\Logger;
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
     * @param Zend_Controller_Response_Http $response
     * @param SplFileInfo                   $file
     *
     * @throws Zend_Controller_Action_Exception  Chosen file was not found
     */
    public function download(Zend_Controller_Response_Http $response, SplFileInfo $file): void
    {
        $fullPath = $file->getRealPath();
        $filename = $file->getFilename();

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
            throw new Zend_Controller_Action_Exception('File not found', 404);
        }
    }
}
