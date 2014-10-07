<?php

class html {

    // 创建文件
    public static function create($url, $content) {
        $file = self::parseUrl($url);
        if ($file) {
            return F($file, str_replace(C('gen_html_replace'), '', $content));
        } else {
            return false;
        }
    }

    //删除文件
    public static function del($url) {
        $file = self::parseUrl($url);
        if (substr($file, -5) === '.html') {
            return F($file, null);
        }
        return false;
    }

    // 读取内容
    public static function read($url) {
        $file = self::parseUrl($url);
        return F($file);
    }

    // 地址解析
    public static function parseUrl($url) {
        if (!C('html') || strpos($url, '?') || strpos($url, '#') || strpos($url, '&') || strpos($url, '=')) return false;
        $path = parse_url($url, PHP_URL_PATH);
        if (strpos(basename($path), '.') === false) {
            $path = trim($path, '/') . '/' . C('HTML_DEFAULTFILE', null, 'index.html');
        } else {
            $path = trim($path, '/');
        }
        if (PT_DIR && substr($path, 0, strlen(PT_DIR)) == PT_DIR) {
            $path = trim(substr($path, strlen(PT_DIR)), '/');
        }
        return PT_ROOT . '/' . $path;
    }
}