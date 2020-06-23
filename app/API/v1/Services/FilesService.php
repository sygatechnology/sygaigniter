<?php namespace App\API\v1\Services;

use CodeIgniter\Files\File;

class FilesService
{
    public static function getFile(string $path, bool $checkFile = false)
    {
        return new File($path, $checkFile);
    }

    public static function isZipFile(string $path)
    {
        $file = static::getFile($path, true);
        $ext = $file->guessExtension();
        return ($ext != null && $ext === 'zip');
    }

    public static function unzip(string $location, $newLocation)
    {
        $zip = new \ZipArchive();
        if ($zip->open($location) === true) {
            $zip->extractTo($newLocation);
            $zip->close();
            return true;
        }
        return false;
    }

    public static function readDir(string $path)
    {
        $files = [];
        if ($fp = @opendir($path)) {
            while (false !== ($file = readdir($fp))) {
                if ($file[0] !== '.' && $file[0] !== '..') {
                    $files[] = $path . DIRECTORY_SEPARATOR . $file;
                }
            }
            closedir($fp);
        }
        return $files;
    }

    public static function listDirs(string $path)
    {
        $dirs = [];
        foreach (static::readDir($path) as $file) {
            if (is_dir($file)) {
                $dirs[] = $file;
            }
        }
        return $dirs;
    }

    public static function listFiles(string $path)
    {
        $dirs = [];
        foreach (static::readDir($path) as $file) {
            if (!is_dir($file)) {
                $dirs[] = $file;
            }
        }
        return $dirs;
    }

    public static function getFilenameWithoutExtension(string $filename)
    {
        $segment = explode('.', $filename);
        $lastIndex = count($segment) - 1;
        unset($segment[$lastIndex]);
        $realFilename = "";
        foreach ($segment as $s) {
            $realFilename .= $s . '_';
        }
        return rtrim($realFilename, '_');
    }

    public static function copy(string $path, string $location, $overwrite = false)
    {
        if (is_dir($path)) {
            if(is_dir($location) && ! $overwrite){
                return true;
            } else if(is_dir($location) && $overwrite){
                self::removeDir($location);
            }
            if (!is_dir($location)) mkdir($location);

            $files = static::readDir($path);
            foreach ($files as $file) {
                $segment = explode(DIRECTORY_SEPARATOR, $file);
                static::copy($file, $location . DIRECTORY_SEPARATOR . end($segment));
            }
        } else if (is_file($path)) {
            $segment1 = explode(DIRECTORY_SEPARATOR, $path);
            $segment2 = explode(DIRECTORY_SEPARATOR, $location);
            if (end($segment1) != end($segment2)) {
                $location = $location . DIRECTORY_SEPARATOR . end($segment1);
            }
            if (!copy($path, $location)) {
                return false;
            }
        }
        return false;
    }

    public static function removeDir($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        chmod($dir, 0777);
        gc_collect_cycles();
        if (!is_dir($dir) || is_link($dir)) {
            return unlink($dir);
        }
        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }
            if (!self::removeDir($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }
}
