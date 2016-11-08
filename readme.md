# YunhoDBExport [![Build Status](https://travis-ci.org/juanlopezdev/YunhoDBExport.svg)](https://travis-ci.org/juanlopezdev/YunhoDBExport)

> Librería para exportar a MS Excel desde MySQL

## Uso

```php
<?php

// Requerir librería
require '../src/YunhoDBExport.php';

// Configuración de base de datos
$host = 'localhost';
$name = 'dbtest';
$user = 'usr';
$password = 'pwd';

// Asignar zona horaria por defecto
date_default_timezone_set('America/Lima');

// Inicializar librería
$export = new YunhoDBExport($host, $name, $user, $password);

// Conectarse a la base de datos MySQL
$export->connect();

// Mapeo de campos para cabecera
$campos = array(
  'id' => 'ID',
  'model_family' => array(
    'label' => 'Modelo de vehículo',
    'mask' => '<a href="https://www.google.com.pe/#safe=off&q=[value]" target="_blank">Ver Modelo</a>'
  ),
  'color' => 'Color',
  'all_quantity' => 'Cantidad Total',
  'current_quantity' => 'Cantidad actual'
);

// Consulta SQL
$export->query("
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

// Formato MS Excel
$export->to_excel();

// Construir tabla de datos
$export->build_table($campos);

// Descargar
$export->download();

// Control de errores
if ($dbhex = $export->get_error()) {
  die($dbhex->getMessage());
}
```

## Formato de campos

### label

````php
<?php

$campos = array(
  'fecha_registro' => array(
    'label' => 'Registrado'
  )
)
````

### mask

````php
<?php

$campos = array(
  'facebook_id' => array(
    'mask' => '<a href="https://facebook.com/[value]" target="_blank">Ver perfil</a>',
  )
)
````

### switch
````php
<?php

$campos = array(
  'sexo' => array(
    'label' => 'Sexo',
    'switch' => array(
      '0' => 'Femenino',
      '1' => 'Masculino'
    )
  )
)
````

### dateformat
````php
<?php

$campos = array(
  'fecha_registro' => array(
    'label' => 'Registrado',
    'dateformat' => 'd/m/Y H:i:s',
  )
)
````

### CodeIgniter 3
Ejemplo de uso en CodeIgniter 3

En controller:

```php

<?php
// Cargar la librería
$this->load->library('YunhoDBExport');

// Array de datos
$data = $this->user_model->find();

$fields = array(
  'firstname' => 'Nombres',
  'lastname' => 'Apellidos',
  'email' => 'Email',
  'dni' => 'DNI',
  'phone' => 'Teléfono',
  'sex' => 'Sexo',
  'address' => 'Dirección',
  'phone' => 'Phone',
  'province' => 'Provincia',
  'city' => 'Ciudad',
  'district' => 'Distrito',
  'registered' => 'Registrado'
);

$export = new YunhoDBExport();
$export->set_data($data);
$export->to_excel();
$export->build_table($fields);
$export->download('reporte');
```

## Licencia
MIT
