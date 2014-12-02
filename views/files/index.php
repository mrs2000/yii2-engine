<?
/**
 * Медиа-менеджер
 */

use mihaildev\elfinder\ElFinder;

echo ElFinder::widget([
    'language' => 'ru',
    'frameOptions' => ['style' => 'width: 100%; height: 500px; border: 0;'],
    'controller' => 'admin/elfinder',
]);