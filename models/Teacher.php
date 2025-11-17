<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class Teacher extends ActiveRecord implements IdentityInterface
{
    public static function tableName(): string
    {
        return '{{%teachers}}';
    }

    public function rules(): array
    {
        return [
            [['full_name', 'slug'], 'required'],
            [['description'], 'string'],
            [['full_name', 'slug'], 'string', 'max' => 150],
            [['email'], 'string', 'max' => 150],
            [['telephone'], 'string', 'max' => 50],
            [['profile_picture'], 'string', 'max' => 255],
            [['course_type_id'], 'integer'],
            [['slug'], 'unique'],
        ];
    }

    public function getCourseType(): ActiveQuery
    {
        return $this->hasOne(CourseType::class, ['id' => 'course_type_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getCourses(): ActiveQuery
    {
        return $this->hasMany(Course::class, ['id' => 'course_id'])
            ->viaTable('{{%teacher_courses}}', ['teacher_id' => 'id']);
    }

    // IdentityInterface
    public static function findIdentity($id)
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

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::findOne(['slug' => $slug]);
    }
}
