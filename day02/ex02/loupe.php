#!/usr/bin/env php
<?php
libxml_use_internal_errors(true);

function count_previous_occurences_level($el, $text) {
    $count = 0;
    if ($el->nodeType == XML_ELEMENT_NODE) {
        $count += count_previous_occurences_level($el->attributes->item($el->attributes->length - 1), $text);
    }
    if ($el->previousSibling) {
        $count += count_previous_occurences_level($el->previousSibling, $text);
        if ($el->previousSibling->lastChild)
            $count += count_previous_occurences_level($el->previousSibling->lastChild, $text);
    }
    if ($el->nodeType === XML_TEXT_NODE || $el->nodeType === XML_ATTRIBUTE_NODE)
    {
        $count += (strpos($el->nodeValue, $text) !== false ? 1 : 0);
    }
    if ($el->nodeType === XML_HTML_DOCUMENT_NODE)
    {
        $count += (strpos($el->textContent, $text) !== false ? 1 : 0);
    }
    return $count;
}

function count_previous_occurences($el, $text) {
    $count = 0;
    while ($el) {
        $count += count_previous_occurences_level($el, $text);
        $el = $el->parentNode;
    }
    return $count;
}

function replace($el, $data) {
    $pos = 0;
    $i = 0;
    $occ = count_previous_occurences($el, $el->nodeValue);
    if ($el->nodeValue === "youtube")
        var_dump($occ);
    $len = strlen($el->nodeValue);
    while ($i < $occ && $pos !== FALSE)
    {
        if ($pos = strpos($data, $el->nodeValue, $pos))
            $pos += $len;
        $i++;
    }
    if ($pos !== FALSE)
    {
        $el->nodeValue = strtoupper($el->nodeValue);
        $data = substr($data, 0, $pos - $len) . $el->nodeValue . substr($data, $pos);
    }
    return ($data);
}

function uppercase_node($el, $data) {
    if ($el->nodeType == XML_TEXT_NODE && trim($el->textContent) !== '') {
        $data = replace($el, $data);
    } else if ($el->nodeType == XML_ELEMENT_NODE) {
        foreach ($el->childNodes as $child)
            $data = uppercase_node($child, $data);
        foreach ($el->attributes as $attr)
        {
            if ($attr->nodeName === "title")
                $data = replace($attr, $data);
        }
    }
    return $data;
}

if ($argc == 2 && ($data = @file_get_contents($argv[1]))) {
    $dom = DOMDocument::loadHTML($data, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NOERROR | LIBXML_NOWARNING);
    foreach ($dom->getElementsByTagName("a") as $a) {
       $data = uppercase_node($a, $data); 
    }
    echo $data;
}
