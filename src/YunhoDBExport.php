<?php

/**
 * YunhoDBExport
 * Librería PHP para exportar datos a MS Excel desde MySQL.
 *
 * @version 0.1.4
 * @author Juan López <juanlopez.developer@gmail.com>
 * @link https://github.com/JuanLopezDev/YunhoDBExport
 * @license MIT
 */
class YunhoDBExport {

  var $_dbhost;
  var $_dbname;
  var $_dbuser;
  var $_dbpassword;
  var $_data;
  var $_table;
  var $_dsn;
  var $_dbh;
  var $_dbhex;
  var $_is_connected;
  var $_format;

  /**
   * Constructor de la clase
   * @param string $dbhost      Nombre del host
   * @param string $dbname      Nombre de la base de datos
   * @param string $dbuser      Nombre de usuario
   * @param string $dbpassword  Contraseña de usuario
   */
  function __construct($dbhost = '', $dbname = '', $dbuser = '', $dbpassword = '') {
    $this->_dbhost = $dbhost;
    $this->_dbname = $dbname;
    $this->_dbuser = $dbuser;
    $this->_dbpassword = $dbpassword;
    $this->_dsn = 'mysql:dbname=' . $dbname . ';host=' . $dbhost;
    $this->to_excel();
  }

  /**
   * Conexión a base de datos
   * @return void
   */
  public function connect() {
    try {
      $this->_dbh = new PDO($this->_dsn, $this->_dbuser, $this->_dbpassword, array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
      ));
      $this->_dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->_is_connected = TRUE;
    } catch (PDOException $e) {
      $this->_dbhex = $e;
      $this->_is_connected = FALSE;
    }
  }

  /**
   * Verifica la conexión a la base de datos
   * @return bool
   */
  public function is_connected() {
    return $this->_is_connected;
  }

  /**
   * Retorna el objeto PDO
   * @return PDO
   */
  public function get_dbh() {
    return $this->_dbh;
  }

  /**
   * Retorna un objeto PDOException
   * @return PDOException
   */
  public function get_error() {
    return $this->_dbhex;
  }

  /**
   * Retorna el tipo formato
   * @return string
   */
  public function get_format() {
    return $this->_format;
  }

  /**
   * Retorna los datos de consulta
   * @return array
   */
  public function get_data() {
    return $this->_data;
  }

  /**
   * Asigna un objeto PDO existente
   * @param PDO $dbh      Objecto PDO
   * @return void
   */
  public function set_dbh($dbh) {
    $this->_dbh = ($dbh instanceof PDO) ? $dbh : NULL;
  }

  /**
   * Asigna array de datos
   * @param array $data Array de datos
   * @return void
   */
  public function set_data($data) {
    $this->_data = $data;
  }

  /**
   * Ejecuta consulta SQL
   *
   * @param string $sql Consulta SQL
   * @return array
   */
  public function query($sql) {
    $data = NULL;

    try {
      if ($this->is_connected()) {
        $sth = $this->_dbh->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
      }

      $this->_data = $data;
    } catch (PDOException $e) {
      $this->_dbhex = $e;
    }

    return $data;
  }

  /**
   * Construir tabla
   *
   * @param array $fields Lista mapeada de campos para consulta
   * @param array $data Conjunto de datos de la consulta
   * @return string Tabla HTML
   */
  public function build_table($fields, $data = NULL) {
    $num = 0;
    $data = empty($data) ? $this->_data : $data;

    if (empty($data)) {
      return '';
    }

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
     * Cabecera de la tabla
     */
    $table .= '<table cellpadding="0" cellspacing="0" border="1">';
    $table .= '<tr style="background-color:#777777;color:#fff">';
    $table .= '<td> Nro. </td>';

    foreach ($fields as $field) {
      if (is_array($field)) {
        // Etiqueta (label)
        if (array_key_exists('label', $field)) {
          $label = $field['label'];
          $table .= '<td>' . $label . '</td>';
        }
      } else {
        // Por defecto
        $table .= '<td> ' . $field . '</td>';
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

        // Filas con validaciones
        foreach ($fields as $key_field => $field) {
          if (is_array($field)) {

            // Artificio para la etiqueta (label)
            if (count($field) == 1) {
              if (array_key_exists('label', $field)) {
                $value = $row[$key_field];
                $table .= '<td>' . $value . '</td>';
              }
            }

            // Lista de valores (switch)
            if (array_key_exists('switch', $field)) {
              $switch = $field['switch'];
              $value = $row[$key_field];

              // Valores vacíos (empty)
              if (is_array($switch) && array_key_exists('empty', $switch)) {
                if (empty($value)) {
                  $value = $switch['empty'];
                  $table .= ' <td>' . $value . '</td>';
                }
              }

              // Valores y claves asociadas (diferentes de null o empty)
              if (is_array($switch) && array_key_exists($value, $switch)) {
                $table .= ' <td>' . $switch[$value] . '</td>';
              }
            }

            // Máscara (mask)
            if (array_key_exists('mask', $field)) {
              $mask = $field['mask'];
              $value = $row[$key_field];

              if (!empty($value)) {
                $value = str_replace('[value]', $value, $mask);
                $table .= ' <td>' . $value . '</td>';
              }
            }

            // Formato de fecha (dateformat)
            if (array_key_exists('dateformat', $field)) {
              $dateformat = $field['dateformat'];
              $value = $row[$key_field];
              $value = date($dateformat, strtotime($value));
              $table .= ' <td>' . $value . '</td>';
            }
          } else {
            // Por defecto
            $table .= ' <td>&nbsp;' . $row[$key_field] . '</td>';
          }
        }

        $table .= '</tr>';
      }

      $table .= '</table>';

      if ($this->get_format() == 'xls') {
        $table = mb_convert_encoding($table, 'UTF-16LE', 'UTF-8');
        $table = "\xFF\xFE" . $table;
      }

      $this->_table = $table;
    }

    return $table;
  }

  /**
   * Descarga los datos
   * @param string $filename Nombre del archivo sin la extensión.
   * @return void
   */
  public function download($filename = 'filename') {
    header($this->_header);
    header('Content-Disposition: attachment;filename=' . "$filename.$this->_format");
    echo $this->_table;
  }

  /**
   * Despliega los datos en el navegador
   * @return void
   */
  public function output() {
    header($this->_header);
    echo $this->_table;
  }

  /**
   * Preparar salida MS Excel (.xls)
   * @return void
   */
  public function to_excel() {
    $this->_format = 'xls';
    $this->_header = 'Content-type: application/vnd.ms-excel';
  }

  /**
   * Preparar salida HTML
   * @return void
   */
  public function to_html() {
    $this->_format = 'html';
    $this->_header = 'Content-type: text/html; charset=utf-8';
  }

}
