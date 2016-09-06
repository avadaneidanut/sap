<?php

namespace Adanut\Sap\Helpers;

use Adanut\Sap\Facades\Guid;

class Arr
{
    /**
     * UTF8 encode array.
     *
     * @param  array $input
     * 
     * @return array
     */
    public function uft8ize(array $input)
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->utf8ize($value);
            }
        } else if (is_string ($input)) {
            return utf8_encode($input);
        }
        return $input;
    }

    /**
     * Trim all values from an array recursively.
     *
     * @param  array|mixed $array
     * 
     * @return array|mixed
     */
    public function trim($input)
    {
        if (!is_array($input))
            return trim($input);
     
        return array_map([$this, 'trim'], $input);
    }

    /**
     * Convert all the guids to the char32 form.
     * 
     * @param  array|mixed $input
     * @return array|mixed 
     */
    public function byteToChar32($input)
    {
        if (!is_array($input)){
            if (mb_strlen($input) !== strlen($input)) {
                if (strlen($guid = Guid::byteToChar32($input)) === 32) {
                    return $guid;
                }
            }
            return $input;
        }
     
        return array_map([$this, 'byteToChar32'], $input);
    }
}