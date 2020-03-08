<?php
namespace app\components;
use app\models\Posts;
use yii\base\Widget;
use yii\helpers\Html;

class PostOthers extends Widget
{
    public $id;
    public function run()
    {
        $posts = Posts::find()
            ->where(['hide' => 0])
            ->where(['not', ['id' => $this->id]])
            ->orderBy('rand()')
            ->limit(2)
            ->all();
        $trs = "";
        foreach ($posts as $post){
            $img = Html::tag('img', null, [
                'src' => $post->img,
                'alt' => $post->title
            ]);
            $td_1 = Html::tag('td', $img);
            $a_span = Html::tag("a", '&laquo;'.$post->title.'&raquo;', ['href' =>$post->link]);
            $a_span .= Html::tag('td', $post->date, ['class' =>'date']);
            $td_2 = Html::tag('td', $a_span);
            $trs .= Html::tag('tr', $td_1.$td_2);
        }
        return Html::tag('table', $trs, ['id' =>'post_others']);
    }
}