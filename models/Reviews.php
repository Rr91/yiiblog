<?php


namespace app\models;


use yii\db\ActiveRecord;

class Reviews extends ActiveRecord
{
    public function afterFind()
    {
        $this->video = $this->youtube("{youtube:".$this->video.",480,295}");
    }

    private function youtube($text)
    {
        $path = \Yii::$app->basePath.\Yii::$app->params["dir_tmpl"]."youtube.tpl";
        $reg = "/{youtube:([\w-_]*)?,(\d*)?,(\d*)?}/i";
        $text = preg_replace(
            $reg,
            str_replace(
                array("%name%", "%width%", "%height%"),
                array("\\1", "\\2", "\\3"),
                file_get_contents($path)
            ),
            $text
        );
        return $text;
    }
}