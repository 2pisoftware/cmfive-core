<?php

class AdminAddLanguageTable extends CmfiveMigration
{
    public function up(): void
    {
        if (!$this->hasTable('language')) {
        $this->tableWithId('language')
            ->addStringColumn('name')
            ->addStringColumn('native_name')
            ->addStringColumn('iso_639_1')
            ->addStringColumn('iso_639_2')
            ->addCmfiveParameters()
            ->create();
        }

        if (!$this->hasTable('country_language')) {
        $this->tableWithId('country_language')
            ->addIdColumn('country_id')
            ->addIdColumn('language_id')
            ->addCmfiveParameters()
            ->create();
        }
    }

    public function down(): void
    {
        $this->dropTable('language');
        $this->dropTable('country_language');
    }
}
