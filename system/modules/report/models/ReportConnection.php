<?php
/**
 * This object stores connection data to other database sources
 * which can be used with reports.
 * 
 * @author careck
 *
 */
class ReportConnection extends DbObject {
	public $db_host;
	public $db_port;
	public $db_database;
	public $db_driver; // mysql, pgsql, oci, sqlsrv, odbc, sqlite
	public $s_db_user;
	public $s_db_password;
	
	private $db_conn;

	public function __construct(Web $w) {
		parent::__construct($w);
		$this->setPassword(hash("md5", $w->moduleConf("report", "__password")));
	}
	
	/**
	 * returns the database object for this connection
	 */
	public function getDb() {
            if (empty($this->db_conn)) {
                $this->decrypt();
	        $db_config = array(
	            'hostname' => $this->db_host,
                    'port' => $this->db_port,
	            'username' => $this->s_db_user,
	            'password' => $this->s_db_password,
	            'database' => $this->db_database,
	            'driver' => $this->db_driver,
	        );
                $port = isset($this->db_port) && !empty($this->db_port) ? ";port=".$this->db_port : "";
                $url = "{$this->db_driver}:host={$this->db_host};dbname={$this->db_database}{$port}";
                
                $this->db_conn = new PDO($url, $this->s_db_user, $this->s_db_password, null); //new DbPDO($db_config);
                $this->db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $this->db_conn;
	}
	
	public function getSelectOptionTitle() {
		return $this->db_driver.":".$this->db_database."@".$this->db_host;
	}
	
}