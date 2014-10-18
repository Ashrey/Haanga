<?php

class Haanga_Extension_Tag_link
{
    public $is_block = FALSE;

    static function generator($cmp, $args, $redirected)
    {
        $href = end($args[0]);
        $text = end($args[1]);
        $attr = isset($args[2]['string']) ? "{$args[2]['string']}":'';
        $code = hcode();
        $cmp->do_print($code, Haanga_AST::str('<a href="' . PUBLIC_PATH));
        $cmp->do_print($code, Haanga_AST::is_str($args[0]) ? Haanga_AST::str($href): hvar($href));
        $cmp->do_print($code, Haanga_AST::str("\" $attr>"));
        $cmp->do_print($code, Haanga_AST::is_str($args[1]) ? Haanga_AST::str($text): hvar($text));
        $cmp->do_print($code, Haanga_AST::str('</a>'));
        return $code;
    }
}
