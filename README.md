# yii2-auth-setting
yii2 permission control
# Install

## use composer
    composer require icequeen/auth-config -vvv
## use github (use this for newest)   
    git clone https://github.com/IceQueenFly/yii2-auth-setting.git
# Usage And Example
    console配置文件中
    // 生成配置文件
            'store-work-config'=>[
                'class'=>\console\controllers\GetNameController::class,
                'readDic' => '@store/controllers',// 该文件下的控制器
                'moduleId' => 'app-store',
                'namespace' => 'store\\controllers',// @store/controllers文件下控制器的命名空间
                'saveFilePath' => '@common/auth/store/config/store-config.php',// 用来生成权限类
                'workFileNew' => '@common/auth/store/config/store-new.php',
                'workFileOld' => '@common/auth/store/config/store-old.php',// 配置文件的修改在此处
                'startId' => 1000,
                'exceptActionIds' => ['view'],
                'exceptControllerIds' => ['file','depend','home','site','area-depend','min-order','notice','search','upload'],
            ],
            // 生成权限类
            'auth-store-generator' => [
                'class' => \kriss\modules\auth\console\controllers\AuthGeneratorController::class,
                'genClass' => 'common\auth\store\StoreAuth',
                'configFile' => '@common/auth/store/config/store-config.php',
                'permissionId' => 30,
                'roleId' => 40,
                'generateFile'=>'@common/auth/store/StoreAuth.php',
            ],
            // 生成控制器配置文件
            'store-work-controller-config'=>[
                'class'=>\console\controllers\ModuleAuthSetController::class,
                'controllerAuthFile'=>'@common/auth/store/config/store-controller-config.php',
                'authFile' => '@common/auth/store/config/store-config.php',
                'authClass' => 'common\auth\store\StoreAuth',
            ],