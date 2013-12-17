<?php

/**
* A PHP session handler to keep session data within a MySQL database
*
* @author 	Manuel Reinhard <manu@sprain.ch>
* @link		https://github.com/sprain/PHP-MySQL-Session-Handler
*/

class SessionHandler{

    /**
     * a database MySQLi connection resource
     * @var resource
     */
    private $dbConnection;
    
    /**
     * the name of the DB table which handles the sessions
     * @var string
     */
    private $dbTable;
    
    private $dbHost = '';
    private $dbUser = '';
    private $dbPassword = '';
    private $dbDatabase = '';
    
    private $dbConnected = FALSE;
	


	/**
	 * Set db data
	 * @param 	string	$dbHost	
	 * @param	string	$dbUser
	 * @param	string	$dbPassword
	 * @param	string	$dbDatabase
	 */	
	public function setDbDetails($dbHost, $dbUser, $dbPassword, $dbDatabase){

		// Store details in case we need to re-connect
        $this->dbHost = $dbHost;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
        $this->dbDatabase = $dbDatabase;
        
        $this->_openDbConnection();
	}
		
	/**
	 * Inject DB connection from outside
	 * @param 	object	$dbConnection	expects MySQLi object
	 */
	public function setDbTable($dbTable){
	
		$this->dbTable = $dbTable;
		
	}
	
    /**
     * Open the session.
     * 
     * Connects the database, if the connection is not already open
     * 
     * @return bool
     */
    public function open() {
  
        if($this->dbConnected)
        {
            return TRUE;
        }
        
        return $this->_openDbConnection();
    }

    /**
     * Close the session
     * @return bool
     */
    public function close() {

        $this->dbConnected = FALSE;
        
        return $this->dbConnection->close();
    }

    /**
     * Read the session
     * @param int session id
     * @return string string of the sessoin
     */
    public function read($id) {

        $sql = sprintf("SELECT data FROM %s WHERE id = '%s'", $this->dbTable, $this->dbConnection->escape_string($id));
        if ($result = $this->dbConnection->query($sql)) {
            if ($result->num_rows && $result->num_rows > 0) {
                $record = $result->fetch_assoc();
                return $record['data'];
            } else {
                return false;
            }
        } else {
            return false;
        }
        return true;
        
    }
    
    /**
     * Write the session
     * @param int session id
     * @param string data of the session
     */
    public function write($id, $data) {
        $sql = sprintf("INSERT INTO `%s` (`id`, `data`) VALUES ('%s', '%s') ON DUPLICATE KEY UPDATE `data` = '%s';",
                       $this->dbTable,
                       $this->dbConnection->escape_string($id),
                       $this->dbConnection->escape_string($data),
                       $this->dbConnection->escape_string($data));
        return $this->dbConnection->query($sql);

    }

    /**
     * Destroy the session
     * @param int session id
     * @return bool
     */
    public function destroy($id) {

        $sql = sprintf("DELETE FROM %s WHERE `id` = '%s'", $this->dbTable, $this->dbConnection->escape_string($id));
        return $this->dbConnection->query($sql);

	}
	
    /**
     * Garbage Collector
     * @param int life time (sec.)
     * @return bool
     * @see session.gc_divisor      100
     * @see session.gc_maxlifetime 1440
     * @see session.gc_probability    1
     * @usage execution rate 1/100
     *        (session.gc_probability/session.gc_divisor)
     */
    public function gc($lifetime) {

        $sql = sprintf("DELETE FROM %s WHERE `timestamp` < '%s'",
                       $this->dbTable,
                       strftime("%Y-%m-%d %H:%M:%S", time() - intval($lifetime)));
        return $this->dbConnection->query($sql);

    }
    
    /**
     * Connects to the database
     */
    private function _openDbConnection() {
        // Create db connection
        $this->dbConnection = new mysqli($this->dbHost, $this->dbUser, $this->dbPassword, $this->dbDatabase);
        
        // Check we connected ok
        if (mysqli_connect_error()) {
            return FALSE;
        }
        
        $this->dbConnected = TRUE;
        return TRUE;
    }

}
