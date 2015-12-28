<?php

if (function_exists('escapeshellarg') === false) {
    function escapeshellarg($input)
    {
        $input = str_replace('\'', '\\\'', $input);

        return '\''.$input.'\'';
    }
}
