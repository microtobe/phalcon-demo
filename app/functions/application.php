<?php
/**
 * 项目相关函数
 */

/**
 * 项目更新时间
 *
 * @return integer
 */
function app_update_time()
{
    static $time = null;

    if ($time === null) {
        $cache = cache_get('app_update_time');
        if ($cache) return $cache;

        $time = time();
        cache_save('app_update_time', $time);
    }

    return $time;
}

/**
 * 加载 RequireJS
 *
 * @param  string $name
 * @return string
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
            urlArgs:"$urlArgs"
        });
    }
});
</script>
RJS;
}

/**
 * 加载 Bootup.js
 *
 * @return string
 */
function script_bootup()
{
    if (ENVIRONMENT === PRODUCTION) {
        return '<script type="text/javascript">' . file_get_contents(DOC_PATH.'/js/bootup.js') . '</script>';
    }

    return '<script type="text/javascript" src="'.static_url('js_src/bootup.js').'"></script>';
}
