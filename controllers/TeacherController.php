<?php

namespace app\controllers;

use app\models\Teacher;
use app\models\ContactMessage;
use app\models\forms\ContactForm;
use Yii;
use yii\db\Expression;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\data\ActiveDataProvider;

class TeacherController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['update', 'admin', 'create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['admin', 'create', 'delete'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->is_admin;
                        }
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $q = Yii::$app->request->get('q');

        $query = Teacher::find();
        if ($q !== null && $q !== '') {
            $query->andFilterWhere(['or',
                ['ILIKE', 'full_name', $q],
                ['ILIKE', 'description', $q],
            ]);
            // Prefer name matches over description matches
            $query->orderBy(new Expression(
                "CASE WHEN full_name ILIKE :qprefix THEN 0 WHEN full_name ILIKE :qany THEN 1 ELSE 2 END, full_name ASC",
            ))
                ->addParams([
                    ':qprefix' => $q . '%',
                    ':qany' => '%' . $q . '%',
                ]);
        } else {
            $query->orderBy(['full_name' => SORT_ASC]);
        }

        $teachers = $query->all();
        return $this->render('index', [
            'teachers' => $teachers,
            'q' => $q,
        ]);
    }

    public function actionView(string $slug)
    {
        $model = Teacher::findBySlug($slug);
        if (!$model) {
            throw new NotFoundHttpException('Teacher not found.');
        }
        $contactForm = new ContactForm();
        // Pre-fill teacher relation so messages can be associated to the teacher profile
        $contactForm->teacher_id = $model->id;
        if ($contactForm->load(Yii::$app->request->post()) && $contactForm->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Thank you for your message. We will get back to you soon.'));
            return $this->redirect(['view', 'slug' => $model->slug]);
        }

        return $this->render('view', [
            'model' => $model,
            'contactForm' => $contactForm,
        ]);
    }

    public function actionUpdate(int $id)
    {
        $model = Teacher::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        $canEdit = $current->is_admin || ($current->id === $model->id);
        if (!$canEdit) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        // Restrict editable attributes for security
        $safeAttributes = ['full_name', 'email', 'telephone', 'profile_picture', 'description'];
        if ($current->is_admin) {
            // Admins may also toggle admin/active flags
            $safeAttributes[] = 'slug';
            $safeAttributes[] = 'is_admin';
            $safeAttributes[] = 'is_active';
        }

        if ($model->load(Yii::$app->request->post())) {
            // Prevent privilege escalation by non-admins
            if (!$current->is_admin) {
                $model->is_admin = (bool)$model->getOldAttribute('is_admin');
                $model->is_active = (bool)$model->getOldAttribute('is_active');
                // Non-admins are not allowed to change their slug
                $model->slug = (string)$model->getOldAttribute('slug');
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Teacher information updated successfully.');
                return $this->redirect(['admin']);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'safeAttributes' => $safeAttributes,
        ]);
    }

    public function actionAdmin()
    {
        // Admin overview list for quick management
        $dataProvider = new ActiveDataProvider([
            'query' => Teacher::find()->orderBy(['full_name' => SORT_ASC]),
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('admin', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $model = new Teacher();

        // By default do not allow creating an admin unless explicitly set by admin
        $model->is_admin = false;
        $model->is_active = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Teacher created successfully.'));
            return $this->redirect(['admin']);
        }

        // Allow admin to set admin and active flags
        $safeAttributes = ['full_name', 'slug', 'email', 'telephone', 'profile_picture', 'description', 'admin', 'active'];
        return $this->render('create', [
            'model' => $model,
            'safeAttributes' => $safeAttributes,
        ]);
    }

    public function actionDelete(int $id)
    {
        $model = Teacher::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Teacher not found.');
        }
        $model->delete();
        Yii::$app->session->setFlash('success', Yii::t('app', 'Teacher deleted.'));
        return $this->redirect(['admin']);
    }
}
