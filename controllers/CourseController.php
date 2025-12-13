<?php

namespace app\controllers;

use app\models\ContactMessage;
use app\models\CourseNode;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class CourseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                // Keep admin-only restrictions on create and delete.
                // The update action is handled with fine-grained checks inside actionUpdate,
                // and actionAdmin will filter results for non-admin teachers.
                'only' => ['create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin;
                        },
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $q = Yii::$app->request->get('q');

        $query = CourseNode::find();
        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'name', $q],
                ['ILIKE', 'description', $q],
                ['ILIKE', 'short_description', $q],
            ]);
            // Prefer name matches over description matches (and prefix matches first)
            $query->orderBy(new Expression(
                "CASE WHEN name ILIKE :qprefix THEN 0 WHEN name LIKE :qany THEN 1 ELSE 2 END, name ASC"
            ))
            ->addParams([
                ':qprefix' => $q . '%',
                ':qany' => '%' . $q . '%',
            ]);
        } else {
            $query->orderBy(['name' => SORT_ASC]);
        }

        $courses = $query->all();
        return $this->render('index', [
            'courses' => $courses,
            'q' => $q,
        ]);
    }

    public function actionView($slug = null)
    {
        $model = CourseNode::findBySlug($slug);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        $contact = new ContactMessage();

        if ($contact->load(Yii::$app->request->post()) && $contact->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Thanks! Your signup has been received.'));
            return $this->refresh();
        }

        return $this->render('view', [
            'model' => $model,
            'signup' => $contact,
        ]);
    }

    public function actionAdmin()
    {
        $current = Yii::$app->user->identity;
        if ($current === null) {
            throw new NotFoundHttpException('Not allowed.');
        }

        // Admins see all courses; teachers see only their linked courses
        $query = $current->is_admin
            ? CourseNode::find()
            : $current->getTaughtCourses();

        $dataProvider = new ActiveDataProvider([
            'query' => $query->orderBy(['name' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new CourseNode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Course created successfully.'));
            return $this->redirect(['admin']);
        }

        return $this->render('create', [
            'model' => $model,
            'assignedTeacherIds' => [],
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = CourseNode::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        $current = Yii::$app->user->identity;
        $isAdmin = $current && $current->is_admin;

        if (!$isAdmin) {
            // Only teachers linked to this course can edit it (limited fields)
            if (!$current instanceof \app\models\Teacher) {
                throw new NotFoundHttpException('Not allowed.');
            }
            $linkedTeacherIds = $model->getTeachers()->select('id')->column();
            if (!in_array($current->id, $linkedTeacherIds, true)) {
                throw new NotFoundHttpException('Not allowed.');
            }
            // Limit editable attributes for teachers
            $model->setScenario(\app\models\CourseNode::SCENARIO_TEACHER_UPDATE);
        }

        // Prepare editable lesson formats for the subform
        $editableFormatsQuery = $model->getLessonFormats();
        if (!$isAdmin && $current instanceof \app\models\Teacher) {
            $editableFormatsQuery->andWhere(['teacher_id' => $current->id]);
        }
        $editableFormats = $editableFormatsQuery->all();

        // Handle POST: course + nested lesson formats
        if ($model->load(Yii::$app->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if (!$model->save()) {
                    throw new \RuntimeException('Course save failed');
                }

                $posted = Yii::$app->request->post('LessonFormats', []);
                $editableById = [];
                foreach ($editableFormats as $fmt) {
                    $editableById[$fmt->id] = $fmt;
                }

                // Process updates/deletes for existing formats
                foreach ($posted as $key => $row) {
                    $id = isset($row['id']) && $row['id'] !== '' ? (int)$row['id'] : null;
                    $toDelete = isset($row['__delete']) && (int)$row['__delete'] === 1;
                    if ($id && isset($editableById[$id])) {
                        $fmt = $editableById[$id];
                        if ($toDelete) {
                            if (!$fmt->delete()) {
                                throw new \RuntimeException('Delete failed');
                            }
                            continue;
                        }
                        // Lock course_id for all, and teacher_id for non-admin in beforeValidate
                        $fmt->load(['LessonFormat' => $row]);
                        // Ensure course stays the same
                        $fmt->course_id = $model->id;
                        if (!$isAdmin) {
                            $fmt->teacher_id = $current->id;
                        }
                        if (!$fmt->save()) {
                            throw new \RuntimeException('Lesson format update failed');
                        }
                    }
                }

                // Handle creation of new rows under the "new" key
                $newRows = Yii::$app->request->post('NewLessonFormats', []);
                foreach ($newRows as $row) {
                    // Skip entirely empty rows
                    $hasData = false;
                    foreach ($row as $k => $v) {
                        if ($v !== '' && $v !== null && $k !== 'id') { $hasData = true; break; }
                    }
                    if (!$hasData) { continue; }
                    $fmt = new \app\models\LessonFormat();
                    $fmt->course_id = $model->id;
                    if ($isAdmin) {
                        // Admin may set teacher_id; if omitted, keep null which will fail validation and show errors
                        $fmt->teacher_id = isset($row['teacher_id']) && $row['teacher_id'] !== '' ? (int)$row['teacher_id'] : null;
                    } else {
                        $fmt->teacher_id = $current->id;
                    }
                    $fmt->load(['LessonFormat' => $row]);
                    if (!$fmt->save()) {
                        throw new \RuntimeException('Lesson format create failed');
                    }
                }

                $transaction->commit();

                Yii::$app->session->setFlash('success', Yii::t('app', 'Course updated successfully.'));
                if ($isAdmin) {
                    return $this->redirect(['admin']);
                }
                return $this->redirect(['course/view', 'slug' => $model->slug]);
            } catch (\Throwable $e) {
                $transaction->rollBack();
            }
        }

        $assigned = $model->getTeachers()->select('id')->column();
        return $this->render('update', [
            'model' => $model,
            'assignedTeacherIds' => $assigned,
            'editableLessonFormats' => $editableFormats,
            'canEditAllFormats' => $isAdmin,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = CourseNode::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Course deleted.'));
        return $this->redirect(['admin']);
    }
}
