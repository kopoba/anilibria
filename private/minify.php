<?php

function sanitize_output($buffer) {
    $search = [
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    ];
    $replace = [
        '>',
        '<',
        '\\1',
        ''
    ];
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}

if($conf['minify']){
	ob_start("sanitize_output");
}
