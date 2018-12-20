<?php

namespace zyq\console\controllers;

use kriss\modules\auth\components\CodeFile;
use ReflectionClass;
use ReflectionMethod;
use Yii;
use yii\base\Module;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class MakeAuthConfigController extends Controller
{
    public $readDic = "@backend/controllers"; // 哪个文件下的controller
    public $moduleId = 'app-backend'; // 模块ID
    public $namespace = "backend\\controllers";// controller 的命名空间
    public $saveFilePath = "@console/backend-config.php"; // 生成的配置文件
    public $workFileNew = "@console/backend-new.php"; // 生成的新的配置
    public $workFileOld = "@console/backend-old.php"; // 旧的配置文件,修改权限的名称在此文件
    public $startId = 10000;
    public $exceptControllerIds = [];// 不包括的的控制器
    public $exceptActionIds = [];// 不包括的的方法

    const PLACEHOLDER='ControllerPlaceholder'; // 控制器文件下的文件里的控制器id中的‘/’占位符

    public function actionIndex()
    {
        $config['file'] = $this->getControllerName();
        $config['namespace'] = $this->namespace;
        $config['module-id'] = $this->moduleId;
        $configArr = $this->getConfig($config);
        $oldOk = true;
        if (!file_exists(Yii::getAlias($this->workFileOld))) {
            $oldOk = $this->getFile($configArr, $this->workFileOld);
        }

        $newOk = $this->getFile($configArr, $this->workFileNew);
        if (!($oldOk && $newOk)) {
            return;
        }
        $oldConfig = require(Yii::getAlias($this->workFileOld));
        $newConfig = require(Yii::getAlias($this->workFileNew));
        $dataConfig = ArrayHelper::merge($newConfig, $oldConfig);
        $this->getFile($dataConfig, $this->workFileOld);
        $this->getFile($dataConfig, $this->saveFilePath, '@console/template/do-template.php');
    }

    // 生成配置文件
    private function getFile($constArr, $fileName, $template = "@console/template/template.php")
    {
        $fileName = Yii::getAlias($fileName);
        $file = new CodeFile($fileName, $this->renderFile(Yii::getAlias($template), [
            'constArr' => $constArr,
            'getName' => $this,

        ]));
        $result = $file->save();
        if ($result) {
            echo "成功\n";
        } else {
            echo "失败\n";
        }

        return $result;
    }

    private function makeConfig($name, $namespace, $moduleId)
    {
        $this->exceptActionIds = array_merge($this->exceptActionIds, ['previous-redirect']);
        $controllerName = "{$namespace}\\{$name}";
        $controllerHumpName = str_replace('\\','/',lcfirst(str_replace('Controller', '', $name)));
        $controllerId = Inflector::camel2id($controllerHumpName, '-');
        if (in_array($controllerId, $this->exceptControllerIds)) {
            return false;
        }
        $methodsName = [];
        // 获取非actions中的action
        $reflection = new ReflectionClass($controllerName);
        $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $item) {
            $splitStr = Inflector::camel2id($item->name, '-');
            $nameArr = explode('-', $splitStr);
            if (reset($nameArr) == 'action') {
                unset($nameArr[0]);
                $name = implode('-', $nameArr);
                $methodsName[] = $name;
            }

        }


        // 获取actions中的方法
        $module = new Module($moduleId);
        /** @var \yii\base\Controller $controller */
        $controller = new $controllerName($controllerId, $module);
        $methodsName = array_merge($methodsName, array_keys($controller->actions()));

        $controllerArray = [];
        $controllerArray['key'] = str_replace('/',static::PLACEHOLDER,$controllerHumpName);
        $controllerArray['name'] =  str_replace('/',static::PLACEHOLDER,$controllerHumpName);
        foreach ($methodsName as $value) {
            if (in_array($value, $this->exceptActionIds)) {
                continue;
            }
            $controllerArray['items'][] = [
                'key' => lcfirst(Inflector::id2camel($value)),
                'name' => lcfirst(Inflector::id2camel($value)),
                'id' => $value,
            ];
        }
        return $controllerArray;
    }

    // 得到配置数组
    private function getConfig($store)
    {
        $array = [];
        foreach ($store['file'] as $item) {
            try {
                $data = $this->makeConfig($item, $store['namespace'], $store['module-id']);
                is_array($data) ? $array[] = $data : false;
            } catch (\Exception $exception) {
                echo "error:{$store['module-id']}{$item}\n";
                continue;
            }

        }
        return $array;
    }

    // 读取文件夹下的文件
    private function readAllDir($dir)
    {

        $result = array();
        $handle = opendir($dir);//读资源
        if ($handle) {
            while (($file = readdir($handle)) !== false) {
                if ($file != '.' && $file != '..') {
                    $curPath = $dir . DIRECTORY_SEPARATOR . $file;
                    if (is_dir($curPath)) {//判断是否为目录，递归读取文件
                        $result = $this->readAllDir($curPath);
                    } else {
                        $result[] = $dir . DIRECTORY_SEPARATOR . $file;
                    }
                }
            }
            closedir($handle);
        }
        return $result;
    }

    // 获取控制器名称
    private function getControllerName()
    {
        $controllerNames = $this->readAllDir(Yii::getAlias($this->readDic));
        foreach ($controllerNames as $key => $value) {
            // 获取控制器id名例如：account\AccountController，AccountController
            $controllerNames[$key] =str_replace('.php', '', str_replace(Yii::getAlias('@store/controllers') . '\\', '', $value)) ;

        }
        return $controllerNames;
    }
}