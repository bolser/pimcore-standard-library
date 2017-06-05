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

use Bolser\Pimcore\Elastica\IElasticaHandler;

/**
 * Class AbstractEventHandler
 *
 * @package Website\EventHandler
 */
abstract class AbstractEventHandler implements IEventHandler
{
    /**
     * @var IElasticaHandler $elasticaHandler
     */
    protected $elasticaHandler;

    /**
     * @inheritDoc
     */
    function onPreUpdate($object)
    {
        return $this->elasticaHandler->addDocument($object);
    }

    /**
     * @inheritDoc
     */
    function onPreDelete($object)
    {
        return $this->elasticaHandler->removeDocument($object);
    }
}