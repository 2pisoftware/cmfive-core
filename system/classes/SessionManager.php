<?php
///////////////////////////////////////////////////////////////////////////////
//
//                        Database Session Handling
//
///////////////////////////////////////////////////////////////////////////////
class SessionManager extends DbService
{
    private $tableName = 'sessions';

    function __construct(Web $w)
    {
        parent::__construct($w);

        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );

        register_shutdown_function('session_write_close');
    }

    // Open and close aren't needed to be overloaded (this is handled in DbService)
    function open($save_path, $session_name)
    {
        return true;
    }
    function close()
    {
        return true;
    }

    function read($id)
    {
        // Get session by id
        $rs = $this->_db->get($this->tableName)->where("session_id", $id)->fetchAll();
        // Should always only be one row
        if (count($rs) == 1) {
            return $rs[0]["session_data"];
        }

        // It doesnt exist, create it
        $this->_db->insert($this->tableName, array('session_id' => $id, "expires" => time()))->execute();
        return '';
    }

    function write($id, $data)
    {
        $db_data = array(
            'session_id' => $id,
            'session_data' => $data,
            'expires' => time()
        );

        // Check is session id is already in db
        $rs = $this->_db->get($this->tableName)->where("session_id", $id)->fetchAll();
        // Should always only be one row
        if (count($rs) !== 1) {
            // Create if missing...?
            $this->_db->insert($this->tableName, $db_data)->execute();
        } else {
            // Update instead
            $this->_db->update($this->tableName, $db_data)->execute();
        }
        return true;
    }

    function destroy($id)
    {
        $this->_db->delete($this->tableName)->where("session_id", $id)->execute();
        return true;
    }

    function gc($lifetime)
    {
        // Garbage Collection
        $this->_db->delete($this->tableName)->where("expires < ?", (time() - $lifetime))->execute();

        return true;
    }
}
