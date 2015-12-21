<?php
class shortURL {
    const ALPHABET = '23456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ-_';
    const BASE = 51; // strlen(self::ALPHABET)
    public static function encode($num) {
        $str = '';
        while ($num > 0) {
            $str = substr(self::ALPHABET, ($num % self::BASE), 1) . $str;
            $num = floor($num / self::BASE);
        }
        return $str;
    }
    public static function decode($str) {
        $num = 0;
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * self::BASE + strpos(self::ALPHABET, $str[$i]);
        }
        return $num;
    }
}
