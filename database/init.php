<?php

require_once "modules/BridgeMethods.php";

trait DatabaseMethods {
    private function getDatabase() : mysqli
    {
        return $this->db;
    }

    use BridgeMethods;
}

class database{
    use DatabaseMethods;

    private $db = null;

	public function __construct($database){
		$instance = new mysqli($database['hostname'], $database['login'], $database['password'], $database['dbname'], $database['port']);

		$this->db = $instance;
		$this->db->set_charset("utf8mb4");
	}
}

?>