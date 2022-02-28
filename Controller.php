<?php

namespace mrssoft\engine;

use app\components\UploadFilesAction;
use mrssoft\engine\behaviors\Search;
use mrssoft\engine\helpers\Admin;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\HttpException;
use yii\web\Response;

/**
 * Базовый класс для контроллеров админ. панели
 *
 * @property array $selectedItems
 */
class Controller extends \yii\web\Controller
{
    /**
     * @var string Заголовок страницы
     */
    protected string $title = '';

    /**
     * @var array Кнопки действий в списке объктов
     */
    protected array $buttons = ['add', 'copy', 'delete', 'public'];

    /**
     * @var string Класс модели
     */
    protected string $modelClass = '';

    /**
     * @var string Название модели
     */
    protected string $modelName = '';

    public array $urlParams = [];

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['cp'],
                    ],
                ],
                'denyCallback' => function () {
                    $this->redirect(Yii::$app->user->loginUrl);
                }
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (array_key_exists('id', $_GET)) {
            unset($_GET['id']);
        }
        $this->urlParams = $_GET;

        return parent::beforeAction($action);
    }

    public function renderDefault($view, array $params = []): string
    {
        return parent::render($view, $params);
    }

    public function render($view, $params = []): string
    {
        return parent::render(Admin::getView($view), $params);
    }

    public function renderPartial($view, $params = []): string
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
        $model->attachBehavior('search', Search::class);

        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('grid', ['model' => $model]);
        }

        return $this->render('table', [
            'model' => $model,
            'title' => $this->title,
            'buttons' => $this->buttons,
        ]);
    }

    /**
     * Редактирование записи
     * @param int|string $id
     * @return string
     * @throws HttpException
     */
    public function actionEdit(int|string $id = 0)
    {
        $model = $this->getModel($id, 'create');
        if ($model) {
            return $this->edit($model);
        }

        throw new HttpException(404, Yii::t('yii', 'Page not found.'));
    }

    /**
     * @throws HttpException
     */
    protected function update(): array
    {
        $result['result'] = false;
        if (Yii::$app->request->isPost) {
            $id = Yii::$app->request->post('id');
            $model = $this->getModel($id);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Admin::success(Yii::t('admin/main', 'Data saved successfully.'));
                $result['result'] = true;
            }
            $result['model'] = $model;

            return $result;
        }

        throw new HttpException(400, Yii::t('admin/main', 'Invalid query.'));
    }

    /**
     * Сохранение
     * @throws HttpException
     */
    public function actionUpdate()
    {
        $result = $this->update();
        if ($result['result']) {
            return $this->redir();
        }

        return $this->edit($result['model']);
    }

    protected function edit($model): string
    {
        AssetEdit::register($this->view);
        $this->view->registerJs("$('input[name *= \"title\"]').typograf(); $('[data-typograf=\"on\"]').typograf();");
        return $this->render('edit', ['model' => $model]);
    }

    /**
     * Применить
     * @param null $id
     * @return string
     * @throws HttpException
     */
    public function actionApply($id = null)
    {
        if (Yii::$app->request->isGet && !empty($id)) {
            return $this->actionEdit($id);
        }

        $result = $this->update();
        return $this->edit($result['model']);
    }

    /**
     * Создание копий объектов
     */
    public function actionCopy()
    {
        $className = $this->getModelClass();

        foreach ($this->getSelectedItems() as $id) {
            $source = $this->getModel($id);
            if (empty($source) === false) {
                $source->copy();

                $attributes = $source->getAttributes();
                if (array_key_exists('id', $attributes)) {
                    unset($attributes['id']);
                }

                /** @var ActiveRecord $copy */
                $copy = new $className();
                $copy->scenario = 'copy';
                $copy->setAttributes($attributes, false);
                $copy->save();
                if ($copy->hasMethod('afterCopy')) {
                    $copy->afterCopy($source);
                }
            }
        }

        return $this->redir();
    }

    /**
     * Удаление записей
     * @throws HttpException
     * @throws \Exception
     * @throws \Throwable
     */
    public function actionDelete()
    {
        if (Yii::$app->request->isPost) {
            $items = Yii::$app->request->post('selection');
            foreach ($items as $id) {
                $model = $this->getModel($id);
                if ($model instanceof \yii\db\ActiveRecord) {
                    try {
                        $result = $model->delete();
                        if ($result === false || $result === 0) {
                            Admin::error('Ошибка удадения записи ' . $model->primaryKey);
                            return $this->redir();
                        }
                    } catch (Exception) {
                        Admin::error(Yii::t('admin/main', 'Failed to delete the record. Perhaps at it is referenced by other objects.'));
                        return $this->redir();
                    }
                }
            }
            Admin::success(Yii::t('admin/main', 'Data successfully deleted.'));
        } else {
            throw new HttpException(400, Yii::t('admin/main', 'Invalid query.'));
        }

        return $this->redir();
    }

    /**
     * Отмена редактирования
     */
    public function actionCancel()
    {
        return $this->redir();
    }

    /**
     * Изменение состояния атрибута
     * @param string $attribute - название поля
     * @param string $value - новое состояние
     * @return Response
     */
    public function actionState(string $attribute, string $value)
    {
        /* @var $model \yii\db\ActiveRecord */
        /* @var $obj \yii\db\ActiveRecord */

        $modelName = $this->getModelClass();
        $model = new $modelName;

        foreach ($this->getSelectedItems() as $id) {
            $obj = $model::findOne($id);
            if ($obj) {
                if (array_key_exists('change-state', $obj->scenarios())) {
                    $obj->scenario = 'change-state';
                }
                $obj->{$attribute} = $value;
                if (!$obj->save()) {
                    Admin::error($obj);
                    return $this->redir();
                }
            }
        }

        Admin::success(Yii::t('admin/main', 'Status changed successfully.'));

        return $this->redir();
    }

    /**
     * Изменение позиции
     */
    public function actionPosition()
    {
        $position = Yii::$app->request->post('position');
        $id = $this->getSelectedItems()[0];

        if (!empty($id) && ($model = $this->getModel($id)) && $model->hasMethod('changePosition')) {
            $model->changePosition($position[$id]);
        }

        return $this->redir();
    }

    /**
     * Сохранение видимости колонок таблицы
     * @return Response
     */
    public function actionTableConfig()
    {
        $columns = Yii::$app->request->post('table-config', []);
        $visible = Yii::$app->request->post('table-config-visible', []);
        $hidden = array_diff($columns, $visible);

        $cookieName = 'egc-' . Yii::$app->controller->id;

        Yii::$app->response->cookies->add(new Cookie([
            'name' => $cookieName,
            'value' => $hidden,
            'expire' => time() + 2592000
        ]));

        return $this->redir();
    }

    /**
     * @param \app\components\UploadFilesAction $action
     * @return Response
     */
    public function afterUpload(UploadFilesAction $action)
    {
        if ($action->error) {
            Admin::error($action->error);
        } else {
            Admin::success(Yii::t('admin/main', 'File successfully downloaded.'));
        }

        return $this->redir();
    }

    /**
     * Название модели на основе названия текущего контроллера
     * @return string
     */
    public function getModelName(): string
    {
        if (empty($this->modelName)) {
            /** @noinspection PhpDeprecationInspection */
            $value = self::className(); //так надо
            $n = mb_strrpos($value, '\\');
            if ($n !== false) {
                $value = substr($value, $n + 1);
            }
            $this->modelName = str_replace('Controller', '', $value);
        }

        return $this->modelName;
    }

    /**
     * Класс модели на основе названия текущего контроллера
     * Вначале ищет [\app\modules\admin\models\xxx.php], если нет - возвращает \app\models\xxx
     * @return string
     */
    public function getModelClass(): string
    {
        if (empty($this->modelClass)) {
            $name = $this->getModelName();
            if (is_file(Yii::getAlias('@app/modules/admin/models/' . $name) . '.php')) {
                $this->modelClass = 'app\\modules\\admin\\models\\' . $name;
            } else {
                $this->modelClass = 'app\\models\\' . $name;
            }
        }

        return $this->modelClass;
    }

    /**
     * Загрузка или создание новой модели
     * @param int|string $id
     * @param string|null $scenario
     * @return ActiveRecord|\yii\db\ActiveRecord|Response
     */
    protected function getModel(int|string $id = 0, string $scenario = null)
    {
        $options = empty($scenario) ? null : ['scenario' => $scenario];

        /** @var \yii\db\ActiveRecord $model */
        $class = $this->getModelClass();
        $model = new $class($options);

        if (empty($id) === false) {
            $model = $model::findOne($id);
            if (empty($model)) {
                Admin::error(Yii::t('admin/main', 'Record with ID [ {0} ] not found.', $id));

                return $this->redir();
            }
        }

        $model->ensureBehaviors();
        return $model;
    }

    /**
     * Отмеченные позиции
     * @return array
     */
    protected function getSelectedItems(): array
    {
        return Yii::$app->request->post('selection', []);
    }

    /**
     * Редирект после действия
     * @param string $action
     * @return Response
     */
    public function redir(string $action = 'index'): Response
    {
        $params = Yii::$app->request->post('urlParams', '');
        if (!empty($params)) {
            $params = '?' . $params;
        }

        return $this->redirect('/' . $this->module->id . '/' . $this->id . '/' . $action . Yii::$app->urlManager->suffix . $params);
    }

    /**
     * @param $route
     * @param null|array $params
     * @return string
     */
    public function createUrl($route, ?array $params = null): string
    {
        if ($params !== null) {
            $params = ArrayHelper::merge($this->urlParams, $params);
        } else {
            $params = $this->urlParams;
        }

        $httpParams = http_build_query($params);
        if (!empty($httpParams)) {
            $httpParams = '?' . $httpParams;
        }

        return Url::toRoute($route) . $httpParams;
    }
}