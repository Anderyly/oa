<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2021
 */

namespace ay\drive;

use ay\lib\Db;
use ay\lib\Curl;

class Update
{
    public static function instance()
    {
        return new self();
    }

    public function autoUpdate($url)
    {
        // 获取在线更新文档
        $zip = Curl::url($url)->get();
        $path = TEMP . '/update.zip';
        $fp = fopen($path, "a");
        fwrite($fp, $zip);
        fclose($fp);
        return $this->unzip($path);
    }

    public function start($url, $version)
    {
        // 获取在线更新文档
        $res = Curl::url($url)->get();
        $res = json_decode($res, true);
        if ($res['code'] == 400) return $res;
        if ($res['data']['is_force'] == 1) {
            $this->autoUpdate($res['data']['url']);
        } else {
            return $res;
        }
    }

    private function unzip($path)
    {
        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $zip->extractTo(ROOT);
            $zip->close();
            $this->op();
            unlink($path);
            return true;
        } else {
            return false;
        }
    }

    private function op()
    {
        // 处理shell
        $shellPath = ROOT . '/shell.txt';
        if (!file_exists($shellPath)) return true;
        $content = file_get_contents($shellPath);
        $contentArr = explode(";", $content);
        foreach ($contentArr as $v) {
            $path = explode('>', $v);
            $val = str_replace(array("\r\n", "\r", "\n"), "", $path[1]);
            switch (str_replace(array("\r\n", "\r", "\n"), "", $path[0])) {
                case "del":
                    if (file_exists($path[1])) {
                        @unlink(ROOT . '/' . $val);
                    } else {
                        Dir::del(ROOT . '/' . $val);
                    }
                    break;
                case "sql":
                    Db::instance()->doSql($val);
                    break;
                case "version":
                    $str = <<<EOF
<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

define('APP_NAME', 'app');
define('PRODUCTVEWRSION', "{$val}");

include "../ay/ay.php";
EOF;
                    file_put_contents(PUB . '/index.php', $str);
                    break;
                default :
                    break;
            }
        }
        @unlink($shellPath);
    }

}

