<?php

namespace app\controllers;

use Yii;
use yii\helpers\Url;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\Users;
use app\models\Profile;
use app\models\Categories;
use app\models\Subjects;
use app\models\Posts;

class SiteController extends Controller {
//Check rights before we act
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
//basic functions
  public function actions() {
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

  public function actionLogout() {
      Yii::$app->user->logout();
      return $this->goHome();
  }

  public function actionContact() {
      $model = new ContactForm();
      if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
          Yii::$app->session->setFlash('contactFormSubmitted');

          return $this->refresh();
      }
      return $this->render('contact', [
          'model' => $model,
      ]);
  }

  public function actionAbout() {
      return $this->render('about');
  }
  // Site/Index
  public function actionIndex() {
    $categories = Categories::find()->all();
    $result = [];
    foreach ($categories as $category){
      $result["$category->name"]['id'] = $category->id;
      $subjects = Subjects::find()->where(['category_id' => $category->id])->all();
      foreach ($subjects as $subject){
        $posts = Posts::find()->where(['subject_id' => $subject->id])->count();
        $result["$category->name"]['content']["$subject->name"]['count'] = $posts;
        $result["$category->name"]['content']["$subject->name"]['id'] = $subject->id;
      }
    }
    return $this->render('index', ['result' => $result]);
  }
  // Login actions
  public function actionLogin() {
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }
    $model = new LoginForm();
    if ($model->load(Yii::$app->request->post()) && $model->login()) {
      //Set role if its not setted
      $auth = Yii::$app->authManager;
      if (count($auth->getRolesByUser(Yii::$app->user->id)) < 2){
        $role = $auth->getRole(Yii::$app->user->identity->role);
        $auth->assign($role, Yii::$app->user->id);
      //Revoke role if its changed, and set new
      } elseif ( array_keys($auth->getRolesByUser(Yii::$app->user->id))[1] != Yii::$app->user->identity->role ){
        $auth->revoke($auth->getRole(array_keys($auth->getRolesByUser(Yii::$app->user->id))[1]), Yii::$app->user->id);
        $role = $auth->getRole(Yii::$app->user->identity->role);
        $auth->assign($role, Yii::$app->user->id);
      }
      return $this->goBack();
    }

    $model->password = '';
    return $this->render('login', [
      'model' => $model,
    ]);
  }

  //Signup new user
  public function actionSignup() {
    if (!Yii::$app->user->isGuest) {
      return $this->goHome();
    }
    $model = new SignupForm();
    if ($model->load(Yii::$app->request->post()) && $model->validate()){
      $users = new Users();
      $users->username = $model->username;
      $users->password = \Yii::$app->security->generatePasswordHash($model->password);
      if($users->save()){
        return $this->redirect(['site/login']);
      }else{
        return $this->render('signup', ['model' => $model]);
      }
    }
    return $this->render('signup', ['model' => $model]);
  }

  //Profile still unfinished
  public function actionProfile() {
    $model = new Profile();
    $model->user = Yii::$app->user;
    return $this->render('profile', ['model' => $model->renderData()]);
  }

  //View category/subject/post
  public function actionView($category = 0, $subject = 0, $post = 0) {
      if (!$category && !$subject && !$post){
        return $this->redirect(['site/index']);
      }
        $result = [];
        if ($category){
          $cat = Categories::find()->where(['id' => $category])->one();
          if (!$cat){
            throw new \yii\web\NotFoundHttpException('Not found, obliviously');
          }
          $result["$cat->name"]['id'] = $cat->id;
          $subjects = Subjects::find()->where(['category_id' => $cat->id])->all();
          foreach ($subjects as $subject){
            $posts = Posts::find()->where(['subject_id' => $subject->id])->count();
            $result["$cat->name"]['content']["$subject->name"]['count'] = $posts;
            $result["$cat->name"]['content']["$subject->name"]['id'] = $subject->id;
          }
          return $this->render('view', ['result' => $result, 'type' => 'category']);
        }elseif($subject){
          $sub = Subjects::find()->where(['id' => $subject])->one();
          if (!$sub){
            throw new \yii\web\NotFoundHttpException('Not found, obliviously');
          }
          $cat = Categories::find()->where(['id' => $sub->category_id])->one();
          $result["$cat->name"]['id'] = $cat->id;
          $posts = Posts::find()->where(['subject_id' => $sub->id])->all();
          foreach ($posts as $post){
            $posts = Posts::find()->where(['subject_id' => $sub->id])->count();
            $result["$cat->name"]['content']["$sub->name"]['post'][$post->id] = $post;
            $result["$cat->name"]['content']["$sub->name"]['id'] = $sub->id;
            $result["$cat->name"]['content']["$sub->name"]['count'] = 0;
          }
          return $this->render('view', ['result' => $result, 'type' => 'subject']);
        }elseif($post){

        }

      //return $this->render('about');
    }
}
