<?php

function autoloader($class) {
    if (preg_match('/^QifTransformer/', $class) === false) {
        return;
    }
    include_once __DIR__ . DIRECTORY_SEPARATOR . $class . '.php';
}

spl_autoload_register('autoloader');