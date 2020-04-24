<?php

namespace app\controllers;

use app\models\Users;
use Yii;
use yii\base\Action;
use yii\base\Exception;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\Profile;
use app\models\Categories;
use app\models\Subjects;
use app\models\Posts;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;


class SiteController extends Controller
{
    /**
     * @param Action $action
     * @return bool
     * @throws BadRequestHttpException
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        if (parent::beforeAction($action)) {
            if (!\Yii::$app->user->can($action->id)) {
                throw new ForbiddenHttpException('You have no acces for this page, contact adminitrator to get more information');
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return array
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
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * @return string|Response
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
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * @return string
     */
    public function actionIndex()
    {
        $categories = Categories::find()->all();
        return $this->render('index', ['result' => $this->getCategoryContent($categories)]);
    }

    /**
     * @return string|Response
     * @throws \Exception
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            //Set role if its not setted
            $auth = Yii::$app->authManager;
            if (count($auth->getRolesByUser(Yii::$app->user->id)) < 2) {
                $role = $auth->getRole(Yii::$app->user->identity->role);
                $auth->assign($role, Yii::$app->user->id);
                //Revoke role if its changed, and set new
            } elseif (array_keys($auth->getRolesByUser(Yii::$app->user->id))[1] != Yii::$app->user->identity->role) {
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

    /**
     * @return mixed
     * @throws Exception
     */
    public function actionSignup()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $users = new Users();
            $users->username = $model->username;
            $users->password = \Yii::$app->security->generatePasswordHash($model->password);
            if ($users->save()) {
                return $this->redirect(['site/login']);
            } else {
                return $this->render('signup', ['model' => $model]);
            }
        }
        return $this->render('signup', ['model' => $model]);
    }


    /**
     * @return string
     */
    public function actionProfile()
    {
        $model = new Profile();
        $model->user = Yii::$app->user;
        return $this->render('profile', ['model' => $model->renderData()]);
    }

    /**
     * @param int $categoryId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionCategory($categoryId = 1)
    {
        $category = Categories::find()->where(['id' => $categoryId])->one();
        if (!$category) {
            throw new NotFoundHttpException('Not found, obliviously');
        }
        return $this->render('category', ['result' => $this->getCategoryContent(array($category))]);
    }

    /**
     * @param int $subjectId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionSubject($subjectId = 1)
    {
        $result = [];
        $subject = Subjects::find()->where(['id' => $subjectId])->one();
        if (!$subject) {
            throw new NotFoundHttpException('Not found, obliviously');
        }
        //Define subjects category
        $category = Categories::find()->where(['id' => $subject->category_id])->one();
        $result["$subject->name"]['category']['id'] = $category->id;
        $result["$subject->name"]['category']['name'] = $category->name;
        //Get all posts, skip replies
        $posts = Posts::find()->where(['subject_id' => $subject->id, 'reply_to_post_id' => NULL])->all();
        foreach ($posts as $post) {
            $result["$subject->name"]['post'][$post->id] = $post;
            $result["$subject->name"]['id'] = $subject->id;
        }
        return $this->render('subject', ['result' => $result]);
    }

    /**
     * @param int $postId
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionPost($postId = 1)
    {
        $result = [];
        $post = Posts::find()->where(['id' => $postId])->one();
        if (!$post) {
            throw new NotFoundHttpException('Not found, obliviously');
        }
        //Define post subject, category
        $subject = Subjects::find()->where(['id' => $post->subject_id])->one();
        $category = Categories::find()->where(['id' => $subject->category_id])->one();
        $result['post'] = $post;
        $result['subject'] = $subject;
        $result['category'] = $category;
        //Get all replies
        $replies = Posts::find()->where(['reply_to_post_id' => $postId])->all();
        foreach ($replies as $reply) {
            $result['reply'][$reply->id] = $reply;
        }
        return $this->render('post', ['result' => $result]);
    }

    /**
     * @param array $categories
     * @return array
     */
    private function getCategoryContent(array $categories = []){
        $result = [];
        foreach ($categories as $category) {
            $result["$category->name"]['id'] = $category->id;
            $subjects = Subjects::find()->where(['category_id' => $category->id])->all();
            foreach ($subjects as $subject) {
                $posts = Posts::find()->where(['subject_id' => $subject->id, 'reply_to_post_id' => NULL])->count();
                $result["$category->name"]['content']["$subject->name"]['count'] = $posts;
                $result["$category->name"]['content']["$subject->name"]['id'] = $subject->id;
            }
        }
        return $result;
    }

}
