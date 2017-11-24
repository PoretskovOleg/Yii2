<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_history`.
 * Has foreign keys to the tables:
 *
 * - `driver_status_trips`
 */
class m170813_151149_create_driver_history_trip_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_history_trip', [
            'id' => $this->primaryKey(),
            'trip' => $this->integer(),
            'status' => $this->integer(),
            'createdAt' => $this->integer(),
            'author' => $this->integer(),
            'comment' => $this->string(),
        ]);

        // creates index for column `status`
        $this->createIndex(
            'idx-driver_history_trip-status',
            'driver_history_trip',
            'status'
        );

        // add foreign key for table `driver_status_trips`
        $this->addForeignKey(
            'fk-driver_history_trip-status',
            'driver_history_trip',
            'status',
            'driver_status_trips',
            'id',
            'CASCADE'
        );

        // creates index for column `trip`
        $this->createIndex(
            'idx-driver_history_trip-trip',
            'driver_history_trip',
            'trip'
        );

        // add foreign key for table `driver_trips`
        $this->addForeignKey(
            'fk-driver_history_trip-trip',
            'driver_history_trip',
            'trip',
            'driver_trips',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        // drops foreign key for table `driver_status_trips`
        $this->dropForeignKey(
            'fk-driver_history_trip-status',
            'driver_history_trip'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-driver_history_trip-status',
            'driver_history_trip'
        );

        // drops foreign key for table `driver_trips`
        $this->dropForeignKey(
            'fk-driver_history_trip-trip',
            'driver_history_trip'
        );

        // drops index for column `trip`
        $this->dropIndex(
            'idx-driver_history_trip-trip',
            'driver_history_trip'
        );

        $this->dropTable('driver_history_trip');
    }
}
