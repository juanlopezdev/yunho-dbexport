<?php

/**
 * YunhoDBExport
 * Librería para exportar a MS Excel desde MySQL
 * 
 * @version 1.0.0
 * @author Juan López <juanlopez.developer@gmail.com>
 * @link https://github.com/JuanLopezDev/YunhoDBExport
 */
class YunhoDBExport {

  var $dbhost;
  var $dbname;
  var $dbuser;
  var $dbuserpass;
  var $data;
  var $table;
  var $dsn;
  var $cnn;

  function __construct($dbhost, $dbname, $dbuser, $dbuserpass) {
    $this->dbhost = $dbhost;
    $this->dbname = $dbname;
    $this->dbuser = $dbuser;
    $this->dbuserpass = $dbuserpass;
    $this->dsn = 'mysql:dbname=' . $dbname . ';host=' . $dbhost;
  }

  /**
   * Conección a base de datos
   * @return void
   */
  public function connect() {
    try {
      $this->cnn = new PDO($this->dsn, $this->dbuser, $this->dbuserpass);
    } catch (PDOException $e) {
      die('Ha courrido un error al conectarse a la base de datos: ' . $e->getMessage());
    }
  }

  /**
   * Ejecutar consulta
   * 
   * @param string $sql Consulta SQL
   * @return array
   */
  public function query($sql) {
    $sth = $this->cnn->prepare($sql);
    $sth->execute();
    $data = $sth->fetchAll(PDO::FETCH_ASSOC);
    $this->data = $data;
    return $data;
  }

  /**
   * Construir tabla
   * 
   * @param array $fields Lista mapeada de camposde consulta
   * @param array $data Conjunto de datos de la consulta
   * @return string HTML Tabla
   */
  public function buildTable($fields, $data = NULL) {
    $num = 0;
    $data = empty($data) ? $this->data : $data;

    $table = '<table>';
    $table .= '<tr>';
    $table .= '<td colspan="5" valign="middle">';
    $table .= '<h1 style="font-size:18px;">';
    $table .= ' REPORTE GENERADO EL ' . date('d-M-Y');
    $table .= '</h1>';
    $table .= '</td>';
    $table .= '</tr>';
    $table .= '</table>';

    /*
     * Cabecera tabla
     */
    $table .= '<table cellpadding="0" cellspacing="0" border="1">';
    $table .= '<tr style="background-color:#666666;color:#fff">';
    $table .= '<td> Nro. </td>';

    foreach ($fields as $field) {
      if (is_array($field)) {
        // Etiqueta (label)
        if (array_key_exists('label', $field)) {
          $label = $field['label'];
          $table .= '<td>' . utf8_decode($label) . '</td>';
        }
      } else {
        // Por defecto
        $table .= '<td> ' . utf8_decode($field) . '</td>';
      }
    }

    $table .= ' </tr>';

    if (!empty($data)) {
      foreach ($data as $row) {
        // Color entre filas
        if ($num % 2) {
          $bgcolor = '#d3d3d3';
        } else {
          $bgcolor = '#e7e7e7';
        }

        $num++;

        // Fila
        $table .= '<tr style="background-color:' . $bgcolor . ';">';
        $table .= ' <td>' . $num . '</td>';

        // Columnas dinámicas
        foreach ($fields as $key_field => $field) {
          if (is_array($field)) {
            // Mascara
            if (array_key_exists('mask', $field)) {
              $mask = $field['mask'];
              $value = str_replace('[value]', utf8_decode($row[$key_field]), $mask);
              $table .= ' <td>' . $value . '</td>';
            }
          } else {
            // Por defecto
            $table .= ' <td>' . $row[$key_field] . '</td>';
          }
        }

        $table .= '</tr>';
      }

      $table .= '</table>';

      $this->table = $table;
    }

    return $table;
  }

  /**
   * Exportar a archivo MS Excel
   * @param string $table Objeto Build Table
   * @return void
   */
  public function exportToExcel($name = '', $table = NULL) {
    header('Content-type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename=' . $name . '-key' . md5(date('g:i:s')) . '.xls');

    echo empty($table) ? $this->table : $table;
    echo '<script type="text/javascript">window.close();</script>';
  }

  /**
   * Salida HMTL
   * @param string $table Table HMTL
   * @return void
   */
  public function outputHTML($table = NULL) {
    header('Content-type: text/html; charset=utf-8');
    $table = empty($table) ? $this->table : $table;
    echo $table;
  }

}
