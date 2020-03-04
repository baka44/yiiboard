<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\EntryForm;
use app\models\SignupForm;
use app\models\Users;
use app\models\Profile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
          //  'access' => [
          //      'class' => AccessControl::className(),
          //      'only' => ['logout'],
          //      'rules' => [
          //          [
          //              'actions' => ['logout'],
          //              'allow' => true,
          //              'roles' => ['@'],
          //          ],
          //      ],
          //  ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }
    public function beforeAction($action) {
      if (parent::beforeAction($action)) {
          if (!\Yii::$app->user->can($action->id)) {
              throw new \yii\web\ForbiddenHttpException('You have no acces for this page, contact adminitrator to get more information');
          }
          return true;
      } else {
          return false;
      }
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
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin() {
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
    /**
    *custom public function
    */
    public function actionSignup() {
      if (!Yii::$app->user->isGuest) {
          return $this->goHome();
      }
      $model = new SignupForm();
      if ($model->load(Yii::$app->request->post()) && $model->validate()){
        $users = new Users();
        $users->username = $model->username;
        $users->password = \Yii::$app->security->generatePasswordHash($model->password);
      //  echo "<pre>"; print_r($users); die;
        if($users->save()){
            return $this->goHome();
        }else{
          return $this->render('signup', ['model' => $model]);
        }
      }
      return $this->render('signup', ['model' => $model]);
    }
    public function actionProfile(){
      //print_r(Yii::$app->user);die;
      $model = new Profile();
      $model->user = Yii::$app->user;
      return $this->render('profile', ['model' => $model->renderData()]);
    }
    public function actionEntry()
    {
      $model = new EntryForm();
      if ($model->load(Yii::$app->request->post()) && $model->validate()){
        return $this->render('entry-confirm', ['model' => $model]);
      }else{
        return $this->render('entry', ['model' => $model]);
      }
    }
}
