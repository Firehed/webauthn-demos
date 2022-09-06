<?php

declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906224346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $creds = $schema->createTable('credentials');

        $creds->addColumn('id', 'string');
        $creds->addColumn('user_id', 'string');
        $creds->addColumn('nickname', 'string');
        $creds->addColumn('credential', 'text');

        $creds->setPrimaryKey(['id']);
        $creds->addForeignKeyConstraint('users', ['user_id'], ['id']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('credentials');
    }
}
