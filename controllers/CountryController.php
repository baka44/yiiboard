<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\data\Pagination;
use app\models\Country;
use app\models\ListSelect;

class CountryController extends Controller
{
  public function actionIndex()
  {

    $query = Country::find();
    $model = new ListSelect();
    if ($model->load(Yii::$app->request->post()) && $model->validate()){
      $limit = $model->userLimit | 5;
      $pg = new Pagination([
        'defaultPageSize' => $limit,
        'totalCount' => $query->count()
       ]);
       $countries = $query->orderBy('name')
        ->offset($pg->offset)
        ->limit($pg->limit)
        ->all();
        return $this->render('index', [
          'countries' => $countries,
          'pagination' => $pg,
          'model' => $model,
          'limit' => $limit
        ]);
    }
    $limit = $model->userLimit | 5;
    $pg = new Pagination([
      'defaultPageSize' => $limit,
      'totalCount' => $query->count()
     ]);
     $countries = $query->orderBy('name')
      ->offset($pg->offset)
      ->limit($pg->limit)
      ->all();
      return $this->render('index', [
        'countries' => $countries,
        'pagination' => $pg,
        'model' => $model,
        'limit' => $limit
      ]);
  }
}

?>
