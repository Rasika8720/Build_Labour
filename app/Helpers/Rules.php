<?php


namespace App\Helpers;


class Rules {

    /**
     * checks if maxwords
     * @param $attribute
     * @param $value
     * @param $parameters
     * @return bool
     */
    public static function maxWords( $attribute, $value = 5, $parameters ) {

        if (!empty($parameters)) {

            $noOfWords = $parameters[0];
        }

        if (count(explode(' ', $value)) > $noOfWords) {

            return false;
        }

        return true;
    }

}