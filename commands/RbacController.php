<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use \app\rbac\UserRoleRule;

class RbacController extends Controller
{
    public function actionInit()
    {
        $authManager = \Yii::$app->authManager;

        // Create roles
        $admin  = $authManager->createRole('admin');
        $user = $authManager->createRole('user');
        $guest  = $authManager->createRole('guest');

        // Create simple, based on action{$NAME} permissions
        $signUp = $authManager->createPermission('signup');
        $login  = $authManager->createPermission('login');
        $logout = $authManager->createPermission('logout');
        $profile = $authManager->createPermission('profile');
        $error  = $authManager->createPermission('error');
        $index  = $authManager->createPermission('index');
        $view   = $authManager->createPermission('view');
        $create = $authManager->createPermission('create');
        $update = $authManager->createPermission('update');
        $delete = $authManager->createPermission('delete');
        $close = $authManager->createPermission('close');
        $about = $authManager->createPermission('about');
        $contact = $authManager->createPermission('contact');
        // Add permissions in Yii::$app->authManager
        $authManager->add($signUp);
        $authManager->add($login);
        $authManager->add($logout);
        $authManager->add($profile);
        $authManager->add($error);
        $authManager->add($index);
        $authManager->add($view);
        $authManager->add($create);
        $authManager->add($update);
        $authManager->add($delete);
        $authManager->add($close);
        $authManager->add($about);
        $authManager->add($contact);

        // Add rule, based on UserExt->group === $user->group
               $userRoleRule = new UserRoleRule();
               $authManager->add($userRoleRule);

               // Add rule "UserRoleRule" in roles
               $admin->ruleName  = $userRoleRule->name;
               $user->ruleName  = $userRoleRule->name;
               $guest->ruleName  = $userRoleRule->name;

               // Add roles in Yii::$app->authManager
               $authManager->add($guest);
               $authManager->add($user);
               $authManager->add($admin);

               // Add permission-per-role in Yii::$app->authManager
               // Guest
               $authManager->addChild($guest, $index);
               $authManager->addChild($guest, $login);
               $authManager->addChild($guest, $logout);
               $authManager->addChild($guest, $error);
               $authManager->addChild($guest, $signUp);
               $authManager->addChild($guest, $view);

               // USER
               $authManager->addChild($user, $create);
               $authManager->addChild($user, $update);
               $authManager->addChild($user, $profile);
               //$authManager->addChild($user, $guest);

               // Admin
               $authManager->addChild($admin, $about);
               $authManager->addChild($admin, $close);
               $authManager->addChild($admin, $delete);
               $authManager->addChild($admin, $user);
           }
       }
