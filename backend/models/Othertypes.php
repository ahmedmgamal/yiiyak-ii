<?php

namespace app\models;

use Yii;
use \app\models\base\Othertypes as BaseOthertypes;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "other_types".
 */
class Othertypes extends BaseOthertypes
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }
}
