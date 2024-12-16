<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%jegy}}`.
 */
class m241122_233302_create_jegy_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%jegy}}', [
            'id' => $this->primaryKey(),
            'eloadas_id' => $this->integer()->notNull(), // Foreign key to `eloadas` table
            'seat_row' => $this->integer()->notNull(), // Seat row number
            'seat_number' => $this->integer()->notNull(), // Seat number in the row
            'customer_name' => $this->string(255)->notNull(), // Customer name
            'customer_email' => $this->string(255)->notNull(), // Customer email
            'customer_phone' => $this->string(20)->notNull(), // Customer phone
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'), // Timestamp for booking creation
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP')->append('ON UPDATE CURRENT_TIMESTAMP'), // Timestamp for booking update
        ]);

        // Add foreign key constraint to link jegy to eloadas
        $this->addForeignKey(
            'fk-jegy-eloas_id', // Foreign key name
            'jegy',             // Table with the foreign key column
            'eloadas_id',       // Column in jegy that is the foreign key
            'eloadas',          // Reference table
            'id',               // Reference column in eloadas table
            'CASCADE',          // On delete cascade
            'CASCADE'           // On update cascade
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Remove foreign key before dropping the table
        $this->dropForeignKey('fk-jegy-eloas_id', 'jegy');
        $this->dropTable('{{%jegy}}');
    }
}
