<?php

use yii\db\Migration;

/**
 * Handles the creation of table `driver_trips`.
 */
class m170803_124505_create_driver_trips_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('driver_trips', [
            'id' => $this->primaryKey(),
            'status' => $this->smallInteger(),
            'typeOfTrip' => $this->smallInteger(),
            'priority' => $this->smallInteger(1),
            'notice' => $this->string(),
            'orderId' => $this->integer(),
            'subscribeOrder' => $this->string(),
            'weightOrder' => $this->float(),
            'timeLoad' => $this->smallInteger(),
            'length' => $this->float(),
            'width' => $this->float(),
            'height' => $this->float(),
            'firstDate' => $this->integer(),
            'from' => $this->smallInteger(),
            'adressFrom' => $this->string(),
            'zoneFrom' => $this->smallInteger(1),
            'consignerName' => $this->string(),
            'consignerPhone' => $this->string(),
            'consignerUser' => $this->string(),
            'consignerUserPhone' => $this->string(),
            'consignerInn' => $this->string(),
            'consigneeName' => $this->string(),
            'consigneePhone' => $this->string(),
            'consigneeUser' => $this->string(),
            'consigneeUserPhone' => $this->string(),
            'consigneeInn' => $this->string(),
            'to' => $this->smallInteger(),
            'adressTo' => $this->string(),
            'zoneTo' => $this->smallInteger(1),
            'terminalTC' => $this->string(),
            'authorId' => $this->integer(),
            'createdAt' => $this->integer(),
            'desiredDateFrom' => $this->integer(),
            'desiredDateTo' => $this->integer(),
            'dateTrip' => $this->integer(),
            'tripTicketId' => $this->integer(),
            'dedline' => $this->integer(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('driver_trips');
    }
}
