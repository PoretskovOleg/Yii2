<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    private $_rules;

    public function checkRule($module_name, $rule_id) {
        if (is_null($this->_rules)) {
            $old_rules = Yii::$app->old_db->createCommand('
               SELECT rule_id FROM user_rule WHERE allow = 1 AND user_id = :user_id
            ', ['user_id' => $this->getId()])->queryColumn();

            if (empty($old_rules)) {
                $this->_rules = [];
                return false;
            }

            $new_rules = Yii::$app->db->createCommand('
               SELECT * FROM rules
            ')->queryAll();

            $this->_rules = [];
            foreach ($new_rules as $rule) {
                if (in_array($rule['old_rule_id'], $old_rules)) {
                    $this->_rules[] = ['id' => $rule['rule_id'], 'module' => $rule['module']];
                }
            }
        }

        return in_array(['id' => $rule_id, 'module' => $module_name], $this->_rules);
    }

    public static function getDb()
    {
        return Yii::$app->old_db;
    }

    public static function tableName()
    {
        return 'user';
    }

    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'is_active' => self::STATUS_ACTIVE]);
    }

    public static function findByLogin($login)
    {
        return static::findOne(['login' => $login, 'is_active' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        if (is_null($this->auth_key)) {
            $this->generateAuthKey();
            $this->save();
        }
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->compareString($this->password, md5($password));
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function getShortName() {
        return $this->last_name . ' ' . mb_substr($this->first_name, 0, 1) . '.' . mb_substr($this->patronymic, 0, 1) . '.';
    }

    public function getPostId() {
        return Yii::$app->old_db->createCommand('
          SELECT post_id FROM member_list WHERE member_list_id = :member_list_id
        ', ['member_list_id' => $this->member_list_id])->queryScalar();
    }

    static public function getUsersDepartment($department) {
        $oldDbName = explode('=', Yii::$app->old_db->dsn)[2];
        $result = array();
        $usersDepartment = self::find()
            ->innerJoin($oldDbName . '.member_list', 'user.member_list_id = member_list.member_list_id')
            ->innerJoin($oldDbName . '.department', 'department.department_id = member_list.department_id')
            ->where(['and', ['department.dep_name' => $department], ['user.is_active' => 1]])->all();
        foreach ($usersDepartment as $user) {
            $result[$user->user_id] = $user->shortName;
        }
        return $result;
    }

    static public function getUsersParams($params, $value) {
        $result = array();
        $users = self::find()
            ->where(['and', [$params => $value], ['is_active' => 1]])->all();
        foreach ($users as $user) {
            $result[$user->user_id] = $user->shortName;
        }
        return $result;
    }
}
