<?php

class ThemeHouse_PromotionMsg_Option
{
    public static function verifyOption(&$userName, XenForo_DataWriter $dw, $fieldName)
    {
        if (!$userName) {
            return true;
        }

        /* @var $userModel XenForo_Model_User */
        $userModel = XenForo_Model::create('XenForo_Model_User');

        $user = $userModel->getUserByName($userName);

        if (!$user) {
            return false;
        }

        return true;
    } /* END verifyOption */
}