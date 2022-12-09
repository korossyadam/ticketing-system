<?php

namespace backend\models;

use yii\base\Model;

class AdminUserForm extends Model
{
    public $date_start;
    public $date_end;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['date_start', 'date_end'], 'required', 'message' => 'Ez a mező nem lehet üres.'],
        ];
    }

    /**
     * This function returns query-filter data as an associative array
     * @return array
     */
    public function getData(): array
    {
        return [
            'date_start' => $this->date_start,
            'date_end' => $this->date_end,
        ];
    }
}
