<?php

class Monty_Tools {

    public static function jsonify($data) {
        if (is_array($data)) {
            $ret_array = array();
            foreach ($data as $item) {
                $ret_array[] = self::jsonify($item);
            }
            return $ret_array;
        }
        if ($data instanceof Monty_Model) {
            return $data->toJSON();
        }
    }
}
