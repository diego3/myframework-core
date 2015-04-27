<?php
require_once 'util/database/Where.php';

class WhereTest extends PHPUnit_Framework_TestCase {

    public function testSimpleFormat() {
        $formats = array(
            array(
                'data' => array('campo1' => 1),
                'sql' => 'WHERE campo1 = ?',
                'params' => array(1)
            ),
            array(
                'data' => array('campo1' => array('v1', 'v2')),
                'sql' => 'WHERE campo1 IN ?',
                'params' => array(array('v1', 'v2'))
            ),
            array(
                'data' => array('campo1' => 1, 'campo2' => 2),
                'sql' => 'WHERE campo1 = ? AND campo2 = ?',
                'params' => array(1, 2)
            ),
            array(
                'data' => array('campo1' => 1, 'campo2' => 2, 'campo3' => array('v1', 'v2')),
                'sql' => 'WHERE campo1 = ? AND campo2 = ? AND campo3 IN ?',
                'params' => array(1, 2, array('v1', 'v2'))
            ),
            array(
                'data' => array('campo1' => 'v1', array('campo2' => 'v2', 'campo3' => 'v3')),
                'sql' => 'WHERE campo1 = ? AND (campo2 = ? OR campo3 = ?)',
                'params' => array('v1', 'v2', 'v3')
            ),
            array(
                'data' => array(array('campo1' => 'v1', 'campo2' => 'v2')),
                'sql' => 'WHERE (campo1 = ? OR campo2 = ?)',
                'params' => array('v1', 'v2')
            ),
            array(
                'data' => array(array('campo1' => 2, array('campo3'=>1, 'campo2' => 4))),
                'sql' => 'WHERE (campo1 = ? OR (campo3 = ? AND campo2 = ?))',
                'params' => array(2, 1, 4)
            )
        );
        foreach ($formats as $test) {
            $where = new Where($test['data']);
            $this->assertEquals($test['sql'], $where->getSQL(), $test['sql']);
            $this->assertEquals($test['params'], $where->getParams(), 'Invalid Params: ' . $test['sql']);
        }
    }

    public function testComplexFormat() {
        $formats = array(
            array(
                'data' => array(array('attribute' => 'campo1', 'operator' => '=', 'value' => 1)),
                'sql' => 'WHERE campo1 = ?',
                'params' => array(1)
            ),
            array(
                'data' => array(array('attribute' => 'campo1', 'operator' => '<>', 'value' => 21)),
                'sql' => 'WHERE campo1 <> ?',
                'params' => array(21)
            ),
            array(
                'data' => array(array('attribute' => 'campo1', 'operator' => 'NOT IN', 'value' => array('v1', 'v2'))),
                'sql' => 'WHERE campo1 NOT IN ?',
                'params' => array(array('v1', 'v2'))
            ),
            array(
                'data' => array(
                    array('attribute' => 'campo1', 'operator' => '=', 'value' => 1),
                    array('attribute' => 'campo2', 'operator' => '>', 'value' => 2)
                ),
                'sql' => 'WHERE campo1 = ? AND campo2 > ?',
                'params' => array(1, 2)
            ),
            array(
                'data' => array(
                    array('attribute' => 'campo1', 'operator' => '=', 'value' => 1),
                    array('attribute' => 'campo2', 'operator' => '=', 'value' => 2),
                    array('attribute' => 'campo3', 'operator' => 'IN', 'value' => array('v1', 'v2'))
                ),
                'sql' => 'WHERE campo1 = ? AND campo2 = ? AND campo3 IN ?',
                'params' => array(1, 2, array('v1', 'v2'))
            ),
            array(
                'data' => array(
                    array('attribute' => 'campo1', 'operator' => '=', 'value' => 'v1'),
                    array(
                        array('attribute' => 'campo2', 'operator' => '=', 'value' => 'v2'),
                        array('attribute' => 'campo3', 'operator' => '=', 'value' => 'v3')
                    )
                ),
                'sql' => 'WHERE campo1 = ? AND (campo2 = ? OR campo3 = ?)',
                'params' => array('v1', 'v2', 'v3')
            ),
            array(
                'data' => array(
                    array(
                        array('attribute' => 'campo1', 'operator' => '=', 'value' => 'v1'),
                        array('attribute' => 'campo2', 'operator' => '=', 'value' => 'v2')
                    )
                 ),
                'sql' => 'WHERE (campo1 = ? OR campo2 = ?)',
                'params' => array('v1', 'v2')
            ),
            array(
                'data' => array(
                    array(
                        array('attribute' => 'campo1', 'operator' => '=', 'value' => 2),
                        array(
                            array('attribute' => 'campo3', 'operator' => '=', 'value' => 1),
                            array('attribute' => 'campo2', 'operator' => '=', 'value' => 4)
                        )
                    )
                 ),
                'sql' => 'WHERE (campo1 = ? OR (campo3 = ? AND campo2 = ?))',
                'params' => array(2, 1, 4)
            )
        );
        foreach ($formats as $test) {
            $where = new Where($test['data']);
            $this->assertEquals($test['sql'], $where->getSQL(), $test['sql']);
            $this->assertEquals($test['params'], $where->getParams(), 'Invalid Params: ' . $test['sql']);
        }
    }
    
    public function testMixedFormat() {
        $formats = array(
            array(
                'data' => array(
                    'campo1' => 1,
                    array('attribute' => 'campo2', 'operator' => '>', 'value' => 2)
                ),
                'sql' => 'WHERE campo1 = ? AND campo2 > ?',
                'params' => array(1, 2)
            ),
            array(
                'data' => array(
                    'campo1' => 1,
                    array('attribute' => 'campo2', 'operator' => '=', 'value' => 2),
                    'campo3' => array('v1', 'v2')
                ),
                'sql' => 'WHERE campo1 = ? AND campo2 = ? AND campo3 IN ?',
                'params' => array(1, 2, array('v1', 'v2'))
            ),
            array(
                'data' => array(
                    'campo1' => 'v1',
                    array(
                        array('attribute' => 'campo2', 'operator' => '=', 'value' => 'v2'),
                        'campo3' => 'v3'
                    )
                ),
                'sql' => 'WHERE campo1 = ? AND (campo2 = ? OR campo3 = ?)',
                'params' => array('v1', 'v2', 'v3')
            ),
            array(
                'data' => array(
                    array('attribute' => 'campo1', 'operator' => '=', 'value' => 'v1'),
                    array('campo2' => 'v2', 'campo3' => 'v3')
                ),
                'sql' => 'WHERE campo1 = ? AND (campo2 = ? OR campo3 = ?)',
                'params' => array('v1', 'v2', 'v3')
            ),
            array(
                'data' => array(
                    array(
                        array('attribute' => 'campo1', 'operator' => '=', 'value' => 'v1'),
                        'campo2' => 'v2'
                    )
                 ),
                'sql' => 'WHERE (campo1 = ? OR campo2 = ?)',
                'params' => array('v1', 'v2')
            ),
            array(
                'data' => array(
                    array(
                        'campo1' => 2,
                        array(
                            array('attribute' => 'campo3', 'operator' => '=', 'value' => 1),
                            'campo2' => 4,
                            array('attribute' => 'attribute', 'operator' => '<>', 'value' => 'attribute'),
                        )
                    )
                 ),
                'sql' => 'WHERE (campo1 = ? OR (campo3 = ? AND campo2 = ? AND attribute <> ?))',
                'params' => array(2, 1, 4, 'attribute')
            )
        );
        foreach ($formats as $test) {
            $where = new Where($test['data']);
            $this->assertEquals($test['sql'], $where->getSQL(), $test['sql']);
            $this->assertEquals($test['params'], $where->getParams(), 'Invalid Params: ' . $test['sql']);
        }
    }
    
    public function testGetInstance() {
        $this->assertEmpty(Where::getInstance(array())->getSQL(), 'Not Empty');
        $w1 = new Where(array('a' => 'b'));
        $this->assertEquals($w1, Where::getInstance($w1));
        $w2 = Where::getInstance(array('a' => 'b'));
        $this->assertEquals($w1->getSQL(), $w2->getSQL());
        $this->assertEquals($w1->getParams(), $w2->getParams());
        
    }
    
    public function testException() {
        $cases = array(
            array('params' => array(), 'message' => 'Empty'),
            array('params' => "blabla", 'message' => 'Not array - string'),
            array('params' => 3223, 'message' => 'Not array - int'),
            array('params' => array(1,2,3), 'message' => 'Not valid format - list of integer'),
            array('params' => array('1','2','3'), 'message' => 'Not valid format - list of string'),
            array('params' => array(array(1,2,3)), 'message' => 'Not valid format - array of integer list')
        );
        foreach ($cases as $case) {
            $where = new Where($case['params']);
            $this->assertEmpty($where->getSQL(), $case['message']);
            $this->assertEmpty($where->getParams(), $case['message']);
        }
        
        //TODO test partial format with correct and wrong data
    }
}
