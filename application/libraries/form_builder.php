<?php

// -------------------------------------------------------------------------------------------------
/**
 * form_builder
 * Bootstrap form builder
 * 
 * This simple Library builds internal form elements with correct wrappers for Bootstrap 3.
 * 
 * It extends the Bootstrap form helper and will not work without it.
 * 
 *
 * Carabiner is inspired by Minify {@link http://code.google.com/p/minify/ by Steve Clay}, PHP 
 * Combine {@link http://rakaz.nl/extra/code/combine/ by Niels Leenheer} and AssetLibPro 
 * {@link http://code.google.com/p/assetlib-pro/ by Vincent Esche}, among other things.
 *
 * @package		codeigniter_form_builder
 * @subpackage          Libraries
 * @category            Form Bilder
 * @author		Tyler Wall <tyler.r.wall@gmail.com>	
 * @version		0.6
 * @license		http://opensource.org/licenses/MIT MIT licensed.
 *
 * @todo		fix new bugs. Duh :D
 * @todo                test Objects
 * @todo                Add more attributes for form_elements
 * @todo                Add radio 
 */
/*
  ===============================================================================================
  USAGE
  ===============================================================================================

  1. Load codeigniter 'form' helper       ---         $this->load->helper('form');
  2. Load this library                    ---         $this->load->library('form_builder');
  3. Open your form (include the approprate class and col-md-* for formating
  4. Echo out the output of the form_builder->build_*
  5. Close your form ('</form>').
  6. Enjoy easy forms

  -----------------------------------------------------------------------------------------------

  <? $this->load->helper('form'); ?>
  <? $this->load->library('form_builder'); ?>

  <form id="item_form" name="item_form" method="post" class="col-md-7 form-horizontal" action="">
  <?=
  $this->form_builder->build_form_horizontal(
  array(
  array(
  'id' => 'name',
  'placeholder' => 'Item Name',
  ),
  array(
  'id' => 'subtitle',
  'placeholder' => 'Subtitle'
  )
  ), $item);
  ?>
  </form>
 */
class Form_builder {

    private $config = array(/* Config array - can be overrided by passing in array in ini() */
        'default_input_type' => 'form_input',
        'default_input_container_class' => 'form-group',
        'bootstrap_required_input_class' => 'form-control',
        'default_dropdown_class' => 'valid',
        'default_control_label_class' => 'col-md-3 control-label',
        'default_form_control_class' => 'col-md-9',
        'default_form_class' => 'form-horizontal col-md-12',
        'default_submit_classes' => 'btn btn-primary',
        'default_date_post_addon' => '<span class="input-group-btn"><button class="btn default" type="button"><i class="fa fa-calendar"></i></button></span>'
    );
    private $func; /* Global function holder - used in switches */
    private $data_source; /* Global holder for the source of the data */
    private $elm_options; /* Global options holder */
    private $elm_options_help;
    private $print_string = ''; /* An output buffer */

    /**
     * @property array $input_addons  
     * This is for adding input-groups and addons.
     * pre/post do not have to be inputed as arrays but will be turned into ones
     * so that we can handle multipal pre/post input addons.
     */
    private $input_addons = array(
        'exists' => false, /* does the specific input have an addon? */
        'pre' => array(), /* container for pre addons */
        'pre_html' => '',
        'post' => array(), /* container for post addons */
        'post_html' => ''
    );

    function __construct() {
        $this->func = $this->config['default_input_type'];
    }

    function init($config = array()) {
        if (!empty($config)) {
            foreach ($config as $k => $v) {
                $this->config[$k] = $v;
            }
            $this->func = $this->config['default_input_type'];
        }
    }

    function open_form($options) {
        // <form id="item_form" name="item_form" method="post" class="col-md-12 form-horizontal" action="">
        $action = '';
        if (isset($options['action'])) {
            $action = $options['action'];
            unset($options['action']);
        } else {
            show_error('No action set for form. Please include array(\'action\' => \'\') in the open_form(...) function call');
        }

        $class = $this->config['default_form_class'];
        if (isset($options['class'])) {
            $class = $options['class'];
        }
        $options['class'] = $class;

        return $this->_build_form_open($action, $options);
    }

    function close_form() {
        return form_close();
    }

    function auto_db_to_options($ary) {
        $options = array();

        foreach ($ary as $k => $v) {
            $elm_options = array(
                'id' => $k,
                'value' => $v
            );

            /*
             * TODO: this should be put in the options. It is suited specificaly for my database
             * configuration and my practices - which include having a 'id', 'modified', 'created', and 'active'
             * column in *ALL* databases - as well as some specific data.
             * 
             * NOTE: This function will likely see a lot of change to make sure it is working and/or re-built
             * 'the right way' 
             */
            if (is_json($v)) {
                $elm_options['type'] = 'json';
            } else {
                switch ($k) {
                    case 'id':
                        $elm_options['readonly'] = 'readonly';
                        break;

                    case 'modified':
                    case 'created':
                        $elm_options['type'] = 'date';
                        $elm_options['readonly'] = 'readonly';
                        break;

                    case 'active':
                        $elm_options['type'] = 'dropdown';
                        $elm_options['options'] = array(
                            '1' => 'Active',
                            '0' => 'De-Active'
                        );
                        $elm_options['readonly'] = 'readonly';
                        break;
                }
            }
            $options[] = $elm_options;
        }

        return $options;
    }

    /**
     * Build From  Horizontal
     * @access	public
     * @param	Array - The array of options for the form.
      array(
      array(
      See function _prep_options() for what this needs to contain
      )
      )
     * @return  form elements+wrappers HTML
     */
    function build_form_horizontal($options, $data_source = array()) {
        $this->_reset_builder();
        $this->data_source = (array) $data_source;

        foreach ($options as $elm_options) {
            $this->elm_options = $elm_options;

            if (is_array($this->elm_options)) {
                $this->_prep_options();
                switch ($this->func) {
                    case 'form_hidden':
                        $this->print_string .= $this->_build_input();
                        break;
                    default:
                        $this->print_string .= $this->_pre_elm();
                        $this->print_string .= $this->_label();
                        $this->print_string .= $this->_build_input();
                        $this->print_string .= $this->_post_elm();
                        break;
                }
            }
        }
        return $this->squish_HTML($this->print_string);
    }

    /**
     * Function build_display
     * 
     * Function is to build a viewable form using the easy form builder.
     * Includes token form_open and form_close just to make sure all the styling
     * works correctly. 
     * 
     * @param array $options - the elements/options for the form elemnts being built.
     * @param array/object $data_source - a default source for the value
     * @return string - the elements 
     */
    function build_display($options, $data_source = array()) {
        $this->_reset_builder();
        $this->data_source = (array) $data_source;

        /* styling prefrence */
        $this->config['default_control_label_class'] .= ' bold';

        $this->print_string .= $this->_build_form_open('', array('class' => $this->config['default_form_class']));

        foreach ($options as $elm_options) {
            $this->elm_options = $elm_options;

            if (is_array($this->elm_options)) {
                $this->_prep_options();
                if ($this->func != 'form_json') {
                    $this->func = 'form_label'; /* The only difference */
                }
                $this->print_string .= $this->_pre_elm();
                $this->print_string .= $this->_label();
                $this->print_string .= $this->_build_input();
                $this->print_string .= $this->_post_elm();
            }
        }
        $this->print_string .= $this->close_form();
        return $this->squish_HTML($this->print_string);
    }

    private function _prep_options() {
        foreach ($this->elm_options as &$opt) {
            /* trying again to change everything to an array */
            if (is_object($opt)) {
                $opt = (array) $opt;
            }
        }
        $this->func = $this->config['default_input_type'];
        /* Pull the input type from the array */
        if (isset($this->elm_options['type']) && !empty($this->elm_options['type'])) {
            $this->func = 'form_' . $this->elm_options['type'];
            unset($this->elm_options['type']);
        } else {
            $this->func = $this->config['default_input_type'];
        }
//        TODO: add some error checking that checks this function
//        if (!function_exists($this->func)) { /* check if the function exists */
//            $this->func = $this->config['default_input_type'];
//        }


        /* make sure to add 'form-control' to the class array */
        $class = $this->config['bootstrap_required_input_class'];
        if (isset($this->elm_options['class'])) {
            $class .= ' ' . trim(str_replace($this->config['bootstrap_required_input_class'], '', $this->elm_options['class']));
        }
        $this->elm_options['class'] = $class;

        /* make sure there is a name' attribute */
        if (!isset($this->elm_options['name'])) {
            /* put the id as the name by default - makes smaller 'config' arrays */
            if (isset($this->elm_options['id'])) {
                $this->elm_options['name'] = $this->elm_options['id'];
            } else {
                $this->elm_options['name'] = '';
            }
        }

        /* make sure there is a 'value' attribute 
         * Also, make for fun defaulting by passing an object 
         */
        $default_value = '';
        if (isset($this->elm_options['name']) && isset($this->data_source[$this->elm_options['name']])) {
            $default_value = $this->data_source[$this->elm_options['name']];
        } elseif (isset($this->elm_options['value'])) {
            $default_value = $this->elm_options['value'];
        }
        $this->elm_options['value'] = $this->adv_set_value($this->elm_options['name'], $default_value);


        /* ====== Handle input_addons ======== */

        /* FIRST - clear the input_addons global array from any previous elemets */
        $this->input_addons = array(
            'exists' => false,
            'pre' => array(),
            'pre_html' => '',
            'post' => array(),
            'post_html' => ''
        );

        /* playing nice: handling the singular case */
        if (isset($this->elm_options['input_addon'])) {
            $this->elm_options['input_addons'] = $this->elm_options['input_addon'];
            unset($this->elm_options['input_addon']);
        }

        /* set the new input_addons array */
        if (isset($this->elm_options['input_addons']) && !empty($this->elm_options['input_addons'])) {
            /* there are input addons */
            $this->input_addons['exists'] = true;

            /* check for pre addons */
            if (isset($this->elm_options['input_addons']['pre']) && !empty($this->elm_options['input_addons']['pre'])) {
                $pre = $this->elm_options['input_addons']['pre'];
                if (!is_array($pre)) { /* to handle more than one, this needs to be an array - but should handle the easy case of one string */
                    $pre = array($pre);
                }
                $this->input_addons['pre'] = $pre;
            }

            /* then check for post addons */
            if (isset($this->elm_options['input_addons']['post']) && !empty($this->elm_options['input_addons']['post'])) {
                $post = $this->elm_options['input_addons']['post'];
                if (!is_array($post)) { /* to handle more than one, this needs to be an array - but should handle the easy case of one string */
                    $post = array($post);
                }
                $this->input_addons['post'] = $post;
            }

            /* accomidate hard coding of custom elements */
            if (isset($this->elm_options['input_addons']['pre_html']) && !empty($this->elm_options['input_addons']['pre_html'])) {
                $this->input_addons['pre_html'] = $this->elm_options['input_addons']['pre_html'];
            }
            if (isset($this->elm_options['input_addons']['post_html']) && !empty($this->elm_options['input_addons']['post_html'])) {
                $this->input_addons['post_html'] = $this->elm_options['input_addons']['post_html'];
            }


            /* unset it so that no funky stuff happens */
            unset($this->elm_options['input_addons']);
        }

        /* remove help element - don't need help properties to be in input elements */
        $this->elm_options_help = (isset($this->elm_options['help']) && !empty($this->elm_options['help'])) ? $this->elm_options['help'] : '';
        unset($this->elm_options['help']);
        return;
    }

    /**
     * Form Value
     *
     * Upgraded from Codeigniter Form Helper
     * 
     * Grabs a value from the POST or GET array for the specified field so you can
     * re-populate an input field or textarea.  If Form Validation
     * is active it retrieves the info from the validation class
     *
     * @access	public
     * @param	string
     * @return	mixed
     * @author ExpressionEngine Dev Team
     * @author Tyler Wall <tyler.r.wall@gmail.com>
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

    function squish_HTML($html) {
        return str_replace(array("\r\n", "\r", "\n"), "", $html);
    }

    /*
      ===============================================================================================
      PRIVATE FUNCTIONS
      ===============================================================================================
     */

    private function _build_input($include_pre_post = true) {
        $input_html_string = '';
        /* Combine elements have multiple input elements on the same line.
         * This block will call this function, '_build_input' call recursivly.
         * 
         * Example use: Credit Card EXP month/year
         */
        if ($this->func == 'form_combine') {
            if (!isset($this->elm_options['elements'])) {
                dump($this->elm_options);
                show_error('Tried to create `form_combine` with no elements. (id="' . $this->elm_options['name'] . '")');
            }
            $elm_options_backup = $this->elm_options; /* We need to make a copy for everything to work correctly */

            foreach ($elm_options_backup['elements'] as $elm) {
                $this->elm_options = $elm; /* We override elm_options */
                $this->_prep_options(); /* Run Prep on the new one */
                $input_html_string .= $this->_build_input(false);
            }

            $this->elm_options = $elm_options_backup; /* We put our options back */
            $this->_prep_options(); /* Run Prep to restore the state in which we begain */
        } else {
            switch ($this->func) {
                /*
                 * This should eventualy be expanded to be able to edit individual elements in the k=>v 
                 * For now it will just display them.
                 */
                case 'form_json':
                    $kv_str = '';
                    $input_html_string = $this->_recursive_build_json((array) json_decode($this->elm_options['value']));
                    break;
                case 'form_label':
                    $input_html_string = form_label($this->_make_label($this->elm_options['value']), '', array(
                        'class' => ' control-label'
                    ));
                    break;
                case 'form_date':
                    $this->input_addons['exists'] = true;
                    $this->input_addons['post_html'] = $this->config['default_date_post_addon'];

                    if (empty($this->elm_options['value'])) {
                        $this->elm_options['value'] = date('Y-m-d', strtotime('today'));
                    } else {
                        $this->elm_options['value'] = date("Y-m-d", strtotime($this->elm_options['value']));
                    }
                    $input_html_string = form_input($this->elm_options);
                    break;
                case 'form_email':
                    $this->elm_options['type'] = 'email';
                    $input_html_string = form_input($this->elm_options);
                    break;
                case 'form_tel':
                    $this->elm_options['type'] = 'tel';
                    $input_html_string = form_input($this->elm_options);
                    break;
                case 'form_input':
                    $input_html_string = form_input($this->elm_options);
                    break;
                case 'form_hidden':
                    return form_hidden($this->elm_options['id'], $this->elm_options['value']);
                case 'form_submit':
                    $name = $this->elm_options['id'];
                    $value = $this->elm_options['value'];
                    
                    unset($this->elm_options['label']);
                    unset($this->elm_options['name']);

                    $class = str_replace($this->config['default_submit_classes'], '', $this->elm_options['class']);
                    $class = str_replace($this->config['bootstrap_required_input_class'], '', $this->elm_options['class']); /* remove the 'form-control' class */
                    /* add class="valid" to all dropdowns (makes them not full width - and works better with select2 plugin) */
                    if (strpos($class, $this->config['default_submit_classes']) === FALSE) {
                        $class .= ' ' . $this->config['default_submit_classes'];
                    }
                    $this->elm_options['class'] = trim($class);

                    $input_html_string = form_submit($name, $value, $this->_create_extra_string($this->elm_options));
                    break;
                case 'form_dropdown':
                    /* form_dropdown is different than an input */
                    if (isset($this->elm_options['options']) && !empty($this->elm_options['options'])) {
                        $name = $this->elm_options['name'];
                        $options = $this->elm_options['options'];
                        $value = $this->elm_options['value'];

                        unset($this->elm_options['name']);
                        unset($this->elm_options['value']);
                        unset($this->elm_options['options']);

                        if (!empty($this->config['default_dropdown_class'])) {
                            $class = str_replace($this->config['bootstrap_required_input_class'], '', $this->elm_options['class']);
                            /* add class="valid" to all dropdowns (makes them not full width - and works better with select2 plugin) */
                            if (strpos($class, $this->config['default_dropdown_class']) === FALSE) {
                                $class .= ' ' . $this->config['default_dropdown_class'];
                            }
                            $this->elm_options['class'] = trim($class);
                        }

                        $input_html_string = form_dropdown($name, $options, $value, $this->_create_extra_string());
                    } else {
                        dump($this->elm_options);
                        show_error('Tried to create `form_dropdown` with no options. (id="' . $this->elm_options['name'] . '")');
                    }
                    break;
                default:
                    $input_html_string = call_user_func($this->func, $this->elm_options);
                    break;
            }
        }
        $ret_string = '';
        $ret_string .= ($include_pre_post) ? $this->_pre_input() : '';
        $ret_string .= $this->_build_input_addons_pre();
        $ret_string .= $input_html_string;
        $ret_string .= $this->_build_input_addons_post();
        $ret_string .= ($include_pre_post) ? $this->_build_help_block() : '';
        $ret_string .= ($include_pre_post) ? $this->_post_input() : '';
        
        return $ret_string;
    }

    private function _build_input_addons_pre() {
        $ret_string = '';
        if ($this->input_addons['exists']) {
            if (!empty($this->input_addons['pre_html'])) {
                $ret_string = $this->input_addons['pre_html'];
            } else {
                $ret_string .= '<div class="input-group">';
                foreach ($this->input_addons['pre'] as $pre_addon) {
                    $ret_string .= '<span class="input-group-addon">' . $pre_addon . '</span>';
                }
            }
        }
        return $ret_string;
    }

    private function _build_input_addons_post() {
        $ret_string = '';
        if ($this->input_addons['exists']) {
            if (!empty($this->input_addons['post_html'])) {
                $ret_string = $this->input_addons['post_html'];
            } else {
                foreach ($this->input_addons['post'] as $post_addon) {
                    $ret_string .= '<span class="input-group-addon">' . $post_addon . '</span>';
                }
            }
            $ret_string .= '</div>';
        }
        return $ret_string;
    }

    private function _create_extra_string() {
        $extra = '';
        foreach ($this->elm_options as $k => $v) {
            $extra .= "{$k}=\"{$v}\"";
        }
        return trim($extra);
    }

    private function _build_form_open($action, $attributes) {
        return form_open($action, $attributes);
    }

    private function _pre_elm() {
        return '<div class="' . $this->config['default_input_container_class'] . '">';
    }

    private function _post_elm() {
        return '</div>';
    }

    private function _pre_input() {
        if ($this->func == 'form_date') {
            return '<div class="input-group date date-picker ' . $this->config['default_form_control_class'] . '" data-date="' . $this->elm_options['value'] . '" data-date-format="yyyy-mm-dd" data-date-viewmode="years">';
        }
        return '<div class="' . $this->config['default_form_control_class'] . '">';
    }

    private function _build_help_block() {
        if (!empty($this->elm_options_help)) {
            return '<span class="help-block">' . $this->elm_options_help . '</span>';
        }
        return '';
    }

    private function _post_input() {
        return '</div>';
    }

    private function _label() {
        $label = '';
        if (isset($this->elm_options['label'])) {
            $label = $this->elm_options['label'];
        } elseif (isset($this->elm_options['id']) && $this->func != 'form_submit') {
            $label = $this->_make_label($this->elm_options['id']);
        }

        return form_label($label, $this->elm_options['name'], array(
            'class' => $this->config['default_control_label_class']
        ));
    }

    private function _make_label($str) {
        return str_replace(array('_', '[', ']'), array(' ', ' ', ' '), $str);
    }

    private function _reset_builder() {
        $this->print_string = '';
        $this->func = $this->config['default_input_type'];
    }

    private function _recursive_build_json($ary, $offset = 0) {
        $kv_str = '';
        foreach ($ary as $k => $v) {
            /* This offset class doesn't look that great :/ ' */
            $offset_class = '';
            if ($offset >= 1) {
                $offset_class = 'col-md-offset-' . $offset;
            }

            if ((is_array($v) || is_object($v)) && !is_string($v)) {
                $new_offset = $offset + 1;
                $innter_str = $this->_recursive_build_json((array) $v, $new_offset);
                $kv_str .= '<div class="' . $offset_class . '"><strong>' . ucwords(strtolower(str_replace(array('_', '-'), ' ', $k))) . '</strong>' . $innter_str . '</div>';
            } else {
                $kv_str .= '<div class="' . $offset_class . '"><strong>' . ucwords(strtolower(str_replace(array('_', '-'), ' ', $k))) . '</strong>: ' . $v . '</div>';
            }
        }
        return $kv_str;
    }

    /*
      ===============================================================================================
      Specific Input_*
      ===============================================================================================
     */
}
