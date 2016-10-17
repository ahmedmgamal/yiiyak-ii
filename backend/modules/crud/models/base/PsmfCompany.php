<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\crud\models\base;

use Yii;

/**
 * This is the base-model class for table "psmf_company".
 *
 * @property integer $psmf_id
 * @property integer $company_id
 * @property integer $version
 * @property string $aliasModel
 */
abstract class PsmfCompany extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'psmf_company';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id'], 'required'],
            [['company_id', 'version'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'psmf_id' => 'Psmf ID',
            'company_id' => 'Company ID',
            'version' => 'Version',
        ];
    }


    public function getCompany ()
    {
        return $this->hasOne(\backend\modules\crud\models\Company::className(),['id' => 'company_id']);
    }


    public function getPsmfSections ()
    {
        return $this->hasMany(\backend\modules\crud\models\PsmfSection::className(),['psmf_id' => 'psmf_id']);
    }

}