<?php

namespace app\console;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

/**
 * Console controller for testing email configuration.
 */
class MailController extends Controller
{
    /**
     * Sends a test email to the specified address.
     * @param string $to Recipient email address.
     * @return int Exit code.
     */
    public function actionTest($to)
    {
        if (empty($to)) {
            $this->stderr("Error: Recipient email address is required.\n", Console::FG_RED);
            $this->stdout("Usage: php yii mail/test <email>\n");
            return ExitCode::USAGE;
        }

        $this->stdout("Sending test email to: $to...\n", Console::FG_CYAN);

        $adminEmail = Yii::$app->params['adminEmail'] ?? 'no-reply@example.com';
        $appName = Yii::$app->name ?? 'Yii2 App';

        try {
            $message = Yii::$app->mailer->compose()
                ->setTo($to)
                ->setFrom([$adminEmail => $appName])
                ->setSubject("Test Email from $appName")
                ->setTextBody("This is a test email to verify the mailing setup for $appName.")
                ->setHtmlBody("<p>This is a test email to verify the mailing setup for <b>$appName</b>.</p>");

            if ($message->send()) {
                $this->stdout("Success: Test email has been sent successfully!\n", Console::FG_GREEN);
                return ExitCode::OK;
            } else {
                $this->stderr("Error: Failed to send test email.\n", Console::FG_RED);
                return ExitCode::UNSPECIFIED_ERROR;
            }
        } catch (\Exception $e) {
            $this->stderr("Exception: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
