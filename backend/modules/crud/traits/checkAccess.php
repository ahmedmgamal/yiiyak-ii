<?php
namespace backend\modules\crud\traits;

trait checkAccess {

    // you must define getCompany method before using the trait

    public static function checkAccess($user_id,$obj_id)
    {

        if (isset($obj_id) && !empty($obj_id))
        {
            $company = self::findOne($obj_id)->company;

            if (!$company->getUser($user_id))
            {
                return false;
            }

        }
        return true;
    }
}