<?php
	class Db{
        private static $db=null;
        private static $flag;
        private static $dsn = '';
        private static $user= '';
        private static $pass= '';

        function __construct(){
            throw new Exception("Static class");
        }

        private static function init($config=array()){
            self::$dsn = $config['dsn'];
            self::$user= $config['user'];
            self::$pass= $config['pass'];
        }

        static function instance($table){
            $config = require 'db.config.php';

            self::init($config[$table]);
            if($config[$table]['dsn']!=self::$flag || self::$db === null){
                self::$flag = self::$dsn;
                self::$db = new PDO(self::$dsn,self::$user,self::$pass,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '".$config[$table]['lang']."';"));
            }

            return self::$db;
        }

    }
