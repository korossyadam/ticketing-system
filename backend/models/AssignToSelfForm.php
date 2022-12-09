<?php

namespace backend\models;

use common\models\Ticket;
use Yii;
use yii\base\Model;

class AssignToSelfForm extends Model
{
    public $hidden;
    public $assign_to_self;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['hidden', 'assign_to_self'], 'default', 'value' => 0],
        ];
    }

    /**
     * This function assigns Ticket so self if checkbox was checked
     * @return Ticket
     */
    public function assignToSelfIfNeeded(Ticket $ticket): Ticket
    {
        if ($this->assign_to_self) {
            $ticket->admin_id = Yii::$app->user->id;
        }

        return $ticket;
    }
}
