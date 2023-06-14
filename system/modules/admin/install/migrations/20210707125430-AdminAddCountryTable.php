<?php

class AdminAddCountryTable extends CmfiveMigration
{
    public function up(): void
    {
        if (!$this->hasTable('country')) {
        $this->tableWithId('country')
            ->addStringColumn('name')
            ->addStringColumn('alpha_2_Code')
            ->addStringColumn('alpha_3_Code')
            ->addStringColumn('capital')
            ->addStringColumn('region')
            ->addStringColumn('subregion')
            ->addStringColumn('demonym')
            ->addCmfiveParameters()
            ->create();
        }
    }

    public function down(): void
    {
        $this->dropTable('country');
    }
}
