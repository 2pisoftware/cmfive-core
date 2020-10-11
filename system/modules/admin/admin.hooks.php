<?php

/**
 * Store audit entries for any deleting DbObjects
 *
 * @param Web $w
 * @param DbObject $object
 */
function admin_core_dbobject_after_delete(Web $w, DbObject $object): void
{
    if (!empty($object->id) && $object->__use_auditing === true) {
        $w->Audit->addDbAuditLogEntry("delete", get_class($object), $object->id);
    }
}

/**
 * Store audit entries for any updating DbObjects
 *
 * @param Web $w
 * @param DbObject $object
 */
function admin_core_dbobject_after_update(Web $w, DbObject $object): void
{
    if (!empty($object->id) && $object->__use_auditing === true) {
        $w->Audit->addDbAuditLogEntry("update", get_class($object), $object->id);
    }
}

/**
 * Store audit entries for any inserting DbObjects
 *
 * @param Web $w
 * @param DbObject $object
 */
function admin_core_dbobject_after_insert(Web $w, DbObject $object): void
{
    if (!empty($object->id) && $object->__use_auditing === true) {
        $w->Audit->addDbAuditLogEntry("insert", get_class($object), $object->id);
    }
}

/**
 * Log all web access
 *
 * @param Web $w
 */
function admin_core_web_before(Web $w): void
{
    $w->Audit->addAuditLogEntry();
}

function admin_core_dbobject_after_update_Contact(Web $w, Contact $contact): void
{
    $user = $contact->GetUser();
    if (!empty($user)) {
        if ($user->is_external == 1) {
            $user->login = $contact->email;
            $user->insertOrUpdate();
        }
    }
}
