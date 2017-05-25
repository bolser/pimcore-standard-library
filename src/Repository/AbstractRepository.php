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

use Pimcore\Model\Object\Concrete;
use Zend_Db_Expr;

/**
 * Class AbstractRepository
 *
 * @package Bolser\Pimcore\Repository
 */
abstract class AbstractRepository
{
    /**
     * @var Concrete $model
     */
    protected $model;
    protected $tableName;
    protected $dao;

    /**
     * AbstractRepository constructor.
     *
     * @param Concrete $model
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->tableName = "object_" . $model->getClassId();
        $this->dao = $model->getDao();
    }

    /**
     * Gets a model by it's ID
     *
     * @param int $id The ID to find
     *
     * @return mixed
     */
    public function getById(int $id)
    {
        $query = $this->dao->db->select()
            ->from($this->tableName)
            ->where('oo_id = ?', $id)
            ->where('o_published = 1');

        return $this->transformSingular($this->dao->db->fetchRow($query));
    }

    /**
     * Gets a model by it's key
     *
     * @param string $key The key to find
     *
     * @return mixed
     */
    public function getByKey(string $key)
    {
        $query = $this->dao->db->select()
            ->from($this->tableName)
            ->where('o_key = ?', $key)
            ->where('o_published = ?', 1);

        return $this->transformSingular($this->dao->db->fetchRow($query));
    }

    /**
     * Gets all entries for a model, optional ordering available
     *
     * @param string $orderKey The field to order the results on
     * @param string $order    The method of ordering
     *
     * @return array
     */
    public function getAll(string $orderKey = 'name', string $order = 'asc')
    {
        $query = $this->dao->db->select()
            ->from($this->tableName)
            ->order([$orderKey . ' ' . $order])
            ->where('o_published = ?', 1);

        return $this->transformMultiple($this->dao->db->fetchAll($query));
    }

    /**
     * Gets data from the database randomly
     *
     * @param int $count The total amount to get
     *
     * @return array
     */
    public function getRandom(int $count = 10)
    {
        $query = $this->dao->db->select()
            ->from($this->tableName)
            ->order([new Zend_Db_Expr("RAND()")])
            ->where('o_published = ?', 1)
            ->limit($count);

        return $this->transformMultiple($this->dao->db->fetchAll($query));
    }

    /**
     * Transforms a two dimensional array into an array of models
     *
     * @param array $input
     *
     * @return array
     */
    protected function transformMultiple(array $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (!array_key_exists('oo_id', $input[0])) {
            return [];
        }

        $output = [];

        foreach ($input as $item) {
            $output[] = $this->userFuncGetById(intval($item['oo_id']));
        }

        return $output;
    }

    /**
     * Transforms a one dimensional array into an array of models
     *
     * @param array $input
     *
     * @return array
     */
    protected function transformSingular(array $input)
    {
        if (empty($input)) {
            return [];
        }

        if (!array_key_exists('oo_id', $input)) {
            return [];
        }

        return $this->userFuncGetById(intval($input['oo_id']));
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
     * Gets the log name for this repository
     *
     * @return string The log name
     */
    abstract function getLogName(): string;

    /**
     * Gets the class definition of the class using the repository
     *
     * @return string
     */
    abstract function getClassDefinition(): string;
}
