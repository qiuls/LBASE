<?php


/**
 * 压缩css和js
 * Class CompressService
 */
class CompressService{
    /**
     *   合并压缩css
     */
    function cssText($text)
    {
        $css_content = $text;
        $css_content = str_replace("\r\n", '', $css_content); //清除换行符
        $css_content = str_replace("\n", '', $css_content); //清除换行符
        $css_content = str_replace("\t", '', $css_content); //清除制表符
        return $css_content;
    }


}