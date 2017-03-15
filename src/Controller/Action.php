<?php

/**
 * Action
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Controller;

use Pimcore\Controller\Action\Frontend;
use Zend_Controller_Router_Exception;
use Zend_Locale;
use Zend_Registry;

/**
 * Class Action
 *
 * @package Bolser\Pimcore\Controller
 */
abstract class Action extends Frontend
{
    /**
     * @throws Zend_Controller_Router_Exception
     */
    public function init()
    {
        if (self::$isInitial) {
            // only do this once
            // eg. registering view helpers or other generic things
        }

        parent::init();

        if (Zend_Registry::isRegistered("Zend_Locale")) {
            $locale = Zend_Registry::get("Zend_Locale");
        } else {
            $locale = new Zend_Locale("en");
            Zend_Registry::set("Zend_Locale", $locale);
        }

        $this->view->language = (string)$locale;
        $this->language = (string)$locale;
    }
}
