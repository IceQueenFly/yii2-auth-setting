# yii2-auth-setting
yii2 permission control
# Install

## use composer
    composer require icequeen/auth-config -vvv
## use github (use this for newest)   
    git clone https://github.com/IceQueenFly/yii2-auth-setting.git
## after install
    bootstrap.php write after other Alisa
    Yii::setAlias('@icequeen', dirname(dirname(__DIR__)) . '/vendor/icequeen');
    Yii::setAlias('@icequeen/auth', dirname(dirname(__DIR__)) . '/vendor/icequeen/auth-config/src');
# Usage And Example
    console配置文件中
    // 生成配置文件
                    'admin-work-config'=>[
                        'class'=>\icequeen\auth\console\controllers\MakeAuthConfigController::class,
                        'readDic' => '@admin/controllers',
                        'moduleId' => 'app-admin',
                        'namespace' => 'admin\\controllers',
                        'saveFilePath' => '@common/auth/admin/config/admin-config.php',
                        'workFileNew' => '@common/auth/admin/config/admin-new.php',
                        'workFileOld' => '@common/auth/admin/config/admin-old.php',
                        'startId' => 1000,
                        'exceptActionIds' => ['view'],
                        'exceptControllerIds' => ['file','depend','home','site','area-depend','min-order','notice','search','upload'],
                    ],
                    // 生成权限类
                    'auth-admin-generator' => [
                        'class' => \kriss\modules\auth\console\controllers\AuthGeneratorController::class,
                        'genClass' => 'common\auth\admin\AdminAuth',
                        'configFile' => '@common/auth/admin/config/admin-config.php',
                        'permissionId' => 30,
                        'roleId' => 40,
                        'generateFile'=>'@common/auth/admin/AdminAuth.php',
                    ],
                    // 生成控制器配置文件
                    'admin-work-controller-config'=>[
                        'class'=>\icequeen\auth\console\controllers\ModuleAuthSetController::class,
                        'controllerAuthFile'=>'@common/auth/admin/config/admin-controller-config.php',
                        'authFile' => '@common/auth/admin/config/admin-config.php',
                        'authClass' => 'common\auth\admin\AdminAuth',
                    ],