<?php
/**
 * @author anderyly
 * @email admin@aaayun.cc
 * @link http://blog.aaayun.cc/
 * @copyright Copyright (c) 2018
 */

namespace ay\drive;

class View
{

    private static $data = [];

    public static function assign($k, $v)
    {
        self::$data[$k] = $v;
    }

    /**
     * 显示模板
     * @param $filePath
     * @param $data
     * @throws \Exception
     */
    public static function view($filePath = '', $data = [])
    {
        if (!is_dir(CACHE)) {
            mkdir(CACHE, 0777, true);
        }
        
        defined('MODE') or define('MODE', "error");
        defined('CONTROLLER') or define('CONTROLLER', "error");
        defined('ACTION') or define('ACTION', "error");

        if (empty($filePath)) {
            $filePath = APP_PATH . MODE . '/view/' . strtolower(CONTROLLER) . '/' . ACTION . '.html';
        } else {
            //
            $suffix = strchr($filePath, '.', false);
            $arr = explode('/', $filePath);
            $num = count($arr);

            switch ($num) {
                case ($num == 3) :
                    $filePath = empty($suffix) ? APP_PATH . $arr[0] . '/view/' . $arr[1] . '/' . $arr[2] . '.html' : APP_PATH . $arr[0] . '/view/' . $arr[1] . '/' . $arr[2];
                    break;
                case ($num == 2) :
                    $filePath = empty($suffix) ? APP_PATH . $arr[0] . '/view/' . $filePath . '.html' : APP_PATH . MODE . '/view/' . $filePath;
                    break;
                case ($num == 1) :
                    $filePath = empty($suffix) ? APP_PATH . MODE . '/view/' . strtolower(CONTROLLER) . '/' . $arr[0] . '.html' : APP_PATH . MODE . '/view/' . strtolower(CONTROLLER) . '/' . $arr[0];
                    break;
                default :
                    break;
            }
            //
        }
        
        if (!file_exists(CACHE . "/html/")) {
            mkdir(CACHE . "/html/", 0777, true);
        }
        $enptyFilePath = CACHE . "/html/" . md5(MODE . CONTROLLER . MODE . $filePath);

        if (!C('CACHE')) {
            self::isFile(self::remPlacer($filePath, null), $data);
        } else {
            //
            if (is_file($enptyFilePath . '.html')) {
                $fileT = filemtime($enptyFilePath . '.html');
                if ((time() - $fileT) >= C('CACHE_TIME')) {
                    self::isFile(self::remPlacer($filePath, $enptyFilePath . '.html'), $data);
                } else {
                    self::isFile($enptyFilePath . '.html', $data);
                }
                //
            } else {
                self::isFile(self::remPlacer($filePath, $enptyFilePath . '.html'), $data);
            }
            //
        }
    }

    /**
     * 模板替换
     * @param string $filePath 原模板地址
     * @param string $enptyFilePath 加密后的模板地址
     * @return mixed
     * @throws \Exception
     */
    private static function remPlacer($filePath, $enptyFilePath)
    {
        $cache = C('CACHE');

        if ($cache) {
//            echo $filePath;exit;
            if (is_file($filePath)) {
                $content = @file_get_contents($filePath);
            } else {
                halt('找不到:' . $filePath . ' 模板');
            }

            // 引入模板
            $content = self::merge($content);
            $content = self::judge($content);
//            $content = str_replace(C('TPL_TAG_LEFT') . 'elseif(', '<?php } else if (', $content);
            $content = str_replace('{{$', '<?php echo $', $content);
            $content = str_replace('{{:', '<?php echo $return = ', $content);
            //$content = str_replace(':url', ' echo $return = ', $content);
            $content = str_replace("{{", '<?php ', $content);
            $content = str_replace(';$', ';echo $', $content);
/*            $content = str_replace(':' . C('TPL_TAG_RIGHT') . C('TPL_TAG_RIGHT'), '{ ?>', $content);*/
            $content = str_replace("}}", ';?>', $content);
            @file_put_contents($enptyFilePath, $content);

            return $enptyFilePath;
        }
        return $filePath;
    }

    /**
     * 加载 赋值
     * @param string $path 模板地址
     * @param array $data 传递的数据
     * @throws \Exception
     */
    private static function isFile($path, $data)
    {

        if (!empty($data) and !is_null($data)) {
            extract(array_merge($data, self::$data), EXTR_OVERWRITE, '');
        } else {
            extract(self::$data, EXTR_OVERWRITE, '');
        }

        if (is_file($path)) {
            ob_start();
            include_once $path;
            echo trim(ob_get_clean());
            exit;
        } else {
            halt('找不到:' . $path . ' 模板');
        }
    }

    public static function judge($content)
    {
        if (strstr($content, C('TPL_TAG_LEFT') . "if")) {

            $content = str_replace('{{else}}', '<?php } else { ?>', $content);
            $content = str_replace('{{if}}', '<?php } ?>', $content);
            $content = preg_replace('#elif.*code=\"(.*?)\"#', ' } else if (${1}) { ', $content);
            $content = preg_replace('#if.*code=\"(.*?)\"#', ' if (${1}) { ', $content);
        }
        return $content;

    }

    /**
     * @param string $content 模板内容
     * @return string|string[]|null
     * @throws \Exception
     */
    private static function merge($content)
    {
        if (strstr($content, "{{@")) {
            $count = substr_count($content, '{{@');
            for ($i = 1; $i <= $count; $i++) {
                if (!isset($lll)) {
                    $lll = $content;
                } else {
                    if (!isset($kkk)) {
                        $kkk = '';
                    }
                    $lll = $kkk;
                }

                $filename = substr($lll, strpos($lll, "{{@") + strlen("{{") + 1, strpos($lll, "}}") - 1);
                $filename = preg_replace("#}}.*#is", '', $filename);
                $arr = explode('/', $filename);

                if (count($arr) == 3) {
                    $filename = APP_PATH . $arr[0] . '/view/' . $arr[1] . '/' . $arr[2];
                } elseif (count($arr) == 2) {
                    $filename = APP_PATH . MODE . '/view/' . $arr[0] . '/' . $arr[1];
                }

                if (!strpos($filename, 'html')) {
                    $filename .= '.html';
                }
                if (is_file(($filename))) {
                    $cls = file_get_contents($filename);
                } else {
                    halt('找不到:' . $filename . ' 模板');
                }
                $kkk = preg_replace("/{{@.*?}}/", $cls, $lll, 1);
            }
            $content = $kkk;
        }

        return $content;
    }
}
