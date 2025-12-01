<?php

namespace app\controllers;

use app\models\Teacher;
use app\models\CourseSignup;
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
                'only' => ['update', 'signups', 'messages', 'admin', 'create', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['update', 'signups', 'messages'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['admin', 'create', 'delete'],
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return !Yii::$app->user->isGuest && Yii::$app->user->identity->admin;
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

        $canEdit = $current->admin || ($current->id === $model->id);
        if (!$canEdit) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        // Restrict editable attributes for security
        $safeAttributes = ['full_name', 'email', 'telephone', 'profile_picture', 'description', 'course_type_id'];
        if ($current->admin) {
            // Admins may also toggle admin/active flags
            $safeAttributes[] = 'admin';
            $safeAttributes[] = 'active';
        }

        if ($model->load(Yii::$app->request->post())) {
            // Prevent privilege escalation by non-admins
            if (!$current->admin) {
                $model->admin = (bool)$model->getOldAttribute('admin');
                $model->active = (bool)$model->getOldAttribute('active');
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Teacher information updated successfully.');
                return $this->redirect(['view', 'slug' => $model->slug]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'safeAttributes' => $safeAttributes,
        ]);
    }

    public function actionSignups()
    {
        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        // Legacy route: redirect to the new unified messages page
        return $this->redirect(['messages']);
    }

    public function actionMessages()
    {
        $current = Yii::$app->user->identity;
        if (!$current) {
            throw new NotFoundHttpException('Teacher not found.');
        }

        // Fetch signups for courses taught by the logged-in teacher
        $signupQuery = CourseSignup::find()
            ->joinWith(['course' => function ($q) {
                /** @var yii\db\ActiveQuery $q */
                $q->joinWith('teachers');
            }])
            ->andWhere(['teachers.id' => $current->id])
            ->orderBy(['course_signups.created_at' => SORT_DESC]);

        $signups = $signupQuery->all();

        // Fetch contact messages addressed to the logged-in teacher
        $contacts = ContactMessage::find()
            ->andWhere(['teacher_id' => $current->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        // Normalize to a common structure
        $items = [];
        foreach ($signups as $s) {
            /** @var CourseSignup $s */
            $items[] = [
                'type' => 'signup',
                'course' => $s->course?->name,
                'from_name' => $s->contact_name,
                'email' => $s->email,
                'telephone' => $s->telephone,
                'age' => $s->age,
                'message' => null,
                'created_at' => $s->created_at,
            ];
        }
        foreach ($contacts as $c) {
            /** @var ContactMessage $c */
            $items[] = [
                'type' => 'contact',
                'course' => null,
                'from_name' => $c->name,
                'email' => $c->email,
                'telephone' => null,
                'age' => null,
                'message' => $c->message,
                'created_at' => $c->created_at,
            ];
        }

        // Sort by created_at desc
        usort($items, function ($a, $b) {
            return ($b['created_at'] <=> $a['created_at']);
        });

        return $this->render('messages', [
            'items' => $items,
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
        $model->admin = false;
        $model->active = true;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Teacher created successfully.'));
            return $this->redirect(['admin']);
        }

        // Allow admin to set admin and active flags
        $safeAttributes = ['full_name', 'slug', 'email', 'telephone', 'profile_picture', 'description', 'course_type_id', 'admin', 'active'];
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
