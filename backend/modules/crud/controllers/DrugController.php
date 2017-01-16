<?php
/**
 * /var/www/html/yiiyak/console/runtime/giiant/49eb2de82346bc30092f584268252ed2
 *
 * @package default
 */



namespace backend\modules\crud\controllers;
use backend\modules\crud\models\Drug;
use backend\modules\crud\models\IcsrEvent;
use backend\modules\crud\models\IcsrTest;
use backend\modules\crud\models\search\Drug as DrugSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\helpers\Url;
use yii\filters\AccessControl;
use dmstr\bootstrap\Tabs;
use Yii;

/**
 * This is the class for controller "DrugController".
 */
class DrugController extends \backend\modules\crud\controllers\base\DrugController
{


    /**
     *
     * @inheritdoc
     * @return array
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [

                    [
                        'allow' => true,
                        'matchCallback' => function ($rule, $action) {
                            $user_id = \Yii::$app->user->id;
                            $drug_id = \Yii::$app->request->getQueryParam('id');
                            return Drug::checkAccess($user_id,$drug_id);
                        },
                    ]
                ]
            ]
        ];
    }
/**
	 * Lists all Drug models.
	 *
	 * @return mixed
	 */

	public function actionIndex() {

		$searchModel  = new DrugSearch;
               // $criteria = ;

        $_GET['Drug']['company_id'] = Yii::$app->user->identity->getCompany()->one()->id;
        $signaledDrugs =  Yii::$app->user->identity->company->getSignaledDrugs();
 		$dataProvider = $searchModel->search($_GET);

		Tabs::clearLocalStorage();

		Url::remember();
		\Yii::$app->session['__crudReturnUrl'] = null;

		return $this->render('index', [
				'dataProvider' => $dataProvider,
				'searchModel' => $searchModel,
                'signaledDrugs' => $signaledDrugs
			]);
	}

        public function actionCreate() {
		$model = new Drug;
        $_POST['Drug']['company_id'] = Yii::$app->user->identity->getCompany()->one()->id;

		try {
			if (Yii::$app->request->isPost && $model->load($_POST) ) {

                if (!($model->isBeyondLimit()) && $model->save()) {
                        return $this->redirect(Url::previous());
                     }

                if ($model->isBeyondLimit())
                {
                    \Yii::$app->getSession()->setFlash('error', \Yii::t('app',"you have exceeded your drugs limit upgrade {$model->company->name} plan to add more drugs "));

                }


			} elseif (!\Yii::$app->request->isPost) {
				$model->load($_GET);
			}
		} catch (\Exception $e) {
			$msg = (isset($e->errorInfo[2]))?$e->errorInfo[2]:$e->getMessage();
			$model->addError('_exception', $msg);
		}
		return $this->render('create', ['model' => $model]);
        }

        public function actionSummaryTabulation($id = null){
            $summary = [];
            $drug = null;
            if($id == null){
                $summaryResult = $this->getSummaryResult();

            }else{
                $drug = $this->findModel($id);
                $summaryResult = $this->getSummaryResultForDrug($id);
            }
            foreach ($summaryResult as $item){
                if(!isset($summary[$item['meddra_pt_text']])){
                    $summary[$item['meddra_pt_text']] = [
                        "Serious" => 0,
                        "notSerious" => 0
                    ];
                }
                if($item['is_serious'] == 1){
                    $summary[$item['meddra_pt_text']]['Serious'] = $item['interval'];
                }
                if($item['is_serious'] == 0){
                    $summary[$item['meddra_pt_text']]['notSerious'] = $item['interval'];
                }
            }
            return $this->render('summaryTabulation',[
                'summary' => $summary,
                'drug'=>$drug
            ]);
        }

        private function getSummaryResult(){
            $companyId = Yii::$app->user->identity->getCompany()->one()->id;
            $sql = "SELECT icsr_event.meddra_pt_text ,IF(icsr.is_serious = 0, 0, 1) AS 'is_serious' , count(icsr.is_serious) AS 'interval'
                      FROM icsr
                      INNER JOIN icsr_event
                    ON(icsr_event.icsr_id = icsr.id)
                        INNER JOIN drug
                          ON(icsr.drug_id = drug.id)
                        WHERE drug.company_id = :companyId
                    GROUP BY icsr_event.meddra_pt_text , icsr.is_serious";
            $summary = Yii::$app->db->createCommand($sql,[":companyId"=>$companyId])->queryAll();
            return $summary;
        }
    private function getSummaryResultForDrug($id){
        $sql = "SELECT icsr_event.meddra_pt_text ,IF(icsr.is_serious = 0, 0, 1) AS 'is_serious' , count(icsr.is_serious) AS 'interval'
                      FROM icsr
                      INNER JOIN icsr_event
                    ON(icsr_event.icsr_id = icsr.id)
                        INNER JOIN drug
                          ON(icsr.drug_id = drug.id)
                        WHERE drug.id = :id
                    GROUP BY icsr_event.meddra_pt_text , icsr.is_serious";
        $summary = Yii::$app->db->createCommand($sql,[":id"=>$id])->queryAll();
        return $summary;
    }


}
