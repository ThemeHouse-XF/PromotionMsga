<?php

/**
 *
 * @see XenForo_Model_UserGroupPromotion
 */
class ThemeHouse_PromotionMsg_Extend_XenForo_Model_UserGroupPromotion extends XFCP_ThemeHouse_PromotionMsg_Extend_XenForo_Model_UserGroupPromotion
{

    public function promoteUser(array $promotion, $userId, $state = 'automatic')
    {
        $nodeExists = $this->_checkCongratulationsNodeExists();
        if ($promotion && $nodeExists) {
            $this->_createCongratulationsThread($promotion, $userId);
        }

        parent::promoteUser($promotion, $userId, $state);
    } /* END promoteUser */

    protected function _createCongratulationsThread($promotion, $userId)
    {
        /* @var $userModel XenForo_Model_User */
        $userModel = XenForo_Model::create('XenForo_Model_User');
        $user = $userModel->getUserById($userId);
        $username = $user['username'];
        $nodeId = XenForo_Application::getOptions()->th_promotionMessage_congratsMessageNodeId;
        $threadStarterUsername = XenForo_Application::getOptions()->th_promotionMessage_congratsMessageUserName;
        $threadStarterUser = array();
        if ($threadStarterUsername) {
            $threadStarterUser = $userModel->getUserByName($threadStarterUsername);
        }
        if ($threadStarterUser) {
            $threadStarterUserId = $threadStarterUser['user_id'];
        } else {
            $threadStarterUsername = $username;
            $threadStarterUserId = $userId;
        }

        $userGroupTitles = $this->_getUserGroupTitlesFromIds($promotion['extra_user_group_ids']);

        foreach ($userGroupTitles as $userGroupTitle) {
            $writer = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread', 'ERROR_SILENT');
            $writer->bulkSet(
                array(
                    'user_id' => $threadStarterUserId,
                    'username' => $threadStarterUsername,
                    'title' => new XenForo_Phrase("th_congratulations_thread_title_promotionmessage",
                        array(
                            'username' => $username,
                            'usergroup' => $userGroupTitle
                        )),
                    'node_id' => $nodeId
                ));
            $postWriter = $writer->getFirstMessageDw();
            $postWriter->set('message',
                new XenForo_Phrase("th_congratulations_thread_message_promotionmessage",
                    array(
                        'username' => $username,
                        'usergroup' => $userGroupTitle
                    )));
            $writer->save();
        }
    } /* END _createCongratulationsThread */

    protected function _getUserGroupTitlesFromIds($userGroupIds)
    {
        $allUserGroupTitles = $this->_getUserGroupModel()->getAllUserGroupTitles();
        $userGroupTitles = array();
        if ($userGroupIds) {
            $userGroupIds = explode(',', $userGroupIds);
            foreach ($userGroupIds as $userGroupId) {
                $userGroupTitles[] = $allUserGroupTitles[$userGroupId];
            }
            $userGroupTitles = array_unique($userGroupTitles);
        }
        return $userGroupTitles;
    } /* END _getUserGroupTitlesFromIds */

    protected function _checkCongratulationsNodeExists()
    {
        $nodeModel = $this->_getNodeModel();

        $node = $nodeModel->getNodeById(
            XenForo_Application::getOptions()->th_promotionMessage_congratsMessageNodeId);

        return (!empty($node) && is_array($node) && array_key_exists('node_id', $node) &&
             array_key_exists('parent_node_id', $node));
    } /* END _checkCongratulationsNodeExists */

    /**
     * Gets the user group model object.
     *
     * @return XenForo_Model_UserGroup
     */
    protected function _getUserGroupModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserGroup');
    } /* END _getUserGroupModel */

    /**
     * Gets the node model object.
     *
     * @return XenForo_Model_Node
     */
    protected function _getNodeModel()
    {
        return $this->getModelFromCache('XenForo_Model_Node');
    } /* END _getNodeModel */
}