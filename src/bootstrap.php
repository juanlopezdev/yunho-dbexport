<?php

/**
 * PHPUnit Autoloader Classes
 * @version 0.1.0
 * @author José Luis Quintana <quintana.io>
 * @license MIT
 */

date_default_timezone_set('America/Lima');

function loader($class) {
  $file = "src/$class.php";

  if (file_exists($file)) {
    require $file;
  }
}

spl_autoload_register('loader');
