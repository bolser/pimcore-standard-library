<?php

/**
 * ViewFactory
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Service\Email;

use Zend_View;

/**
 * Class ViewFactory
 *
 * @package Bolser\Pimcore\Service\Email
 */
class ViewFactory
{
    const EMAIL_PATH = '/views/scripts/emails';
    /**
     * Gets a Zend/View object with the script path set to the views/scripts/emails folder
     *
     * @return Zend_View
     */
    public function getView(): Zend_View
    {
        $view = new Zend_View();
        $view->setScriptPath(PIMCORE_WEBSITE_PATH . self::EMAIL_PATH);

        return $view;
    }
}
