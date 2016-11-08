<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace backend\modules\crud\models\base;

use Yii;

/**
 * This is the base-model class for table "meddra_hlgt_hlt".
 *
 * @property string $hlgt_id
 * @property string $hlt_id
 * @property string $aliasModel
 */
abstract class MeddraHlgtHlt extends \yii\db\ActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meddra_hlgt_hlt';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hlgt_id', 'hlt_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hlgt_id' => 'Hlgt ID',
            'hlt_id' => 'Hlt ID',
        ];
    }




}
