<?
namespace mrssoft\engine\widgets;

class TextEditor extends \mihaildev\ckeditor\CKEditor
{
    public function init()
    {
        $this->editorOptions = \mihaildev\elfinder\ElFinder::ckeditorOptions('admin/elfinder', [
            'preset' => 'standard',
            'forcePasteAsPlainText' => true,
            'pasteFromWordRemoveFontStyles' => true,
            'allowedContent' => true
        ]);

        parent::init();
    }

    public function run()
    {
        parent::run();
        $this->view->registerJs('CKEDITOR.config.protectedSource.push(/<script[\s\S]*?script>/g)');
    }
}