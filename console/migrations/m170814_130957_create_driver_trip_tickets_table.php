<?php

use yii\db\Migration;

class m170814_130957_create_driver_trip_tickets_table extends Migration
{
    public function up()
    {
        $this->createTable('driver_trip_tickets', [
            'id' => $this->primaryKey(),
            'createdAt' => $this->integer(),
            'status' => $this->integer(),
            'driver' => $this->integer(),
            'car' => $this->integer(),
            'author' => $this->integer(),
            'departureDate' => $this->integer(),
            'departurePlace' => $this->integer(),
            'finishPlace' => $this->integer()
        ]);

        // creates index for column `status`
        $this->createIndex(
            'idx-driver_trip_tickets-status',
            'driver_trip_tickets',
            'status'
        );

        // add foreign key for table `driver_status_trips`
        $this->addForeignKey(
            'fk-driver_trip_tickets-status',
            'driver_trip_tickets',
            'status',
            'driver_status_trip_tickets',
            'id',
            'CASCADE'
        );

        // creates index for column `car`
        $this->createIndex(
            'idx-driver_trip_tickets-car',
            'driver_trip_tickets',
            'car'
        );

        // add foreign key for table `driver_car`
        $this->addForeignKey(
            'fk-driver_trip_tickets-car',
            'driver_trip_tickets',
            'car',
            'driver_car',
            'id',
            'CASCADE'
        );

        // creates index for column `departurePlace`
        $this->createIndex(
            'idx-driver_trip_tickets-departure_place',
            'driver_trip_tickets',
            'departurePlace'
        );

        // add foreign key for table `driver_address`
        $this->addForeignKey(
            'fk-driver_trip_tickets-departure_place',
            'driver_trip_tickets',
            'departurePlace',
            'driver_address',
            'id',
            'CASCADE'
        );

        // creates index for column `finishPlace`
        $this->createIndex(
            'idx-driver_trip_tickets-finish_place',
            'driver_trip_tickets',
            'finishPlace'
        );

        // add foreign key for table `driver_address`
        $this->addForeignKey(
            'fk-driver_trip_tickets-finish_place',
            'driver_trip_tickets',
            'finishPlace',
            'driver_address',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        // drops foreign key for table `driver_status_trips`
        $this->dropForeignKey(
            'fk-driver_trip_tickets-status',
            'driver_trip_tickets'
        );

        // drops index for column `status`
        $this->dropIndex(
            'idx-driver_trip_tickets-status',
            'driver_trip_tickets'
        );

        // drops foreign key for table `driver_car`
        $this->dropForeignKey(
            'fk-driver_trip_tickets-car',
            'driver_trip_tickets'
        );

        // drops index for column `car`
        $this->dropIndex(
            'idx-driver_trip_tickets-car',
            'driver_trip_tickets'
        );

        // drops foreign key for table `driver_address`
        $this->dropForeignKey(
            'fk-driver_trip_tickets-departure_place',
            'driver_trip_tickets'
        );

        // drops index for column `departurePlace`
        $this->dropIndex(
            'idx-driver_trip_tickets-departure_place',
            'driver_trip_tickets'
        );

        // drops foreign key for table `driver_address`
        $this->dropForeignKey(
            'fk-driver_trip_tickets-finish_place',
            'driver_trip_tickets'
        );

        // drops index for column `finishPlace`
        $this->dropIndex(
            'idx-driver_trip_tickets-finish_place',
            'driver_trip_tickets'
        );

        $this->dropTable('driver_trip_tickets');
    }
}
