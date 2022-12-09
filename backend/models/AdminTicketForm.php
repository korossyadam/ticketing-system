<?php

namespace backend\models;

use yii\base\Model;

class AdminTicketForm extends Model
{
    public $date_start;
    public $date_end;
    public $user_id;
    public $comment_text;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['date_start', 'date_end'], 'required', 'message' => 'Ez a mező nem lehet üres.'],
            [['user_id', 'comment_text'], 'default', 'value' => ''],
            [['user_id'], 'integer', 'message' => 'Az ID csak szám lehet.'],
            [['user_id'], 'integer', 'min' => 0, 'max' => 2147483647, 'tooSmall' => 'Az ID értéke túl alacsony.', 'tooBig' => 'Az ID értéke túl nagy.'],
            [['comment_text'], 'string']
        ];
    }

    /**
     * This function returns query-filter data as an associative array
     * @return array
     */
    public function getData(): array
    {
        return [
            'date_start'=> $this->date_start,
            'date_end' => $this->date_end,
            'user_id' => $this->user_id,
            'comment_text' => $this->comment_text,
        ];
    }
}
