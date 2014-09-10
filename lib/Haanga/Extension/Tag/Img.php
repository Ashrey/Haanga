<?php

class Haanga_Extension_Tag_Img
{
    public $is_block = FALSE;

    static function generator($cmp, $args, $redirected)
    {
        $src = end($args[0]);
        $attr = isset($args[1]['string']) ? "{$args[1]['string']}":'';
        $code = hcode();
        $cmp->do_print($code, Haanga_AST::str('<img src="' . PUBLIC_PATH.'img/'));
        $cmp->do_print($code, Haanga_AST::is_str($args[0]) ? Haanga_AST::str($src): hvar($src));
        $cmp->do_print($code, Haanga_AST::str("\" $attr/>"));
        return $code;
    }
}
