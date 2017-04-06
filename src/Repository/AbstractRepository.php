<?php

/**
 * AbstractRepository
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md
 * file distributed with this source code.
 *
 * @copyright  Copyright (c) 2001-2017 Bolser Digital Agency (https://www.bolser.co.uk)
 * @license    GNU General Public License version 3 (GPLv3)
 */

namespace Bolser\Pimcore\Repository;

use Pimcore\Model\Object\Listing;

abstract class AbstractRepository
{
    /**
     * @var Listing $model
     */
    protected $model;

    /**
     * Return all results
     */
    public function all()
    {
        return $this->model->load();
    }

    /**
     * Get all objects and order them
     *
     * @param string $orderKey The key to order the results upon
     * @param string $order    The order to show the results E.G. asc or desc
     *
     * @return mixed
     */
    public function getAll(string $orderKey = 'name', string $order = 'asc')
    {
        return $this
            ->setCondition('o_published', true)
            ->setOrderKey($orderKey)
            ->setOrder($order)
            ->load();
    }

    /**
     * Gets a single object by it's ID
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getById(int $id)
    {
        return current(
            $this
                ->setCondition('o_published', true)
                ->setCondition('oo_id', $id)
                ->setLimit(1)
                ->load()
        );
    }

    /**
     * Gets a single object by it's key
     *
     * @param $key
     *
     * @return mixed
     */
    public function getByKey($key)
    {
        return current(
            $this
                ->model
                ->setCondition('o_published', true)
                ->setCondition("o_key = ?", $key)
                ->setLimit(1)
                ->load()
        );
    }

    /**
     * Set results order
     *
     * @param $order
     *
     * @return Listing
     */
    public function setOrder($order)
    {
        $this->model->setOrder($order);

        return $this->model;
    }

    /**
     * Set order by key
     *
     * @param $orderKey
     *
     * @return Listing
     */
    public function setOrderKey($orderKey)
    {
        $this->model->setOrderKey($orderKey);

        return $this->model;
    }

    /**
     * Set the limit for the returned values
     *
     * @param $limit
     *
     * @return Listing
     */
    public function setLimit($limit)
    {
        $this->model->setLimit($limit);

        return $this->model;
    }

    /**
     * Select via a condition
     *
     * @param $key
     * @param $value
     *
     * @return Listing
     */
    public function setCondition($key, $value)
    {
        $this->model->setCondition("$key = ?", $value);

        return $this->model;
    }

    /**
     * Select via multiple conditions
     *
     * @param $key
     * @param $value
     *
     * @return Listing
     */
    public function setConditions($key, $value)
    {
        $this->model->setCondition($key, $value);

        return $this->model;
    }

    /**
     * Load the list
     *
     * @return mixed
     */
    public function load()
    {
        return $this->model->load();
    }

    /**
     * Gets a total count of all the models in the listing
     *
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->model->getTotalCount();
    }
}
