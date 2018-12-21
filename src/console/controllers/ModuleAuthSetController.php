<?php

namespace icequeen\auth\console\controllers;

use kriss\modules\auth\components\CodeFile;
use Yii;
use yii\console\Controller;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

class ModuleAuthSetController extends Controller
{
    public $controllerAuthFile = "@console/backend-controller-config.php";
    public $authFile = "@console/backend-config.php";
    public $authClass = 'console\Auth';

    public function actionIndex()
    {
        $authConfig = require(Yii::getAlias($this->authFile));
        $controllerAuthArray = [];
        $authName = $this->getClassName($this->authClass);
        foreach ($authConfig as $controller) {
            $cKey = $controller['key'];
            $controllerPlaceholder=str_replace(MakeAuthConfigController::PLACEHOLDER,'/',$cKey);
            $arr= array_map(function ($item){
                return Inflector::camel2id($item, '-');
            },explode('/',$controllerPlaceholder));
            $controllerId = implode('/',$arr);
            $controllerAuthArray[$controllerId] = [];
            foreach ($controller['items'] as $item) {
                $aKey=$item['key'];
                $const = strtoupper(Inflector::camel2id($cKey, '_').'_'.Inflector::camel2id($aKey, '_'));
                $controllerAuthArray[$controllerId][$item['id']] = "{$authName}::{$const}";
            }
        }
        $this->getFile($controllerAuthArray,$this->controllerAuthFile);
    }

    private function getFile($constArr, $fileName, $template = "@icequeen/console/template/controller-template.php")
    {
        $fileName = Yii::getAlias($fileName);
        $file = new CodeFile($fileName, $this->renderFile(Yii::getAlias($template), [
            'constArr' => $constArr,
            'useClass'=>$this->authClass,
        ]));
        $result = $file->save();
        if ($result) {
            echo "成功\n";
        } else {
            echo "失败\n";
        }

        return $result;
    }

    /**
     * 获取 class 的名字
     * @param $class
     * @return string
     */
    public function getClassName($class)
    {
        return StringHelper::basename($class);
    }

}
