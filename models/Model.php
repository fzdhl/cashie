<?php
    class Model {
        protected $dbconn;

        public function __construct() {
            $host = 'localhost';
            $dbuser = 'root';
            $dbpass = '';
            $dbname = 'cashie';
            $dbport = '3306';
        
            $this->dbconn = new mysqli($host, $dbuser, $dbpass, $dbname, $dbport);
        
            if ($this->dbconn->connect_errno) {
                die('Database connection failure');
            }
        }
    }