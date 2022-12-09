<?php

namespace console\controllers;

use common\models\Ticket;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\BaseConsole;

class MaintenanceController extends Controller
{
    public $message;

    public function options($actionID)
    {
        return ['message'];
    }

    public function optionAliases()
    {
        return ['m' => 'message'];
    }

    /**
     * This function closes all Tickets that are:
     *  - opened
     *  - it's last Comment was created by an admin
     *  - the User's last Comment is more than 2 weeks old
     * Run this function from the console, with the './yii maintenance' command
     * @return int ExitCode
     */
    public function actionIndex()
    {
        // Colored console constants
        $OK = $this->ansiFormat('[OK]', BaseConsole::FG_GREEN);
        $CLOSED = $this->ansiFormat('[CLOSED]', BaseConsole::FG_YELLOW);
        $ERROR = $this->ansiFormat('[ERROR]', BaseConsole::FG_RED);

        echo "Query for all Tickets..";
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("
            SELECT t.id FROM ticket t
            LEFT JOIN comment c
            ON c.ticket_id = t.id
            LEFT JOIN comment last_c
            ON last_c.ticket_id = t.id AND last_c.created_at > c.created_at
            LEFT JOIN customer cu
            ON cu.id = c.customer_id AND cu.is_admin = true    
            WHERE last_c.id IS NULL AND c.created_at < NOW() - INTERVAL '2 weeks' AND t.is_closed = false
        ;");

        // Get all Tickets by previous query results
        $result = $command->queryAll();
        $tickets = Ticket::find()->where(["id" => $result])->all();
        echo $OK . "\n";

        echo "Updating Tickets..";
        $modified = 0;
        try {
            $modified = Ticket::updateAll([
                "is_closed" => true,
            ], ["id" => $result]);
            echo $OK;
        } catch (\Exception $e) {
            echo $ERROR;
        }

        echo "\nNumber of Tickets modified: " . $modified . "\n";

        return ExitCode::OK;
    }
}
