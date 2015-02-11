<?php

/**
 * YunhoDBExport
 * Libreria para exportar a Excel
 * 
 * @author Juan López <juanlopez.developer@gmail.com>
 * @link URL Repositorio
 */
class YunhoDBExport {

    var $dbhost;
    var $dbname;
    var $dbuser;
    var $dbuserpass;
    var $dsn;
    var $cn;

    function __construct($dbhost, $dbname, $dbuser, $dbuserpass) {

        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbuserpass = $dbuserpass;
        $this->dsn = 'mysql:dbname=' . $dbname . ';host=' . $dbhost;
    }

    /**
     * Conección a base de datos
     */
    public function connectDB() {
        try {
            $this->cn = new PDO($this->dsn, $this->dbuser, $this->dbuserpass);
        } catch (PDOException $e) {
            die('There was a problem connecting to the database: ' . $e->getMessage());
        }
    }

    /**
     * Ejecutar consulta
     * 
     * @param string $sql
     * @return array
     */
    public function runQuery($sql) {
        $sth = $this->cn->prepare($sql);
        $sth->execute();
        $data = $sth->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    }

    /**
     * Construir tabla
     * 
     * @param array $fields
     * @param array $data
     * @return string HTML Tabla
     */
    public function buildTable($fields, $data) {

        $num = 0;

        $table = '<table>';
        $table .= '<tr>';
        $table .= '<td colspan="5" valign="middle">';
        $table .= '<h1 style="font-size:18px;">';
        $table .= ' REPORTE GENERADO EL ' . date("d-M-Y");
        $table .= '</h1>';
        $table .= '</td>';
        $table .= '</tr>';
        $table .= '</table>';

        /*
         * Cabecera tabla
         */
        $table .= '<table cellpadding="0" cellspacing="0" border="1">';
        $table .= ' <tr style="background-color:#666666;color:#fff">';
        $table .= '    <td> Nro. </td>';
        foreach ($fields as $key => $field) {
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

        /*
         * Cuerpo tabla
         */
        foreach ($data as $row) {
            // Color entre filas
            if ($num % 2) {
                $bgcolor = "#d3d3d3";
            } else {
                $bgcolor = "#e7e7e7";
            }

            $num++;

            // Fila
            $table .= '<tr style="background-color:' . $bgcolor . ';">';
            $table .= ' <td>' . $num . '</td>';

            // Columnas dinamicas
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
                    $table .= ' <td>' . utf8_decode($row[$key_field]) . '</td>';
                }
            }

            $table .= '</tr>';
        }

        $table .= '</table>';

        return $table;
    }

    /**
     * Salida a Excel
     * @param string $table Objeto Build Table
     */
    public function outputExcel($name = '', $table = '') {

        header("Content-type: application/vnd.ms-excel");
        header("Content-Disposition: attachment;filename=" . $name . "-key" . md5(date('g:i:s')) . ".xls");
        echo $table;
        echo '  <script type="text/javascript">
                    window.close();
                </script>';
    }

}
