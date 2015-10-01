<?
namespace mrssoft\engine;

class AssetManager extends \yii\web\AssetManager
{
    public function getAssetUrl($bundle, $asset)
    {
        $url = parent::getAssetUrl($bundle, $asset);
        if (!(substr($url, 0, 2) === '//') && !(substr($url, 0, 4) === 'http') && \Yii::$app->controller->module->id !== 'admin') {
            $url = '/' . filemtime('.' . $url) . $url;
        }

        return $url;
    }
}