<?php

/**
 * 获取配置文件内容
 */
function config($name)
{
    static $cached = array();

    if (! isset($cached[$name])) {
        $file = APP_PATH . '/config/' . $name . '.php';

        if (is_file($file)) {
            $cached[$name] = include $file;
        }
    }

    return isset($cached[$name]) ? $cached[$name] : null;
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
 * 将数组转换为xml
 *
 * @see http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes/
 */
function array2xml(array $array, $node = 'root')
{
    return LSS\Array2XML::createXML($node, $array)->saveXML();
}

/**
 * 将 xml 转换为数组
 *
 * @see http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array/
 */
function xml2array($xml)
{
    return LSS\XML2Array::createArray($xml);
}

/**
 * 简化日志写入方法
 */
function write_log($name, $message, $type = null, $addUrl = false)
{
    static $logger;

    if (! isset($logger[$name])) {
        $logfile = ROOT_PATH . '/logs/' . date('/Ym/') . $name . '_' . date('Ymd') . '.log';
        if (! is_dir(dirname($logfile))) {
            mkdir(dirname($logfile), 0755, true);
        }

        $logger[$name] = new Phalcon\Logger\Adapter\File($logfile);
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
 * 获取某个浮点数的 digit，比如 12.39719 返回 5
 *
 * @param  float  $number
 * @return int
 */
function get_digits($number)
{
    return strlen(preg_replace('/\d+\./', '', $number));
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
<script>
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
