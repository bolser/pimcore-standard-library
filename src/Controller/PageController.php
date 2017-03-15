<?php

/**
 * PageController
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

/**
 * Class PageController
 *
 * @package Bolser\Pimcore\Controller
 */
abstract class PageController extends AbstractController
{
    /**
     * @throws Zend_Controller_Router_Exception
     */
    public function init()
    {
        parent::init();

        $this->enableLayout();

        $this->view->language = (string)$this->getLocale();
        $this->language = (string)$this->getLocale();
    }
}
