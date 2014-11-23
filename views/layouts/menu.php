<?php
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;

NavBar::begin([
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
    ],
]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav'],
    'items' => [
        [
            'label' => 'Материалы', 'items' => [
                ['label' => 'Новости', 'url' => ['news/index']],
                ['label' => 'Страницы', 'url' => ['page/index']],
                ['label' => 'Модули', 'url' => ['sitewidget/index']],
                '<li class="divider"></li>',
                ['label' => 'Файлы', 'url' => ['files/index']],
            ]
        ],
        [
            'label' => 'Компоненты', 'items' => [
                ['label' => 'Showroom', 'url' => ['showroom/index']],
                ['label' => 'Партнёры', 'url' => ['partner/index']],
                '<li class="divider"></li>',
                ['label' => 'Фабрики', 'url' => ['factory/index']],
                ['label' => 'Страны', 'url' => ['country/index']],
                '<li class="divider"></li>',
                ['label' => 'СМИ', 'url' => ['media/index']],
                ['label' => 'Типы СМИ', 'url' => ['mediatype/index']],
                '<li class="divider"></li>',
                ['label' => 'Решения для бизнеса', 'url' => ['solution/index']],
                ['label' => 'Категории решений бизнеса', 'url' => ['solutioncategory/index']],
            ]
        ],
        [
            'label' => 'Пользователи', 'url' => ['user/index']
        ],
    ],
]);
echo Nav::widget([
    'options' => ['class' => 'navbar-nav navbar-right'],
    'items' => [
        ['label' => 'Просмотр сайта', 'url' => ['/'], 'linkOptions' => ['target' => '_blank']],
        ['label' => 'Выход (' . Yii::$app->user->identity->username . ')', 'url' => ['auth/logout'], 'linkOptions' => ['data-method' => 'post']],
    ],
]);
NavBar::end();