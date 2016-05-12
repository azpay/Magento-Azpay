<?php

/**
 * Utils Class
 *
 * Class with utility methods
 *
 * @author Gabriel Guerreiro <gabrielguerreiro.com>
 * */
class Utils {

    /**
     * Return a number formated
     *
     * @param  int    $number [number]
     * @return string
     */
    public static function formatNumber($number) {

        $int = filter_var($number, FILTER_SANITIZE_NUMBER_INT);

        return str_replace(array('-', '+'), '', $int);
    }

    /**
     * Return a Slug formated
     *
     * @param  string $slug [slug]
     * @return string
     */
    public static function formatSlug($slug) {

        return trim(strtolower($slug));
    }

    /**
     * Return a Amount formated
     *
     * @param  int    $amount [Price/Value]
     * @return string
     */
    public static function formatAmount($amount) {

        if (strpos($amount, ',') !== false || strpos($amount, '.') !== false)
            $amount = number_format($amount, 2, '', '');

        if (strlen($amount) <= 2)
            $amount = str_pad($amount, 3, "0", STR_PAD_RIGHT);

        return $amount;
    }

}

?>