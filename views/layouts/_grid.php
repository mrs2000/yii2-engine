<?

use mrssoft\engine\helpers\AdminHelper;
use mrssoft\engine\widgets\Grid;

echo Grid::widget([
    'model' => $model,
    'columns' => [
        AdminHelper::columnEdit(),
    ]
]);