<?php
/**
 * Syga CI Api
 *
 */

if(! function_exists('starts_with')){
    function starts_with($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}

if (! function_exists('dasherize_spaces'))
{
	/**
	 * Replaces spaces with dashes in the string.
	 *
	 * @param  string $string Input string
	 * @return string
	 */
	function dasherize_spaces(string $string): string
	{
		return str_replace(' ', '-', $string);
	}
}