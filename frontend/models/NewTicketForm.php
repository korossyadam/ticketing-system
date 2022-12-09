<?php

namespace frontend\models;

use common\models\Ticket;
use Exception;
use Yii;
use yii\base\Model;
use yii\helpers\Html;


class NewTicketForm extends Model
{
    public $title;
    public $verifyCode;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['title'], 'required', 'message' => 'Ez a mező nem lehet üres.'],
            [['title'], 'string', 'min' => 3, 'max' => 30, 'tooShort' => 'Az cím hossza túl rövid.', 'tooLong' => 'A cím hossza túl hosszú.'],
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'verifyCode' => 'Verification Code',
        ];
    }

    /**
     * This function creates a new Ticket under the currently logged in User's ID
     * @return Ticket The newly created Ticket
     * @throws Exception
     */
    public function createNewTicket(): Ticket
    {
        $ticket = new Ticket();
        $ticket->customer_id = Yii::$app->user->id;
        $ticket->title = Html::encode($this->title);
        $ticket->is_closed = false;
        $ticket->closed_by = Ticket::DEFAULT_VALUE;
        $ticket->admin_id = Ticket::DEFAULT_VALUE;
        $ticket->status = Ticket::STATUS_ACTIVE;

        // Set dates
        $ticket->last_comment_at = date('m/d/Y h:i:s a', time());
        $ticket->created_at = date('m/d/Y h:i:s a', time());
        $ticket->updated_at = date('m/d/Y h:i:s a', time());

        // Attempt to save Ticket
        if (!$ticket->save()) {
            throw new Exception('Failed to save ticket: ' . json_encode($ticket->attributes) . json_encode($ticket->getErrors()));
        }

        return $ticket;
    }

}
