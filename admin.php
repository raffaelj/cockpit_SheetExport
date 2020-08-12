<?php

// Autoload from lib folder (PSR-0)
spl_autoload_register(function($class){
    $class_path = __DIR__.'/lib/'.str_replace('\\', '/', $class).'.php';
    if(file_exists($class_path)) include_once($class_path);
});

include(__DIR__.'/lib/vendor/autoload.php');

// $this->bindClass('SheetExport\\Controller\\Export', 'sheetexport');

// override core collections/export route
$this->bind('/collections/export/:collection', function($param) {
    return $this->invoke('SheetExport\\Controller\\Export', 'export', $param);
});

// quick helper to determine, if fields filters are active and to check, which columns exist/should be empty etc.
$this->module('collections')->extend([

    'is_filtered_out' => function($field_name, $fields = null, $primary_key = '') {

        // select all
        if (!$fields)
            return false;

        // one filter is set to true - don't select any other fields
        if (in_array(true, $fields)) {

            if (isset($fields[$field_name]) && $fields[$field_name] == true)
                return false;

            // return primary_key, too if not explicitly set to false
            if ($field_name == $primary_key && ( !isset($fields[$primary_key]) || $fields[$primary_key] == true))
                return false;

            return true;

        }

        else {

            if (!isset($fields[$field_name]))
                return false;

            if (isset($fields[$field_name]) && $fields[$field_name] == false)
                return true;

        }

    }, // end of is_filtered_out()

]);
