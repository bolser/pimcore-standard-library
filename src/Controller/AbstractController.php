<?php

/**
 * AbstractController
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Controller;

use Zend_Controller_Router_Exception;
use Zend_Locale;
use Zend_Registry;

/**
 * Class AbstractController
 *
 * @package Bolser\Pimcore\Controller
 */
abstract class AbstractController extends Action
{
    /**
     * @var Zend_Locale $locale
     */
    protected $locale;

    /**
     * @throws Zend_Controller_Router_Exception
     */
    public function init()
    {
        parent::init();

        if (Zend_Registry::isRegistered("Zend_Locale")) {
            $locale = Zend_Registry::get("Zend_Locale");
        } else {
            $locale = new Zend_Locale("en");
            Zend_Registry::set("Zend_Locale", $locale);
        }

        $this->setLocale($locale);
    }

    /**
     * Get the Zend Locale
     *
     * @return Zend_Locale
     */
    public function getLocale(): Zend_Locale
    {
        return $this->locale;
    }

    /**
     * Set the Zend Locale
     *
     * @param $locale
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;
    }
}
