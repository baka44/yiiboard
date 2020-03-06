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
        $posts = Posts::find()->where(['subject_id' => $subject->id, 'reply_to_post_id' => NULL])->count();
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

  //View category
  public function actionCategory($categoryId=1) {
    $result = [];
      $category = Categories::find()->where(['id' => $categoryId])->one();
      if (!$category){
        throw new \yii\web\NotFoundHttpException('Not found, obliviously');
      }
      $result["$category->name"]['id'] = $category->id;
      $subjects = Subjects::find()->where(['category_id' => $category->id])->all();
      foreach ($subjects as $subject){
        //Count only new subjects, skip replies
        $posts = Posts::find()->where(['subject_id' => $subject->id, 'reply_to_post_id' => NULL])->count();
        $result["$category->name"]['content']["$subject->name"]['count'] = $posts;
        $result["$category->name"]['content']["$subject->name"]['id'] = $subject->id;
      }
      return $this->render('category', ['result' => $result]);
  }
  //View Subjects threads
  public function actionSubject($subjectId = 1) {
    $result = [];
    $subject = Subjects::find()->where(['id' => $subjectId])->one();
    if (!$subject){
      throw new \yii\web\NotFoundHttpException('Not found, obliviously');
    }
    //Define subjects category
    $category = Categories::find()->where(['id' => $subject->category_id])->one();
    $result["$subject->name"]['category']['id'] = $category->id;
    $result["$subject->name"]['category']['name'] = $category->name;
    //Get all posts, skip replies
    $posts = Posts::find()->where(['subject_id' => $subject->id, 'reply_to_post_id' => NULL])->all();
    foreach ($posts as $post){
      $result["$subject->name"]['post'][$post->id] = $post;
      $result["$subject->name"]['id'] = $subject->id;
    }
    return $this->render('subject', ['result' => $result]);
  }

  //View Post
  public function actionPost($postId = 1) {
    $result = [];
    $post = Posts::find()->where(['id' => $postId])->one();
    if (!$post){
      throw new \yii\web\NotFoundHttpException('Not found, obliviously');
    }
    //Define post subject, category
    $subject = Subjects::find()->where(['id' => $post->subject_id])->one();
    $category = Categories::find()->where(['id' => $subject->category_id])->one();
    $result['post'] = $post;
    $result['subject'] = $subject;
    $result['category'] = $category;
    //Get all replies
    $replies = Posts::find()->where(['reply_to_post_id' => $postId])->all();
    foreach ($replies as $reply){
      $result['reply'][$reply->id] = $reply;
    }
    //echo "</pre>"; print_r($result); die;
    return $this->render('post', ['result' => $result]);
  }

}
