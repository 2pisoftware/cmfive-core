<?php
class Contact extends DbObject {

	// this object will be automatically indexed for fulltext search
	// public $_searchable;
	
	// these parameters will be excluded from indexing
	public $_exclude_index = array("is_deleted","private_to_user_id");
	
	public $firstname;
	public $lastname;
	public $othername;
	public $title;
	public $homephone;
	public $workphone;
	public $mobile;
	public $priv_mobile;
	public $fax;
	public $email;
	public $is_deleted;
	public $dt_created; // this is automatically excluded from indexing
	public $dt_modified;  // this is automatically excluded from indexing
	public $private_to_user_id;

	function getFullName() {
		if ($this->firstname && $this->lastname) {
			return $this->firstname." ".$this->lastname;
		} else if ($this->firstname) {
			return $this->firstname;
		} else if ($this->lastname) {
			return $this->lastname;
		} else if ($this->othername) {
			return ($this->othername);
		}
	}

	function getFirstName()
	{
		return $this->firstname;
	}

	function getSurname()
	{
		return $this->lastname;
	}

	function getShortName() {
		if ($this->firstname && $this->lastname) {
			return $this->firstname[0]." ".$this->lastname;
		} else {
			return $this->getFullName();
		}
	}

	function getPartner() {
		return null;
	}

	function getUser() {
		return $this->w->Auth->getUserForContact($this->id);
	}

	function printSearchTitle() {
		$buf = $this->getFullName();
		return $buf;
	}
	function printSearchListing() {
                $buf = "";
		if ($this->private_to_user_id) {
			$buf .= "<img src='".$this->w->localUrl("/templates/img/Lock-icon.png")."' border='0'/>";
		}
		$first = true;
		if ($this->workphone) {
			$buf .= __("work phone ").$this->workphone;
			$first = false;
		}
		if ($this->mobile) {
			$buf.= ($first ? "":", ").__("mobile ").$this->mobile;
			$first = false;
		}
		if ($this->email) {
			$buf.=($first ? "":", ").$this->email;
			$first = false;
		}
		return $buf;
	}

	function printSearchUrl() {
		return "contact/view/".$this->id;
	}

	function canList(User $user = null) {
		if (null === $user)
			return false;
		if ($this->private_to_user_id &&
		$this->private_to_user_id != $user->id &&
		!$user->hasRole("administrator")) {
			return false;
		}
		return true;
	}

	function canView(User $user = null) {
		if (null === $user) {
			$user = $this->w->Auth->user();
		}
		// only owners or admin can see private contacts
		if ($this->private_to_user_id &&
		$this->private_to_user_id != $user->id &&
		!$user->hasRole("administrator")) {
			return false;
		}
		// don't show contacts of suspended users
		$u = $this->getUser();
		if ( $u && (!$u->is_active || $u->is_deleted)) {
			return false;
		}
		return true;
	}
	function canEdit(User $user = null) {
		if (null === $user)
			return false;
		return ($user->hasRole("contact_editor")||$this->private_to_user_id == $user->id);
	}

	function canDelete(User $user = null) {
		if (null === $user)
			return false;
		$is_admin = $user->hasRole("contact_editor");
		$is_private = $this->private_to_user_id == $user->id;
		return $is_private || $is_admin;
	}

	function getDbTableName() {
		return "contact";
	}

	function getSelectOptionTitle() {
		return $this->getFullName();
	}
}
