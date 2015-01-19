<?
namespace mrssoft\engine;

class AssetManager extends \yii\web\AssetManager
{
    public function getAssetUrl($bundle, $asset)
    {
        $url = parent::getAssetUrl($bundle, $asset);
        if (!(substr($url, 0, 5) == 'http:' || substr($url, 0, 2) == '//' || substr($url, 0, 7) == '/assets')) {
            $url = '/' . filemtime('.' . $url) . $url;
        }

        return $url;
    }
}