{exec, spawn} = require 'child_process'
readline      = require 'readline'

spawnWithLog = (cmd, opts = [], closeMsg = 'done', rl = null) ->
  initTime = process.hrtime()
  child    = spawn cmd, opts
  {stdout} = child
  stdout.setEncoding 'utf8'
  stdout.on 'data', (data) -> console.log data
  stdout.on 'close', ->
    t = process.hrtime initTime
    msg = "-----------------------------------------\n
#{closeMsg} (took #{t[0]} s and #{t[1] / 1000000} ms)"
    console.log msg
    rl.close() if rl

confirm = (->
  isYes = (str) ->
    str.trim().toLowerCase() is 'y'

  (msg, yesFn, noFn) ->
    rl = readline.createInterface
      input: process.stdin
      output: process.stdout

    rl.question "#{msg}:", (answer) ->
      if isYes answer
        yesFn?(rl)
      else
        noFn?()
        rl.close()
)()

task 'build', 'build for test, no optimize', ->
  spawnWithLog 'r.js', ['-o', 'app.build.js', 'optimize=none'], 'Merge JS done'

task 'pro', 'build for production, uglifyjs optimized', ->
  spawnWithLog 'r.js', ['-o', 'app.build.js'], 'Merge/Optimize JS done'

task 'php', 'PHP Coding Standards Fixer', ->
  spawnWithLog 'bin/fix-php-coding-style.sh', []

task 'build-file', 'update app.build.js', ->
  # https://github.com/jrburke/r.js/blob/master/build/example.build.js
  BUILD_FILE = 'app.build.js'
  JS_DIR     = 'public/js_src'
  fs         = require 'fs'
  result     =
    appDir                 : 'public/js_src/'
    baseUrl                : '.'
    dir                    : 'public/js/'
    optimizeCss            : 'standard.keepLines'
    mainConfigFile         : 'public/js_src/config.js'
    skipDirOptimize        : yes
    normalizeDirDefines    : 'skip'
    preserveLicenseComments: no
    findNestedDependencies : yes
    stubModules            : []
    modules                : [
      name   : 'config',
      include: [
        'jquery'
      ]
    ]

  fs.readdirSync(JS_DIR).forEach (file) ->
    if (file.substr(-3) is '.js') and (not (file in ['config.js', 'bootup.js']))
      result.modules.push
        name   : file.replace('.js', '')
        exclude: ['config', 'coffee-script']

  _content = "// 不要直接编辑此文件，请运行 `cake build-file` 自动生成\n
(#{JSON.stringify(result, null, '\t')}
)\n"
  fs.writeFileSync BUILD_FILE, _content
  console.log 'done'
