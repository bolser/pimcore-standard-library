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
 * Class CleanUrlGenerator
 *
 * @package Bolser\Pimcore\Util
 */
class CleanUrlGenerator
{
    /**
     * Gets a clean URL that removes any special characters from a string
     *
     * @param string $str       The string to clean
     * @param string $delimiter The URL delimiter
     *
     * @return string A cleaned string
     */
    public static function getCleanUrlKey(string $str, string $delimiter = '-'): string
    {
        $friendlyURL = htmlentities($str, ENT_COMPAT, "UTF-8", false);
        $friendlyURL = preg_replace('/&([a-z]{1,2})(?:acute|circ|lig|grave|ring|tilde|uml|cedil|caron);/i', '\1',
            $friendlyURL);
        $friendlyURL = html_entity_decode($friendlyURL, ENT_COMPAT, "UTF-8");
        $friendlyURL = preg_replace('/[^a-z0-9-]+/i', $delimiter, $friendlyURL);
        $friendlyURL = preg_replace('/-+/', $delimiter, $friendlyURL);
        $friendlyURL = trim($friendlyURL, $delimiter);
        $friendlyURL = strtolower($friendlyURL);

        return $friendlyURL;
    }
}