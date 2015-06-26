<?php

class SV_UsernameRestrictions_Listener
{
    const AddonNameSpace = 'SV_UsernameRestrictions';

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.'_'.$class;
    }
}
