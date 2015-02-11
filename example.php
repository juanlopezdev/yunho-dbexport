<?php

// Importar libreria
include 'library/YunhoDBExport.php';

// Configuración
$dbhost = 'localhost';
$dbname = 'db_rutinalove';
$dbuser = 'root';
$dbuserpass = '';

// Iniciar libreria
$objExport = new YunhoDBExport($dbhost, $dbname, $dbuser, $dbuserpass);

// Conectarse a la base de datos
$objExport->connectDB();

// Campos
$fields = array(
    'user_fbid' => array(
        'label' => 'Facebook',
        'mask' => '<a href="https://www.facebook.com/[value]" target="_blank">Perfil de facebook</a>'
    ),
    'user_dni' => 'Dni',
    'user_name' => 'Nombres y Apellidos',
    'user_email' => 'Email',
    'user_recorddate' => 'Fecha de registro'
);

// Consulta
$data = $objExport->runQuery('SELECT * FROM t_user');

// Construir tabla
$table = $objExport->buildTable($fields, $data);

//Exportar a Excel
$objExport->outputExcel('econopticas', $table);

die();

