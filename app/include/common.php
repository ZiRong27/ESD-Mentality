<?php

// session related stuff

session_start();

function isMissingOrEmpty($name) {
    if (!isset($_REQUEST[$name])) {
        return "$name cannot be empty";
    }

    // client did send the value over
    $value = $_REQUEST[$name];
    if (empty($value)) {
        return "$name cannot be empty";
    }
}

# check if an int input is an int and non-negative
function isNonNegativeInt($var) {
    if (is_numeric($var) && $var >= 0 && $var == round($var))
        return TRUE;
}

# check if a float input is is numeric and non-negative
function isNonNegativeFloat($var) {
    if (is_numeric($var) && $var >= 0)
        return TRUE;
}

# this is better than empty when use with array, empty($var) returns FALSE even when
# $var has only empty cells
function isEmpty($var) {
    if (isset($var) && is_array($var))
        foreach ($var as $key => $value) {
            if (empty($value)) {
               unset($var[$key]);
            }
        }

    if (empty($var))
        return TRUE;
}