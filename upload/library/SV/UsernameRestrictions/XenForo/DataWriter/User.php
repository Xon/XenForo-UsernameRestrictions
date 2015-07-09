<?php

class SV_UsernameRestrictions_XenForo_DataWriter_User extends XFCP_SV_UsernameRestrictions_XenForo_DataWriter_User
{
    protected function _verifyUsername(&$username)
    {
        $ret = parent::_verifyUsername($username);
        if (empty($ret) || empty($username))
        {
            return $ret;
        }

        $options = XenForo_Application::getOptions();
        if (!$options->sv_ur_apply_to_admins && $this->getOption(self::OPTION_ADMIN_EDIT))
        {
            return $ret;
        }

        $blockSubset = $options->sv_ur_block_group_subset;
        $username_lowercase = utf8_strtolower($username);
        $groups = $this->_getUserGroupModel()->getAllUserGroups();
        foreach($groups as $group)
        {
            $groupname = utf8_strtolower($this->standardizeWhiteSpace($group['title']));
            if (strcmp($groupname, $username_lowercase) === 0)
            {
                $this->error(new XenForo_Phrase('usernames_must_be_unique'), 'username');
                return false;
            }

            if ($blockSubset && (utf8_strpos($groupname, $username_lowercase, 0) === 0))
            {
                $this->error(new XenForo_Phrase('usernames_must_be_unique'), 'username');
                return false;
            }

            // compare against romanized name to help reduce confusable issues
            $groupname = utf8_strtolower(utf8_deaccent(utf8_romanize($groupname)));
            if (strcmp($groupname, $username_lowercase) === 0)
            {
                $this->error(new XenForo_Phrase('usernames_must_be_unique'), 'username');
                return false;
            }

            if ($blockSubset && (utf8_strpos($groupname, $username_lowercase, 0) === 0))
            {
                $this->error(new XenForo_Phrase('usernames_must_be_unique'), 'username');
                return false;
            }
        }
        return $ret;
    }

    function standardizeWhiteSpace($text)
    {
        $text = preg_replace('/\s+/u', ' ', $text);
        try
        {
            // if this matches, then \v isn't known (appears to be PCRE < 7.2) so don't strip
            if (!preg_match('/\v/', 'v'))
            {
                $newName = preg_replace('/\v+/u', ' ', $text);
                if (is_string($newName))
                {
                    $text = $newName;
                }
            }
        }
        catch (Exception $e) {}

        return trim($text);
    }

    protected function _getUserGroupModel()
    {
        return $this->getModelFromCache('XenForo_Model_UserGroup');
    }
}
