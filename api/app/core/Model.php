<?php

namespace App\Core;

use App\Core\Helpers\SqlHelper;
use App\Core\Interfaces\ModelInterface;

class Model implements ModelInterface
{
    private $sqlHelper;

    protected $table;
    protected $db;

    /**
     * Model constructor.
     * @param \PDO $db
     */
    protected function __construct($db)
    {
        $this->sqlHelper = new SqlHelper();

        $classWithNamespace = explode('\\', get_class($this));
        $this->table = array_pop($classWithNamespace);
        $this->db = $db;
    }

    /**
     * Create entry
     * @param array $data
     * @return int
     * @throws \Exception
     */
    public function create($data)
    {
        try {
            $parametersAliases = $this->sqlHelper->prepareParameters($data);
            $query = "INSERT INTO " . $this->table . " (`" . join("`,`", array_keys($data)) . "`) VALUES (" . join(',', array_keys($parametersAliases)) . ")";
            $stmt = $this->db->prepare($query);

            $insertedRowID = 0;
            if ($stmt->execute($parametersAliases)) {
                $insertedRowID = $this->db->lastInsertId();
            }

            return $insertedRowID;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Get entry/entries
     * @param array $data
     * @return array
     * @throws \Exception
     */
    public function get($data = [])
    {
        try {
            $query = "SELECT * FROM " . $this->table;
            $query .= count($data) > 0 ? " WHERE " . join('', $this->sqlHelper->prepareAliases($data, '',' AND ')) : '';
            $stmt = $this->db->prepare($query);
            $stmt->execute($this->sqlHelper->prepareParameters($data));

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Update entry
     * @param $data
     * @return int
     * @throws \Exception
     */
    public function update($data)
    {
        try {
            $model = $this->get([ 'id' => $data['id'] ]);
            if ($model) {
                $modelData = $data;
                unset($modelData['id']);
                $query = "UPDATE " . $this->table . " SET " . join(',', $this->sqlHelper->prepareAliases($modelData)) . " WHERE `id` = :id";
                $stmt = $this->db->prepare($query);
                $data = $this->sqlHelper->prepareParameters($data);
                $stmt->execute($data);

                return $stmt->rowCount();
            }
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * Delete entry
     * @param $data
     * @return int
     * @throws \Exception
     */
    public function delete($data)
    {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE `id` = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $data['id']);
            $stmt->execute();

            return $stmt->rowCount();
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}