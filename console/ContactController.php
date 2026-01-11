<?php

namespace app\console;

use app\models\ContactMessage;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Manage various contact message-related tasks.
 */
class ContactController extends Controller
{
    /**
     * List contact message recipients.
     *
     * This action displays contact messages and their assigned receivers.
     *
     * @param string|null $since Filter messages created after this date/time.
     *                           Format: 'YYYY-MM-DD HH:MM:SS' or any format accepted by the database.
     *                           Example: '2024-01-01 00:00:00' or '2024-01-01'
     *                           Leave empty to show all messages.
     * @param bool $noReceivers Set to 1 or true to show only messages without any assigned receivers.
     *                          Default is false (shows all messages).
     *                          Example usage: ./yii contact/recipients --noReceivers=1
     * @param bool $fullMessage Set to 1 or true to show full message body. Default is false (shows only message subject).
     */
    public function actionRecipients($since = null, $noReceivers = false, $fullMessage = false)
    {
        $query = ContactMessage::find();

        if ($since !== null) {
            $query->andWhere(['>=', 'created_at', $since]);
        }

        if ($noReceivers) {
            $query->leftJoin('{{%contact_message_user}} cmu', 'cmu.contact_message_id = {{%contact_message}}.id')
                ->andWhere(['cmu.contact_message_id' => null]);
        }

        /** @var ContactMessage $message */
        foreach ($query->all() as $message) {
            $receivers = $message->getUsers()->select(['full_name'])->column();

            $this->stdout("From: $message->name\n", Console::FG_GREY);

            if (empty($receivers)) {
                $this->stdout("To:   (no receivers)\n", Console::FG_RED);
            } else {
                $this->stdout("To:   " . implode(', ', $receivers) . "\n", Console::FG_GREEN);
            }

            if ($fullMessage) {
                $this->stdout("Sender Email: $message->email\n", Console::FG_CYAN);
                $this->stdout("Message Body: $message->message\n", Console::FG_CYAN);
            }

            $this->stdout("\n");
        }
    }
}