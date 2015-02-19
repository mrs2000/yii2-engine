<?
namespace mrssoft\engine;

class AssetManager extends \yii\web\AssetManager
{
    public function getAssetUrl($bundle, $asset)
    {
        $url = parent::getAssetUrl($bundle, $asset);
        if (!(substr($url, 0, 2) == '//') && \Yii::$app->controller->module->id != 'admin') {
            $url = '/' . filemtime('.' . $url) . $url;
        }

        return $url;
    }
}