<?php
namespace MyFrameWork\DataBase;

use MyFrameWork\Factory;
use MyFrameWork\LoggerApp;

//@todo 
//Fatal error: Class 'LoggerAppenderPhp' not found in D:\site\www\myframework\src\LoggerApp.php on line 7

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-06-09 at 21:43:21.
 */
class DataBaseTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Database
     */
    protected $db;

    protected function setUp() {
        LoggerApp::clear();
        $this->db = Factory::database();
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertNotNull($this->db, 'Invalid connection');
        
        $sql = <<<'SQL'
   CREATE TABLE IF NOT EXISTS test (
     id    serial PRIMARY KEY,
     name  varchar(40) NOT NULL CHECK (name <> '')
   )
SQL;
        try {
            $this->db->exec($sql);
            $this->db->exec('DELETE FROM test');
            $this->assertTrue($this->db->exec("INSERT INTO test (name) VALUES ('value 1')")>0);
            $this->assertTrue($this->db->exec("INSERT INTO test (name) VALUES ('value 2')")>0);
            $this->assertTrue($this->db->exec("INSERT INTO test (name) VALUES ('value 3')")>0);
        }
        catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    protected function tearDown() {
        try {
            $this->db->exec('DROP TABLE IF EXISTS test');
        }
        catch (Exception $e) {
            $this->fail($e->getMessage());
        }
    }

    /**
     * @covers Database::execute
     * @covers Database::rowCount
     */
    public function testExecute() {
        $this->assertEquals(
            1,
            $this->db->execute('INSERT INTO test (name) VALUES (?)', 'Value 4'),
            LoggerApp::getLastError()
        );
        
        $this->assertEquals(
            1,
            $this->db->execute("INSERT INTO test (name) VALUES (?)", array('teste')),
            LoggerApp::getLastError()    
        );
        $this->assertEquals(1, $this->db->rowCount(), 'Erro no numero de linhas inseridas');
        
        $this->assertEquals(
            5,
            $this->db->execute("UPDATE test SET name = name || ?", '_teste'),
            LoggerApp::getLastError()
        );
        $this->assertEquals(5, $this->db->rowCount(), 'Erro no numero de linhas alteradas');
        
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertEquals(
            0,
            $this->db->execute("DELETE INVALIDTest SET name = ?", 'teste 2'),
            'Comando inválido'
        );
        $this->assertTrue(LoggerApp::hasError(), 'Erro inválido');
        $this->assertEquals(0, $this->db->rowCount(), 'Nenhuma linha afetada');
    }
    
    /**
     * @covers DataBase::fetchAll
     */
    public function testFetchAll(){
        $result = $this->db->fetchAll("SELECT * FROM test");
        $this->assertInternalType('array', $result, LoggerApp::getLastError());
        $this->assertEquals(3, count($result));
        $this->assertEquals(3, $this->db->rowCount());
        for ($i = 1; $i <= 3; $i++) {
            $this->assertEquals('value ' . $i, $result[$i-1]['name']);
        }
        
        $r1 = $this->db->fetchAll("SELECT * FROM test WHERE id > ? ORDER BY id", 1);
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertEquals(2, count($r1));
        $this->assertEquals(2, $this->db->rowCount());
        for ($i = 2; $i <= 3; $i++) {
            $this->assertEquals('value ' . $i, $r1[$i-2]['name']);
        }
        
        $r2 = $this->db->fetchAll("SELECT * FROM test WHERE id < ?", array(0));
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertEquals(0, $this->db->rowCount());
        $this->assertEmpty($r2, 'Retorno deveria ser vazio');
    }
    
    /**
     * @covers DataBase::fetch
     */
    public function testFetch(){
        $result = $this->db->fetch("SELECT * FROM test ORDER BY id");
        $this->assertInternalType('array', $result);
        $this->assertEquals(3, $this->db->rowCount());
        $this->assertEquals(2, count($result));
        $this->assertEquals(array('id', 'name'), array_keys($result));
        $this->assertEquals(1, $result['id']);
        $this->assertEquals('value 1', $result['name']);
        $r1 = $this->db->fetch();
        $this->assertEquals(array('id', 'name'), array_keys($r1));
        $this->assertEquals(2, $r1['id']);
        $this->assertEquals('value 2', $r1['name']);
        $r3 = $this->db->fetch(); // 3 linha
        $this->assertEquals(3, $r3['id']);
        $this->assertEquals('value 3', $r3['name']);
        $this->assertEmpty($this->db->fetch());
   
        $r2 = $this->db->fetch("SELECT * FROM testecase WHERE id > ?", 10);
        $this->assertInternalType('array', $r2);
        $this->assertEmpty($r2);
    }
    
    /**
     * @covers DataBase::fetchField
     */
    public function testFetchField(){
        $value = $this->db->fetchField("SELECT name FROM test WHERE id = ?", array(1));
        $this->assertEquals('value 1', $value);
        
        $v1 = $this->db->fetchField("SELECT id, name FROM test ORDER BY id");
        $this->assertEquals(1, $v1);
        for ($i=2; $i <= 3; $i++) {
            $this->assertEquals($i, $this->db->fetchField());
        }
        $this->assertEmpty($this->db->fetchField());
        $this->assertEmpty($this->db->fetchField("SELECT id, name FROM test WHERE id = -1"), 'Resulado vazio');
    }
    
    /**
     * @covers Database::insert
     * @covers Database::selectOne
     */
    public function testInsert() {
        $this->assertEquals(1, $this->db->insert('test', array('name' => 'Other test')));
        $this->assertEquals(4, $this->db->lastInsertId('test', 'id'));
        $r1 = $this->db->selectOne('*', 'test', array('id' => 4));
        $this->assertEquals('Other test', $r1['name']);
        $this->assertEquals(array('id', 'name'), array_keys($r1));
        
        //Errors test
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertEquals(0, $this->db->insert('test', array('name' => 'Other test', 'id' => 1)));
        $this->assertTrue(LoggerApp::hasError(), 'Não gerou o erro esperado');
        
        LoggerApp::clear();
        $this->assertEquals(0, $this->db->insert('testinvalid', array('name' => 'Other test', 'xyz' => 1)));
        $this->assertTrue(LoggerApp::hasError(), 'Não gerou o erro esperado');
    }
    
    /**
     * @covers DataBase::update
     * @covers Database::select
     * @covers Database::selectOne
     */
    public function testUpdate() {
        $r0 = $this->db->selectOne('name', 'test', array('id' => 1));
        $this->assertEquals('value 1', $r0['name']);
        $this->assertEquals(1, $this->db->update('test', array('name' => 'Other test'), array('id' => 1)));
        $r1 = $this->db->selectOne('name', 'test', array('id' => 1));
        $this->assertEquals('Other test', $r1['name']);
        
        $this->assertEquals(3, $this->db->update('test', array('name' => 'Other test'), Where::one('id', '>', 0)));
        $result = $this->db->select('*', 'test');
        foreach ($result as $id => $row) {
            $this->assertEquals('Other test', $row['name']);
        }
        
        $this->assertEquals(0, $this->db->update('test', array('name' => 'Other test'), array('id' => 0)));
        
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertEquals(0, $this->db->update('testinvalid', array('xyz' => 1), array('id' => 1)));
        $this->assertTrue(LoggerApp::hasError(), 'Não gerou o erro esperado');
        
        //Update without where
        $this->assertEquals(0, $this->db->update('test', array('name' => 'a'), array()));
    }
    
    /**
     * @covers DataBase::delete
     * @covers Database::select
     * @covers Database::selectOne
     */
    public function testDelete() {
        $this->db->select('name', 'test');
        $this->assertEquals(3, $this->db->rowCount());
        $this->assertEquals(1, $this->db->delete('test', array('id' => 1)));
        $this->assertEmpty($this->db->selectOne('name', 'test', array('id' => 1)));
        
        $this->db->select('name', 'test');
        $this->assertEquals(2, $this->db->rowCount());
        $result = $this->db->select('*', 'test');
        
        $this->assertEquals(0, $this->db->delete('test', array('id' => 0)));
        
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $this->assertEquals(0, $this->db->delete('testinvalid', array('xyz' => 1)));
        $this->assertTrue(LoggerApp::hasError(), 'Não gerou o erro esperado');
        
        //Update without where
        $this->assertEquals(0, $this->db->delete('test', array()));
    }
}
