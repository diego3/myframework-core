<?php

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-06-16 at 08:34:43.
 */
class FactoryTest extends PHPUnit_Framework_TestCase {

    public function testResponse() {
        $this->assertNotNull(Factory::response('Html'));
        $this->assertEquals(Factory::response('Html'), Factory::response('html'));
        $this->assertNotEquals(Factory::response('Empty'), Factory::response('html'));
        $this->assertNotNull(Factory::response('json'));
        $this->assertNotEquals(Factory::response('Empty'), Factory::response('Json'));
        $this->assertNotNull(Factory::response('auxua'));
        $this->assertEquals(Factory::response('Empty'), Factory::response('auxua'));
    }

    public function testPage() {
        $this->assertNotNull(Factory::page('Main'));
        $this->assertEquals(Factory::page('Main'), Factory::page('main'));
        $this->assertNotEquals(Factory::page('ErrorPage'), Factory::page('main'));
        $this->assertNotNull(Factory::page('Painel'));
        $this->assertNotEquals(Factory::page('ErrorPage'), Factory::page('Painel'));
        $this->assertNotNull(Factory::page('auxua'));
        $this->assertEquals(Factory::page('ErrorPage'), Factory::page('auxua'));
    }

    public function testDatabase() {
        LoggerApp::clear();
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $defaultDb = Factory::database();
        $this->assertFalse(LoggerApp::hasError(), LoggerApp::getLastError());
        $types = array('pgsql' => 'PgDataBase', 'mysql' => 'MyDataBase');
        $this->assertTrue(is_a($defaultDb, $types[DATABASE_DRIVER]), 'Invalid class');
        $this->assertEquals($defaultDb, Factory::database());
        $this->assertSame($defaultDb, Factory::database());
        $this->assertNull(Factory::database(array('dbname' => 'testxyz')));
        $this->assertTrue(LoggerApp::hasError(), 'Conexão inválida não gerou um erro');
        $this->assertNull(Factory::database(array('driver' => 'testxyz')));
        $this->assertNull(Factory::database(array('driver' => 'testxyz', 'dbname' => 'test')));
        $this->assertNull(Factory::database(array('driver' => 'testxyz', 'dbname' => 'test', 'user' => 'test')));
        
        foreach ($types as $driver => $database) {
            $db = Factory::database(array('driver' => $driver, 'dbname' => 'xyz', 'user' => 'xyz'));
            $this->assertNull($db);
        }
    }
    
    public function testLog() {
        $log = Factory::log();
        $this->assertEquals(Logger::getLogger('main'), $log);
        $this->assertEquals(Factory::log(), $log);
        Memory::set('debug', true);
        $this->assertEquals(Logger::getLogger('debug'), Factory::log());
        Memory::clear('debug');
        $this->assertEquals(Logger::getLogger('main'), $log);
    }
    
    public function testDAO() {
        $grupoDAO = Factory::DAO('grupo');
        $this->assertNotNull($grupoDAO);
        $this->assertEquals($grupoDAO, Factory::DAO('GrupoDAO'));
        $this->assertSame($grupoDAO, Factory::DAO('grupo'));
        
        $this->assertNull(Factory::DAO('invalid'));
        $this->assertNull(Factory::DAO('DAO'));
    }
    
    public function testDatatype() {
        $int = Factory::datatype('int');
        $this->assertNotNull($int);
        $this->assertSame($int, Factory::datatype('int'));
        $this->assertSame($int, Factory::datatype('Int'));
        $this->assertSame($int, Factory::datatype('DatatypeInt'));
               
        //TODO listar os tipos dinamicamente
        $datatypes = array('bool', 'boolean', 'email', 'HTML', 'int', 'integer', 'numeric', 'string', 'stringBase', 'text');
        foreach ($datatypes as $type) {
            $type = str_replace('.php', '', $type);
            if ($type == 'Datatype') {
                continue;
            }
            $this->assertNotNull(Factory::datatype($type), "Falha para o tipo: " . $type);
        }
        
        $this->assertNull(Factory::datatype('invalid'));
        $this->assertNull(Factory::datatype('Datatype'));
    }
}
