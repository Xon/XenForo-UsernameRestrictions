<?php

class SV_UsernameRestrictions_Listener
{
    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_UsernameRestrictions_'.$class;
    }
}
