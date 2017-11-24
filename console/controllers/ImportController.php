<?php

namespace console\controllers;

use yii\console\Controller;
use \console\models\Import;

class ImportController extends Controller
{
    public function actionSubjects()
    {
        $this->simpleImport('order_theme', 'subjects', ['name'], function($row) {
            return ['name' => $row['name']];
        });
    }

    public function actionStocks()
    {
        $this->simpleImport('stock', 'stocks', ['name', 'address'], function($row) {
            return ['name' => $row['stock_name'], 'address' => $row['address']];
        });
    }

    private function simpleImport($old_table, $new_table, $fields, $sorter)
    {
        $old_rows = Import::getAll($old_table);
        $new_rows = [];

        foreach ($old_rows as $row)
            $new_rows[] = $sorter($row);

        Import::batchInsert($new_table, $fields, $new_rows);
    }
}

