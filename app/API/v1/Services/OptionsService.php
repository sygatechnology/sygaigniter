<?php

namespace App\API\v1\Services;

helper(['functions']);

class OptionsService
{
    private static $registered = [];

    public static function register($name, $value)
    {
        if (!isset(static::$registered[$name])) {
            static::$registered[$name] = isSerialized($value) ? unserialize_args($value) : $value;
        }
    }

    public static function get($name = null, $defaultValue = null)
    {
        $db = \Config\Database::connect();
        if ($name != null) {
            if (static::inRegistered($name)) {
                return static::getInRegistered($name);
            }
            if (static::exists($name)) {
                $first = $db->table('options')->where('oname', $name)->get()->getResult()[0];
                return static::setResult($first->ovalue);
            }
            return $defaultValue;
        }
        return static::setResult($db->table('options')->orderBy('oname', 'ASC')->get()->getResult());
    }

    private static function inRegistered($name): bool
    {
        return isset(static::$registered[$name]);
    }

    private static function getInRegistered($name)
    {
        return static::$registered[$name];
    }

    public static function setResult($result)
    {
        if (is_array($result)) {
            $data = [];
            foreach ($result as $value) {
                if (isSerialized($value->ovalue)) {
                    $value->ovalue = unserialize_args($value->ovalue);
                }
                $data[] = $value->ovalue;
            }
            return $data;
        }
        return isSerialized($result) ? unserialize_args($result) : $result;
    }

    public static function exists($name): bool
    {
        $db = \Config\Database::connect();
        return ($db->table('options')->where('oname', $name)->countAllResults() > 0);
    }

    public static function add($name, $value, $autoload = false): bool
    {
        if (!static::exists($name)) {
            $data = [
                'oname' => $name,
                'ovalue' => (is_array($value) || is_object($value)) ? serialize_args($value) : $value,
                'autoload' => $autoload ? 'yes' : 'no'
            ];
            $db = \Config\Database::connect();
            return (bool) $db->table('options')->insert($data);
        }
        return false;
    }

    public static function update($name, $value, $autoload = null)
    {
        if (static::exists($name)) {
            $data = [
                'ovalue' => (is_array($value) || is_object($value)) ? serialize_args($value) : $value
            ];
            if ($autoload != null && is_bool($autoload)) {
                $data['autoload'] = $autoload ? 'yes' : 'no';
            }
            $db = \Config\Database::connect();
            return $db->table('options')->update($data, ['oname' => $name]);
        }
        return false;
    }

    public static function save($name, $value, $autoload = false)
    {
        $data = [
            'ovalue' => (is_array($value) || is_object($value)) ? serialize_args($value) : $value,
            'autoload' => $autoload ? 'yes' : 'no'
        ];
        $db = \Config\Database::connect();
        if (!static::exists($name)) {
            $data['oname'] = $name;
            return $db->table('options')->insert($data);
        } else {
            return $db->table('options')->update($data, ['oname' => $name]);
        }
    }

    public static function delete($name)
    {
        $db = \Config\Database::connect();
        $names = (array) $name;
        return $db->table('options')->whereIn('oname', $names)->delete();
    }
}
