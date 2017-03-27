<?php

namespace mrssoft\engine\widgets;

class TextEditor extends \mihaildev\ckeditor\CKEditor
{
    public function init()
    {
        $this->editorOptions = \mihaildev\elfinder\ElFinder::ckeditorOptions('admin/elfinder', [
            'forcePasteAsPlainText' => true,
            'pasteFromWordRemoveFontStyles' => true,
            'allowedContent' => true,
            'height' => 300,
            'toolbar' => [
                ['name' => 'clipboard', 'items' => ['Cut', 'Copy', 'Paste', '-', 'Undo', 'Redo']],
                ['name' => 'basicstyles', 'items' => ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']],
                ['name' => 'paragraph', 'items' => ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock']],
                ['name' => 'links', 'items' => ['Link', 'Unlink', 'Anchor']],
                ['name' => 'styles', 'items' => ['Format']],
                ['name' => 'colors', 'items' => ['TextColor', 'BGColor']],
                ['name' => 'links', 'items' => ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'PageBreak']],
                ['name' => 'tools', 'items' => ['Maximize', 'ShowBlocks']],
                ['name' => 'document', 'items' => ['Source']],
            ],
            'format_tags' => 'p;h2;h3'
        ]);

        parent::init();
    }

    public function run()
    {
        parent::run();
        $this->view->registerJs('CKEDITOR.config.protectedSource.push(/<script[\s\S]*?script>/g)');
    }
}