<?php

namespace app\console;

use app\models\User;
use Yii;
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

    /**
     * Create a new user.
     * @param string $fullName The full name of the user.
     * @param string $email The email of the user.
     * @param int $isAdmin Whether the user is an admin (0 or 1).
     */
    public function actionCreate($fullName, $email, $isAdmin = 0)
    {
        $password = Yii::$app->security->generateRandomString(12);

        $user = new User();
        $user->full_name = $fullName;
        $user->email = $email;
        $user->is_admin = (bool)$isAdmin;
        $user->is_active = true;
        $user->setPassword($password);
        $user->generateAuthKey();

        if ($user->save()) {
            $this->stdout("User created successfully!\n", Console::FG_GREEN);
            $this->stdout("ID: ", Console::BOLD);
            $this->stdout($user->id . "\n");
            $this->stdout("Email: ", Console::BOLD);
            $this->stdout($user->email . "\n");
            $this->stdout("Password: ", Console::BOLD);
            $this->stdout($password . "\n", Console::FG_CYAN);
            $this->stdout("\nIMPORTANT: Store this password safely as it will not be shown again.\n", Console::FG_YELLOW);
            return ExitCode::OK;
        } else {
            $this->stderr("Failed to create user:\n", Console::FG_RED);
            foreach ($user->getErrors() as $attribute => $errors) {
                foreach ($errors as $error) {
                    $this->stderr("- $attribute: $error\n");
                }
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Activate a user.
     * @param int $id The user ID.
     */
    public function actionActivate($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            $this->stderr("User with ID $id not found.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user->is_active = true;
        if ($user->save(false)) {
            $this->stdout("User $user->full_name (ID: $id) has been activated.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stderr("Failed to activate user.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Deactivate a user.
     * @param int $id The user ID.
     */
    public function actionDeactivate($id)
    {
        $user = User::findOne($id);
        if (!$user) {
            $this->stderr("User with ID $id not found.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user->is_active = false;
        if ($user->save(false)) {
            $this->stdout("User $user->full_name (ID: $id) has been deactivated.\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stderr("Failed to deactivate user.\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
