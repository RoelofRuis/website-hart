<?php

namespace app\controllers;

use app\models\CourseNode;
use app\models\LessonFormat;
use app\models\Teacher;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class LessonFormatController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['admin', 'create', 'update', 'delete', 'copy'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAdmin()
    {
        /** @var Teacher $current */
        $current = Yii::$app->user->identity;

        if (!$current) {
            throw new NotFoundHttpException('Not allowed.');
        }

        $formats = $current->getLessonFormats()->with('course')->all();
        $linked_courses = $current->getAccessibleCourses()->orderBy(['name' => SORT_ASC])->all();

        return $this->render('admin', [
            'formats' => $formats,
            'linkedCourses' => $linked_courses,
        ]);
    }

    public function actionCreate(int $course_id)
    {
        $course = CourseNode::findOne($course_id);
        if (!$course) {
            throw new NotFoundHttpException('Course not found.');
        }
        $current = Yii::$app->user->identity;
        if (!$current instanceof Teacher) {
            throw new NotFoundHttpException('Not allowed.');
        }
        // Allow admins, or teachers already linked to this course, to add a lesson option.
        // Linking teachers to courses is admin-only; teachers cannot self-link by creating a format.
        $isLinked = $course->getLessonFormats()->andWhere(['teacher_id' => $current->id])->exists();
        $allowed = $current->is_admin || $isLinked;
        if (!$allowed) {
            throw new NotFoundHttpException('Not allowed.');
        }

        $model = new LessonFormat([
            'course_id' => $course->id,
            'teacher_id' => $current->id,
            'price_display_type' => LessonFormat::PRICE_DISPLAY_PER_PERSON,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if (!$current->is_admin) {
                // Prevent spoofing by non-admins
                $model->course_id = $course->id;
                $model->teacher_id = $current->id;
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Lesson option created.'));
                return $this->redirect(['lesson-format/admin']);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'course' => $course,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = LessonFormat::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Lesson format not found.');
        }
        $current = Yii::$app->user->identity;
        if (!$current instanceof Teacher) {
            throw new NotFoundHttpException('Not allowed.');
        }
        $allowed = $current->is_admin || $model->teacher_id === $current->id;
        if (!$allowed) {
            throw new NotFoundHttpException('Not allowed.');
        }

        if ($model->load(Yii::$app->request->post())) {
            if (!$current->is_admin) {
                // Lock ownership fields for non-admins
                $model->teacher_id = $current->id;
                // Do not allow changing course
                $model->course_id = $model->getOldAttribute('course_id');
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Lesson option updated.'));
                return $this->redirect(['lesson-format/admin']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'course' => $model->course,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = LessonFormat::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Lesson format not found.');
        }
        $current = Yii::$app->user->identity;
        if (!$current instanceof Teacher) {
            throw new NotFoundHttpException('Not allowed.');
        }
        $allowed = $current->is_admin || $model->teacher_id === $current->id;
        if (!$allowed) {
            throw new NotFoundHttpException('Not allowed.');
        }
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Lesson option deleted.'));
        return $this->redirect(['lesson-format/admin']);
    }

    public function actionCopy(int $id)
    {
        $source = LessonFormat::findOne($id);
        if (!$source) {
            throw new NotFoundHttpException('Lesson format not found.');
        }

        $current = Yii::$app->user->identity;
        if (!$current instanceof Teacher) {
            throw new NotFoundHttpException('Not allowed.');
        }

        $allowed = $source->teacher_id === $current->id;
        if (!$allowed) {
            throw new NotFoundHttpException('Not allowed.');
        }

        $attrs = $source->getAttributes(null, ['id']); // all safe attributes except id
        $model = new LessonFormat();
        $model->setAttributes($attrs, false);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Lesson option copied.'));
            return $this->redirect(['lesson-format/admin']);
        }

        return $this->render('create', [
            'model' => $model,
            'course' => $model->course ?? $source->course,
        ]);
    }
}
