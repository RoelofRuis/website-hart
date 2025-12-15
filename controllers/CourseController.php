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

        $query = CourseNode::find()
            ->where(['is_taught' => true]);

        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'name', $q],
                ['ILIKE', 'description', $q],
                ['ILIKE', 'short_description', $q],
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
            'contact' => $contact,
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Course updated successfully.'));
            if ($isAdmin) {
                return $this->redirect(['admin']);
            }
            return $this->redirect(['course/view', 'slug' => $model->slug]);
        }

        $assigned = $model->getTeachers()->select('id')->column();

        return $this->render('update', [
            'model' => $model,
            'assignedTeacherIds' => $assigned,
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
