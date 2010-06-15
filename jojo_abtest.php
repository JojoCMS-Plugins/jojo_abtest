<?php

class Jojo_Plugin_jojo_abtest extends Jojo_Plugin
{
    /* gets a random number between 1 and 120 */
    static function getAbNumber()
    {
        static $number;
        
        /* number already set */
        if (isset($number)) return $number;
        
        /* read from cookie */
        if (isset($_COOKIE['jojo_ab_number'])) {
            $number = $_COOKIE['jojo_ab_number'];
            return $number;
        }
        
        /* crete a new number */
        $number = mt_rand(1, 120);
        setcookie("jojo_ab_number", $number, time() + (60 * 60 * 24 * 365), '/' . _SITEFOLDER);
        return $number;        
    }
    
    /* returns a variation number to use for testing */
    static function getVariation($num_variations)
    {
        $ab_number = self::getAbNumber();
        return ($ab_number % $num_variations);
    }
    
    /* returns a variation number to use for testing */
    static function addTest($slot, $name, $num_variations)
    {
        self::tests($slot, $name, $num_variations);        
        return true;
    }
    
    /*  */
    static function tests($slot=false, $name=false, $num_variations=false)
    {
        global $smarty;
        static $tests;
        if (!isset($tests)) $tests = array();
        $variation = self::getVariation($num_variations);
        if (!empty($slot)) {
            $tests[$slot] = array('slot' => $slot, 'name' => $name, 'num_variations' => $num_variations, 'variation' => $variation, 'scope' => 1); //todo: make scope variable
        }
        /* assign to Smarty - repurpose as name-indexed to make templates simpler */
        $ab = array();
        foreach ($tests as $test) {
            $ab[$test['name']] = array('slot' => $test['slot'], 'name' => $test['name'], 'num_variations' => $test['num_variations'], 'variation' => $variation);
        }
        $smarty->assign('ab', $ab);
        return $tests;
    }
    
    /*  */
    static function getTests($slot, $name, $num_variations)
    {
        return self::tests();
    }
    
    function analytics_trackPageview_hook()
    {
        $output = '';
        $tests = self::tests();
        foreach ($tests as $test) {
            $output .= 'pageTracker._setCustomVar('.$test['slot'].', "'.$test['name'].'", "'.$test['variation'].'", '.$test['scope'].');'."\n";
        }
        return $output;
    }

}