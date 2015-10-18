<?php

/**
 * PHPUnit / YunhoDBExport Test Class
 * @package YunhoDBExport
 * @version 0.1.0
 * @author José Luis Quintana <quintana.io>
 * @license MIT
 */
class YunhoDBExportTest extends PHPUnit_Framework_TestCase {

  /**
   * Connection test
   * @return YunhoDBExport
   */
  public function testConnection() {
    $host = 'localhost';
    $dbname = 'dbtest';
    $username = 'root';
    $password = '';

    $export = new YunhoDBExport($host, $dbname, $username, $password);
    $export->connect();

    $connected = $export->is_connected();

    $this->assertTrue($connected);

    return $export;
  }

  /**
   * Get DBH test
   * @depends testConnection
   */
  public function testGetDbh($export) {
    $dbh = $export->get_dbh();

    $this->assertNotNull($dbh);
    $this->assertInstanceOf('PDO', $dbh);
  }

  /**
   * Query test
   * @depends testConnection
   */
  public function testQueryOK($export) {
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

    $no_error = $export->get_error();
    $this->assertNull($no_error);
  }

  /**
   * Query Error test
   * @depends testConnection
   */
  public function testQueryError($export) {
    $export->query("
      SELECT
        id,
        model_family,

        color

        COUNT(color) AS 'all_quantity',
        SUM(CASE WHEN state = 1 THEN 1 ELSE 0 END) AS 'current_quantity'

      FROM autos

      WHERE model_family = 'Sedan'
      GROUP BY color
      ORDER BY color
    ");

    $error = $export->get_error();
    $this->assertNotNull($error);
    $this->assertInstanceOf('PDOException', $error);
  }

  /**
   * To Excel test
   * @depends testConnection
   */
  public function testToExcel($export) {
    $export->to_excel();
    $format = $export->get_format();

    $this->assertNotEmpty($format);
    $this->assertEquals($format, 'xls');
  }

  /**
   * To HTML test
   * @depends testConnection
   */
  public function testToHTML($export) {
    $export->to_html();
    $format = $export->get_format();

    $this->assertNotEmpty($format);
    $this->assertEquals($format, 'html');
  }

  /**
   * Build Table test
   * @depends testConnection
   */
  public function testBuildTable($export) {
    $fields = array(
      'id' => 'ID',
      'model_family' => array(
        'label' => 'Modelo de vehículo',
        'mask' => '<a href="https://www.google.com.pe/#safe=off&q=[value]" target="_blank">Ver Modelo</a>'
      ),
      'color' => 'Color',
      'all_quantity' => 'Cantidad Total',
      'current_quantity' => 'Cantidad actual'
    );

    $str = $export->build_table($fields);

    $this->assertNotEmpty($str);

    return $export;
  }

  /**
   * Get Data test
   * @depends testBuildTable
   */
  public function testGetData($export) {
    $data = $export->get_data();

    $this->assertNotEmpty($data);
    $this->assertCount(count($data), $data);
  }
}
