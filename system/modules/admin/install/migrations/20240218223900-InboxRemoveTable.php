<?php
/**
 * Removes the inbox table from the Inbox module that is no longer used or supported.
 */

class InboxRemoveTable extends CmfiveMigration
{
    public function up(): void
    {
        //Drop the tables
        $this->dropTable('inbox');
        $this->dropTable('inbox_message');

        //Clear out the related user roles
        $this->w->db->delete('user_role')->where('role', 'inbox_sender')->execute();
        $this->w->db->delete('user_role')->where('role', 'inbox_reader')->execute();
    }

    public function down(): void
    {
    }

    public function description() {
        return 'Remove inbox table and roles';
    }
}
