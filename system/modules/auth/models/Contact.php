<?php
class Contact extends DbObject {

	// this object will be automatically indexed for fulltext search
	// public $_searchable;
	
	// these parameters will be excluded from indexing
	public $_exclude_index = array("is_deleted","private_to_user_id");
	
	public $firstname;
	public $lastname;
	public $othername;
	public $title_lookup_id;
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
		$buf = $this->getTitle();
		if ($this->firstname and $this->lastname) {
			return $buf . " " . $this->firstname . " " . $this->lastname;
		} else if ($this->firstname) {
			return $buf . " " . $this->firstname;
		} else if ($this->lastname) {
			return $buf . " " . $this->lastname;
		} else if ($this->othername) {
			return $buf . " " . $this->othername;
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
	
	function getTitle() {
		$title_lookup = $this->w->Admin->getLookupbyId($this->title_lookup_id);
		return !empty($title_lookup) ? $title_lookup->title : '';
	}
	
	function setTitle($title) {
		if (!empty($title)) {
			$title_lookup = $this->w->Admin->getLookupByTypeCode('title', $title);
			if (empty($title_lookup)) {
				$title_lookup = new Lookup($this->w);
				$title_lookup->fill(['type'=>'title', 'code'=>$title, 'title'=>$title]);
				$title_lookup->insert();
			}
			$this->title_lookup_id = $title_lookup->id;	
		} else {
			$this->title_lookup_id = null;
		}
		// make sure to call contact->update(true) after this, if this is not a new contact. Otherwise you will be calling contact->insert() anyway.
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
			$buf .= "work phone ".$this->workphone;
			$first = false;
		}
		if ($this->mobile) {
			$buf.= ($first ? "":", ")."mobile ".$this->mobile;
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
