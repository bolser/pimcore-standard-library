<?php

/**
 * Creative Commons
 *
 * Attribution Non-Commercial NoDerivatives 4.0 International
 *
 * This work is licensed under the Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License.
 * To view a copy of this license, visit http://creativecommons.org/licenses/by-nc-nd/4.0/ or send a letter to Creative
 * Commons, PO Box 1866, Mountain View, CA 94042, USA.
 *
 * @author     Matt Booth <mattbooth@bolser.co.uk>
 * @copyright  2001-2017 Bolser Digital Agency
 * @license    https://creativecommons.org/licenses/by-nc-nd/4.0/legalcode
 */

namespace  Bolser\Pimcore\EventHandler;

/**
 * Class IEventHandler
 *
 * @package Website\EventHandler
 */
interface IEventHandler
{
    /**
     * Called before an object is updated
     *
     * @param $object
     *
     * @return mixed
     */
    function onPreUpdate($object);

    /**
     * Called before an object is deleted
     *
     * @param $object
     *
     * @return mixed
     */
    function onPreDelete($object);
}