<?php

namespace mrssoft\engine\widgets;

use mrssoft\engine\AssetAuthManager;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;
use yii\helpers\ArrayHelper;

class AuthManagerWidget extends Widget
{
    /**
     * @var int
     */
    public $userId;

    /**
     * Exclude roles and permissions
     * @var array
     */
    public $exclude = ['cp'];

    /**
     * Enabled types
     * @var array ['role', 'permission']
     */
    public $types = ['role'];

    /**
     * Default roles
     * @var string
     */
    public $defaultRoles;

    /**
     * Default permissions
     * @var string
     */
    public $defaultPermissions;

    /**
     * @var integer
     */
    public $height;

    private $userRoles = [];

    public function run()
    {
        $labelUser = Html::label('Доступ пользователя');
        $labelRoles = Html::label('Все роли и разрешения');

        $options = ['class' => 'form-control'];
        if ($this->height) {
            $options['style'] = 'height: ' . $this->height . 'px';
        }

        $listUserRoles = Html::listBox('access', null, $this->userAccessList(), $options);
        $listRoles = Html::listBox('roles', null, $this->accessList(), $options);

        $btnAdd = Html::button(Html::icon('chevron-left'), [
            'class' => 'btn btn-success btn-add btn-block',
            'title' => 'Добавить'
        ]);
        $btnRemove = Html::button(Html::icon('chevron-right'), [
            'class' => 'btn btn-danger btn-remove btn-block',
            'title' => 'Удалить'
        ]);

        echo Html::beginTag('div', ['class' => 'auth-widget']);
        echo Html::tag('div', $labelUser . $listUserRoles, ['class' => 'auth-widget-list']);
        echo Html::tag('div', $btnAdd . $btnRemove, ['class' => 'auth-widget-control']);
        echo Html::tag('div', $labelRoles . $listRoles, ['class' => 'auth-widget-list']);
        echo Html::endTag('div');

        echo Html::hiddenInput('access-list', implode(',', $this->userRoles), ['id' => 'input-access-list']);

        AssetAuthManager::register($this->view);
    }

    /**
     * Права пользователя
     * @return array
     */
    private function userAccessList(): array
    {
        $accessList = $this->root();

        if (Yii::$app->request->isPost) {
            $roles = explode(',', Yii::$app->request->post('access-list'));
            $this->initDefault($roles, $roles, $accessList);
        } else if ($this->userId) {
            $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser($this->userId), 'name');
            $permissions = ArrayHelper::getColumn(Yii::$app->authManager->getPermissionsByUser($this->userId), 'name');
            $this->initDefault($roles, $permissions, $accessList);
        } else {
            $this->initDefault($this->defaultRoles, $this->defaultPermissions, $accessList);
        }

        return $accessList;
    }

    private function initDefault($roles, $permissions, array &$accessList)
    {
        if (in_array('role', $this->types) && $roles) {
            foreach ((array)$roles as $defaultRole) {
                $role = Yii::$app->authManager->getRole($defaultRole);
                if ($role && in_array($role->name, $this->exclude) === false) {
                    $accessList['Роли'][$role->name] = $role->description;
                    $this->userRoles[] = $role->name;
                }
            }
        }
        if (in_array('permission', $this->types) && $permissions) {
            foreach ((array)$permissions as $defaultPermission) {
                $permission = Yii::$app->authManager->getPermission($defaultPermission);
                if ($permission && in_array($permission->name, $this->exclude) === false) {
                    $accessList['Разрешения'][$permission->name] = $permission->description;
                    $this->userRoles[] = $permission->name;
                }
            }
        }
    }

    /**
     * Доступные права
     * @return array
     */
    private function accessList(): array
    {
        $accessList = $this->root();

        if (in_array('role', $this->types)) {
            foreach (Yii::$app->authManager->getRoles() as $role) {
                if (in_array($role->name, $this->exclude) === false) {
                    $accessList['Роли'][$role->name] = $role->description;
                }
            }
        }
        if (in_array('permission', $this->types)) {
            foreach (Yii::$app->authManager->getPermissions() as $permission) {
                if (in_array($permission->name, $this->exclude) === false) {
                    $accessList['Разрешения'][$permission->name] = $permission->description;
                }
            }
        }

        return $accessList;
    }

    private function root(): array
    {
        $groups = [];
        if (in_array('role', $this->types)) {
            $groups['Роли'] = [];
        }
        if (in_array('permission', $this->types)) {
            $groups['Разрешения'] = [];
        }
        return $groups;
    }
}