<?php
class Contact extends DbObject
{
    // these parameters will be excluded from indexing
    public $_exclude_index = ["is_deleted", "private_to_user_id"];

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

    public function getFullName()
    {
        if ($this->firstname and $this->lastname) {
            return $this->firstname . " " . $this->lastname;
        } elseif ($this->firstname) {
            return $this->firstname;
        } elseif ($this->lastname) {
            return $this->lastname;
        } elseif ($this->othername) {
            return $this->othername;
        }
    }

    public function getFirstName()
    {
        return $this->firstname;
    }

    public function getSurname()
    {
        return $this->lastname;
    }

    public function getShortName()
    {
        if ($this->firstname && $this->lastname) {
            return $this->firstname[0] . " " . $this->lastname;
        } else {
            return $this->getFullName();
        }
    }

    public function getTitle()
    {
        $title_lookup = LookupService::getInstance($this->w)->getLookup($this->title_lookup_id);
        return !empty($title_lookup) ? $title_lookup->title : '';
    }

    public function setTitle($title)
    {
        if (!empty($title)) {
            $title_lookup = LookupService::getInstance($this->w)->getLookupByTypeAndCodeV2('title', $title);
            if (empty($title_lookup)) {
                $title_lookup = new Lookup($this->w);
                $title_lookup->fill(['type' => 'title', 'code' => $title, 'title' => $title]);
                $title_lookup->insert();
            }
            $this->title_lookup_id = $title_lookup->id;
        } else {
            $this->title_lookup_id = null;
        }
        // make sure to call contact->update(true) after this, if this is not a new contact. Otherwise you will be calling contact->insert() anyway.
    }

    public function getPartner()
    {
        return null;
    }

    public function getUser()
    {
        return AuthService::getInstance($this->w)->getUserForContact($this->id);
    }

    public function printSearchTitle()
    {
        return $this->getFullName();
    }

    public function printSearchListing()
    {
        $buf = "";
        if ($this->private_to_user_id) {
            $buf .= "<img src='" . $this->w->localUrl("/templates/img/Lock-icon.png") . "' border='0'/>";
        }

        $first = true;
        if ($this->workphone) {
            $buf .= "work phone " . $this->workphone;
            $first = false;
        }

        if ($this->mobile) {
            $buf .= ($first ? "" : ", ") . "mobile " . $this->mobile;
            $first = false;
        }

        if ($this->email) {
            $buf .= ($first ? "" : ", ") . $this->email;
            $first = false;
        }

        return $buf;
    }

    public function printSearchUrl()
    {
        return "contact/view/" . $this->id;
    }

    public function canList(User $user = null)
    {
        if (null === $user) {
            return false;
        }

        if ($this->private_to_user_id && $this->private_to_user_id != $user->id && !$user->hasRole("administrator")) {
            return false;
        }

        return true;
    }

    public function canView(User $user = null)
    {
        if (null === $user) {
            $user = AuthService::getInstance($this->w)->user();
        }

        // only owners or admin can see private contacts
        if ($this->private_to_user_id && $this->private_to_user_id != $user->id && !$user->hasRole("administrator")) {
            return false;
        }

        // don't show contacts of suspended users
        $u = $this->getUser();
        if ($u && (!$u->is_active || $u->is_deleted)) {
            return false;
        }

        return true;
    }
    public function canEdit(User $user = null)
    {
        if (null === $user) {
            return false;
        }

        return ($user->hasRole("contact_editor") || $this->private_to_user_id == $user->id);
    }

    public function canDelete(User $user = null)
    {
        if (null === $user) {
            return false;
        }

        $is_admin = $user->hasRole("contact_editor");
        $is_private = $this->private_to_user_id == $user->id;

        return $is_private || $is_admin;
    }

    public function getDbTableName()
    {
        return "contact";
    }

    public function getSelectOptionTitle()
    {
        return $this->getFullName();
    }
}
