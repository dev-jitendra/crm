<?php



function helper1 ($args, $named) {
    $u = (isset($args[0])) ? $args[0] : 'undefined';
    $t = (isset($args[1])) ? $args[1] : 'undefined';
    return "<a href=\"{$u}\">{$t}</a>";
}



function helper2 ($args, $named) {
    $u = isset($named['url']) ? jsraw($named['url']) : 'undefined';
    $t = isset($named['text']) ? jsraw($named['text']) : 'undefined';
    $x = isset($named['ur"l']) ? $named['ur"l'] : 'undefined';
    return "<a href=\"{$u}\">{$t}</a>({$x})";
}



function helper3 ($cx, $args, $named) {
    return array('test1', 'test2', 'test3');
}



function helper4 ($cx, $args, $named) {
    if (isset($named['val']) && is_array($cx)) {
        $cx['helper4_value'] = $named['val'] % 2;
        return $cx;
    }
    if (isset($named['odd'])) {
        return array(1,3,5,7,9);
    }
}



function myeach ($list, $options) {
    foreach ($list as $item) {
        $ret .= $options['fn']($item);
    }
    return $ret;
}


function jsraw ($i) {
    if ($i === true) {
        return 'true';
    }
    if ($i === false) {
        return 'false';
    }
    return $i;
}

