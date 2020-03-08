<?php


namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Courses extends ActiveRecord
{
    public $img;
    public $order;
    public $link;

    public function afterFind()
    {
        $site = Yii::$app->params["insite"];
        $this->link = $site."/order?product_ids=".$this->alias;
        $this->img = '/web/images/courses/'.$this->alias.'.png';
        $this->order = $site."/order?product_ids=".$this->srs_id;
    }

}