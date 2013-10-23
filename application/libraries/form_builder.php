<?

/* 
 */
class form_builder {

    private $default_input_type = 'form_input';
    private $bootstrap_required_input_class = 'form-control';
    private $func;
    private $data_source;
    private $print_string = '';

    function __construct() {
        $this->func = $this->default_input_type;
    }

    function build_form_horizontal($options, $data_source = array()) {
        $this->data_source = (array) $data_source;

        if (is_object($options)) {
            $options = (array) $options;
        }
        if (!is_array($options)) {
            if (!empty($options)) {
                $options = $tmp[] = array($options);
            } else {
                return;
            }
        }

        foreach ($options as $elm_options) {
            if (is_array($elm_options)) {
                $this->_prep_options(&$elm_options);
//                dump($elm_options);
                $this->print_string .= $this->_pre_elm();
                $this->print_string .= $this->_label($elm_options);
                $this->print_string .= $this->_build_input($elm_options);
                $this->print_string .= $this->_post_elm();
            }
        }
        return $this->print_string;
    }

    private function _prep_options($elm_options) {
        /* Pull the input type from the array */
        if (isset($elm_options['type'])) {
            $this->func = 'form_' . $elm_options['type'];
            unset($elm_options['type']);
        }
        if (!function_exists($this->func)) { /* check if the function exists */
            $this->func = $this->default_input_type;
        }

        /* make sure to add 'form-control' to the class array */
        $class = $this->bootstrap_required_input_class;
        if (isset($elm_options['class'])) {
            $class .= ' ' . $elm_options['class'];
        }
        $elm_options['class'] = $class;

        /* make sure there is a name' attribute */
        if (!isset($elm_options['name'])) {
            /* put the id as the name by default - makes smaller 'config' arrays */
            if (isset($elm_options['id'])) {
                $elm_options['name'] = $elm_options['id'];
            } else {
                $elm_options['name'] = '';
            }
        }

        /* make sure there is a 'value' attribute 
         * Also, make for fun defaulting by passing an object 
         */
        $default_value = '';
        if (isset($elm_options['name']) && isset($this->data_source[$elm_options['name']])) {
            $default_value = $this->data_source[$elm_options['name']];
        }
        $elm_options['value'] = set_value($elm_options['name'], $default_value);

        return;
    }

    /**
     * Form Value
     *
     * Upgraded from Codeigniter Form Helper
     * by Tyler Wall
     * 
     * Grabs a value from the POST or GET array for the specified field so you can
     * re-populate an input field or textarea.  If Form Validation
     * is active it retrieves the info from the validation class
     *
     * @access	public
     * @param	string
     * @return	mixed
     */
    function adv_set_value($field = '', $default = '') {
        if (FALSE === ($OBJ = & _get_validation_object())) {
            if (isset($_POST[$field])) {
                return form_prep($_POST[$field], $field);
            } elseif (isset($_GET[$field])) {
                return form_prep($_GET[$field], $field);
            }
            return $default;
        }

        return form_prep($OBJ->set_value($field, $default), $field);
    }

    private function _build_input($elm_options) {
        return $this->_pre_input() . call_user_func($this->func, $elm_options) . $this->_post_input();
    }

    private function _pre_elm() {
        return '<div class="form-group">';
    }

    private function _post_elm() {
        return '</div>';
    }
    
    private function _pre_input() {
        return '<div class="col-md-9">';
    }
    
    private function _post_input() {
        return '</div>';
    }

    private function _label($elm_options) {
        $label = '';
        if (isset($elm_options['label'])) {
            $label = $elm_options['label'];
        } elseif (isset($elm_options['id'])) {
            $label = ucwords(str_replace(array('_', '-', '[', ']'), array(' ', ' ', ' ', ' '), $elm_options['id']));
        }
        return '<label class="col-md-3 control-label" for="name">' . $label . '</label>';
    }

}