<?
namespace mrssoft\engine\helpers;

class Admin
{
    /**
     * Вывод сообщения об успешном выполнении операции
     * @param string $message
     */
    public static function success($message)
    {
        \Yii::$app->session->setFlash('msg-success', $message);
    }

    /**
     * Вывод сообщения об ошибке
     * @param string $message |CActiveRecord
     */
    public static function error($message)
    {
        if ($message instanceof \yii\db\ActiveRecord)
        {
            $message = self::formatModelErrors($message);
        }
        \Yii::$app->session->setFlash('msg-error', $message);
    }

    public function getMessage()
    {
        \Yii::$app->session->getFlash('msg-error');
    }

    /**
     * @param \yii\db\ActiveRecord $model
     * @param bool $applyAlert
     * @return string
     */
    public static function formatModelErrors($model, $applyAlert = false)
    {
        $errors = $model->getErrors();
        $result = '';
        if (!empty($errors)) {
            if ($applyAlert) {
                $result .= '<div class="alert alert-error">';
            }

            $result .= '<ul style="margin-bottom: 0;">';
            foreach ($errors as $error) {
                $result .= '<li>' . $error[0] . '</li>';
            }
            $result .= '</ul>';
            if ($applyAlert) {
                $result .= '</div>';
            }
        }

        return $result;
    }

    /**
     * Колонка с ссылкой на редактирвование записи
     * @param string $attribute
     * @param string $attributeID
     * @return array
     */
    public static function columnEdit($attribute = 'title', $attributeID = 'id')
    {
        return [
            'class' => \mrssoft\engine\columns\Edit::className(),
            'attribute' => $attribute,
            'attributeID' => $attributeID
        ];
    }

    /**
     * Колонка с ссылками на редактирвование записи и переходя к дочерним элементам
     * @param string $attribute
     * @param string $attributeID
     * @param string $attributeParentID
     * @return array
     */
    public static function columnEditCategory($attribute = 'title', $attributeID = 'id', $attributeParentID = 'parent_id')
    {
        return [
            'class' => \mrssoft\engine\columns\EditCategory::className(),
            'attribute' => $attribute,
            'attributeID' => $attributeID,
            'attributeParentID' => $attributeParentID
        ];
    }

    /**
     * Колонка с порядковым номером
     * @return array
     */
    public static function columnSerial()
    {
        return [
            'class' => \yii\grid\SerialColumn::className(),
            'header' => '№',
            'headerOptions' => ['class' => 'column-small'],
            'contentOptions' => ['class' => 'center column-small'],
        ];
    }

    /**
     * Колонка с чекбоксом
     * @return array
     */
    public static function columnCheckbox()
    {
        return [
            'class' => \yii\grid\CheckboxColumn::className(),
            'checkboxOptions' => ['class' => 'select-on-check'],
            'contentOptions' => ['class' => 'center'],
            'headerOptions' => ['class' => 'center column-small'],
        ];
    }

    /**
     * Колонка с ID записи
     * @param string $attribute
     * @return array
     */
    public static function columnID($attribute = 'id')
    {
        return [
            'attribute' => $attribute,
            'contentOptions' => ['class' => 'center'],
            'headerOptions' => ['class' => 'center column-small'],
        ];
    }

    /**
     * Колонка "Опубликовано"
     * @return array
     */
    public static function columnPublic()
    {
        return [
            'class' => \mrssoft\engine\columns\Switcher::className(),
        ];
    }

    /**
     * Колонка с датой
     * @param string $attribute
     * @return array
     */
    public static function columnDate($attribute = 'date')
    {
        return [
            'attribute' => $attribute,
            'format' => 'date',
            'filter' => false,
            'contentOptions' => ['class' => 'center'],
        ];
    }

    public static function columnDateTime($attribute = 'date')
    {
        return [
            'attribute' => $attribute,
            'value' => function ($model, $attribute) {
                return \Yii::$app->formatter->asDate($model->{$attribute}, 'long');
            },
            'contentOptions' => ['class' => 'center'],
            'headerOptions' => ['class' => 'center'],
        ];
    }

    /**
     * Описание колонки "Позиция" для таблицы
     * @param string $attribute
     * @return array
     */
    public static function columnPosition($attribute = 'position')
    {
        return [
            'class' => \mrssoft\engine\columns\Position::className(),
            'attribute' => $attribute
        ];
    }

    // TODO not used
    public static function CKEditorOptions()
    {
        $options['height'] = 300;

        $options['toolbarGroups'] = [
            ['name' => 'clipboard', 'groups' => ['mode','undo', 'selection', 'clipboard','doctools']],
            ['name' => 'editing', 'groups' => ['tools', 'about']],
            '/',
            ['name' => 'paragraph', 'groups' => ['templates', 'list', 'indent', 'align']],
            ['name' => 'insert'],
            '/',
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'colors'],
            ['name' => 'links'],
            ['name' => 'others'],
        ];

        $options['removeButtons'] = 'Smiley,Iframe';

        return $options;
    }

    /**
     * Путь к отображению
     * Вначале ищет файл в @app/modules/admin/views/
     * Если файла нет возвращает /layouts/_xxxxx
     * @param $view
     * @return string
     */
    public static function getView($view)
    {
        $alias = '@app/modules/'.\Yii::$app->controller->module->id.'/views/'.\Yii::$app->controller->id.'/'.$view;
        $pathTableView = \Yii::getAlias($alias.'.php');
        return is_file($pathTableView) ? $alias : '/layouts/_'.$view;
    }
}