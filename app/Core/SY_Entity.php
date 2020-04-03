<?php namespace App\Core;

/**
 * @package    App\Entities
 * @author     SygaTechnology Dev Team
 * @copyright  2019 SygaTechnology Foundation
 */

use CodeIgniter\Entity;

class SY_Entity extends Entity
{
    protected $usePublicAttributes = false;
    protected $publicAttributes = [];
    private $reversedDtataMap = [];
    private $storedResult = null;

    public function __construct(array $data = null)
    {
        parent::__construct($data);
    }

    public function getResult(): array
    {
        if(!is_null($this->storedResult)){
            return $this->storedResult;
        }
        $result = array();
        $reversedDtataMap = [];
        foreach ($this->datamap as $key => $value) {
            $reversedDtataMap[$value] = $key;
        }
        $this->reversedDtataMap = $reversedDtataMap;
        unset($reversedDtataMap);
        if ($this->usePublicAttributes) {
            foreach ($this->attributes as $key => $value) {
                $key = $this->getReversedKey($key);
                if ($this->isPublicAttribute($key)) {
                    $result[$key] = is_numeric($value) ? (int) $value : $value;
                }
            }
        } else {
            foreach ($this->attributes as $key => $value) {
                $key = $this->getReversedKey($key);
                $result[$key] = $value;
            }
        }
        $this->storedResult = $result;
        return $result;
    }

    public function getField($fieldName)
    {
        if(is_null($this->storedResult)){
            $this->getResult();
        }
        return isset($this->storedResult[$fieldName]) ? $this->storedResult[$fieldName] : null;
    }

    public function isNull(){
        return empty($this->storedResult);
    }

    private function getReversedKey($key)
    {
        if (isset($this->reversedDtataMap[$key])) {
            return $this->reversedDtataMap[$key];
        }
        return $key;
    }

    private function isPublicAttribute($key)
    {
        return in_array($key, $this->publicAttributes);
    }
}
