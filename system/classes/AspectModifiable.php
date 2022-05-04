<?php

/**
 * Use this aspect to store creation
 * and modification data for any object
 * @author carsten
 *
 */
class AspectModifiable
{
    private $object;
    private $_mo;

    public function __construct(DbObject &$obj)
    {
        $this->object = $obj;
    }

    private function getMo()
    {
        if ($this->object && $this->object->id && !$this->_mo) {
            $this->_mo = $this->object->getObject("ObjectModification", ["table_name" => $this->object->getDbTableName(), "object_id" => $this->object->id]);
        }
        return $this->_mo;
    }

    /////////////////////////////////////////////////
    // Methods to be used by DbObject
    /////////////////////////////////////////////////

    /**
     * Store creation data
     */
    public function insert()
    {
        if (!$this->getMo()) {
            $mo = new ObjectModification($this->object->w);
            $mo->table_name = $this->object->getDbTableName();
            $mo->object_id = $this->object->id;
            $mo->dt_created = time();
            $user = AuthService::getInstance($mo->w)->user();
            $mo->creator_id = (!empty($user->id) ? $user->id : 0);
            $mo->insert();
        }
    }

    /**
     * Store modification data
     */
    public function update()
    {
        if ($this->getMo()) {
            $this->_mo->dt_modified = time();
            $user = AuthService::getInstance($this->_mo->w)->user();
            $this->_mo->modifier_id = (!empty($user->id) ? $user->id : 0);
            $this->_mo->update();
        }
    }

    /////////////////////////////////////////////////
    // Methods to be used by client object
    /////////////////////////////////////////////////

    public function getCreator()
    {
        if ($this->getMo()) {
            return $this->_mo->getCreator();
        }
    }

    public function getModifier()
    {
        if ($this->getMo()) {
            return $this->_mo->getModifier();
        }
    }

    public function getCreatedDate()
    {
        if ($this->getMo()) {
            return $this->_mo->dt_created;
        }
    }

    public function getModifiedDate()
    {
        if ($this->getMo()) {
            return $this->_mo->dt_modified;
        }
    }
}

//////////////////////////////////////////////////////////////////////////////
//            Generic Modification data for some objects
//////////////////////////////////////////////////////////////////////////////

/**
 * Store creation and modification data of any object
 */
class ObjectModification extends DbObject
{
    public $table_name;
    public $object_id;

    public $dt_created;
    public $dt_modified;
    public $creator_id;
    public $modifier_id;

    // do not audit this table!
    public $__use_auditing = false;

    public static $_db_table = "object_modification";

    /**
     * returns the creator of the
     * object which is attached to this
     * aspect.
     *
     * @return User
     */
    public function getCreator()
    {
        if ($this->creator_id) {
            return AuthService::getInstance($this->w)->getUser($this->creator_id);
        }
    }

    /**
     * returns the modifier user
     * of the object which is attached
     * to this aspect.
     *
     * @return User
     */
    public function getModifier()
    {
        if ($this->modifier_id) {
            return AuthService::getInstance($this->w)->getUser($this->modifier_id);
        }
    }
}
