<?php

namespace app\controllers;

use app\models\Courses;
use app\models\Minicourses;
use app\models\MyForm;
use app\models\Reviews;
use app\models\SearchForm;
use app\models\SiteForm;
use app\models\Sites;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Posts;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */

    public function beforeAction($action)
    {
           $model = new SearchForm();
           if($model->load(Yii::$app->request->post()) && $model->validate()){
               $q = Html::encode($model->q);
               return $this->redirect(Yii::$app->urlManager->createUrl(['site/search', 'q'=>$q]));
           }
           return true;
    }

    public function actionSearch()
    {
        $q = Yii::$app->getRequest()->getQueryParam('q');
        $query = Posts::find()->where(['hide' =>0])->where(['like', 'full_text', $q]);
        $pagination = new Pagination([
            "defaultPageSize" => 2,
            "totalCount" =>$query->count(),
        ]);
        $posts = $query->offset($pagination->offset)
            ->limit($pagination->limit)->all();
        Posts::setNumbers($posts);
        return $this->render('search',
            [
                "posts" => $posts,
                "q" => $q,
                "pagination" => $pagination,
                "active_page" =>Yii::$app->request->get('page', 1),
                "count_pages" => $pagination->getPageCount(),
            ]
        );
    }

    public function actionIndex()
    {
        $query = Posts::find()->where(['hide' => 0]);
        $pagination = new Pagination([
            "defaultPageSize" => 2,
            "totalCount" =>$query->count(),
        ]);
        $posts = $query->orderBy(["date" => SORT_DESC])->offset($pagination->offset)
        ->limit($pagination->limit)->all();
        Posts::setNumbers($posts);
        return $this->render('index',
            [
                "posts" => $posts,
                "active_page" =>Yii::$app->request->get('page', 1),
                "count_pages" => $pagination->getPageCount(),
                "pagination" => $pagination,
            ]
        );
    }

    public function actionAuthor()
    {
        return $this->render("author");
    }

    public function actionVideo()
    {
        $courses = Courses::find()->orderBy(['id' =>SORT_DESC])->all();
        return $this->render('video', ["courses" => $courses]);
    }

    public function actionRev()
    {
        $reviews = Reviews::find()->orderBy('rand()')->all();
        return $this->render('rev', [
            'reviews' => $reviews
        ]);
    }

    public function actionSites()
    {
        $sites = Sites::find()->where(['!=',"active", "0"])->orderBy(["id"=>SORT_DESC])->all();
        return $this->render("sites", ['sites'=>$sites]);
    }

    public function actionPost()
    {
        $post = Posts::find()
            ->where(['id' => Yii::$app->getRequest()->getQueryParams('id')])
            ->one();
        Posts::setNumbers([$post]);
        return $this->render('post', ['post' =>$post]);
    }

    public function actionReleases()
    {

        $query = Posts::find()->where(['hide' => 0, 'is_release' => 1]);
        $pagination = new Pagination([
            'defaultPageSize' => 2,
            'totalCount' =>$query->count()
        ]);
        $posts = $query->orderBy(['date' =>SORT_DESC])
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();
        Posts::setNumbers($posts);

        $minicourses = Minicourses::find()->all();
        return $this->render("releases",[
            'posts' =>$posts,
            'minicourses' =>$minicourses,
            'count_pages' =>$pagination->getPageCount(),
            'pagination' =>$pagination
        ]);
    }

    public function actionAddsite()
    {
        $model = new SiteForm();
        if($model->load(Yii::$app->request->post()) && $model->validate()){
            $site = new Sites();
            $site->address = $model->address;
            $site->description = $model->description;
            $site->active = 0;
            $site->save();
            return $this->render('addsite', [
                'model' =>$model,
                'success' =>true,
                'error'=>false
            ]);
        }
        else{
            if(isset($_POST['address'])) $error = true;
            else $error = false;
            return $this->render('addsite', [
                'model' =>$model,
                'success' =>false,
                'error'=>$error
            ]);

        }
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

}
