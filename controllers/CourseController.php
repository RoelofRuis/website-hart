<?php

namespace app\controllers;

use app\models\Course;
use app\models\CourseSignup;
use app\models\Teacher;
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
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->admin;
                        },
                    ],
                ],
            ],
        ];
    }
    public function actionIndex()
    {
        $q = Yii::$app->request->get('q');

        $query = Course::find();
        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'name', $q],
                ['ILIKE', 'description', $q],
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
        $model = Course::findBySlug($slug);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        $signup = new CourseSignup();
        $signup->course_id = $model->id;

        if ($signup->load(Yii::$app->request->post()) && $signup->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Thanks! Your signup has been received.'));
            return $this->refresh();
        }

        return $this->render('view', [
            'model' => $model,
            'signup' => $signup,
        ]);
    }

    public function actionAdmin()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Course::find()->orderBy(['name' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Course();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->syncCourseTeachers($model);
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
        $model = Course::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $this->syncCourseTeachers($model);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Course updated successfully.'));
            return $this->redirect(['admin']);
        }

        $assigned = $model->getTeachers()->select('id')->column();
        return $this->render('update', [
            'model' => $model,
            'assignedTeacherIds' => $assigned,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = Course::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Course not found.');
        }
        // Remove relations
        Yii::$app->db->createCommand()
            ->delete('{{%teacher_courses}}', ['course_id' => $model->id])
            ->execute();
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Course deleted.'));
        return $this->redirect(['admin']);
    }

    private function syncCourseTeachers(Course $model): void
    {
        $ids = Yii::$app->request->post('teacherIds', []);
        if (!is_array($ids)) {
            $ids = [];
        }
        // sanitize to integers and ensure they exist
        $ids = array_values(array_unique(array_map('intval', $ids)));
        if ($ids) {
            $validIds = Teacher::find()->select('id')->where(['id' => $ids])->column();
        } else {
            $validIds = [];
        }
        $db = Yii::$app->db;
        $db->createCommand()->delete('{{%teacher_courses}}', ['course_id' => $model->id])->execute();
        if (!empty($validIds)) {
            $rows = [];
            foreach ($validIds as $tid) {
                $rows[] = [$model->id, $tid];
            }
            $db->createCommand()->batchInsert('{{%teacher_courses}}', ['course_id', 'teacher_id'], $rows)->execute();
        }
    }
}
