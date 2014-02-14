// JS 库配置
require.config({
	paths: {
		// 各种库
		jquery    : 'bower/jquery/index',

		// require.js 扩展
		cs  : 'bower/cs/index',    // 支持 CoffeeScript https://github.com/jrburke/require-cs
		i18n: 'bower/i18n/index',  // 多语言版本        https://github.com/requirejs/i18n

		'coffee-script': 'bower/coffee-script/index'
	},
	shim: {
	},
	waitSeconds: 100 // ie load modules timeout bug fixed
});
