<?php
/**
 * /var/www/html/yiiyak/console/runtime/giiant/358b0e44f1c1670b558e36588c267e47
 *
 * @package default
 */


// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\crud\controllers\base;

use backend\modules\crud\models\Drug;
use backend\modules\crud\models\search\Drug as DrugSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;

/**
 * DrugController implements the CRUD actions for Drug model.
 */
class DrugController extends Controller
{

	/**
	 *
	 * @var boolean whether to enable CSRF validation for the actions in this controller.
	 * CSRF validation is enabled only when both this property and [[Request::enableCsrfValidation]] are true.
	 */
	public $enableCsrfValidation = false;



	/**
	 * Lists all Drug models.
	 *
	 * @return mixed
	 */
	public function actionIndex() {

		$searchModel  = new DrugSearch;
		$dataProvider = $searchModel->search($_GET);

		Tabs::clearLocalStorage();

		Url::remember();
		\Yii::$app->session['__crudReturnUrl'] = null;

		return $this->render('index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
			]);
	}


	/**
	 * Displays a single Drug model.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionView($id) {
		\Yii::$app->session['__crudReturnUrl'] = Url::previous();
		Url::remember();
		Tabs::rememberActiveState();
        $model = $this->findModel($id);
        $signaledDrugs =  \Yii::$app->user->identity->company->getSignaledDrugs();
        $meddra_pt_text = [];
        $signaledIcsrs = [];
        if ($signaledDrugs) {
            foreach ($signaledDrugs as $key => $row) {
                if ($row['drug_id'] == $model->id) {
                    $meddra_pt_text [] = $row['meddra_pt_text'];
                }
            }

            $signaledIcsrs  = $model->getSignaledIcsrs($meddra_pt_text);

        }

		return $this->render('view', [
				'model' => $model,
                'signaledIcsrs' => $signaledIcsrs,
			]);
	}


	/**
	 * Creates a new Drug model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 *
	 * @return mixed
	 */
	public function actionCreate() {
		$model = new Drug;

		try {
			if ($model->load($_POST) && $model->save()) {
				return $this->redirect(Url::previous());
			} elseif (!\Yii::$app->request->isPost) {
				$model->load($_GET);
			}
		} catch (\Exception $e) {
			$msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
			$model->addError('_exception', $msg);
		}
		return $this->render('create', ['model' => $model]);
	}


	/**
	 * Updates an existing Drug model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionUpdate($id) {
		$model = $this->findModel($id);

		if ($model->load($_POST) && $model->save()) {
			return $this->redirect(Url::previous());
		} else {
			return $this->render('update', [
					'model' => $model,
				]);
		}
	}


	/**
	 * Deletes an existing Drug model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 *
	 * @param integer $id
	 * @return mixed
	 */
	public function actionDelete($id) {
		try {
			$this->findModel($id)->delete();
		} catch (\Exception $e) {
			$msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
			\Yii::$app->getSession()->addFlash('error', $msg);
			return $this->redirect(Url::previous());
		}


		// TODO: improve detection
		$isPivot = strstr('$id', ',');
		if ($isPivot == true) {
			return $this->redirect(Url::previous());
		} elseif (isset(\Yii::$app->session['__crudReturnUrl']) && \Yii::$app->session['__crudReturnUrl'] != '/') {
			Url::remember(null);
			$url = \Yii::$app->session['__crudReturnUrl'];
			\Yii::$app->session['__crudReturnUrl'] = null;

			return $this->redirect($url);
		} else {
			return $this->redirect(['index']);
		}
	}


	/**
	 * Finds the Drug model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @throws HttpException if the model cannot be found
	 * @param integer $id
	 * @return Drug the loaded model
	 */
	protected function findModel($id) {
		if (($model = Drug::findOne($id)) !== null) {
			return $model;
		} else {
			throw new HttpException(404, 'The requested page does not exist.');
		}
	}


}
