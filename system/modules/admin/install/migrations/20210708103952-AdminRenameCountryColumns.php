<?php

class AdminRenameCountryColumns extends CmfiveMigration
{
    public function up(): void
    {
        $this->renameColumnInTable('country', 'alpha_2_Code', 'alpha_2_code');
        $this->renameColumnInTable('country', 'alpha_3_Code', 'alpha_3_code');
    }

    public function down(): void
    {
        $this->renameColumnInTable('country', 'alpha_2_code', 'alpha_2_Code');
        $this->renameColumnInTable('country', 'alpha_3_code', 'alpha_3_Code');
    }
}
