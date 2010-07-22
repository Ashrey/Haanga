<?php

class Dictsort_Tag
{

    /**
     *  Sorted a nested array by '$sort_by'
     *  property on each sub-array. , if you want 
     *  to see the original php file look filters/dictsort.php
     */
    function generator($cmp, $args, $redirected)
    {
        if (!$redirected) {
            throw new Haanga_CompilerException("dictsort must be redirected to a variable using AS <varname>");
        }
        if (count($args) != 2) {
            throw new Haanga_CompilerException("Dictsort must have two params");
        }

        if (!HCode::is_var($args[0])) {
            throw new Haanga_CompilerException("Dictsort: First parameter must be an array");
        }

        $redirected = hvar($redirected);
        $field      = hvar('field');
        $key        = hvar('key');

        $code = hcode();
        $body = hcode();

        $body->decl(hvar('field', $key), hvar('item', $args[1]));

        $code->decl($redirected, $args[0]);
        $code->decl($field, array());
        $code->do_foreach($redirected, 'item', $key, $body);
        $code->do_exec('array_multisort', $field, hconst('SORT_REGULAR'), $redirected);

        return $code;
    }
}
