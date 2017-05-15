<?php

/**
 * FileType
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Service\File;

/**
 * Interface FileType
 *
 * @package Bolser\Pimcore\Service\File
 */
interface FileType
{
    const FILE_TYPE_ARCHIVE = "archive";
    const FILE_TYPE_AUDIO = "audio";
    const FILE_TYPE_DOCUMENT = "document";
    const FILE_TYPE_IMAGE = "image";
    const FILE_TYPE_VIDEO = "video";
}