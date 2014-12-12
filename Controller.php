<?php

namespace mrssoft\engine;

use Yii;
use yii\base\Exception;
use yii\helpers\Url;
use yii\web\HttpException;
use mrssoft\engine\helpers\Admin;

/**
 * @property mixed urlParams
 */

class Controller extends \yii\web\Controller
{
    protected $title = '';

    protected $buttons = ['add', 'copy', 'delete', 'public'];

    protected $modelClass = null;

    protected $modelName = null;

    public $urlParams = '';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['moderator'],
                    ],
                ],
                'denyCallback' => function () { //($rule, $action)
                    $this->redirect(Yii::$app->user->loginUrl);
                }
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (isset($_GET['id'])) unset($_GET['id']);
        $this->urlParams = $_GET;

        return parent::beforeAction($action);
    }

    public function renderDefault($view, $params = [])
    {
        return parent::render($view, $params);
    }

    public function render($view, $params = [])
    {
        return parent::render(Admin::getView($view), $params);
    }

    public function renderPartial($view, $params = [])
    {
        return parent::renderPartial(Admin::getView($view), $params);
    }

    /**
     * Таблица записей
     * @return string
     */
    public function actionIndex()
    {
        $class = $this->getModelClass();

        /** @var \yii\db\ActiveRecord $model */
        $model = new $class(['scenario' => 'search']);

        $model->load(Yii::$app->request->get());
        $model->attachBehavior('search', \mrssoft\engine\behaviors\Search::className());

        if (Yii::$app->request->isAjax)
        {
            return $this->renderPartial('grid', ['model' => $model]);
        }
        else
        {
            return $this->render('table', [
                'model' => $model,
                'title' => $this->title,
                'buttons' => $this->buttons,
            ]);
        }
    }

    /**
     * Редактирование записи
     * @param $id
     * @throws HttpException
     */
    public function actionEdit($id = 0)
    {
        $model = $this->getModel($id, 'create');
        if ($model)
        {
            return $this->render('edit', ['model' => $model]);
        }
        else
        {
            throw new HttpException(404);
        }
    }

    /**
     * @throws HttpException
     */
    protected function update()
    {
        $result['result'] = false;
        if (Yii::$app->request->isPost)
        {
            $id = Yii::$app->request->post('id');
            $model = $this->getModel($id);
            if ($model->load(Yii::$app->request->post()))
            {
                if ($model->save())
                {
                    Admin::success('Данные успешно сохранены.');
                    $result['result'] = true;
                }
            }
            $result['model'] = $model;
            return $result;
        }

        throw new HttpException(400, 'Неверный запрос');
    }

    /**
     * Сохранение
     * @throws HttpException
     */
    public function actionUpdate()
    {
        $result = $this->update();
        if ($result['result'])
        {
            return $this->redir();
        }
        return $this->render('edit', ['model' => $result['model']]);
    }

    /**
     * Применить
     * @throws HttpException
     */
    public function actionApply()
    {
        $result = $this->update();
        return $this->render('edit', ['model' => $result['model']]);
    }

    /**
     * Создание копий объектов
     * @throws HttpException
     */
    public function actionCopy()
    {
        $className = $this->getModelClass();

        foreach ($this->getSelectedItems() as $id)
        {
            $source = $this->getModel($id);
            if (!empty($source))
            {
                $source->copy();

                $attributes = $source->getAttributes();
                if (isset($attributes['id']))
                {
                    unset($attributes['id']);
                }

                /** @var \yii\db\ActiveRecord $copy */
                $copy = new $className();
                $copy->setAttributes($attributes, false);
                if (!$copy->save())
                {
                    Admin::error($copy);
                    break;
                }
            }
        }

        return $this->redir();
    }

    /**
     * Удаление записей
     * @throws HttpException
     * @throws \Exception
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isPost)
        {
            $items = Yii::$app->request->post('selection');
            foreach ($items as $id)
            {
                $model = $this->getModel($id);
                try
                {
                    if (!$model->delete())
                    {
                        Admin::error($model);
                        return $this->redir();
                    }
                }
                catch (Exception $e)
                {
                    Admin::error('Ошибка удаления записи. Возможно на неё ссылаются другие объекты.');
                    return $this->redir();
                }
            }
            Admin::success('Данные успешно удалены.');
        }
        else
        {
            throw new HttpException(400, 'Неверный запрос');
        }
        return $this->redir();
    }

    /**
     * Отмена редактирования
     */
    public function actionCancel()
    {
        $this->redir();
    }

    /**
     * Изменене состояния атрибута
     * @param string $attribute - название поля
     * @param string $value - новое состояние
     */
    public function actionState($attribute, $value)
    {
        /* @var $model ActiveRecord */

        $modelName = $this->getModelClass();
        $model = new $modelName;

        foreach ($this->getSelectedItems() as $id)
        {
            $obj = $model->findOne($id);
            $obj->{$attribute} = $value;
            if (!$obj->save())
            {
                Admin::error($obj);
                return $this->redir();
            }
        }

        Admin::success('Статус успешно изменён.');
        return $this->redir();
    }

    /**
     * Изменение позиции
     */
    public function actionPosition()
    {
        $position = Yii::$app->request->post('position');
        $id = $this->getSelectedItems()[0];

        if (!empty($id))
        {
            if (($model = $this->getModel($id)) && $model->hasMethod('changePosition'))
            {
                call_user_func([$model, 'changePosition'], $position[$id]);
            }
        }
        return $this->redir();
    }

    /**
     * @param \app\components\UploadFilesAction $action
     * @return \yii\web\Response
     */
    public function afterUpload($action)
    {
        if ($action->error)
        {
            Admin::error($action->error);
        }
        else
        {
            Admin::success('Фотографии успешно загружены.');
        }

        return $this->redir();
    }

    /**
     * Название модели на основе названия текущего контроллера
     * @return string
     */
    public function getModelName()
    {
        if (empty($this->modelName))
        {
            $value = $this::className();
            $n = mb_strrpos($value, '\\');
            if ($n !== false)
                $value = substr($value, $n + 1);
            $this->modelName = str_replace('Controller', '', $value);
        }
        return $this->modelName;
    }

    /**
     * Класс модели на основе названия текущего контроллера
     * Вначале ищет [app.modules.admin.models.xxx.php],
     * Затем возвращает app.models.xxx
     * @return string
     */
    public function getModelClass()
    {
        if (empty($this->modelClass))
        {
            $name = $this->getModelName();
            if (is_file(Yii::getAlias('@app/modules/admin/models/' . $name) . '.php'))
            {
                $this->modelClass = 'app\\modules\\admin\\models\\' . $name;
            }
            else
            {
                $this->modelClass = 'app\\models\\' . $name;
            }
        }
        return $this->modelClass;
    }

    /**
     * @param int $id
     * @param string|null $scenario
     * @return \mrssoft\engine\ActiveRecord
     */
    protected function getModel($id = 0, $scenario = null)
    {
        $options = empty($scenario) ? null :['scenario' => $scenario];

        /** @var \yii\db\ActiveRecord $model */
        $class = $this->getModelClass();
        $model = new $class($options);

        if (!empty($id))
        {
            $model = $model::findOne($id);
            if (empty($model))
            {
                Admin::error('Запись с идентификатором '.$id.' не найдена.');
                return $this->redir();
            }
        }

        return $model;
    }

    /**
     * Отмеченные позиции
     * @return array
     */
    protected function getSelectedItems()
    {
        return Yii::$app->request->post('selection', []);
    }

    /**
     * Редирект после действия
     * @param string $action
     * @return \yii\web\Response
     */
    public function redir($action = 'index')
    {
        $params = Yii::$app->request->post('urlParams', '');
        if (!empty($params)) $params = '?'.$params;
        return $this->redirect([$this->id.'/'.$action.$params]);
    }

    public function createUrl($route)
    {
        $params = http_build_query($this->urlParams);
        if (!empty($params)) $params = '?'.$params;
        return Url::toRoute($route).$params;
    }
}
