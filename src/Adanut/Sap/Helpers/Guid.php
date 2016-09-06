<?php

namespace Adanut\Sap\Helpers;

class Guid
{
    /**
     * Convert guid from bytes to char32.
     * 
     * @param  string $bytes
     * @return string
     */
    public function byteToChar32($bytes)
    {
        return strtoupper(unpack('H*', $bytes)[1]);
    }

    /**
     * Convert from char32 to byte.
     * 
     * @param  string $string
     * @return string
     */
    public function char32ToByte($string)
    {
        return pack('H*', $string);
    }
}