<?php
/**
 * Syga CI Api
 *
 */

if(! function_exists('is_associative')){
    function is_associative($array){
        return array_keys($array) !== range(0, count($array) - 1);
    }
}

if(! function_exists('array_keys_exists')){
    function array_keys_exists(array $keys, array $array) {
        return count(array_intersect_key(array_flip($keys), $array)) === count($keys);
    }
}

if(! function_exists('array_key_values')){
    function array_key_values(array $keys, array $array, $default = null) {
        $newArray = [];
        foreach ($keys as $key) {
            if(! isset($array[$key]) && ! is_null($default)){
                $array[$key] = $default;
            }
            if(isset($array[$key])){
              $newArray[$key] = $array[$key];
            }
        }
        return $newArray;
    }
}

if(! function_exists('array_merge_unique')){
    function array_merge_unique(array $array1, array $array2) : array
    {
        return array_unique( array_merge( $array1, $array2 ), SORT_REGULAR );
    }
}
