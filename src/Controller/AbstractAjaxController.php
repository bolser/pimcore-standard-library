<?php

/**
 * AbstractAjaxController
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Controller;

/**
 * Class AbstractAjaxController
 *
 * @package Bolser\Pimcore\Controller
 */
class AbstractAjaxController extends AbstractController
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Disable Layout rendering on Ajax controllers so that we can see the raw Json responses
        $this->disableLayout();
    }
}