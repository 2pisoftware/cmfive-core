<?php

class AdminBulkEmails extends CmfiveMigration {

	public function up() {
		// UP
		$column = parent::Column();
		$column->setName('id')
				->setType('biginteger')
				->setIdentity(true);
                
                /**
		 * Mail Queue TABLE
		 */
		if (!$this->hasTable('mail_queue')) {
			$this->table('mail_queue', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
                                ->addintegerColumn('to_contact_id')
                                ->addIntegerColumn('batch_id')
                                ->addCmfiveParameters()
                                ->create();
                }
                
                /**
		 * Mail batch TABLE
		 */
		if (!$this->hasTable('mail_batch')) {
			$this->table('mail_batch', [
						'id' => false,
						'primary_key' => 'id'
					])->addColumn($column)
                                ->addStringColumn('title')
                                ->addColumn('details', 'text', ['limit' => 4294967295, 'null' => true])
                                ->addStringColumn('status')
                                ->addDateTimeColumn('dt_started')
                                ->addDateTimeColumn('dt_finished')
                                ->addIntegerColumn('user_to_notify')
                                ->addStringColumn('subject')
                                ->addIntegerColumn('number_sent')
                                ->addStringColumn('tag')
                                ->addBooleanColumn('is_main_contact')
                                ->addBooleanColumn('is_billing_contact')
                                ->addBooleanColumn('is_self')
                                ->addIntegerColumn('template_id')
                                ->addColumn('message', 'text', ['limit' => 4294967295, 'null' => true])
                                ->addStringColumn('extra_data')
                                ->addCmfiveParameters()
                                ->create();
                }
                
                /**
		 * Add is_public to attechments
		 */
		if ($this->hasTable('attachment') && !$this->table("attachment")->hasColumn("is_public")) {
                    $this->table("attachment")->addBooleanColumn("is_public")->save();
                }
                
	}

	public function down() {
		// DOWN
            $this->hasTable('mail_queue') ? $this->dropTable('mail_queue') : null;
            $this->hasTable('mail_batch') ? $this->dropTable('mail_batch') : null;
            $this->hasTable('attachment') && $this->table("attachment")->hasColumn("is_public") ? $this->table('attachment')->removeColumn('is_public') : null;
	}

}
