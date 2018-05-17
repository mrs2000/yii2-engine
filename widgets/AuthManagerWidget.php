<?php

namespace mrssoft\engine\widgets;

use mrssoft\engine\AssetAuthManager;
use Yii;
use yii\base\Widget;
use yii\bootstrap\Html;

class AuthManagerWidget extends Widget
{
    /**
     * @var int
     */
    public $userId;

    /**
     * @var array
     */
    public $exclude = ['cp'];

    /**
     * @var array ['role', 'permission']
     */
    public $types = ['role'];

    public function run()
    {
        $labelUser = Html::label('Доступ пользователя');
        $labelRoles = Html::label('Все роли и разрешения');

        $listUserRoles = Html::listBox('access', null, $this->userAccessList(), ['class' => 'form-control']);
        $listRoles = Html::listBox('roles', null, $this->accessList(), ['class' => 'form-control']);

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

        echo Html::hiddenInput('access-list', '', ['id' => 'input-access-list']);

        AssetAuthManager::register($this->view);
    }

    private function userAccessList(): array
    {
        $accessList = $this->root();

        if ($this->userId) {
            if (in_array('role', $this->types)) {
                foreach (Yii::$app->authManager->getRolesByUser($this->userId) as $role) {
                    $accessList['Роли'][$role->name] = $role->description;
                }
            }
            if (in_array('permission', $this->types)) {
                foreach (Yii::$app->authManager->getPermissionsByUser($this->userId) as $permission) {
                    $accessList['Разрешения'][$permission->name] = $permission->description;
                }
            }
        }

        return $accessList;
    }

    private function accessList(): array
    {
        $accessList = $this->root();

        if (in_array('role', $this->types)) {
            foreach (Yii::$app->authManager->getRoles() as $role) {
                $accessList['Роли'][$role->name] = $role->description;
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

    private function root()
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