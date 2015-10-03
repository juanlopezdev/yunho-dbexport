<?php

/**
 * PHPUnit Autoloader Classes
 * @version 1.0.0
 * @author José Luis Quintana <quintana.io>
 * @license MIT
 */

date_default_timezone_set('America/Lima');

function auto_loader_classes($class) {
  $file = __DIR__ . "/$class.php";

  if (file_exists($file)) {
    require $file;
  }
}

spl_autoload_register('auto_loader_classes');
