<?php

// Importar libreria
include '../src/YunhoDBExport.php';

// Configuración de base de datos
$dbhost = 'localhost';
$dbname = 'dbtest';
$dbuser = 'root';
$dbuserpass = 'jose25';

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
        'label' => 'Modelo de vehículo',
        'mask' => '<a href="https://www.google.com.pe/#safe=off&q=[value]" target="_blank">Ver Modelo</a>'
    ),
    'color' => 'Color',
    'registration_date' => array (
        'label' => 'Fecha de Registro',
        'dateformat' => 'd/m/Y H:i:s'
    )
);

// Consulta SQL
$data = $objExport->query('SELECT * FROM auto');

// Construir tabla
$table = $objExport->build_table($fields, $data);

// Exportar a Excel
$objExport->to_excel('econopticas', $table);

// Descargar archivo .xls
$objExport->to_excel('econopticas', $table);

die();
