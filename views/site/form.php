<?php
use \yii\widgets\ActiveForm;
use \yii\helpers\Html;
$f = ActiveForm::begin(['options' => ['enctype'=>'multipart/form-data']]);
?>
<?php if($email && $name){?>
    Вы ввели имя <b><?=$name?></b> и email <b><?=$email;?></b>!
<?php }?>
<?=$f->field($form, 'name');?>
<?=$f->field($form, 'email');?>
<?=$f->field($form, 'file')->fileInput()?>
<?=Html::submitButton("Отправить")?>

<?php  ActiveForm::end(); ?>
