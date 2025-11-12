<?php

namespace app\models;

use app\components\FileDataStore;
use yii\web\IdentityInterface;

class Teacher extends BaseFileModel implements IdentityInterface
{
    public $id;
    public $full_name;
    public $slug;
    public $description;
    public $email;
    public $telephone;
    public $profile_picture;
    public $course_type_id;

    protected static function fileName(): string
    {
        return 'teachers.json';
    }

    protected static function pk(): string { return 'id'; }

    public function rules(): array
    {
        return [
            [['id', 'full_name', 'slug', 'description', 'email', 'telephone', 'profile_picture', 'course_type_id'], 'safe'],
        ];
    }

    public function getCourseType(): ?CourseType
    {
        return CourseType::findOne($this->course_type_id);
    }

    /**
     * @return Course[]
     */
    public function getCourses(): array
    {
        $map = FileDataStore::load('teacher_courses.json');
        $courseIds = [];
        foreach ($map as $row) {
            if ((string)($row['teacher_id'] ?? '') === (string)$this->id) {
                $courseIds[] = (string)$row['course_id'];
            }
        }
        $out = [];
        foreach (Course::findAll() as $c) {
            if (in_array((string)$c->id, $courseIds, true)) {
                $out[] = $c;
            }
        }
        return $out;
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
        return static::findOne($slug, 'slug');
    }
}
