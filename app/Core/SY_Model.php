<?php
namespace App\Core;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;

class SY_Model extends Model
{
    /**
     * Model constructor.
     *
     * @param ConnectionInterface $db
     * @param ValidationInterface $validation
     */
    public function __construct(ConnectionInterface &$db = null, ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);
    }

    /**
     * Works with the find* methods to return only the rows that
     * have been deleted.
     *
     * @return SY_Model
     */
    public function onlyDeleted()
    {
        $this->tempUseSoftDeletes = false;

        $this->where($this->table . '.' . $this->deletedField . ' > "0000-00-00 00:00:00"');

        return $this;
    }

    /**
     * Works with the find* methods to return the rows
     *
     * @return SY_Model
     */
    public function withoutDeleted()
    {
        //$this->oRwhere($this->table . '.' . $this->deletedField . ' = "0000-00-00 00:00:00"');

        return $this;
    }

    public function countAllCompiledResults($withDeleted = false, $where = [])
    {
        $db = \Config\Database::connect();
        $sqlQuery = "SELECT count(*) AS count FROM " . $this->getPrefix() . $this->table;
        if (!$withDeleted) {
            //$where[$this->deletedField . ' ='] = "0000-00-00 00:00:00";
            $where[$this->deletedField] = 'IS NULL';
        }
        if (!empty($where)) {
            $i = 0;
            foreach ($where as $key => $value) {
                $sql = "`".$this->getPrefix().$this->table."`.`".$key."`";
                $value = is_numeric($value) ? " ".(int) $value : ('is null' != strtolower($value) && 'is not null' != strtolower($value) ? " '".$value."'" : " ".$value);
                if ($i === 0) {
                    $sqlQuery .= " WHERE " . $sql . $value;
                } else {
                    $sqlQuery .= " AND " . $sql . $value;
                }
                $i++;
            }
        }
        $result = $db->query($sqlQuery)->getResultArray();
        return (int) $result[0]['count'];
    }
}
