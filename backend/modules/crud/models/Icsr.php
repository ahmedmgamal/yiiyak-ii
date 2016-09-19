<?php

namespace backend\modules\crud\models;

use Yii;
use \backend\modules\crud\models\base\Icsr as BaseIcsr;
use \backend\modules\crud\traits;
use bedezign\yii2\audit\models\AuditTrail;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "icsr".
 */
class Icsr extends BaseIcsr
{
    use traits\checkAccess;
    /**
     * @inheritdoc
     */
    public function attributeHints()
    {
        return array_merge(
            parent::attributeHints(),
            [
            'id' => Yii::t('app', 'A.1.0.1 Sender’s (case) safety report unique identifier safetyreport>   safetyreportid'),
            'drug_id' => Yii::t('app', 'Drug Id'),
                
    
            'patient_identifier' => Yii::t('app', 'B.1.1 Patient (name or initials)'),
            'patient_age' => Yii::t('app', 'B.1.2.2a Age at time of onset of reaction or event'),
            'patient_age_unit' => Yii::t('app', 'B.1.2.2b Age at time of onset of reaction / event unit'),
            'patient_birth_date' => Yii::t('app', 'B.1.2.1 Date of birth'),
            'patient_weight' => Yii::t('app', 'B.1.3 Weight '),
            'patient_weight_unit' => Yii::t('app', 'B.1.3b Weight  Unit'),
            'extra_history' => Yii::t('app', 'B.1.7.2 Text for relevant medical history and concurrent conditions (not including reaction/event)'),
            ]);
    }


    public function behaviors()
    {
        return [
            'AuditTrailBehavior' => [
                'class' => 'bedezign\yii2\audit\AuditTrailBehavior',
                'ignored' => ['id','drug_id'],

            ]
        ];
    }
    public function getIcsrTrails()
    {
           return $this->hasMany(AuditTrail::className(), ['model_id' => 'id'])
               ->andOnCondition(['model' => get_class($this)]);


    }

    public function getIcsrReportersTrails()
    {
        return AuditTrail::find()
            ->orOnCondition([
                'audit_trail.model_id' => ArrayHelper::map($this->getIcsrReporters()->all(),'id','id'),
                'audit_trail.model' => \backend\modules\crud\models\IcsrReporter::className(),
            ]);
    }

    public function getIcsrtEventsTrails ()
    {
        return AuditTrail::find()
            ->orOnCondition([
                'audit_trail.model_id' =>$this->eventsToTrailJsonConverter(),
                'audit_trail.model' => \backend\modules\crud\models\IcsrEvent::className(),
            ]);
    }

   public function eventsToTrailJsonConverter ()
    {
        $temp_arr = [];
        foreach ($this->getIcsrEvents()->all() as $key => $value)
        {
            $temp_arr[] = "{\"id\":$value->id,\"icsr_id\":\"$this->id\"}";


        }
        return $temp_arr;
    }

    public function getDrugPrescriptionsTrails ()
    {
        return AuditTrail::find()
            ->orOnCondition([
                'audit_trail.model_id' => ArrayHelper::map($this->getDrugPrescriptions()->all(),'id','id'),
                'audit_trail.model' => \backend\modules\crud\models\DrugPrescription::className(),
            ]);
    }

    public function getIcsrTestTrails ()
    {
        return AuditTrail::find()
            ->orOnCondition([
                'audit_trail.model_id' => ArrayHelper::map($this->getIcsrTests()->all(),'id','id'),
                'audit_trail.model' => \backend\modules\crud\models\IcsrTest::className(),
            ]);
    }
}
