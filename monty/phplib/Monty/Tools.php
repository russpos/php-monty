<?php

class Monty_Tools {

    public static function jsonify($data) {
        if (is_array($data)) {
            $ret_array = array();
            foreach ($data as $key => $item) {
                $ret_array[$key] = self::jsonify($item);
            }
            return $ret_array;
        }
        if ($data instanceof Monty_Model) {
            return $data->toJSON();
        }
        return $data;
    }
}
