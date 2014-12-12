<?php
namespace mrssoft\engine\controllers;

class FilesController extends \mrssoft\engine\Controller
{
    public function actionIndex()
    {
        return $this->renderDefault('index');
    }
}
