<?php 
    declare(strict_types=1);

    class DatabaseConnection{
        private $db;

        public function __construct(){
            $name = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
            try{
                $this->db = new PDO($name, DB_USER, DB_PASS);
                $this->db->query("SET NAMES 'utf8'");
            }
            catch (Exception $e){
                die ($e->getMessage());
            }
        }

        public function get_db(){
            return $this->db;
        }
    }
?>