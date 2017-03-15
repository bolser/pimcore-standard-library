<?php

/**
 * AbstractForm
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Form;

use Zend_Form;
use Zend_View_Interface;

/**
 * Class AbstractForm
 *
 * @package Bolser\Pimcore\Form
 */
class AbstractForm extends Zend_Form
{
    /**
     * Clear the Zend_Form decorators to make custom form styling much easier
     *
     * Removes <dt> and <li> HTML elements from the form
     *
     * @return AbstractForm
     */
    public function clearDecorators(): AbstractForm
    {
        // IMPORTANT: Add decorators after addElements()
        $this->addDecorator('FormElements');    //This add the form elements first
        $this->addDecorator('Form');            //This removes <dt> and adds the form around the <ul>
        $this->addDecorator('File');

        // Time to remove the <dt> and add the <li>
        $this->setElementDecorators([
            ['ViewHelper'],     // This is important otherwise you won't see your <input> elements
            ['Label'],          // We want the label
            ['Errors'],         // We want the errors too
        ]);

        return $this;
    }

    /**
     * Get the view helper interface
     *
     * @return Zend_View_Interface
     */
    public function getView(): Zend_View_Interface
    {
        if (null === $this->_view) {
            require_once 'Zend/Controller/Action/HelperBroker.php';
            $viewRenderer = \Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
            $this->setView($viewRenderer->view);
        }

        return $this->_view;
    }
}