<?php

namespace common\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class UserSearch extends User
{
    public function rules()
    {
        return [
            [['id'], 'integer', 'message' => 'Az ID csak szám lehet'],
            [['created_at', 'updated_at', 'last_login'], 'match', 'pattern' => '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', 'message' => 'Formátum: 2022-05-10'],
            [['username', 'email', 'created_at', 'updated_at', 'last_login', 'is_admin'], 'safe'],
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
        $query = User::find()->orderBy(["username" => SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Return dataProvider if not searching
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        // Adjust the query by adding filters
        $query->andFilterWhere(["customer.id" => $this->id])
            ->andFilterWhere(["ilike", "username", $this->username])
            ->andFilterWhere(["ilike", "email", $this->email])
            ->andFilterWhere(["between", "customer.created_at", $this->extendDate($this->created_at, true), $this->extendDate($this->created_at, false)])
            ->andFilterWhere(["between", "customer.updated_at", $this->extendDate($this->updated_at, true), $this->extendDate($this->updated_at, false)])
            ->andFilterWhere(["between", "customer.last_login", $this->extendDate($this->last_login, true), $this->extendDate($this->last_login, false)])
            ->andFilterWhere(["is_admin" => $this->mapStringToBoolean($this->is_admin)]);

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
            return true;
        } else {
            return false;
        }
    }
}