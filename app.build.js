// 不要直接编辑此文件，请运行 `cake build-file` 自动生成
({
	"appDir": "public/js_src/",
	"baseUrl": ".",
	"dir": "public/js/",
	"optimizeCss": "standard.keepLines",
	"mainConfigFile": "public/js_src/config.js",
	"skipDirOptimize": true,
	"normalizeDirDefines": "skip",
	"preserveLicenseComments": false,
	"findNestedDependencies": true,
	"stubModules": [
		"cs"
	],
	"modules": [
		{
			"name": "config",
			"include": [
				"jquery",
				"underscore",
				"i18n",
				"nls/root/chart_notices",
				"nls/zh-cn/chart_notices"
			]
		},
		{
			"name": "quotes",
			"exclude": [
				"config",
				"coffee-script"
			]
		}
	]
})
