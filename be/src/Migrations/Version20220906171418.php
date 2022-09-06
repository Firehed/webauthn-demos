<?php

declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220906171418 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $users = $schema->createTable('users');

        $users->addColumn('id', 'string');
        $users->addColumn('name', 'string');
        $users->addColumn('password_hash', 'string');

        $users->setPrimaryKey(['id']);
        $users->addUniqueConstraint(['name']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('users');
    }
}
