<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%eloas}}`.
 */
class m241122_233252_create_eloas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%eloadas}}', [
            'id' => $this->primaryKey(),
            'title' => $this->string()->notNull(), // Movie title
            'show_date' => $this->date()->notNull(), // Date of the show
            'start_time' => $this->time()->notNull(), // Start time of the show
            'end_time' => $this->time()->notNull(), // End time of the show
            'ticket_price' => $this->integer()->notNull(), // Ticket price for the show, round number only
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'), // Timestamp for record creation
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'), // Timestamp for record update
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop the `eloadas` table
        $this->dropTable('{{%eloadas}}');
    }
}
