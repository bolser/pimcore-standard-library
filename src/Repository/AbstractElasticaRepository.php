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

namespace Website\Repository;

use Bolser\Pimcore\Repository\AbstractRepository;
use Elastica\Client;
use Elastica\Index;
use Elastica\Query;
use Elastica\Type;
use Exception;
use Pimcore\Log\Simple;
use Pimcore\Model\Object\Concrete;

/**
 * Class AbstractElasticaRepository
 *
 * @package Website\Repository
 */
abstract class AbstractElasticaRepository extends AbstractRepository
{
    /**
     * Elasticsearch Client
     *
     * @var Client $client
     */
    private $client;

    /**
     * Elasticsearch Index
     *
     * @var Index $index
     */
    protected $index;

    /**
     * Elasticsearch Type
     *
     * @var Type $type
     */
    private $type;

    public function __construct(Concrete $model, Client $client, string $indexName, string $typeName)
    {
        parent::__construct($model);
        $this->client = $client;
        $this->index = $this->client->getIndex($indexName);
        $this->type = $this->index->getType($typeName);
    }

    /**
     * Generate results from type and query
     *
     * @param Query $query
     *
     * @return bool|array
     */
    protected function generateResults(Query $query)
    {
        // Get the results
        try {
            $resultSet = $this->type->search($query);
            $results = $resultSet->getResults();
        } catch (Exception $e) {
            Simple::log($this->getLogName(), $e->getMessage());

            return false;
        }

        // Make sure we have some results to return before doing so.
        if (!empty($results)) {
            return array_filter($results);
        }

        // No results were returned. Log the situation
        $message = sprintf("The elasticsearch query: '%s' did not return any results.", $query->toArray());
        Simple::log($this->getLogName(), $message);

        return false;
    }
}