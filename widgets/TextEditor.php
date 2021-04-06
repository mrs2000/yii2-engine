<?php

namespace mrssoft\engine\widgets;

use yii\helpers\ArrayHelper;

class TextEditor extends \mihaildev\ckeditor\CKEditor
{
    private function defaultOptions(): array
    {
        return [
            'pasteFromWordRemoveStyles' => true,
            'pasteFromWordRemoveFontStyles' => true,
            'allowedContent' => true,
            'height' => 300,
            'toolbar' => [
                ['name' => 'clipboard', 'items' => ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo']],
                [
                    'name' => 'basicstyles',
                    'items' => [
                        'Bold',
                        'Italic',
                        'Underline',
                        'Strike',
                        'Subscript',
                        'Superscript',
                        '-',
                        'RemoveFormat'
                    ]
                ],
                [
                    'name' => 'paragraph',
                    'items' => [
                        'NumberedList',
                        'BulletedList',
                        '-',
                        'Outdent',
                        'Indent',
                        '-',
                        'JustifyLeft',
                        'JustifyCenter',
                        'JustifyRight',
                        'JustifyBlock'
                    ]
                ],
                ['name' => 'links', 'items' => ['Link', 'Unlink', 'Anchor']],
                ['name' => 'styles', 'items' => ['Format']],
                ['name' => 'colors', 'items' => ['TextColor', 'BGColor']],
                ['name' => 'links', 'items' => ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak']],
                ['name' => 'tools', 'items' => ['Maximize', 'ShowBlocks']],
                ['name' => 'document', 'items' => ['Source']],
            ],
            'format_tags' => 'p;h2;h3'
        ];
    }

    public function init()
    {
        $options = ArrayHelper::merge($this->defaultOptions(), $this->editorOptions ?? []);
        $this->editorOptions = \mihaildev\elfinder\ElFinder::ckeditorOptions('admin/elfinder', $options);

        parent::init();
    }

    public function run()
    {
        parent::run();
        $this->view->registerJs('CKEDITOR.config.protectedSource.push(/<script[\s\S]*?script>/g)');
    }
}