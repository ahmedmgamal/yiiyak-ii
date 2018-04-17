<?php
/**
 * /var/www/html/yiiyak/console/runtime/giiant/358b0e44f1c1670b558e36588c267e47
 *
 * @package default
 */


// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\crud\controllers\base;

use backend\modules\crud\models\base\LkpLimits;
use backend\modules\crud\models\Drug;
use backend\modules\crud\models\LkpRoute;
use backend\modules\crud\models\search\Drug as DrugSearch;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Url;
use dmstr\bootstrap\Tabs;
use backend\modules\crud\models\search\Icsr as IcsrSearch;
use backend\modules\crud\models\search\Rmp as RmpSearch;
use backend\modules\crud\models\search\Prsu as PrsuSearch;
use yii\web\UploadedFile;
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
        $model = $this->findModel($id);
        $signalValues= $model->get_signalDetectionArray();


		\Yii::$app->session['__crudReturnUrl'] = Url::previous();
		Url::remember();
		Tabs::rememberActiveState();
        $model = $this->findModel($id);
        $signaledDrugs =  \Yii::$app->user->identity->company->getSignaledDrugs();


        $signaledIcsrs  = $model->getSignaledIcsrsAndIcsrEvenets($signaledDrugs);
        $icsrSeachModel = new IcsrSearch();
        $icsrDataProvider = $icsrSeachModel->search($_GET, 'real');
        $icsrSeachModel = new IcsrSearch();
        $icsrDrafted = $icsrSeachModel->search($_GET, 'draft');

        $rmpSearchModel = new RmpSearch();
        $rmpDataProvider = $rmpSearchModel->search($_GET);

        $prsuSearchModel = new PrsuSearch();
        $prsuDataProvider = $prsuSearchModel->search($_GET);

		return $this->render('view', [
				'model' => $model,
                'signaledDrugs' => $signaledDrugs,
                'signaledIcsrs' => $signaledIcsrs,
                'icsrSeachModel' => $icsrSeachModel,
                'icsrDataProvider' => $icsrDataProvider,
                'icsrDrafted' => $icsrDrafted,
                'signal_detection'=>$signalValues,
                'rmpSearchModel' => $rmpSearchModel,
                'rmpDataProvider' => $rmpDataProvider,
                'prsuSearchModel' => $prsuSearchModel,
                'prsuDataProvider' => $prsuDataProvider
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

    public function actionExcelUpload(){
            $model = new Drug;
            if(\Yii::$app->request->isPost){
                $company = \Yii::$app->user->identity->company;
                $planLimit = $company->getDrugsLimit();
                $companyDrugs = $company->getDrugsCount();
                $maxDrugsUpload = $planLimit - $companyDrugs;
                if($maxDrugsUpload <= 0){
                    \Yii::$app->getSession()->addFlash('error', \Yii::t("app","Max Plan Drugs Limit."));
                    return $this->redirect(['index']);
                }
                if($this->validateUploadedExcel()){
                    $excel =$_FILES['excel']['tmp_name'];
                    $data = \moonland\phpexcel\Excel::import($excel,[
                        'setFirstRecordAsKeys' => true,
                        'setIndexSheetByName' => true
                    ]);
                    $drugs = $this->createUploadData($data);
                    if(count($drugs) > $maxDrugsUpload){
                        \Yii::$app->getSession()->addFlash('error', \Yii::t("app","Max Plan Drugs Limit."));
                        return $this->render('upload',["model"=>$model]);
                    }
                    $result =$this->bulkInsert($drugs);
                    \Yii::$app->getSession()->addFlash('success', $result . " Records Uploaded successfully.");
                    return $this->redirect(['index']);
                }else{
                    \Yii::$app->getSession()->addFlash('error',\Yii::t("app","Invalid Excel File.") );
                    return $this->render('upload',["model"=>$model]);
                }
            }else{
                return $this->render('upload',["model"=>$model]);
            }


    }
    public function actionExcelDownload(){
        $path = \Yii::getAlias('@webroot').'/sample_files/';
        $fileName = StringHelper::basename("upload.xlsx");
        $path .= $fileName;
        \Yii::$app->response->sendFile($path);
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

	private function getRouteLkpId($name,$LkpRoutes){
	    $otherId = null;
	    foreach ($LkpRoutes as $lkpRoute){
	        if(strtolower(trim($name)) == strtolower(trim($lkpRoute->description))){
	            if($name == 'Other'){
                    $otherId = $lkpRoute->id;
                }
	            return $lkpRoute->id;
            }
        }
        return $otherId;
    }

    private function createUploadData($data){
        $drugs = [];
        $firstKey = key($data);
        if(gettype($firstKey) == "string"){
            foreach ($data as $sheet){
                $tem = $this->loadSheet($sheet);
                $drugs = array_merge($drugs,$tem);
            }
        }else{
            $drugs = $this->loadSheet($data);
        }
        return $drugs;
    }
    private function loadSheet($data){
        $company = \Yii::$app->user->identity->company;
        $LkpRoutes = LkpRoute::find()->all();
        foreach ($data as $record){
            $drugs[] = [
                $record['Generic Name'],
                $record['Trade Name'],
                $record['Dosage Form'],//composition
                $company->id,
                $record['Manufacturer'],
                $record['Strength'],
                $this->getRouteLkpId($record['Route Of Administration'],$LkpRoutes)
            ];
        }
        return $drugs;
    }
    private function bulkInsert($data){
       $result =  \Yii::$app->db
            ->createCommand()
            ->batchInsert('drug',
                ['generic_name','trade_name','composition','company_id','manufacturer','strength','route_lkp_id'],
                $data)->execute();
       return $result;
    }

    private function validateUploadedExcel(){
        $exts = ['xlsx','xls'];
        if(is_uploaded_file($_FILES['excel']['tmp_name'])) {
            $extension = pathinfo($_FILES['excel']['name'], PATHINFO_EXTENSION);
            if(in_array($extension,$exts)){
                return true;
            }
        }
        return false;
    }


}
