<?

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
 * @version		0.7
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
  3. Open your form (you can now use `$this->form_builder->open_form(array('action' => 'www.example.com'));` )
  4. Echo out the output of the form_builder->build_*
  5. Close your form ('</form>').
  6. Enjoy easy forms

  -----------------------------------------------------------------------------------------------

  <? $this->load->helper('form'); ?>
  <? $this->load->library('form_builder'); ?>

  <?= $this->form_builder->open_form(array('action' => 'www.example.com')); ?>
      <?
    echo $this->form_builder->build_form_horizontal(
            array(
        array(
            'id' => 'id',
            'type' => 'hidden',
            'value' => $item->id
        ),
        array(
            'id' => 'color',
            'placeholder' => 'Item Color',
            'input_addons' => array(
                'pre' => 'color: #',
                'post' => ';'
            ),
            'help' => 'this is a help block'
        ),
        array(
            'id' => 'published',
            'type' => 'dropdown',
            'options' => array(
                '1' => 'Published',
                '2' => 'Disabled'
            )
        ),
        array(
            'id' => 'description',
            'type' => 'textarea',
            'class' => 'wysihtml5',
            'placeholder' => 'Item Description (HTML or rich text)',
            'value' => html_entity_decode($item->description)
        ),
        array(
            'id' => 'submit',
            'type' => 'submit'
        )
            ), $defaults_object_or_array_from_db);
    ?>
  <?= $this->form_builder->close_form(); ?>
 */
class Form_builder {

    private $config = array(/* Config array - can be overrided by passing in array in ini() */
        'default_input_type' => 'form_input',
        'bootstrap_required_input_class' => 'form-control',
        'default_dropdown_class' => 'valid',
        'default_control_label_width' => 'col-md-2',
        'default_form_control_width' => 'col-md-9',
        'default_form_class' => 'form-horizontal col-md-12',
        'default_submit_classes' => 'btn btn-primary'
    );
    private $func; /* Global function holder - used in switches */
    private $data_source; /* Global holder for the source of the data */
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
        'post' => array() /* container for post addons */
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

        /* untested */
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
                $this->_prep_options($elm_options);
                switch ($this->func) {
                    case 'form_hidden':
                        $this->print_string .= $this->_build_input($elm_options);
                        break;
                    default:
                        $this->print_string .= $this->_pre_elm();
                        $this->print_string .= $this->_label($elm_options);
                        $this->print_string .= $this->_build_input($elm_options);
                        $this->print_string .= $this->_post_elm();
                        break;
                }
            }
        }
        return $this->print_string;
    }

    private function _prep_options(&$elm_options) {
        foreach ($elm_options as &$opt) {
            /* trying again to change everything to an array */
            if (is_object($opt)) {
                $opt = (array) $opt;
            }
        }
        $this->func = $this->config['default_input_type'];
        /* Pull the input type from the array */
        if (isset($elm_options['type'])) {
            $this->func = 'form_' . $elm_options['type'];
            unset($elm_options['type']);
        } else {
            $this->func = $this->config['default_input_type'];
        }
        if (!function_exists($this->func)) { /* check if the function exists */
            $this->func = $this->config['default_input_type'];
        }

        /* make sure to add 'form-control' to the class array */
        $class = $this->config['bootstrap_required_input_class'];
        if (isset($elm_options['class'])) {
            $class .= ' ' . trim(str_replace($this->config['bootstrap_required_input_class'], '', $elm_options['class']));
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
        } elseif (isset($elm_options['value'])) {
            $default_value = $elm_options['value'];
        }
        $elm_options['value'] = $this->adv_set_value($elm_options['name'], $default_value);


        /* ====== Handle input_addons ======== */

        /* FIRST - clear the input_addons global array from any previous elemets */
        $this->input_addons = array(
            'exists' => false,
            'pre' => array(),
            'post' => array()
        );

        /* playing nice: handling the singular case */
        if (isset($elm_options['input_addon'])) {
            $elm_options['input_addons'] = $elm_options['input_addon'];
            unset($elm_options['input_addon']);
        }

        /* set the new input_addons array */
        if (isset($elm_options['input_addons']) && !empty($elm_options['input_addons'])) {
            /* there are input addons */
            $this->input_addons['exists'] = true;

            /* check for pre addons */
            if (isset($elm_options['input_addons']['pre']) && !empty($elm_options['input_addons']['pre'])) {
                $pre = $elm_options['input_addons']['pre'];
                if (!is_array($pre)) { /* to handle more than one, this needs to be an array - but should handle the easy case of one string */
                    $pre = array($pre);
                }
                $this->input_addons['pre'] = $pre;
            }

            /* then check for post addons */
            if (isset($elm_options['input_addons']['post']) && !empty($elm_options['input_addons']['post'])) {
                $post = $elm_options['input_addons']['post'];
                if (!is_array($post)) { /* to handle more than one, this needs to be an array - but should handle the easy case of one string */
                    $post = array($post);
                }
                $this->input_addons['post'] = $post;
            }

            /* unset it so that no funky stuff happens */
            unset($elm_options['input_addons']);
        }
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

    /*
      ===============================================================================================
      PRIVATE FUNCTIONS
      ===============================================================================================
     */

    private function _build_input($elm_options) {
        $input_html_string = '';
        switch ($this->func) {
            case 'form_input':
                $input_html_string = form_input($elm_options);
                break;
            case 'form_hidden':
                return form_hidden($elm_options['id'], $elm_options['value']);
            case 'form_submit':
                $name = $elm_options['id'];
                $label = $this->_make_label((isset($elm_options['label']) ? $elm_options['label'] : $elm_options['id']));

                unset($elm_options['id']);
                unset($elm_options['label']);
                unset($elm_options['name']);

                $class = str_replace($this->config['default_submit_classes'], '', $elm_options['class']);
                /* add class="valid" to all dropdowns (makes them not full width - and works better with select2 plugin) */
                if (strpos($class, $this->config['default_submit_classes']) === FALSE) {
                    $class .= ' ' . $this->config['default_submit_classes'];
                }
                $elm_options['class'] = $class;

                return form_submit($name, $label, $this->_create_extra_string($elm_options));
            case 'form_dropdown':
                /* form_dropdown is different than an input */
                if (isset($elm_options['options']) && !empty($elm_options['options'])) {
                    $name = $elm_options['name'];
                    $options = $elm_options['options'];
                    $value = $elm_options['value'];

                    unset($elm_options['name']);
                    unset($elm_options['value']);
                    unset($elm_options['options']);

                    if (!empty($this->config['default_dropdown_class'])) {
                        $class = str_replace($this->config['bootstrap_required_input_class'], '', $elm_options['class']);
                        /* add class="valid" to all dropdowns (makes them not full width - and works better with select2 plugin) */
                        if (strpos($class, $this->config['default_dropdown_class']) === FALSE) {
                            $class .= ' ' . $this->config['default_dropdown_class'];
                        }
                        $elm_options['class'] = $class;
                    }

                    $input_html_string = form_dropdown($name, $options, $value, $this->_create_extra_string($elm_options));
                } else {
                    show_error('Tried to create `form_dropdown` with no options. (id="' . $elm_options['name'] . '")');
                }
                break;
            default:
                $input_html_string = call_user_func($this->func, $elm_options);
                break;
        }
        $ret_string = '';
        $ret_string .= $this->_pre_input();
        $ret_string .= $this->_build_input_addons_pre();
        $ret_string .= $input_html_string;
        $ret_string .= $this->_build_input_addons_post();
        $ret_string .= $this->_build_help_block($elm_options);
        $ret_string .= $this->_post_input();
        return $ret_string;
    }

    private function _build_input_addons_pre() {
        $ret_string = '';
        if ($this->input_addons['exists']) {
            $ret_string .= '<div class="input-group">';
            foreach ($this->input_addons['pre'] as $pre_addon) {
                $ret_string .= '<span class="input-group-addon">' . $pre_addon . '</span>';
            }
        }
        return $ret_string;
    }

    private function _build_input_addons_post() {
        $ret_string = '';
        if ($this->input_addons['exists']) {
            foreach ($this->input_addons['post'] as $post_addon) {
                $ret_string .= '<span class="input-group-addon">' . $post_addon . '</span>';
            }
            $ret_string .= '</div>';
        }
        return $ret_string;
    }

    private function _create_extra_string($elm_options) {
        $extra = '';
        foreach ($elm_options as $k => $v) {
            $extra .= "{$k}=\"{$v}\"";
        }
        return trim($extra);
    }

    private function _build_form_open($action, $attributes) {
        return form_open($action, $attributes);
    }

    private function _pre_elm() {
        return '<div class="form-group">';
    }

    private function _post_elm() {
        return '</div>';
    }

    private function _pre_input() {
        return '<div class="' . $this->config['default_form_control_width'] . '">';
    }

    private function _build_help_block($elm_options) {
        if (isset($elm_options['help']) && !empty($elm_options['help'])) {
            return '<span class="help-block">' . $elm_options['help'] . '</span>';
        }
        return '';
    }

    private function _post_input() {
        return '</div>';
    }

    private function _label($elm_options) {
        $label = '';
        if (isset($elm_options['label'])) {
            $label = $elm_options['label'];
        } elseif (isset($elm_options['id']) && $this->func != 'form_submit') {
            $label = $this->_make_label($elm_options['id']);
        }
        return '<label class="' . $this->config['default_control_label_width'] . ' control-label" for="name">' . $label . '</label>';
    }

    private function _make_label($str) {
        return ucwords(str_replace(array('_', '-', '[', ']'), array(' ', ' ', ' ', ' '), $str));
    }

    private function _reset_builder() {
        $this->print_string = '';
        $this->func = $this->config['default_input_type'];
    }

}
