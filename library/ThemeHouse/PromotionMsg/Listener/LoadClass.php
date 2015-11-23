<?php

class ThemeHouse_PromotionMsg_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_PromotionMsg' => array(
                'model' => array(
                    'XenForo_Model_UserGroupPromotion'
                ), /* END 'model' */
            ), /* END 'ThemeHouse_PromotionMsg' */
        );
    } /* END _getExtendedClasses */

    public static function loadClassModel($class, array &$extend)
    {
        $loadClassModel = new ThemeHouse_PromotionMsg_Listener_LoadClass($class, $extend, 'model');
        $extend = $loadClassModel->run();
    } /* END loadClassModel */
}