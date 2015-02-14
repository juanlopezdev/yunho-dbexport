# YunhoDBExport

> Librería para exportar a MS Excel desde MySQL

## Uso

```php
<?php

// Requerir librería
require '../src/YunhoDBExport.php';

// Configuración de base de datos
$dbhost = 'localhost';
$dbname = 'dbtest';
$dbuser = 'usr';
$dbuserpass = 'pwd';

// Asignar zona horaria por defecto
date_default_timezone_set('America/Lima');

// Inicializar librería
$objExport = new YunhoDBExport($dbhost, $dbname, $dbuser, $dbuserpass);

// Conectarse a la base de datos MySQL
$objExport->connect();

// Mapeo de campos para cabecera
$fields = array(
  'id' => 'ID',
  'model_family' => array(
    'label' => 'Modelo de veículo',
    'mask' => '<a href="https://www.google.com.pe/#safe=off&q=[value]" target="_blank">Ver Modelo</a>'
  ),
  'color' => 'Color',
  'all_quantity' => 'Cantidad Total',
  'current_quantity' => 'Cantidad actual'
);

// Consulta SQL
$objExport->query("
  SELECT 
    id, 
    model_family, 
    color, 
    COUNT(color) AS 'all_quantity',
    SUM(CASE WHEN state = 1 THEN 1 ELSE 0 END) AS 'current_quantity'
  FROM auto
  WHERE model_family = 'Sedan'
  GROUP BY color
  ORDER BY color
");

// Construir tabla de datos
$objExport->buildTable($fields);

// Exportar a Excel
$objExport->exportToExcel();

```

## Licencia
MIT
