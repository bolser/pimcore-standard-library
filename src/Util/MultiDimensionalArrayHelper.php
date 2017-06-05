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

namespace Bolser\Pimcore\Util;

/**
 * Class MultiDimensionalArrayHelper
 *
 * @package Website\Utils
 */
class MultiDimensionalArrayHelper
{
    /**
     * Checks a multidimensional array if a key exists within
     *
     * @param array $array The array to check
     * @param       $key
     *
     * @return bool
     */
    public function doesKeyExist(array $array, $key): bool
    {
        // Check the base array first
        if (array_key_exists($key, $array)) {
            return true;
        }

        // Check inner arrays in the base array
        foreach ($array as $element) {
            if (is_array($element)) {
                if ($this->doesKeyExist($element, $key)) {
                    return true;
                }
            }
        }

        return false;
    }
}