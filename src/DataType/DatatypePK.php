<?php
namespace MyFrameWork\DataType;

use MyFrameWork\DataType\Datatype;
use MyFrameWork\Factory;

/* 
 * Define o tipo de dado Primary Key (chave primária)
 */
class DatatypePK extends Datatype {
    
    protected function _sanitize($value, $params) {
        return filter_var($value, FILTER_SANITIZE_STRING);
    }
    
    protected function _isValid($value, $params) {
        $dao = Factory::DAO(getValueFromArray($params, Flag::DAO_NAME, ''));
        if (!is_null($dao)) {
            $result = $dao->getById($value, !getValueFromArray($params, Flag::ONLY_ACTIVE, true));
            return !empty($result);
        }
        return false;
    }
    
    public function isEmpty($value) {
        return empty($value);
    }
    
    /**
     * 
     * @param string $name
     * @param string $value
     * @param array $params
     * @param array $attr
     * @return string
     */
    public function getHTMLEditable($name, $value, $params, $attr = array()) {
        $params = $this->normalizeParams($params);
        $dao = $this->getDAO($params);
        $dao_label = getValueFromArray($params, Flag::DAO_LABEL, Flag::DAO_LABEL);
        $dao_value = getValueFromArray($params, Flag::DAO_VALUE, Flag::DAO_VALUE);
        $gerericItems = $dao->listAll();
        $options = array();
        if (!getValueFromArray($params, Flag::REQUIRED, false)) {
            array_unshift($options, '');
        }
        foreach($gerericItems as $line) {
            $options[$line[$dao_value]] = $line[$dao_label];
        }
        if (empty($value)) {
            $value = getValueFromArray($params, Flag::DEFAULT_VALUE, '');
        }
        $attr["value"] = $value;
        $attr = $this->getHTMLAttributes($attr, $params);
        return HTML::select($name, $options, $value, $attr, $name . "_id");
    }
    
    /**
     * 
     * @param array $params
     * @return mixed DAO se a Flag::DAO_NAME for setada ou null caso contrário
     */
    public function getDAO($params) {
        $daoname = getValueFromArray($params, Flag::DAO_NAME, "");
        if(empty($daoname)) {
            Factory::log()->fatal('É necessário informar o nome do DAO... use Flag::DAO_NAME no parameter!');
            return null;
        }
        return Factory::DAO($daoname);
    }
    
}

