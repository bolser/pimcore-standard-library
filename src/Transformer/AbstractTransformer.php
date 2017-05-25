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

namespace Bolser\Pimcore\Transformer;

use Elastica\Result;

/**
 * Class AbstractTransformer
 *
 * @package Bolser\Pimcore\Transformer
 */
abstract class AbstractTransformer
{
    /**
     * Transforms Elastica\Result array into a primitive array of Retailer objects
     *
     * @param array $input
     *
     * @return array
     */
    protected function transform(array $input): array
    {
        $output = [];

        /** @var Result $item */
        foreach ($input as $item) {
            $output[] = $this->userFuncGetById(intval($item->getId()));
        }

        return $output;
    }

    /**
     * Randomise the order of an Array
     *
     * @param array $input
     *
     * @return array
     */
    protected function randomiseArray(array $input): array
    {
        // Shuffle the Array
        shuffle($input);

        // Return the shuffled array
        return $input;
    }

    /**
     * Uses call_user_function to get a Model by it's ID
     *
     * @param int $id
     *
     * @return mixed
     */
    protected function userFuncGetById(int $id)
    {
        return call_user_func($this->getClassDefinition() . '::getById', $id);
    }

    /**
     * Gets the class definition of the class using the transformer
     *
     * @return string
     */
    abstract function getClassDefinition(): string;
}