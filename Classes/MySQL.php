<?php
    namespace Zima\Classes;

    use PDO;
    use PDOException;

    /**
     * @author Arin Zima <arin@arinzima.com>
     * 
     * TODO: Provide query system
     */
    class MySQL
    {
        private const FAILED = 'Could not connect to database: %s';

        private $dbh = null;

        /**
         * Establish a connection
         * 
         * @return object|void
         */
        public function connect()
        {
            try {
                $this->dbh = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME,DB_USER,DB_PASS);
            } catch (PDOException $e) {
                die(sprintf(self::FAILED, $e->getMessage()));
            }
        }
    }