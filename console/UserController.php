<?php

namespace app\console;

use app\models\User;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Console controller for managing users.
 */
class UserController extends Controller
{
    /**
     * Search users by (partial) name.
     * @param string|null $name The name or part of the name to search for.
     */
    public function actionSearch($name = null)
    {
        $query = User::find();
        if ($name) {
            $query->where(['like', 'full_name', $name]);
        }

        $users = $query->orderBy(['id' => SORT_ASC])->all();

        if (empty($users)) {
            $this->stdout("No users found.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        }

        $this->stdout(str_pad('ID', 5) . str_pad('Full Name', 30) . str_pad('Email', 30) . "Status\n", Console::BOLD);
        $this->stdout(str_repeat('-', 75) . "\n");

        foreach ($users as $user) {
            $status = $user->is_active ? 'Active' : 'Inactive';
            $statusColor = $user->is_active ? Console::FG_GREEN : Console::FG_RED;

            $this->stdout(str_pad($user->id, 5));
            $this->stdout(str_pad($user->full_name, 30));
            $this->stdout(str_pad($user->email, 30));
            $this->stdout($status . "\n", $statusColor);
        }

        return ExitCode::OK;
    }
}
