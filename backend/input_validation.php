<?php

class InputValidator {
    public static function sanitize($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitize'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validatePassword($password) {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
        return preg_match($pattern, $password);
    }

    public static function validateName($name) {
        return preg_match('/^[a-zA-Z\s\'-]{2,50}$/', $name);
    }

    public static function validateNumeric($input, $min = null, $max = null) {
        if (!is_numeric($input)) {
            return false;
        }
        if ($min !== null && $input < $min) {
            return false;
        }
        if ($max !== null && $input > $max) {
            return false;
        }
        return true;
    }

    public static function validateDate($date, $format = 'Y-m-d') {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function validatePhone($phone) {
        return preg_match('/^\+?[\d\s-]{10,15}$/', $phone);
    }

    public static function validateAddress($address) {
        return preg_match('/^[a-zA-Z0-9\s\.,#-]{5,100}$/', $address);
    }

    public static function validatePostalCode($postalCode) {
        return preg_match('/^[A-Za-z0-9\s-]{3,10}$/', $postalCode);
    }

    public static function validateBoolean($input) {
        return is_bool($input) || in_array(strtolower($input), ['true', 'false', '1', '0', 'yes', 'no']);
    }

    public static function validateArray($input, $requiredKeys = []) {
        if (!is_array($input)) {
            return false;
        }
        foreach ($requiredKeys as $key) {
            if (!array_key_exists($key, $input)) {
                return false;
            }
        }
        return true;
    }
}
?> 