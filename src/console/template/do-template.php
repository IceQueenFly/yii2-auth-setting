<?php
/** @var $constArr array */
/** @var $getName \console\controllers\GetNameController */

echo "<?php\n";
?>
return <?php
echo "[\n";
foreach ($constArr as $key=>$value){
    if(reset($constArr)===$value){
        echo "{$getName->startId}=>[\n";
    }else{
        echo "[\n";
    }

    foreach ($value as $key1=>$value1){
        if ($key1!='items'){
            echo "'{$key1}'=>'{$value1}',\n";
        }else{
            echo "'{$key1}'=>[\n";
            foreach ($value1 as $key2=>$value2){
                echo "[";
                foreach ($value2 as $key3=>$value3){
                    echo "'{$key3}'=>'{$value3}',";
                }
                echo "],\n";
            }
            echo "],\n";
        }
    }
    echo "],\n";
}
echo "]\n";
?>;
