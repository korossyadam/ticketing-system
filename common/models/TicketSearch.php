<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TicketSearch extends Ticket
{
    public $customerEmail;
    public $adminEmail;

    public function rules()
    {
        return [
            [['id'], 'integer', 'message' => 'Az ID csak szám lehet'],
            [['created_at', 'last_comment_at'], 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Formátum: 2022-05-10'],
            [['is_closed', 'customerEmail', 'title', 'created_at', 'last_comment_at', 'adminEmail'], 'safe'],
        ];
    }

    /**
     * This function bypasses scenarios() implementation in the parent class
     * @return array|array[]
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * This function creates an ActiveDataProvider with filters
     * Only uses filters that are not empty
     * @param $params
     * @return ActiveDataProvider
     */
    public function search($params): ActiveDataProvider
    {
        // Create dataProvider
        $query = Ticket::find()->orderBy(["is_closed" => SORT_ASC, "updated_at" => SORT_DESC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Join with relations
        $query->joinWith("customer AS customer");
        $query->joinWith("admin AS admin");

        // Enable sorting for customer.email
        $dataProvider->sort->attributes["customer.email"] = [
            'asc' => ["customer.email" => SORT_ASC],
            'desc' => ["customer.email" => SORT_DESC],
        ];

        // Enable sorting for admin.email
        $dataProvider->sort->attributes["admin.email"] = [
            'asc' => ["admin.email" => SORT_ASC],
            'desc' => ["admin.email" => SORT_DESC],
        ];

        // Return dataProvider if not searching
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // Adjust the query by adding filters
        $query->andFilterWhere(["is_closed" => $this->mapStringToBoolean($this->is_closed)])
            ->andFilterWhere(["ticket.id" => $this->id])
            ->andFilterWhere(["ilike", "customer.email", $this->customerEmail])
            ->andFilterWhere(["ilike", "title", $this->title])
            ->andFilterWhere(["between", "ticket.created_at", $this->extendDate($this->created_at, true), $this->extendDate($this->created_at, false), false])
            ->andFilterWhere(["between", "ticket.last_comment_at", $this->extendDate($this->last_comment_at, true), $this->extendDate($this->last_comment_at, false)]);

        // Add additional filter for admin.email with special case of searching for 'nincs' or 'Nincs'
        $adminField = $this->adminEmail;
        if ($adminField === 'nincs' || $adminField === 'Nincs') {
            $query->andWhere(["=", "admin_id", Ticket::DEFAULT_VALUE]);
        } else {
            $query->andFilterWhere(["ilike", "admin.email", $adminField]);
        }

        return $dataProvider;
    }

    /**
     * This function converts a user input to a 24-hour date range
     * @param string $str The user input, ex.: 2022-05-09
     * @param bool $lowerLimit Whether to return the floor or the ceiling of the range
     * @return string The floor or the ceiling of the range
     */
    public function extendDate(string $str, bool $lowerLimit): string
    {
        if ($str === '')
            return $str;

        if ($lowerLimit) {
            return explode(' ', $str)[0];
        } else {
            return explode(' ', $str)[0] . ' 23:59:59';
        }
    }

    /**
     * This function is used as a 3-state variable
     * In filtering it either returns a boolean or a null, the latter meaning 'both'
     * @param string $str Either '0', '1', or '2'
     * @return bool|null If returns null, no filter is applied
     */
    public function mapStringToBoolean(string $str): ?bool
    {
        if ($str === '0') {
            return null;
        } else if ($str === '1') {
            return false;
        } else {
            return true;
        }
    }
}