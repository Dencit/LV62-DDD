<?php
namespace Extend\Artisanx;

use PDO;
use PDOException;

/**
 * notes:
 * @author 陈鸿扬 | @date 2020/12/10 13:49
 * Class PdoDB
 * @package Extend\thinkex
 */
class PdoDB{
    protected $dbh;
    protected $opt;
    public function __construct($opt){
        try { $this->opt=$opt;
            $connect=$opt['driver'].':host='.$opt['host'].';dbname='.$opt['database'];
            $this->dbh = new PDO( $connect , $opt['username'] , $opt['password'] );
        }catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";die();
        }
    }
    public function __destruct(){ $this->dbh = null;}

    function getTableFields($childName){
        $result=[]; $childName=strtolower($childName);

        $stmt = $this->dbh->query('SELECT * from '.$this->opt['prefix'].$childName.'s'.' limit 0,1');
        if ( !$stmt ){
            $stmt = $this->dbh->query('SELECT * from '.$this->opt['prefix'].$childName.' limit 0,1' );
        }else if ( !$stmt ){
            $stmt = $this->dbh->query('SELECT * from '.$this->opt['prefix'].trim($childName,'s').' limit 0,1' );
        }

        try {
            for($i=0; $i<$stmt->columnCount(); $i++) { $result[]=$stmt->getColumnMeta($i); }
        }catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";die();
        }

        return $result;
    }

    function showFullColumns($childName){
        $result=null; $childName=strtolower($childName);

        $stmt = $this->dbh->query('SHOW FULL COLUMNS FROM '.$this->opt['prefix'].$childName.'s');
        if ( !$stmt ){
            $stmt = $this->dbh->query('SHOW FULL COLUMNS FROM '.$this->opt['prefix'].$childName );
        }else if ( !$stmt ){
            $stmt = $this->dbh->query('SHOW FULL COLUMNS FROM '.$this->opt['prefix'].trim($childName,'s') );
        }

        try {
            $result = $stmt->fetchAll();
        }catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";die();
        }

        return $result;
    }

    function showTableStatus($childName){
        $result=null; $childName=strtolower($childName);

        $tableName = $childName.'s'; $stmt = $this->dbh->query('SHOW TABLE STATUS LIKE \''.$this->opt['prefix'].$tableName.'\'');
        if ( !$stmt ){
            $tableName = $childName; $stmt = $this->dbh->query('SHOW TABLE STATUS LIKE \''.$this->opt['prefix'].$tableName.'\'' );
        }else if ( !$stmt ){
            $tableName = trim($childName,'s'); $stmt = $this->dbh->query('SHOW TABLE STATUS LIKE \''.$this->opt['prefix'].$tableName.'\'' );
        }

        try {
            $result = $stmt->fetch(); $result["TableName"]=$tableName;
        }catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";die();
        }

        return $result;
    }

    function query($queryStr){
        $stmt = $this->dbh->query($queryStr);
        return $stmt;
    }

}