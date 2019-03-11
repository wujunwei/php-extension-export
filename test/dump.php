<?php
/**
 * Created by PhpStorm.
 * User: dell
 * Date: 2019-03-06
 * Time: 15:15
 */

require "../vendor/autoload.php";

try {
    FirstW\Export\Extension::dump('mysqli', './pdo/');
} catch (Exception $e) {
    echo 'fail:'.$e->getMessage();
}