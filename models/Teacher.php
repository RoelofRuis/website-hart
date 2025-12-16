<?php

namespace app\models;

use DateTime;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;

/**
 * @property int $id
 * @property string $full_name
 * @property string $slug
 * @property string $description
 * @property string $email
 * @property string $website
 * @property string $telephone
 * @property string $profile_picture
 * @property string $password_hash
 * @property string|null $auth_key
 * @property bool $is_admin
 * @property bool $is_active
 * @property DateTime|null $last_login
 */
class Teacher extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%teacher}}';
    }

    public function rules(): array
    {
        return [
            [['full_name', 'slug', 'email'], 'required'],
            [['description'], 'string', 'max' => 1000],
            [['full_name', 'slug'], 'string', 'max' => 64],
            [['email'], 'string', 'max' => 150],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['website'], 'string', 'max' => 255],
            [['telephone'], 'string', 'max' => 50],
            [['profile_picture'], 'string', 'max' => 255],
            [['is_admin'], 'boolean'],
            [['is_active'], 'boolean'],
            [['slug'], 'unique'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'full_name' => Yii::t('app', 'Full Name'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'email' => Yii::t('app', 'Email'),
            'telephone' => Yii::t('app', 'Telephone'),
            'website' => Yii::t('app', 'Website'),
            'profile_picture' => Yii::t('app', 'Profile Picture'),
            'is_admin' => Yii::t('app', 'Administrator'),
            'is_active' => Yii::t('app', 'Active'),
            'last_login' => Yii::t('app', 'Last Login'),
        ];
    }

    public function getAccessibleCourses(): ActiveQuery
    {
        return $this->hasMany(CourseNode::class, ['id' => 'course_node_id'])
            ->viaTable('{{%course_node_teacher}}', ['teacher_id' => 'id']);
    }

    public function getTaughtCourses(): ActiveQuery
    {
        return $this->hasMany(CourseNode::class, ['id' => 'course_node_id'])
            ->viaTable(LessonFormat::tableName(), ['teacher_id' => 'id'])
            ->distinct();
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['teacher_id' => 'id'])
            ->orderBy(['course_id' => SORT_ASC, 'persons_per_lesson' => SORT_ASC]);
    }

    public static function findIdentity($id): Teacher|IdentityInterface|null
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }

    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email]);
    }

    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
