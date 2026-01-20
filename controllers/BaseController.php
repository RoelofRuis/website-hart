<?php

namespace app\controllers;

use app\models\Teacher;
use app\models\User;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

abstract class BaseController extends Controller
{
    /**
     * @throws NotFoundHttpException if the currently logged-in user is not a teacher.
     * return array [User, Teacher|null]
     */
    protected function getIdentityData($allow_non_teacher = false): array
    {
        /** @var User $current */
        $current = Yii::$app->user->identity;
        if (!$current instanceof User) {
            throw new NotFoundHttpException('Not allowed.');
        }

        $teacher = $current->getTeacher()->one();
        if (!$allow_non_teacher && !$teacher instanceof Teacher) {
            throw new NotFoundHttpException('Not allowed.');
        }

        return [$current, $teacher];
    }
}