<?
namespace mrssoft\engine;

class AssetManager extends \yii\web\AssetManager
{
    public function getAssetUrl($bundle, $asset)
    {
        $url = parent::getAssetUrl($bundle, $asset);
        if (!(strpos($url, '//') === 0) && !(strpos($url, 'http') === 0) && !(strpos($url, '/assets/') === 0)) {
            $url = '/' . filemtime('.' . $url) . $url;
        }

        return $url;
    }
}