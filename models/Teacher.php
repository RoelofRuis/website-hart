<?php

namespace app\models;

use DateTime;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property string $full_name // TODO: remove
 * @property string $slug
 * @property string $description
 * @property string $email // TODO: remove
 * @property string $website
 * @property string $telephone
 * @property string $profile_picture
 * @property string $password_hash // TODO: remove
 * @property string|null $auth_key // TODO: remove
 * @property bool $is_admin // TODO: remove
 * @property bool $is_active // TODO: remove
 * @property bool $is_teaching // TODO: remove
 * @property DateTime|null $last_login // TODO: remove
 */
class Teacher extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%teacher}}';
    }

    public function behaviors(): array
    {
        return [
        ];
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
            [['is_admin', 'is_active', 'is_teaching'], 'boolean'],
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
            'is_teaching' => Yii::t('app', 'Is Teaching'),
            'last_login' => Yii::t('app', 'Last Login'),
        ];
    }

    public function getAccessibleCourses(): ActiveQuery
    {
        return $this->hasMany(Course::class, ['id' => 'course_id'])
            ->viaTable('{{%course_teacher}}', ['teacher_id' => 'id']);
    }

    public function getTaughtCourses(): ActiveQuery
    {
        return $this->hasMany(Course::class, ['id' => 'course_id'])
            ->viaTable(LessonFormat::tableName(), ['teacher_id' => 'id'])
            ->distinct();
    }

    public function getLessonFormats(): ActiveQuery
    {
        return $this->hasMany(LessonFormat::class, ['teacher_id' => 'id'])
            ->orderBy(['course_id' => SORT_ASC, 'persons_per_lesson' => SORT_ASC]);
    }

    public function getUser(): ActiveQuery
    {
        // TODO: implement
    }

    /** @deprecated */
    public static function findIdentity($id): Teacher|IdentityInterface|null
    {
        return static::findOne($id);
    }

    /** @deprecated */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    /** @deprecated */
    public function getId()
    {
        return $this->id;
    }

    /** @deprecated */
    public function getAuthKey(): ?string
    {
        return $this->auth_key;
    }

    /** @deprecated */
    public function validateAuthKey($authKey): bool
    {
        return $this->getAuthKey() === $authKey;
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }

    /** @deprecated */
    public static function findByEmail(string $email): ?self
    {
        return static::findOne(['email' => $email]);
    }

    /** @deprecated */
    public function setPassword(string $password): void
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /** @deprecated */
    public function validatePassword(string $password): bool
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public static function findIndexable(): ActiveQuery
    {
        return static::find()
            ->where(['is_active' => true]);
    }

    /** @deprecated */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
