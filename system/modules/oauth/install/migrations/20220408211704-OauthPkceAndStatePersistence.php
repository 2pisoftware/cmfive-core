<?php

class OauthPkceAndStatePersistence extends CmfiveMigration
{
    public function up()
    {
        // UP
        $column = parent::Column();
        $column->setName('id')
            ->setType('biginteger')
            ->setIdentity(true);

        // Create state table
        if (!$this->hasTable("oauth_flow")) {
            $this->table('oauth_flow', [
                'id' => false,
                'primary_key' => 'id'
            ])->addColumn($column)
                ->addColumn('app_id', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('state', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('pkce_challenge', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('pkce_method', 'string', ['limit' => 255, 'null' => false])
                ->addColumn('pkce_verifier', 'string', ['limit' => 255, 'null' => false])
                ->addCmfiveParameters(['dt_created', 'dt_modified', 'creator_id', 'modifier_id'])
                ->create();
        }
    }

    public function down()
    {
        // DOWN

        $this->hasTable("oauth_flow") ? $this->dropTable('oauth_flow') : null;
    }

    public function preText()
    {
        return null;
    }

    public function postText()
    {
        return null;
    }

    public function description()
    {
        return null;
    }
}
