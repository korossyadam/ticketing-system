<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "comment".
 *
 * @property int $id
 * @property int $customer_id
 * @property int $ticket_id
 * @property string $text
 * @property string $img_url
 * @property boolean $closed_ticket
 * @property boolean $reopened_ticket
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property User $customer
 * @property Ticket $ticket
 */
class Comment extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;
    const DEFAULT_VALUE = -1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comment';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            //TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'ticket_id', 'text'], 'required'],
            [['customer_id', 'ticket_id', 'status', 'created_at', 'updated_at'], 'default', 'value' => null],
            [['customer_id', 'ticket_id', 'status'], 'integer'],
            [['text'], 'string', 'max' => 255],
            [['closed_ticket', 'reopened_ticket'], 'boolean'],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::className(), 'targetAttribute' => ['ticket_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Customer ID'),
            'ticket_id' => Yii::t('app', 'Ticket ID'),
            'text' => Yii::t('app', 'Text'),
            'img_url' => Yii::t('app', 'Image URL'),
            'closed_ticket' => Yii::t('app', 'Closed Ticket'),
            'reopened_ticket' => Yii::t('app', 'Reopened Ticket'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery|TicketQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }

    /**
     * {@inheritdoc}
     * @return CommentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CommentQuery(get_called_class());
    }
}
