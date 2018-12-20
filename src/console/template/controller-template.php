<?php
/** @var $constArr array */
/** @var $useClass string */

echo "<?php\n";
?>
use <?= $useClass ?>;
return <?php
echo "[\n";
foreach ($constArr as $key=>$value){
    echo "'{$key}'=>[\n";
    foreach ($value as $index=>$item){
        echo "'{$index}'=>{$item},\n";
    }
    echo "],\n";
}
echo "]\n";
?>;
