<?php
namespace MyFrameWork\DataBase;

use MyFrameWork\DataBase\Query;

class TheQuery extends Query {
    
}

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-06-14 at 12:15:33.
 */
class QueryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Query
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new TheQuery;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        
    }

    public function testInsert() {
        $r1 = $this->object->insert('teste', array('cp1' => 1, 'cp2' => 2));
        $this->assertTrue($r1['status'], $r1['message']);
        $this->assertEquals('INSERT INTO teste (cp1,cp2) VALUES (?,?)', $r1['sql']);
        $this->assertEquals(array(1, 2), $r1['values']);

        //Error test
        $r2 = $this->object->insert('', array('a' => 2));
        $this->assertFalse($r2['status'], 'Invalid table name');
        $r3 = $this->object->insert('     ', array('a' => 2));
        $this->assertFalse($r3['status'], 'Empty table name');

        $r4 = $this->object->insert('teste', array());
        $this->assertFalse($r4['status'], 'Empty params');
        $r5 = $this->object->insert('teste', array(1, 2, 3));
        $this->assertFalse($r5['status'], 'Invalid params');
    }

    public function testUpdate() {
        $r1 = $this->object->update('teste', array('cp1' => 1, 'cp2' => 2), array('id' => 99));
        $this->assertTrue($r1['status'], $r1['message']);
        $this->assertEquals('UPDATE teste SET cp1=?,cp2=? WHERE id = ?', $r1['sql']);
        $this->assertEquals(array(1, 2, 99), $r1['values']);

        //Partial where error
        $r0 = $this->object->update('teste', array('cp1' => 1), array('a'=>'b', 2));
        $this->assertTrue($r0['status'], $r0['message']);
        $this->assertEquals('UPDATE teste SET cp1=? WHERE a = ?', $r0['sql']);
        $this->assertEquals(array(1, 'b'), $r0['values']);
        
        //Error test
        $r2 = $this->object->update('teste', array('cp1' => 1, 'cp2' => 2), array());
        $this->assertFalse($r2['status'], 'Update without conditions');
        
        $r = $this->object->update('', array('a' => 2), array());
        $this->assertFalse($r['status'], 'Invalid table name');
        $r3 = $this->object->update('     ', array('a' => 2), array());
        $this->assertFalse($r3['status'], 'Empty table name');

        $r4 = $this->object->update('teste', array(), array());
        $this->assertFalse($r4['status'], 'Empty params');
        $r5 = $this->object->update('teste', array(1, 2, 3), array());
        $this->assertFalse($r5['status'], 'Invalid params');
        
        $r6 = $this->object->update('teste', array('a' => 2), 1);
        $this->assertFalse($r6['status'], 'Invalid where');
    }

    public function testDelete() {
        $r1 = $this->object->delete('teste', array('cp1' => 1, 'cp2' => 2));
        $this->assertTrue($r1['status'], $r1['message']);
        $this->assertEquals('DELETE FROM teste WHERE cp1 = ? AND cp2 = ?', $r1['sql']);
        $this->assertEquals(array(1, 2), $r1['values']);

        //Partial where error
        $r0 = $this->object->delete('teste', array('a'=>'b', 2));
        $this->assertTrue($r0['status'], $r0['message']);
        $this->assertEquals('DELETE FROM teste WHERE a = ?', $r0['sql']);
        $this->assertEquals(array('b'), $r0['values']);
        
        //Error test
        $r2 = $this->object->delete('teste', array());
        $this->assertFalse($r2['status'], 'DELETE without conditions');
        
        $r = $this->object->delete('', array('a' => 2));
        $this->assertFalse($r['status'], 'Invalid table name');
        $r3 = $this->object->delete('     ', array('a' => 2));
        $this->assertFalse($r3['status'], 'Empty table name');
        
        $r5 = $this->object->delete('teste', array(1, 2, 3));
        $this->assertFalse($r5['status'], 'Invalid result empty conditions');
        $r6 = $this->object->delete('teste', 1);
        $this->assertFalse($r6['status'], 'Invalid where');
    }
  
    public function testSelect() {        
        $r1 = $this->object->select('*', 'teste', Where::one('a', '>', 0));
        $this->assertTrue($r1['status'], $r1['message']);
        $this->assertEquals('SELECT * FROM teste WHERE a > ?', trim($r1['sql']));
        $this->assertEquals(array(0), $r1['values']);
        
        $r2 = $this->object->select(array('a', 'b'), 'teste', array('id' => 2), array('limit' => 10, 'orderBy' => 'id'));
        $this->assertTrue($r2['status'], $r2['message']);
        $this->assertEquals('SELECT a,b FROM teste WHERE id = ? ORDER BY id ASC LIMIT ?', trim($r2['sql']));
        $this->assertEquals(array(2, 10), $r2['values']);
        
        $r3 = $this->object->select(
            '*', 'teste', array('id' => 2),
            array(
                'groupBy' => array('id', 'teste'),
                'having' => array('teste' => 1)
            )
        );
        $this->assertTrue($r3['status'], $r3['message']);
        $this->assertEquals('SELECT * FROM teste WHERE id = ? GROUP BY id,teste HAVING teste = ?', trim($r3['sql']));
        $this->assertEquals(array(2, 1), $r3['values']);
        
        $r4 = $this->object->select(array('a', 'b'), 'teste', null, array('limit' => 10, 'offset' => 11));
        $this->assertTrue($r4['status'], $r4['message']);
        $this->assertEquals('SELECT a,b FROM teste LIMIT ? OFFSET ?', trim($r4['sql']));
        $this->assertEquals(array(10, 11), $r4['values']);
        
        $r5 = $this->object->select('a, b, c', 'xyz', 'WHERE a > ? AND b = ?', array(3, 5));
        $this->assertTrue($r5['status'], $r5['message']);
        $this->assertEquals('SELECT a, b, c FROM xyz WHERE a > ? AND b = ?', trim($r5['sql']));
        $this->assertEquals(array(3, 5), $r5['values']);
        
        $extra = array('orderBy' => array('id' => 'desc'), 'limit' => 10, 'offset' => 0);
        $r6 = $this->object->select('*', 'table1', array('ativo' => true), $extra);
        $this->assertTrue($r6['status'], $r6['message']);
        $this->assertEquals('SELECT * FROM table1 WHERE ativo = ? ORDER BY id DESC LIMIT ?', trim($r6['sql']));
        $this->assertEquals(array(true, 10), $r6['values']);
    }
    
    public function testSelectJoin() {
        $r1 = $this->object->select('*', array('t1' => '', 't2' => 'ON t1.cod = t2.codigo'));
        $this->assertTrue($r1['status'], $r1['message']);
        $this->assertEquals('SELECT * FROM t1 INNER JOIN t2 ON t1.cod = t2.codigo', trim($r1['sql']));
        
        $r2 = $this->object->select('*', array('t1' => '', 't2' => 't1.cod = t2.codigo'));
        $this->assertTrue($r2['status'], $r2['message']);
        $this->assertEquals('SELECT * FROM t1 INNER JOIN t2 ON t1.cod = t2.codigo', trim($r2['sql']));
        
        $r3 = $this->object->select('*', array('t1' => '', 't2' => 't1.cod = t2.codigo'), array('t1.x' => 2));
        $this->assertTrue($r3['status'], $r3['message']);
        $this->assertEquals('SELECT * FROM t1 INNER JOIN t2 ON t1.cod = t2.codigo WHERE t1.x = ?', trim($r3['sql']));
        $this->assertEquals(array(2), $r3['values']);
    }
}
