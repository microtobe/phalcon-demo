<?php

/**
 * 多语言处理
 */
class I18n
{
    /**
     * 默认语言
     *
     * @var string
     */
    protected $_default = 'en-us';

    /**
     * 语言目录
     *
     * @var array
     */
    protected $_directories = array();

    /**
     * 已加载的语言包
     *
     * @var array
     */
    protected $_packages = array();

    /**
     * 所有语言缓存
     *
     * @var array
     */
    protected $_cached = array();

    /**
     * 语言别名
     *
     * @var array
     */
    protected $_aliases = array();

    /**
     * 构造方法，通过 protected 保持单例
     */
    public function __construct($lang = null)
    {
        if ($lang !== null) {
            $this->setDefault($lang);
        }
    }

    /**
     * 增加语言目录
     *
     * @param  string|array $dirs
     * @return I18n
     */
    public function addDirectory($dirs)
    {
        if (! is_array($dirs)) {
            $dirs = array($dirs);
        }

        // 获取新增的目录
        $newDirs = array();
        foreach ($dirs as $dir) {
            $dir = realpath($dir);
            if ($dir !== false && ! in_array($dir, $this->_directories)) {
                array_push($newDirs, $dir);
            }
        }

        if ($newDirs) {
            // 加入到目录列表
            $this->_directories = array_merge($this->_directories, $newDirs);

            // 清空缓存
            $this->_cached = array();
        }

        return $this;
    }

    /**
     * 设置默认语言
     *
     * @param  string $lang
     * @return I18n
     */
    public function setDefault($lang)
    {
        $this->_default = $this->getLangByAlias($lang);

        return $this;
    }

    /**
     * 返回默认语言
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->_default;
    }

    /**
     * 添加语言别名
     *
     * @param  array $aliases
     * @return I18n
     */
    public function addAliases(array $aliases)
    {
        foreach ($aliases as $key => $values) {
            $key = strtolower($key);

            if (! isset($this->_aliases[$key])) {
                $this->_aliases[$key] = array();
            }

            if (! is_array($values)) {
                $values = array($values);
            }

            foreach ($values as $alias) {
                if (! in_array($alias, $this->_aliases[$key])) {
                    array_push($this->_aliases[$key], $alias);
                }
            }
        }

        // reset the default language
        $this->setDefault($this->_default);

        return $this;
    }

    /**
     * 根据别名获取语言类型
     *
     * @return string
     */
    public function getLangByAlias($alias)
    {
        $alias = strtolower($alias);

        foreach ($this->_aliases as $lang => $aliases) {
            if (in_array($alias, $aliases)) {
                return $lang;
            }
        }

        return $alias;
    }

    /**
     * 判断语言是否存在
     *
     * @param  string  $lang
     * @return boolean
     */
    public function hasLang($lang)
    {
        $lang = $this->getLangByAlias($lang);

        foreach ($this->_directories as $dir) {
            if (is_dir("{$dir}/{$lang}")) {
                return true;
            }
        }

        return false;
    }

    /**
     * 加载语言包
     *
     * @param  string|array $packages
     * @return I18n
     */
    public function import($packages)
    {
        if (! is_array($packages)) {
            $packages = array($packages);
        }

        // 新增的语言包
        $diff = array_diff($packages, $this->_packages);

        // 合并到已加载的语言包中
        $this->_packages = array_merge($this->_packages, $diff);

        // 载入新增的语言包
        foreach (array_keys($this->_cached) as $lang) {
            $this->_loadPackages($diff, $lang);
        }

        return $this;
    }

    /**
     * 执行翻译
     *
     * @param  string $string
     * @param  array  $values
     * @param  string $lang
     * @return string
     */
    public function translate($string, array $values = NULL, $lang = null)
    {
        if ($lang === null) {
            $lang = $this->getDefault();
        }

        $lang = strtolower($lang);

        // 初始化加载
        if (! isset($this->_cached[$lang])) {
            $this->_initialize($lang);
        }

        // 语言包键名
        $key = (strpos($string, '.') === false) ? "$lang.$string" : $string;

        // 自动载入语言包
        $package = substr($key, 0, strrpos($key, '.'));
        if ($package !== $lang && ! in_array($package, $this->_packages)) {
            $this->import($package, $lang);
        }

        // 转换翻译
        $translate = isset($this->_cached[$lang][$key]) ? $this->_cached[$lang][$key] : $string;

        return is_array($values) ? strtr($translate, $values) : $translate;
    }

    /**
     * 初始化语言
     *
     * @param string $lang
     */
    protected function _initialize($lang)
    {
        $lang     = strtolower($lang);
        $packages = $this->_packages;

        // 将语言默认包加入
        array_unshift($packages, $lang);

        // 加载默认语言包
        $this->_loadPackages($packages, $lang);
    }

    /**
     * 加载语言包
     *
     * @param array  $packages
     * @param string $lang
     */
    protected function _loadPackages(array $packages, $lang)
    {
        $lang = strtolower($lang);

        // 初始化缓存
        if (! isset($this->_cached[$lang])) {
            $this->_cached[$lang] = array();
        }

        $cached = & $this->_cached[$lang];

        // 扫描目录并加载语言包
        foreach ($packages as $package) {
            foreach ($this->_directories as $dir) {
                $file = "{$dir}/{$lang}/" . str_replace('.', '/', $package) . '.php';
                if (is_file($file)) {
                    $data = include $file;
                    if (is_array($data)) {
                        foreach ($data as $key => $value) {
                            $cached["$package.$key"] = $value;
                        }
                    }
                }
            }
        }
    }

}
