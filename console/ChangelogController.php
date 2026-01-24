<?php

namespace app\console;

use app\models\Changelog;
use app\models\User;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use yii\helpers\Json;

/**
 * Console command for managing and inspecting the changelog.
 */
class ChangelogController extends Controller
{
    /**
     * Inspect changes since a given date.
     * @param string $since Date in format YYYY-MM-DD (optionally with time)
     */
    public function actionInspect($since)
    {
        $query = Changelog::find()
            ->where(['>=', 'changed_at', $since])
            ->orderBy(['changed_at' => SORT_DESC]);

        $count = $query->count();
        if ($count == 0) {
            $this->stdout("No changes found since $since.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $this->stdout("Found $count changes since $since:\n\n", Console::BOLD);

        foreach ($query->each() as $log) {
            $this->renderLog($log);
        }

        return ExitCode::OK;
    }

    /**
     * Find all changes by a given user.
     * @param int $userId ID of the user
     */
    public function actionByUser($userId)
    {
        $user = User::findOne($userId);
        if (!$user) {
            $this->stderr("User with ID $userId not found.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $query = Changelog::find()
            ->where(['changed_by' => $userId])
            ->orderBy(['changed_at' => SORT_DESC]);

        $count = $query->count();
        $this->stdout("Found $count changes by user {$user->full_name} (ID: $userId):\n\n", Console::BOLD);

        foreach ($query->each() as $log) {
            $this->renderLog($log);
        }

        return ExitCode::OK;
    }

    /**
     * Prune the changelog before a given date.
     * @param string|null $before Date in format YYYY-MM-DD. Defaults to a year ago.
     */
    public function actionPrune($before = null)
    {
        if ($before === null) {
            $before = date('Y-m-d', strtotime('-1 year'));
        }

        $count = Changelog::find()
            ->where(['<', 'changed_at', $before])
            ->count();

        if ($count == 0) {
            $this->stdout("No records found to prune before $before.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        if ($this->confirm("Are you sure you want to delete $count changelog entries created before $before?")) {
            $deleted = Changelog::deleteAll(['<', 'changed_at', $before]);
            $this->stdout("Successfully deleted $deleted entries.\n", Console::FG_GREEN);
        } else {
            $this->stdout("Pruning cancelled.\n", Console::FG_YELLOW);
        }

        return ExitCode::OK;
    }

    /**
     * Helper to render a single log entry.
     */
    protected function renderLog(Changelog $log)
    {
        $userStr = $log->changed_by ? "User #$log->changed_by" : "System/Guest";
        $this->stdout("[$log->changed_at] $log->model_class (ID: $log->model_id) by $userStr\n", Console::FG_CYAN);
        
        foreach ($log->changes as $attribute => $values) {
            $old = is_scalar($values['old']) ? $values['old'] : Json::encode($values['old']);
            $new = is_scalar($values['new']) ? $values['new'] : Json::encode($values['new']);
            
            $this->stdout("  - $attribute: ", Console::FG_YELLOW);
            $this->stdout("$old", Console::FG_RED);
            $this->stdout(" -> ");
            $this->stdout("$new\n", Console::FG_GREEN);
        }
        $this->stdout("\n");
    }
}
