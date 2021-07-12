<?php

if(!function_exists("isMengalamiGejala")){
    function isMengalamiGejala(int $int) : bool{
        return $int > 0;
    }
}

if (!function_exists("convertKeparahan")) {
    function convertKeparahan(int $int)
    {
        if ($int == 1) {
            return 'Akut';
        } else if ($int == 2) {
            return 'Kronis';
        } else if ($int == 3) {
            return 'Periodik';
        }
        return '-';
    }
}