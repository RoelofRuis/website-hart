<?php

namespace app\controllers;

use app\models\ContactMessage;
use app\models\Course;
use app\models\Teacher;
use app\models\StaticContent;
use app\models\User;
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
                'only' => ['admin', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $q = Yii::$app->request->get('q');

        $query = Course::find()
            ->where(['is_taught' => true]);

        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'name', $q],
                ['ILIKE', 'description', $q],
                ['ILIKE', 'summary', $q],
            ])
                ->orderBy(new Expression("CASE WHEN name ILIKE :qprefix THEN 0 WHEN name LIKE :qany THEN 1 ELSE 2 END, name ASC"))
                ->addParams([
                    ':qprefix' => $q . '%',
                    ':qany' => '%' . $q . '%',
                ]);
        } else {
            $query->orderBy(['name' => SORT_ASC]);
        }

        $courses = $query->all();
        $staticContent = StaticContent::findByKey('courses-index');
        return $this->render('index', [
            'courses' => $courses,
            'q' => $q,
            'staticContent' => $staticContent,
        ]);
    }

    public function actionView($slug = null)
    {
        $model = Course::findBySlug($slug);

        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        $contact = new ContactMessage();

        if ($contact->load(Yii::$app->request->post()) && $contact->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Thanks! Your signup has been received.'));
            return $this->refresh();
        }

        $parent_model = null; // TODO: remove

        return $this->render('view', [
            'model' => $model,
            'parent_model' => $parent_model,
            'contact' => $contact,
            'teachers' => $model->getTeachers()->all(),
        ]);
    }

    public function actionAdmin()
    {
        /** @var User $current */
        $current = Yii::$app->user->identity;
        if ($current === null) {
            throw new NotFoundHttpException('Not allowed.');
        }

        // Admins see all courses; teachers see only their linked courses
        $query = $current->is_admin
            ? Course::find()
            : $current->getAccessibleCourses(); // TODO: fix this query

        // Eager-load related data used in the grid to avoid N+1 queries
        $dataProvider = new ActiveDataProvider([
            'query' => $query->with(['teachers', 'lessonFormats'])->orderBy(['name' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Course();
        $request = Yii::$app->request;

        if ($model->load($request->post()) && $model->save()) {
            // Only admins can assign teachers during create
            /** @var User $current */
            $current = Yii::$app->user->identity;
            if ($current && $current->is_admin) {
                $teacherIds = $request->post('teacherIds', []);
                $this->syncCourseTeachers($model->id, $teacherIds);
            }

            Yii::$app->session->setFlash('success', Yii::t('app', 'Course created successfully.'));
            return $this->redirect(['admin']);
        }

        $assigned = (array)$request->post('teacherIds', []);

        return $this->render('create', [
            'model' => $model,
            'assignedTeacherIds' => $assigned,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = Course::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        /** @var User $current */
        $current = Yii::$app->user->identity;
        $isAdmin = $current && $current->is_admin;

        if (!$isAdmin) {
            // Only teachers linked to this course can edit it (limited fields)
            if (!$current instanceof User) {
                throw new NotFoundHttpException('Not allowed.');
            }
            $linkedTeacherIds = $model->getTeachers()->select('id')->column();
            if (!in_array($current->id, $linkedTeacherIds, true)) { // TODO: fix this check!
                throw new NotFoundHttpException('Not allowed.');
            }
            // Limit editable attributes for teachers
            $model->setScenario(Course::SCENARIO_TEACHER_UPDATE);
        }

        $request = Yii::$app->request;
        if ($model->load($request->post()) && $model->save()) {
            if ($isAdmin) {
                $teacherIds = $request->post('teacherIds', []);
                $this->syncCourseTeachers($model->id, $teacherIds);
            }

            Yii::$app->session->setFlash('success', Yii::t('app', 'Course updated successfully.'));
            return $this->redirect(['admin']);
        }

        // If form posted but validation failed, keep posted teacherIds for preselection
        $assigned = $isAdmin
            ? (array)$request->post('teacherIds', $model->getTeachers()->select('id')->column())
            : $model->getTeachers()->select('id')->column();

        return $this->render('update', [
            'model' => $model,
            'assignedTeacherIds' => $assigned,
        ]);
    }

    /**
     * Synchronize teacher relations for a course using the pivot table course_teacher.
     * Only integer IDs are considered; non-existing teachers are ignored by FK constraints.
     */
    private function syncCourseTeachers(int $courseId, array $teacherIds): void
    {
        // Normalize and de-duplicate IDs
        $teacherIds = array_values(array_unique(array_map('intval', $teacherIds)));

        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            // Remove existing links
            $db->createCommand()
                ->delete('{{%course_teacher}}', ['course_id' => $courseId])
                ->execute();

            // Insert new links
            if (!empty($teacherIds)) {
                $rows = [];
                foreach ($teacherIds as $tid) {
                    if ($tid > 0) {
                        $rows[] = [$courseId, $tid];
                    }
                }
                if (!empty($rows)) {
                    $db->createCommand()
                        ->batchInsert('{{%course_teacher}}', ['course_id', 'teacher_id'], $rows)
                        ->execute();
                }
            }

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error('Failed to sync course teachers: ' . $e->getMessage(), __METHOD__);
            // Re-throw to let the controller show an error if needed
            throw $e;
        }
    }

    public function actionDelete(int $id)
    {
        $model = Course::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Course deleted.'));
        return $this->redirect(['admin']);
    }
}
