<?php
namespace mrssoft\engine;

class ElFinderExt extends \yii\base\Object
{

    public function rename($cmd, $result, $args, $elfinder)
    {
        foreach ($result['added'] as &$file)
        {
            $path = $elfinder->realpath($file['phash']);

            $new_name = $this->translite($file['name']);
            if ($new_name != $file['name'])
            {
                if (file_exists($path.DIRECTORY_SEPARATOR.$new_name))
                {
                    $new_name = $this->uniqueFileName($path, $new_name);
                }
                $elfinder->exec('rename', array('target' => $file['hash'], 'name' => $new_name));
            }
            return true;
        }
        return false;
    }

    private function uniqueFileName($dir, $name)
    {
        $dir = rtrim($dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;

        if (!file_exists($dir.$name)) return $name;

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
        static $tbl = array(
            'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ж'=>'g', 'з'=>'z',
            'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p',
            'р'=>'r', 'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'ы'=>'i', 'э'=>'e', 'А'=>'A',
            'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ж'=>'G', 'З'=>'Z', 'И'=>'I',
            'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
            'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Ы'=>'I', 'Э'=>'E', 'ё'=>"yo", 'х'=>"h",
            'ц'=>"ts", 'ч'=>"ch", 'ш'=>"sh", 'щ'=>"shch", 'ъ'=>"", 'ь'=>"", 'ю'=>"yu", 'я'=>"ya",
            'Ё'=>"YO", 'Х'=>"H", 'Ц'=>"TS", 'Ч'=>"CH", 'Ш'=>"SH", 'Щ'=>"SHCH", 'Ъ'=>"", 'Ь'=>"",
            'Ю'=>"YU", 'Я'=>"YA", ' '=>'-'
        );
        return strtr($str, $tbl);
    }
}