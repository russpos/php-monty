<?php

define('MONTY_TPL_DIR', MONTY_DIR.DS.'templates');

class Monty_Response_HTML extends Monty_Response {

    private $response = '';

    private $template_vars = array();
    private $layout_vars = array();
    private $layout = 'base.php';

    public function assign($name, $val) {
        if (is_string($name)) {
            $this->template_vars[$name] = Monty_Tools::jsonify($val);
        } else if (is_array($name)) {
            foreach ($name as $var_name => $value) {
                $this->assign($var_name, $value);
            }
        }
    }

    public function render($template) {
        $body = $this->partial($template, $this->template_vars);
        if (!empty($this->layout)) {
            $this->layout_vars['body'] = $body;
            $body = $this->partial($this->layout, $this->layout_vars);
        }
        $this->response = $body;
    }

    private function partial($template, $vars) {
        $output = '';
        $path = MONTY_TPL_DIR.DS.$template;
        if (file_exists($path)) {
            extract($vars);
            ob_start();
            include($path);
            $output = ob_get_clean();
            return $output;
        }
        return $output;
    }

    public function generateOutput() {
        echo $this->response;
    }
}
