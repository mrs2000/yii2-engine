<?php
namespace mrssoft\engine;

class ElFinderExt extends \yii\base\BaseObject
{
    public $imageMinWidth = false;
    public $imageMinHeight = false;
    public $imageMaxWidth = false;
    public $imageMaxHeight = false;

    public function resize($path)
    {
        $info = @getimagesize($path);
        if ($info)
        {
            $ih = new \mrssoft\image\ImageHandler();
            $ih->load($path);
            $change = false;

            if ($this->imageMinWidth && $ih->getWidth() < $this->imageMinWidth)
            {
                $ih->resize($this->imageMinWidth, false);
                $change = true;
            }
            if ($this->imageMinHeight && $ih->getHeight() < $this->imageMinHeight)
            {
                $ih->resize(false, $this->imageMinHeight);
                $change = true;
            }
            if ($this->imageMaxWidth && $ih->getWidth() > $this->imageMaxWidth)
            {
                $ih->resize($this->imageMaxWidth, false);
                $change = true;
            }
            if ($this->imageMaxHeight && $ih->getHeight() > $this->imageMaxHeight)
            {
                $ih->resize(false, $this->imageMaxHeight);
                $change = true;
            }

            if ($change)
            {
                $ih->save(false, false, 100);
            }
        }
    }

    /**
     * Отслеживаем перемещение и переименование
     * @param $cmd
     * @param $result
     * @param $args
     * @param \elFinder $elfinder
     * @return bool
     */
    public function change($cmd, $result, $args, $elfinder)
    {
        foreach ($result['added'] as &$file)
        {
            $path = $elfinder->realpath($file['phash']);

            if ($cmd === 'upload' && $file['mime'] !== 'directory')
            {
                $this->resize($path.DIRECTORY_SEPARATOR.$file['name']);
            }

            $new_name = mb_strtolower($this->translite($file['name']));
            if (file_exists($path.DIRECTORY_SEPARATOR.$file['name']) && $new_name !== $file['name'])
            {
                $new_name = $this->uniqueFileName($path, $new_name);
            }

            if ($new_name !== $file['name'])
            {
                $elfinder->exec('rename', ['target' => $file['hash'], 'name' => $new_name]);
            }
        }
        return true;
    }

    private function uniqueFileName($dir, $name)
    {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if (!file_exists($dir.$name)) {
            return $name;
        }

        $file = $name;
        $n = 1;
        $ext = '';

        if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
            $ext  = '.'.$m[1];
            $name = substr($name, 0,  strlen($name) - strlen($m[0]));
        }

        while (file_exists($dir.$file.$ext))
        {
            $file = $name.'-'.$n;
            $n++;
        }

        return $file.$ext;
    }

    /**
     * Переводит русский текст в транслит
     * @param $str
     * @return string
     */
    private function translite($str)
    {
        $str = \dosamigos\transliterator\TransliteratorHelper::process($str);
        return preg_replace('/[^-A-Za-z0-9_\.]+/', '', $str);
    }
}