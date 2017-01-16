<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\crud\models\base;

use Yii;

/**
 * This is the base-model class for table "rmp".
 *
 * @property integer $id
 * @property integer $drug_id
 * @property string $version
 * @property string $version_description
 * @property string $rmp_file_url
 * @property string $ack_file_url
 * @property integer $rmp_created_by
 * @property string $rmp_created_at
 * @property integer $ack_created_by
 * @property string $ack_created_at
 * @property string $aliasModel
 */
abstract class Rmp extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rmp';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['drug_id', 'rmp_created_by','rmp_file_url','version'], 'required'],
            [['drug_id', 'rmp_created_by', 'ack_created_by'], 'integer'],
            [['version'], 'number'],
            [['rmp_created_at', 'ack_created_at'], 'safe'],
            [['next_rmp_date'],'date','format' => 'php:Y-m-d'],
            [['version_description', 'rmp_file_url', 'ack_file_url'], 'string', 'max' => 255],
            [['drug_id'], 'exist', 'skipOnError' => true, 'targetClass' => Drug::className(), 'targetAttribute' => ['drug_id' => 'id']],
            [['rmp_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['rmp_created_by' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'drug_id' => 'Drug ID',
            'version' => 'Version',
            'version_description' => 'Version Description',
            'rmp_file_url' => 'Rmp File Url',
            'ack_file_url' => 'Ack File Url',
            'rmp_created_by' => 'Rmp Created By',
            'rmp_created_at' => 'Rmp Created At',
            'ack_created_by' => 'Letter Header Created By',
            'ack_created_at' => 'Letter Header Created At',
            'next_rmp_date' => 'Next Submission Date',

        ];
    }


    public function getRmpUser ()
    {
        return $this->hasOne(\backend\modules\crud\models\User::className(),['id' => 'rmp_created_by']);
    }

    public function getRmpAckUser()
    {
        return $this->hasOne(\backend\modules\crud\models\User::className(),['id' => 'ack_created_by']);

    }
}
