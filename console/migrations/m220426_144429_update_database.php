<?php

use yii\db\Migration;

/**
 * Class m220426_144429_update_database
 */
class m220426_144429_update_database extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        date_default_timezone_set(@date_default_timezone_get());

        $tableOptions = null;

        // Creating the Customer table
        $this->createTable('{{%customer}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'email' => $this->string()->notNull()->unique(),
            'is_admin' => $this->boolean()->notNull(),
            'last_login' => $this->timestamp()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);

        // Add Customer verification token
        $this->addColumn('{{%customer}}', 'verification_token', $this->string()->defaultValue(null));

        // Creating the Ticket table
        $this->createTable('{{%ticket}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'admin_id' => $this->integer()->notNull(),
            'is_closed' => $this->boolean()->notNull(),
            'closed_by' => $this->integer()->notNull(),
            'last_comment_at' => $this->timestamp()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);

        // Creating the Comment table
        $this->createTable('{{%comment}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'ticket_id' => $this->integer()->notNull(),
            'text' => $this->string()->notNull(),
            'img_url' => $this->string(),
            'closed_ticket' => $this->boolean()->notNull(),
            'reopened_ticket' => $this->boolean()->notNull(),

            'status' => $this->smallInteger()->notNull()->defaultValue(10),
            'created_at' => $this->timestamp()->notNull(),
            'updated_at' => $this->timestamp()->notNull(),
        ], $tableOptions);

        // Index for Ticket - customer_id
        $this->createIndex(
            'idx-ticket-customer_id',
            'ticket',
            'customer_id'
        );

        // Index for Comment - customer_id
        $this->createIndex(
            'idx-comment-customer_id',
            'comment',
            'customer_id'
        );

        // Index for Comment - ticket_id
        $this->createIndex(
            'idx-comment-ticket_id',
            'comment',
            'ticket_id'
        );

        // Foreign key for Ticket - customer_id
        $this->addForeignKey(
            'fk-ticket-customer_id',
            'ticket',
            'customer_id',
            'customer',
            'id',
            'CASCADE'
        );

        // Foreign key for Comment - customer_id
        $this->addForeignKey(
            'fk-comment-customer_id',
            'comment',
            'customer_id',
            'customer',
            'id',
            'CASCADE'
        );

        // Foreign key for Comment - ticket_id
        $this->addForeignKey(
            'fk-comment-ticket_id',
            'comment',
            'ticket_id',
            'ticket',
            'id',
            'CASCADE'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drops Index for Ticket - customer_id
        $this->dropIndex(
            'idx-ticket-customer_id',
            'ticket'
        );

        // Drops Index for Comment - customer_id
        $this->dropIndex(
            'idx-comment-customer_id',
            'comment'
        );

        // Drops Index for Comment - ticket_id
        $this->dropIndex(
            'idx-comment-ticket_id',
            'comment'
        );

        // Drops Foreign key for Ticket - customer_id
        $this->dropForeignKey(
          'fk-ticket-customer_id',
          'ticket'
        );

        // Drops Foreign key for Comment - customer_id
        $this->dropForeignKey(
            'fk-comment-customer_id',
            'comment'
        );

        // Drops Foreign key for Comment - ticket_id
        $this->dropForeignKey(
            'fk-comment-ticket_id',
            'comment'
        );

        // Drop tables
        $this->dropTable('comment');
        $this->dropTable('ticket');
        $this->dropTable('customer');


        //return false;
    }

}
