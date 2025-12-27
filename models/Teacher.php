<?php

namespace app\models;

use app\components\behaviors\TagBehavior;
use DateTime;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * @property int $id
 * @property int $user_id
 * @property string $slug
 * @property string|null $description
 * @property string|null $website
 * @property string|null $telephone
 * @property string|null $profile_picture
 * @property bool $mon
 * @property bool $tue
 * @property bool $wed
 * @property bool $thu
 * @property bool $fri
 * @property bool $sat
 * @property bool $sun
 * @property string $tags
 *
 * @property User $user
 * @property Tag[] $tags_relation
 */
class Teacher extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%teacher}}';
    }

    public function behaviors(): array
    {
        return [
            'tag' => [
                'class' => TagBehavior::class,
                'tagRelation' => 'tags_relation',
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['user_id', 'slug'], 'required'],
            [['user_id'], 'integer'],
            [['description', 'tags'], 'string'],
            [['description'], 'string', 'max' => 2000],
            [['slug'], 'string', 'max' => 64],
            [['website'], 'string', 'max' => 255],
            [['telephone'], 'string', 'max' => 50],
            [['profile_picture'], 'string', 'max' => 255],
            [['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'], 'boolean'],
            [['slug'], 'unique'],
            [['user_id'], 'exist', 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'user_id' => Yii::t('app', 'User'),
            'slug' => Yii::t('app', 'Slug'),
            'description' => Yii::t('app', 'Description'),
            'telephone' => Yii::t('app', 'Telephone'),
            'website' => Yii::t('app', 'Website'),
            'profile_picture' => Yii::t('app', 'Profile Picture'),
            'mon' => Yii::t('app', 'Monday'),
            'tue' => Yii::t('app', 'Tuesday'),
            'wed' => Yii::t('app', 'Wednesday'),
            'thu' => Yii::t('app', 'Thursday'),
            'fri' => Yii::t('app', 'Friday'),
            'sat' => Yii::t('app', 'Saturday'),
            'sun' => Yii::t('app', 'Sunday'),
            'tags' => Yii::t('app', 'Tags'),
        ];
    }

    public function getTags_relation(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('{{%teacher_tag}}', ['teacher_id' => 'id']);
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
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getFull_name(): ?string
    {
        return $this->user?->full_name;
    }

    public function getEmail(): ?string
    {
        return $this->user?->email;
    }

    /**
     * Returns selected day names as array (translated)
     * @return string[]
     */
    public function getDayNames(): array
    {
        $labels = [
            'mon' => Yii::t('app', 'Monday'),
            'tue' => Yii::t('app', 'Tuesday'),
            'wed' => Yii::t('app', 'Wednesday'),
            'thu' => Yii::t('app', 'Thursday'),
            'fri' => Yii::t('app', 'Friday'),
            'sat' => Yii::t('app', 'Saturday'),
            'sun' => Yii::t('app', 'Sunday'),
        ];
        $days = [];
        foreach (array_keys($labels) as $key) {
            if (!empty($this->$key)) {
                $days[] = $labels[$key];
            }
        }
        return $days;
    }

    /**
     * Comma separated days string.
     */
    public function getFormattedDays(): string
    {
        return implode(', ', $this->getDayNames());
    }

    public function getLocations(): ActiveQuery
    {
        return $this->hasMany(Location::class, ['id' => 'location_id'])
            ->viaTable('{{%teacher_location}}', ['teacher_id' => 'id']);
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
            ->innerJoinWith('user')
            ->where(['user.is_active' => true]);
    }

    /** @deprecated */
    public function generateAuthKey(): void
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
}
