<?php
 
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);
 
return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'basePath' => '@app/modules/v1',
            'class' => 'api\modules\v1\Module'   // here is our v1 modules
        ]
    ],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/country',   // our country api rule,
                    'tokens' => [
                        '{id}' => '<id:\\w+>'
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => 'v1/test',
                ],
                [
                    'class'=>'yii\rest\UrlRule',  //<-- this is the standard rule class
                    'controller'=>'v1/sensor',    // <-- which controller   "contacts/{rule}"        
                    'extraPatterns'=>[
                        'GET get-list-sensor' => 'get-list-sensor',
                        'POST set-data-track' => 'set-data-track', 
                        'GET set-data-track-sim' => 'set-data-track-sim', 
                    ]  
                ],
                [
                    'class'=>'yii\rest\UrlRule',  //<-- this is the standard rule class
                    'controller'=>'v1/asset-item',    // <-- which controller   "contacts/{rule}"        
                    'extraPatterns'=>[
                        'GET hello' => 'hello',
                        'GET index' => 'index',
                        'GET all' => 'all',
                        'GET get-item' => 'get-item',
                        'GET all-filter-by-category' => 'all-filter-by-category',
                        'GET search-by-name' => 'search-by-name',
                        'GET search-by-code' => 'search-by-code',
                        //'GET get-list-sensor' => 'get-list-sensor',
                        //'POST set-data-track' => 'set-data-track', 
                        //'GET set-data-track-sim' => 'set-data-track-sim', 
                    ]  
                ],
                [
                    'class'=>'yii\rest\UrlRule',  //<-- this is the standard rule class
                    'controller'=>'v1/asset-mapping',    // <-- which controller   "contacts/{rule}"        
                    'extraPatterns'=>[
                        'GET hello' => 'hello',
                        'GET index' => 'index',
                        'GET all' => 'all',
                        //'GET get-list-sensor' => 'get-list-sensor',
                        'POST post-location' => 'post-location', 
                        //'GET set-data-track-sim' => 'set-data-track-sim', 
                    ]  
                ],
                [
                    'class'=>'yii\rest\UrlRule',  //<-- this is the standard rule class
                    'controller'=>'v1/asset-category',    // <-- which controller   "contacts/{rule}"        
                    'extraPatterns'=>[
                        'GET hello' => 'hello',
                        'GET all' => 'all',
                        'GET get-item' => 'get-item',
                        //'GET get-list-sensor' => 'get-list-sensor',
                        //'POST set-data-track' => 'set-data-track', 
                        //'GET set-data-track-sim' => 'set-data-track-sim', 
                    ]  
                ],
            ],
            'as cors' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => true,
                    'Access-Control-Max-Age' => 3600,
                    'Access-Control-Expose-Headers' => ['*'],
                ],
            ],
        ]
    ],
    'params' => $params,
];