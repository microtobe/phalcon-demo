<?php

/**
 * 获取配置文件内容
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
 */
function model($name)
{
    // 格式化类名
    $class = implode('_', array_map('ucfirst', explode('_', $name)));

    return new $class(Phalcon\DI::getDefault());
}

/**
 * 简化 Phalcon\Di::getDefault()->getShared($service)
 */
function service($service)
{
    return Phalcon\DI::getDefault()->getShared($service);
}

/**
 * 获取完整的 url 地址
 */
function url($uri = null)
{
    return service('url')->get($uri);
}

/**
 * 获取静态资源地址
 */
function static_url($uri = null)
{
    return service('url')->getStatic($uri);
}

/**
 * 获取包含域名在内的 url
 */
function baseurl($uri = null, $base = HTTP_BASE)
{
    return HTTP_BASE . ltrim($uri, '/');
}

/**
 * 根据 query string 参数生成 url
 */
function url_param($uri, array $params)
{
    return $uri . (strpos($uri, '?') ? '&' : '?') . http_build_query(array_unique($params));
}

/**
 * 加载局部视图
 */
function partial_view($partialPath, array $params = null)
{
    return service('view')->partial($partialPath, $params);
}

/**
 * 选择不同的视图来渲染，并做为最后的 controller/action 输出
 */
function pick_view($renderView)
{
    return service('view')->pick($renderView);
}

/**
 * 文本翻译简化方法
 */
function __($str, $from, $to = null)
{
    // @TODO 翻译
    return strtr($str, $from, $to);
}

/**
 * 返回格式化的 json 数据
 */
function json_it($array, $pretty = true, $unescaped = true)
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
 * 过滤系统路径
 */
function strip_path($path)
{
    return str_replace(ROOT_PATH, '~', $path);
}

/**
 * email格式检查 (支持验证host有效性)
 */
function is_email($email, $test_mx = false)
{
    if (preg_match('/^([_a-z0-9+-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i', $email)) {
        if ($test_mx) {
            list( , $domain) = explode("@", $email);

            return getmxrr($domain, $mxrecords);
        }

        return true;
    }

    return false;
}

/**
 * 检查是否效的 url
 */
function is_url($url)
{
    return preg_match('/^https?:\/\/([a-z0-9\-]+\.)+[a-z]{2,3}([a-z0-9_~#%&\/\'\+\=\:\?\.\-])*$/i', $url);
}

/**
 * CURL POST 请求
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
 * 获取缓存
 */
function cache_get($key)
{
    return service('cache')->get($key . '.cache');
}

/**
 * 写入缓存
 */
function cache_save($key, $data, $lifetime = 86400, $stopBuffer = false)
{
    return service('cache')->save($key . '.cache', $data, $lifetime, $stopBuffer);
}

/**
 * 删除缓存
 */
function cache_delete($key)
{
    return service('cache')->delete($key . '.cache');
}

/**
 * 因 number_format 默认参数带来的千分位是逗号的hack
 * 功能和 number_format一致，只是设定了固定的第3，4个参数
 */
function num_format($number, $decimals = 0)
{
    return number_format($number , $decimals, '.', '');
}
/**
 * 项目更新时间
 */
function app_update_time()
{
    $cache = cache_get('app_update_time');
    if ($cache) return $cache;

    $s = time();
    cache_save('app_update_time', $s);

    return $s;
}

/**
 * 加载 RequireJS
 */
function require_js($name)
{
    if (ENVIRONMENT === PRODUCTION) {
        $baseUrl  = static_url('js');
        $urlArgs  = app_update_time();
    } else {
        $baseUrl  = static_url('js_src');
        $urlArgs  = time();
    }

    $bootup = script_bootup();

    return <<<RJS
$bootup
<script type="text/javascript">
new BootUp([
    "$baseUrl/bower/require/index.js",
    "$baseUrl/config.js",
    "$baseUrl/$name.js"
], {
    version: "$urlArgs",
    success: function () {
        require({
            baseUrl:"$baseUrl",
            urlArgs:"$urlArgs",
            config:{i18n:{locale:"zh-cn"}}
        });
    }
});
</script>
RJS;
}

function script_bootup()
{
    if (ENVIRONMENT === PRODUCTION) {
        return '<script>' . file_get_contents(DOC_PATH.'/js/bootup.js') . '</script>';
    }

    return '<script src="'.static_url('js_src/bootup.js').'"></script>';
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
if (! function_exists('array_merge_deep')) {
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
}

/**
 * Make a string's first character lowercase
 *
 * @param  string $string
 * @return string
 */
if (! function_exists('lcfirst')) {
    function lcfirst($string)
    {
        $string = (string) $string;

        return empty($string) ? '' : strtolower($string{0}) . substr($string, 1);
    }
}

/**
 * Lowercase the first character of each word in a string
 *
 * @param  string $string
 * @return string
 */
if (! function_exists('lcwords')) {
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
}
