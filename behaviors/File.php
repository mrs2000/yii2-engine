<?php
namespace mrssoft\engine\behaviors;

use dosamigos\transliterator\TransliteratorHelper;
use yii;
use yii\db\ActiveRecord;

/**
 * Обработка загрузки файлов
 *
 * @property mixed $uploadPath
 * @property string $fileUrl
 * @property void $oldValue
 * @property null|\mrssoft\engine\behaviors\ImageFunctions $imageFunctionsBehavior
 */
class File extends \yii\base\Behavior
{
    /**
     * @var string Path to upload
     */
    public $path = '';

    /**
     * @var string model attribute to save failename
     */
    public $attribute = 'file';

    public $nameLenght = 6;

    public $uniqueFilename = true;

    /** @var ActiveRecord */
    public $owner;

    /**
     * @var \yii\web\UploadedFile
     */
    private $file;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_DELETE => 'beforeDelete',
        ];
    }

    public function beforeValidate()
    {
        $this->file = \yii\web\UploadedFile::getInstance($this->owner, $this->attribute);
        $this->owner->{$this->attribute} = $this->file;
    }

    public function afterValidate()
    {
        if ($this->owner->hasErrors($this->attribute)) {
            $this->getOldValue();
        }
    }

    public function beforeSave()
    {
        $this->getOldValue();

        if (!empty($this->file->size)) {
            $path = $this->preparePath($this->getUploadPath());

            if ($this->createPath($path)) {
                $filename = $this->createFilename($path, $this->file->name);

                if ($this->file->saveAs($path . $filename)) {
                    $this->deleteFile();
                    $this->owner->{$this->attribute} = $filename;

                    if ($this->owner->hasMethod('afterUploadFile')) {
                        /** @noinspection PhpUndefinedMethodInspection */
                        $this->owner->afterUploadFile($this->attribute);
                    }
                }
            }
        }
    }

    private function getUploadPath()
    {
        if (empty($this->path) && ($imageFunctions = $this->getImageFunctionsBehavior())) {
            return $imageFunctions->getImagePath();
        }

        return Yii::getAlias($this->path);
    }

    /**
     * @return \mrssoft\engine\behaviors\ImageFunctions|null
     */
    private function getImageFunctionsBehavior()
    {
        foreach ($this->owner->behaviors() as $name => $behaviorOptions) {
            if ($behaviorOptions['class'] == \mrssoft\engine\behaviors\ImageFunctions::className()) {
                /** @var \mrssoft\engine\behaviors\ImageFunctions $imageFunctions */
                $imageFunctions = $this->owner->getBehavior($name);
                if ($imageFunctions && $imageFunctions->attribute == $this->attribute) {
                    return $imageFunctions;
                }
            }
        }

        return null;
    }

    public function beforeDelete()
    {
        $this->deleteFile();
    }

    public function deleteFile()
    {
        if ($imageFunctions = $this->getImageFunctionsBehavior()) {
            $imageFunctions->deleteImages();

            return;
        }

        if (!empty($this->owner->{$this->attribute})) {
            $path = $this->preparePath($this->getUploadPath()) . $this->owner->{$this->attribute};
            if (@is_file($path)) {
                @unlink($path);
            }
        }
    }

    private function preparePath($path)
    {
        $path = '.' . ltrim($path, '.');

        return rtrim($path, '/') . '/';
    }

    public function getFileUrl()
    {
        return Yii::$app->request->baseUrl . rtrim($this->path, '/') . '/' . $this->owner->{$this->attribute};
    }

    private function createFilename($path, $filename)
    {
        if ($this->uniqueFilename) {
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            do {
                $name = mb_strtolower(substr(md5(uniqid('', true)), 0, $this->nameLenght)) . '.' . $ext;
            } while (is_file($path . $name));

            return $name;
        }

        return TransliteratorHelper::process($filename);
    }

    private function createPath($path)
    {
        $parts = explode('/', $path);
        $p = '';
        foreach ($parts as $part) {
            $p .= $part . '/';
            if (!file_exists($p) && !mkdir($p)) {
                return false;
            }
        }

        return true;
    }

    private function getOldValue()
    {
        $this->owner->{$this->attribute} = null;
        if ($obj = $this->owner::findOne($this->owner->primaryKey)) {
            $this->owner->{$this->attribute} = $obj->{$this->attribute};
        }
    }
}