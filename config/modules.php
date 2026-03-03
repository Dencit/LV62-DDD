<?php

use Nwidart\Modules\Activators\FileActivator;

return [

    /*
    |--------------------------------------------------------------------------
    | Module Namespace
    |--------------------------------------------------------------------------
    |
    | Default module namespace.
    |
    */

    'namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Module Stubs
    |--------------------------------------------------------------------------
    |
    | Default module stubs.
    |
    */

    'stubs' => [
        'enabled' => false,
        'path' => base_path() . '/extend\LaravelModules\Stubs',
        'files' => [
            'routes/cross' => 'Http/api.php',
//            'routes/client' => 'Http/Client/api.php',
//            'routes/platform' => 'Http/Platform/api.php',
            //
            'scaffold/config' => 'Config/Config.php',
            'enum' => 'Enums/Enum.php',
            'error' => 'Errors/Error.php',
            //
            //'webpack' => 'webpack.mix.js',
            //'package' => 'package.json',
            //'views/index' => 'Resources/views/index.blade.php',
            //'views/master' => 'Resources/views/layouts/master.blade.php',
            //'assets/js/app' => 'Resources/assets/js/app.js',
            //'assets/sass/app' => 'Resources/assets/sass/app.scss',
            //
            'composer' => 'composer.json',
        ],
        'replacements' => [
            'routes/cross' => ['LOWER_NAME'],
//            'routes/client' => ['LOWER_NAME'],
//            'routes/platform' => ['LOWER_NAME'],
            //
            'scaffold/config' => ['STUDLY_NAME'],
            'enum' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'error' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
            //
            'webpack' => ['LOWER_NAME'],
            'json' => ['LOWER_NAME', 'STUDLY_NAME', 'MODULE_NAMESPACE', 'PROVIDER_NAMESPACE'],
            'views/index' => ['LOWER_NAME'],
            'views/master' => ['LOWER_NAME', 'STUDLY_NAME'],
            //
            'composer' => [
                'LOWER_NAME',
                'STUDLY_NAME',
                'VENDOR',
                'AUTHOR_NAME',
                'AUTHOR_EMAIL',
                'MODULE_NAMESPACE',
                'PROVIDER_NAMESPACE',
            ],
        ],
        'gitkeep' => true,
    ],
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Modules path
        |--------------------------------------------------------------------------
        |
        | This path used for save the generated module. This path also will be added
        | automatically to list of scanned folders.
        |
        */

        'modules' => base_path('modules'),
        /*
        |--------------------------------------------------------------------------
        | Modules assets path
        |--------------------------------------------------------------------------
        |
        | Here you may update the modules assets path.
        |
        */

        'assets' => public_path('modules'),
        /*
        |--------------------------------------------------------------------------
        | The migrations path
        |--------------------------------------------------------------------------
        |
        | Where you run 'module:publish-migration' command, where do you publish the
        | the migration files?
        |
        */

        'migration' => base_path('Database/migrations'),
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Set the generate key to false to not generate that folder
        */
        'generator' => [
            'provider' => ['path' => 'Providers', 'generate' => true],        //模块注册
//#视图层
            'assets' => ['path' => 'Resources/assets', 'generate' => false],  //页面资源
            'lang' => ['path' => 'Resources/lang', 'generate' => false],      //多语言
            'views' => ['path' => 'Resources/views', 'generate' => false],    //视图

//#应用层 - 面向业务设计 - 定制化 - 低耦合

            'controller' => ['path' => 'Http/Controllers', 'generate' => true],             //中台 - 控制器
            'logic' => ['path' => 'Http/Logics', 'generate' => true],                   //中台 - 业务组合层 - 有共性的业务特征 需 沉淀到领域服务层

            'request' => ['path' => 'Requests', 'generate' => true],            //输入验证 - 调用输入验证规则
            'rules' => ['path' => 'Rules', 'generate' => false],                //验证规则 - 自定义验证规则
            'resource' => ['path' => 'Trans', 'generate' => true],              //输出转换规则 - 对应数据模型

//#领域层 - 面向抽象对象设计 - 原子化 - 高内聚

            'config' => ['path' => 'Config', 'generate' => true],              //模块设置
            'enum' => ['path' => 'Enums', 'generate' => true],                  //常量
            'error' => ['path' => 'Errors', 'generate' => true],                //业务异常码

            'filter' => ['path' => 'Middleware', 'generate' => false],          //路由中间件 - 用于区分授权用户权重

            'service' => ['path' => 'Srv', 'generate' => true],                //领域服务层 - 业务内聚函数
            'repository' => ['path' => 'Reposit', 'generate' => false],        //数据仓库 - 数据组合层 - 调用同名数据模型

            'model' => ['path' => 'Models', 'generate' => true],                //数据模型 - 数据内聚层 - 只许同名数据仓库调用
            'policies' => ['path' => 'Policies', 'generate' => false],          //数据策略 - 模型操作策略 - 限制数据操作权限

            'migration' => ['path' => 'Database/Migrations', 'generate' => true], //数据迁移-模型
            'seeder' => ['path' => 'Database/Seeders', 'generate' => true],       //数据迁移-数据
            'factory' => ['path' => 'Database/factories', 'generate' => false],   //数据迁移-工厂

            'command' => ['path' => 'Consoles', 'generate' => true],             //脚本
            'jobs' => ['path' => 'Jobs', 'generate' => true],                    //队列

            'event' => ['path' => 'Events', 'generate' => false],                //事件
            'listener' => ['path' => 'Listeners', 'generate' => false],          //监控

            'notifications' => ['path' => 'Notifications', 'generate' => false], //通知
            'emails' => ['path' => 'Emails', 'generate' => false],               //邮件
//#单测
            'test' => ['path' => 'Tests/Unit', 'generate' => false],
            'test-feature' => ['path' => 'Tests/Feature', 'generate' => false],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Scan Path
    |--------------------------------------------------------------------------
    |
    | Here you define which folder will be scanned. By default will scan vendor
    | directory. This is useful if you host the package in packagist website.
    |
    */

    'scan' => [
        'enabled' => false,
        'paths' => [
            base_path('vendor/*/*'),
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Composer File Template
    |--------------------------------------------------------------------------
    |
    | Here is the config for composer.json file, generated by this package
    |
    */

    'composer' => [
        'vendor' => 'nwidart',
        'author' => [
            'name' => 'Nicolas Widart',
            'email' => 'n.widart@gmail.com',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Caching
    |--------------------------------------------------------------------------
    |
    | Here is the config for setting up caching feature.
    |
    */
    'cache' => [
        'enabled' => false,
        'key' => 'laravel-modules',
        'lifetime' => 60,
    ],
    /*
    |--------------------------------------------------------------------------
    | Choose what laravel-modules will register as custom namespaces.
    | Setting one to false will require you to register that part
    | in your own Service Provider class.
    |--------------------------------------------------------------------------
    */
    'register' => [
        'translations' => true,
        /**
         * load files on boot or register method
         *
         * Note: boot not compatible with asgardcms
         *
         * @example boot|register
         */
        'files' => 'register',
    ],

    /*
    |--------------------------------------------------------------------------
    | Activators
    |--------------------------------------------------------------------------
    |
    | You can define new types of activators here, file, database etc. The only
    | required parameter is 'class'.
    | The file activator will store the activation status in storage/installed_modules
    */
    'activators' => [
        'file' => [
            'class' => FileActivator::class,
            'statuses-file' => base_path('modules_statuses.json'),
            'cache-key' => 'activator.installed',
            'cache-lifetime' => 604800,
        ],
    ],

    'activator' => 'file',
];
