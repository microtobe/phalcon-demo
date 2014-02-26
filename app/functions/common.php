<?php

/**
 * 公共函数文件
 */

/**
 * 加载函数库
 *
 *     load_functions('tag', ...)
 *     load_functions(array('tag', ...))
 *
 * @param  string|array $names
 */
function load_functions($names)
{
    static $cached = array('common');

    if (func_num_args() > 1) {
        $names = func_get_args();
    } elseif (! is_array($names)) {
        $names = array($names);
    }

    $names = array_map('strtolower', $names);

    foreach ($names as $name) {
        if (! isset($cached[$name])) {
            $file = APP_PATH . "/functions/{$name}.php";
            if (is_file($file)) {
                require_once $file;
            }
        }
    }
}

/**
 * 加载配置文件数据
 *
 *     config('database')
 *     config('database.default.adapter')
 *
 * @param  string $name
 * @return mixed
 */
function config($name)
{
    static $cached = array();

    // 移除多余的分隔符
    $name = trim($name, '.');

    if (! isset($cached[$name])) {
        $filename = $name;

        // 获取配置名及路径
        if (strpos($name, '.') !== false) {
            $paths = explode('.', $name);
            $filename = array_shift($paths);
        }

        // 查找配置文件
        $file = APP_PATH . '/config/' . $filename . '.php';
        if (! is_file($file)) {
            return null;
        }

        // 从文件中加载配置数据
        $data = include $file;
        if (is_array($data)) {
            $data = new Phalcon\Config($data);
        }

        // 缓存文件数据
        $cached[$filename] = $data;

        // 支持路径方式获取配置，例如：config('file.key.subkey')
        if (isset($paths)) {
            foreach ($paths as $key) {
                if (is_array($data) && isset($data[$key])) {
                    $data = $data[$key];
                } elseif (is_object($data) && isset($data->{$key})) {
                    $data = $data->{$key};
                } else {
                    $data = null;
                }
            }
        }

        // 缓存数据
        $cached[$name] = $data;
    }

    return $cached[$name];
}

/**
 * 实例化一个 model
 *
 *     model('user_data')
 *     model('UserData')
 *
 * @param  string $name
 * @return object
 */
function model($name)
{
    // 格式化类名
    $class = implode('_', array_map('ucfirst', explode('_', $name)));

    return new $class(Phalcon\DI::getDefault());
}

/**
 * 简化 Phalcon\Di::getDefault()->getShared($service)
 *
 *     service('url')
 *     service('db')
 *     ...
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_DI.html
 * @param  string $service
 * @return object
 */
function service($service)
{
    return Phalcon\DI::getDefault()->getShared($service);
}

/**
 * 获取完整的 url 地址
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Url.html
 * @param  string $uri
 * @return string
 */
function url($uri = null)
{
    return service('url')->get($uri);
}

/**
 * 获取静态资源地址
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_Url.html
 * @param  string $uri
 * @return string
 */
function static_url($uri = null)
{
    return service('url')->getStatic($uri);
}

/**
 * 获取包含域名在内的 url
 *
 * @param  string $uri
 * @param  string $base
 * @return string
 */
function baseurl($uri = null, $base = HTTP_BASE)
{
    return HTTP_BASE . ltrim($uri, '/');
}

/**
 * 根据 query string 参数生成 url
 *
 *     url_param('item/list', array('page' => 1)) // item/list?page=1
 *     url_param('item/list?page=1', array('limit' => 10)) // item/list?page=1&limit=10
 *
 * @param  string $uri
 * @param  array  $params
 * @return string
 */
function url_param($uri, array $params)
{
    return $uri . (strpos($uri, '?') ? '&' : '?') . http_build_query(array_unique($params));
}

/**
 * 获取视图内容
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View.html
 * @return string
 */
function get_content()
{
    return service('view')->getContent();
}

/**
 * 判断视图是否存在
 *
 * @param  string       $viewFile
 * @param  string|array $suffixes
 * @return boolean
 */
function has_view($viewFile, $suffixes = null)
{
    $file = service('view')->getViewsDir() . $viewFile;

    if ($suffixes === null) {
        $suffixes = array('phtml', 'volt');
    } elseif (! is_array($suffixes)) {
        $suffixes = array($suffixes);
    }

    foreach ($suffixes as $suffix) {
        if (is_file($file . '.' . $suffix)) {
            return true;
        }
    }

    return false;
}

/**
 * 加载局部视图
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View.html
 * @param  string $partialPath
 * @param  array  $params
 * @return string
 */
function partial_view($partialPath, array $params = null)
{
    return service('view')->partial($partialPath, $params);
}

/**
 * 选择不同的视图来渲染，并做为最后的 controller/action 输出
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Mvc_View.html
 * @param  string $renderView
 * @return string
 */
function pick_view($renderView)
{
    return service('view')->pick($renderView);
}

/**
 * 语言转换
 *
 *     // file: ~/app/i18n/{$lang}/category.item.php
 *     __('category.item.list')
 *
 *     // data: array('welcome' => 'Hello, :name')
 *     __('welcome', array(':name' => 'zhouyl')) // Hello, zhouyl
 *
 * @param  string $string 要转换的字符串，默认传入中文
 * @param  array  $values 需要替换的参数
 * @param  string $lang   指定的语言类型
 * @return string
 */
function __($string, array $values = null, $lang = null)
{
    return service('i18n')->translate($string, $values, $lang);
}

/**
 * 简化三元表达式
 *
 * @param  $boolean $boolValue
 * @param  mixed    $trueValue
 * @param  mixed    $falseValue
 * @return mixed
 */
function on($boolValue, $trueValue, $falseValue = null)
{
    return $boolValue ? $trueValue : $falseValue;
}

/**
 * 返回格式化的 json 数据
 *
 * @param  array   $array
 * @param  boolean $pretty    美化 json 数据
 * @param  boolean $unescaped 关闭 Unicode 编码
 * @return string
 */
function json_it(array $array, $pretty = true, $unescaped = true)
{
    // php 5.4+
    if (defined('JSON_PRETTY_PRINT') && defined('JSON_UNESCAPED_UNICODE')) {
        if ($pretty && $unescaped)
            $options = JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE;
        elseif ($pretty)
            $options = JSON_PRETTY_PRINT;
        elseif ($unescaped)
            $options = JSON_UNESCAPED_UNICODE;
        else
            $options = null;

        return json_encode($array, $options);
    }

    if ($unescaped) {
        // convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127).
        // So such characters are being "hidden" from normal json_encoding
        $tmp = array();
        array_walk_recursive($array, function (&$item, $key) {
            if (is_string($item)) {
                $item = mb_encode_numericentity($item, array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
            }
        });
        $json = mb_decode_numericentity(json_encode($array), array(0x80, 0xffff, 0, 0xffff), 'UTF-8');
    } else {
        $json = json_encode($array);
    }

    if ($pretty) {
        $result      = '';
        $pos         = 0;
        $strLen      = strlen($json);
        $indentStr   = "\t";
        $newLine     = "\n";
        $prevChar    = '';
        $outOfQuotes = true;

        for ($i = 0; $i <= $strLen; $i++) {

            // Grab the next character in the string.
            $char = substr($json, $i, 1);

            // Are we inside a quoted string
            if ($char == '"' AND $prevChar != '\\') {
                $outOfQuotes = ! $outOfQuotes;

            // If this character is the end of an element,
            // output a new line and indent the next line.
            } elseif (($char == '}' OR $char == ']') AND $outOfQuotes) {
                $result .= $newLine;
                $pos--;
                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            // Add the character to the result string.
            $result .= $char;

            // If the last character was the beginning of an element,
            // output a new line and indent the next line.
            if (($char == ',' OR $char == '{' OR $char == '[') AND $outOfQuotes) {
                $result .= $newLine;
                if ($char == '{' OR $char == '[') {
                    $pos++;
                }

                for ($j = 0; $j < $pos; $j++) {
                    $result .= $indentStr;
                }
            }

            $prevChar = $char;
        }

        $json = $result;
    }

    return $json;
}

/**
 * 简化日志写入方法
 *
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Logger.html
 * @see    http://docs.phalconphp.com/en/latest/api/Phalcon_Logger_Adapter_File.html
 * @param  string  $name    日志名称
 * @param  string  $message 日志内容
 * @param  string  $type    日志类型
 * @param  boolean $addUrl  记录当前 url
 * @return Phalcon\Logger\Adapter\File
 */
function write_log($name, $message, $type = null, $addUrl = false)
{
    static $logger, $formatter;

    if (! isset($logger[$name])) {
        $logfile = ROOT_PATH . '/logs/' . date('/Ym/') . $name . '_' . date('Ymd') . '.log';
        if (! is_dir(dirname($logfile))) {
            mkdir(dirname($logfile), 0755, true);
        }

        $logger[$name] = new Phalcon\Logger\Adapter\File($logfile);

        // Set the logger format
        if ($formatter === null) {
            $formatter = new Phalcon\Logger\Formatter\Line();
            $formatter->setDateFormat('Y-m-d H:i:s O');
        }

        $logger[$name]->setFormatter($formatter);
    }

    if ($type === null) {
        $type = Phalcon\Logger::INFO;
    }

    if ($addUrl) {
        $logger[$name]->log('URL: ' . HTTP_URL, Phalcon\Logger::INFO);
    }

    $logger[$name]->log($message, $type);

    return $logger[$name];
}

/**
 * 隐藏当前系统路径
 *
 *     strip_path('/web/myapp/app/config/db.php') // ~/app/config/db.php
 *
 * @param  string $path
 * @return string
 */
function strip_path($path)
{
    return str_replace(ROOT_PATH, '~', $path);
}

/**
 * Email格式检查 (支持验证host有效性)
 *
 * @param  string  $email
 * @param  boolean $testMX
 * @return boolean
 */
function is_email($email, $testMX = false)
{
    if (preg_match('/^([_a-z0-9+-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $email)) {
        if ($testMX) {
            list( , $domain) = explode("@", $email);

            return getmxrr($domain, $mxrecords);
        }

        return true;
    }

    return false;
}

/**
 * 检查是否效的 url
 *
 * @param  string  $url
 * @return boolean
 */
function is_url($url)
{
    return preg_match('/^https?:\/\/([a-z0-9\-]+\.)+[a-z]{2,3}([a-z0-9_~#%&\/\'\+\=\:\?\.\-])*$/i', $url);
}

/**
 * CURL POST 请求
 *
 * @param  string $url
 * @param  array  $postdata
 * @param  array  $curl_opts
 * @return string
 */
function curl_post($url, array $postdata = null, array $curl_opts = null)
{
    $ch = curl_init();

    if ($postdata !== null) {
        $postdata = http_build_query($postdata);
    }

    curl_setopt_array($ch, array(
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_URL            => $url,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_POST           => 1,
        CURLOPT_POSTFIELDS     => $postdata,
        CURLOPT_RETURNTRANSFER => 1,
    ));

    if ($curl_opts !== null) {
        curl_setopt_array($ch, $curl_opts);
    }

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

/**
 * CURL GET 请求
 *
 * @param  string $url
 * @param  array  $curl_opts
 * @return string
 */
function curl_get($url, array $curl_opts = null)
{
    $ch = curl_init();

    curl_setopt_array($ch, array(
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_URL            => $url,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_RETURNTRANSFER => 1,
    ));

    if ($curl_opts !== null) {
        curl_setopt_array($ch, $curl_opts);
    }

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

/**
 * 写入缓存
 *
 * @see    http://docs.phalconphp.com/en/latest/reference/cache.html
 * @param  string  $key
 * @param  mixed   $data
 * @param  integer $lifetime
 * @param  boolean $stopBuffer
 */
function cache_save($key, $data, $lifetime = 86400, $stopBuffer = false)
{
    return service('cache')->save($key . '.cache', $data, $lifetime, $stopBuffer);
}

/**
 * 获取缓存
 *
 * @see    http://docs.phalconphp.com/en/latest/reference/cache.html
 * @param  string $key
 * @param  mixed  $default
 * @return mixed
 */
function cache_get($key, $default = null)
{
    $cache = service('cache')->get($key . '.cache');

    return $cache === null ? $default : $cache;
}

/**
 * 删除缓存
 *
 * @see    http://docs.phalconphp.com/en/latest/reference/cache.html
 * @param  string $key
 * @return boolean
 */
function cache_delete($key)
{
    return service('cache')->delete($key . '.cache');
}

/**
 * 设置 cookie 值
 *
 * @param string  $name
 * @param mixed   $value
 * @param integer $lifetime
 */
function cookie_set($name, $value, $lifetime = null)
{
    return service('cookies')->set($name, $value, $lifetime);
}

/**
 * 获取 cookie 值
 *
 * @param  string $name
 * @param  mixed  $default
 * @return mixed
 */
function cookie_get($name, $default = null)
{
    return service('cookies')->get($name, $default);
}

/**
 * 删除 cookie
 *
 * @param  string $name
 * @return boolean
 */
function cookie_delete($name)
{
    return service('cookies')->delete($name);
}

/**
 * 设置 session 值
 *
 * @see   http://docs.phalconphp.com/en/latest/reference/session.html
 * @see   http://docs.phalconphp.com/en/latest/api/Phalcon_Session_AdapterInterface.html
 * @param string  $name
 * @param mixed   $value
 */
function session_set($name, $value)
{
    return service('session')->set($name, $value);
}

/**
 * 获取 session 值
 *
 * @param  string $name
 * @param  mixed  $default
 * @return mixed
 */
function session_get($name, $default = null)
{
    return service('session')->get($name, $value);
}

/**
 * 删除 session
 *
 * @param  string $name
 * @return boolean
 */
function session_delete($name)
{
    return service('session')->remove($name);
}

/**
 * 按指定的长度切割字符串
 *
 * @param  string  $string 需要切割的字符串
 * @param  integer $length 长度
 * @param  string  $suffix 切割后补充的字符串
 * @return string
 */
function str_break($string, $length, $suffix = '')
{
    if (strlen($string) <= $length + strlen($suffix)) {
        return $string;
    }

    $n = $tn = $noc = 0;
    while ($n < strlen($string)) {
        $t = ord($string[$n]);
        if ($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
            $tn = 1; $n++; $noc++;
        } elseif (194 <= $t && $t <= 223) {
            $tn = 2; $n += 2; $noc += 2;
        } elseif (224 <= $t && $t < 239) {
            $tn = 3; $n += 3; $noc += 2;
        } elseif (240 <= $t && $t <= 247) {
            $tn = 4; $n += 4; $noc += 2;
        } elseif (248 <= $t && $t <= 251) {
            $tn = 5; $n += 5; $noc += 2;
        } elseif ($t == 252 || $t == 253) {
            $tn = 6; $n += 6; $noc += 2;
        } else {
            $n++;
        }
        if ($noc >= $length) {
            break;
        }
    }
    $noc > $length && $n -= $tn;
    $strcut = substr($string, 0, $n);
    if (strlen($strcut) < strlen($string)) {
        $strcut .= $suffix;
    }

    return $strcut;
}

/**
 * 字符串高亮
 *
 * @param  string  $string  需要的高亮的字符串
 * @param  mixed   $keyword 关键字，可以是一个数组
 * @return string
 */
function highlight_keyword($string, $keyword)
{
    $string = (string) $string;

    if ($string && $keyword) {
        if (! is_array($keyword)) {
            $keyword = array($keyword);
        }

        $pattern = array();
        foreach ($keyword as $word) {
            if (! empty($word)) {
                $pattern[] = '(' . str_replace('/', '\/',  preg_quote($word)) . ')';
            }
        }

        if (! empty($pattern)) {
            $string = preg_replace(
                '/(' . implode('|', $pattern) . ')/is',
                '<span style="background:#FF0;color:#E00;">\\1</span>',
                $string
            );
        }
    }

    return $string;
}

/**
 * 将 HTML 转换为文本
 *
 * @param  string $html
 * @return string
 */
function html2txt($html)
{
    $html = trim($html);
    if (empty($html))
        return $html;
    $search = array("'<script[^>]*?>.*?</script>'si",
        "'<style[^>]*?>.*?</style>'si",
        "'<[\/\!]*?[^<>]*?>'si",
        "'([\r\n])[\s]+'",
        "'&(quot|#34);'i",
        "'&(amp|#38);'i",
        "'&(lt|#60);'i",
        "'&(gt|#62);'i",
        "'&(nbsp|#160)[;]*'i",
        "'&(iexcl|#161);'i",
        "'&(cent|#162);'i",
        "'&(pound|#163);'i",
        "'&(copy|#169);'i",
        "'&#(\d+);'e"
    );
    $replace = array("", "", "", "\\1", "\"", "&", "<", ">", " ",
                     chr(161), chr(162), chr(163), chr(169), "chr(\\1)");

    return preg_replace($search, $replace, $html);
}

/**
 * 递归地合并一个或多个数组(不同于 array_merge_recursive)
 *
 * @return array
 */
function array_merge_deep()
{
    $a = func_get_args();
    for ($i = 1; $i < count($a); $i++) {
        foreach ($a[$i] as $k => $v) {
            if (isset($a[0][$k])) {
                if (is_array($v)) {
                    if (is_array($a[0][$k])) {
                        $a[0][$k] = array_merge_deep($a[0][$k], $v);
                    } else {
                        $v[] = $a[0][$k];
                        $a[0][$k] = $v;
                    }
                } else {
                    $a[0][$k] = is_array($a[0][$k]) ? array_merge($a[0][$k], array($v)) : $v;
                }
            } else {
                $a[0][$k] = $v;
            }
        }
    }

    return $a[0];
}

/**
 * Lowercase the first character of each word in a string
 *
 * @param  string $string
 * @return string
 */
function lcwords($string)
{
    $tokens = explode(' ', $string);
    if (! is_array($tokens) || count($tokens) <= 1) {
        return lcfirst($string);
    }

    $result = array();
    foreach ($tokens as $token) {
        $result[] = lcfirst($token);
    }

    return implode(' ', $result);
}
