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

use Zend_Paginator;

/**
 * Class AbstractJsonTransformer
 *
 * @package Website\Transformer
 */
abstract class AbstractJsonTransformer extends AbstractTransformer implements JsonTransformer
{
    /**
     * @inheritDoc
     */
    public function transformToJson(Zend_Paginator $paginator): array
    {
        $output = [];

        $this->getItems($output, $paginator);

        $this->getPagination($output, $paginator);

        return $output;
    }

    /**
     * Adds specific pagination items to the bottom of a page in the JSON reponse
     *
     * @param array          $output
     * @param Zend_Paginator $paginator
     */
    private function getPagination(array &$output, Zend_Paginator $paginator)
    {
        $output['page'] = $paginator->getCurrentPageNumber();
        $output['perPage'] = $paginator->getItemCountPerPage();
        $output['totalPages'] = $paginator->getPages()->pageCount;
        $output['totalItems'] = $paginator->getTotalItemCount();
    }

    /**
     * Get the items for a Json response array
     *
     * @param array          $output
     * @param Zend_Paginator $paginator
     *
     * @return mixed
     */
    public abstract function getItems(array &$output, Zend_Paginator $paginator);
}