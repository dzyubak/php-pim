<?php
/**
 * Copyright (C) 2014, 2015 Dmytro Dzyubak
 * 
 * This file is part of php-pim.
 * 
 * php-pim is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * php-pim is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with php-pim. If not, see <http://www.gnu.org/licenses/>.
 */

class Validator {
    public static function validateLettersOnly($str) {
        return preg_match("/^[a-zA-Z\p{Cyrillic}]+$/u", $str);
    }

    public static function validateLettersNumbersOnly($str) {
        return preg_match("/^[a-zA-Z0-9]+$/", $str);
    }

    public static function validateEmail($email) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return FALSE; // address is invalid
        } else {
            $email = explode('@', $email);
            $domain = array_pop($email);
            if (!checkdnsrr($domain, 'MX')) {
                return FALSE; // domain is not valid
            }
        }
        return TRUE; // email address seems to be valid
    }

    public static function validatePassword($pass, $passConf) {
        if($pass == "" || $pass != $passConf) { return FALSE; }
        return TRUE;
    }
}