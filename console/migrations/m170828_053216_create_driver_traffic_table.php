<?php

use yii\db\Migration;

class m170828_053216_create_driver_traffic_table extends Migration
{
    public function up()
    {
        $this->createTable('driver_traffic', [
            'id' => $this->primaryKey(),
            'trip_ticket' => $this->integer(),
            'number' => $this->smallInteger(),
            'duration' => $this->smallInteger(),
            'distance' => $this->smallInteger(),
            'address_start' => $this->string(255),
            'address_finish' => $this->string(255),
        ]);

        // creates index for column `trip_ticket`
        $this->createIndex(
            'idx-driver_traffic-trip_ticket',
            'driver_traffic',
            'trip_ticket'
        );

        // add foreign key for table `driver_trip_tickets`
        $this->addForeignKey(
            'fk-driver_traffic-trip_ticket',
            'driver_traffic',
            'trip_ticket',
            'driver_trip_tickets',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        // drops foreign key for table `driver_trip_tickets`
        $this->dropForeignKey(
            'fk-driver_traffic-trip_ticket',
            'driver_traffic'
        );

        // drops index for column `trip_ticket`
        $this->dropIndex(
            'idx-driver_traffic-trip_ticket',
            'driver_traffic'
        );

        $this->dropTable('driver_traffic');
    }
}
