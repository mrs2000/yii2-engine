<?

use mrssoft\engine\helpers\Admin;
use mrssoft\engine\widgets\Grid;

echo Grid::widget([
    'model' => $model,
    'columns' => [
        Admin::columnEdit(),
    ]
]);