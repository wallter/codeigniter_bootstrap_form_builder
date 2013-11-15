<?
/**
 * Bootstrap 3 Address
 * 
 * Impliments autocomplete standard.
 *      IETF RFC3106:           http://www.ietf.org/rfc/rfc3106.txt
 *      Living HTML Standard:   http://www.whatwg.org/specs/web-apps/current-work/multipage/association-of-controls-and-forms.html#autofill-field
 * 
 * @pram string $pref - this is the prefix (i.e. 'shipping' or 'billing') for the items
 *      - It is also used in the autocomplete 
 * @pram string $input_span - this is a varable class that can be used to specify the width 
 *      class of the input fields to make the form fit in tighter spots
 * 
 *  -----------------------------
 * Call this view as folows:
 * echo $this->load->view('store/template/bootstrap_3_address', array('prefix' => 'shipping_'));
 * 
 */

/* ===== Checking if Var's were passed into the file ===== */
$pref = (isset($prefix)) ? $prefix : '';
$group_sufx = (isset($prefix) && !empty($prefix)) ? str_replace('_', '', $prefix) : uniqid();
$input_span = (isset($input_span)) ? $input_span : '';

/* ===== BEGIN FORM BUILDING ===== */

$country_options = array();
$zone_options = array();
foreach ($this->store_service->get_countries() as $country) {
    $country_options[$country->id] = $country->name;
}
foreach ($this->store_service->get_zones_by_country((!empty($country_id) ? $country_id : '223')) as $zone) {
    $zone_options[$zone->id] = $zone->name;
}

$form_options = array(
    array(
        'id' => $pref . 'first_name',
        'autocomplete' => $group_sufx . ' given-name',
        'placeholder' => 'First Name',
        'label' => 'First Name',
        'class' => $input_span,
        'value' => !empty($first_name) ? $first_name : ''
    ),
    array(
        'id' => $pref . 'last_name',
        'autocomplete' => $group_sufx . ' family-name',
        'placeholder' => 'Last Name',
        'label' => 'Last Name',
        'class' => $input_span,
        'value' => !empty($last_name) ? $last_name : ''
    ),
    array(
        'id' => $pref . 'phone',
        'type' => 'tel',
        'autocomplete' => $group_sufx . ' tel',
        'placeholder' => 'Phone Number',
        'label' => 'Phone',
        'class' => $input_span,
        'value' => !empty($phone) ? $phone : ''
    ),
    array(
        'id' => $pref . 'email',
        'type' => 'email',
        'autocomplete' => $group_sufx . ' email',
        'placeholder' => 'Email Address',
        'label' => 'Email',
        'class' => $input_span,
        'value' => !empty($email) ? $email : ''
    ),
    array(
        'id' => $pref . 'address1',
        'autocomplete' => $group_sufx . ' address-line1',
        'placeholder' => 'Address',
        'label' => 'Address',
        'class' => $input_span,
        'value' => !empty($address1) ? $address1 : ''
    ),
    array(
        'id' => $pref . 'address2',
        'autocomplete' => $group_sufx . ' address-line2',
        'placeholder' => 'Address 2nd line',
        'label' => '',
        'class' => $input_span,
        'value' => !empty($address2) ? $address2 : ''
    ),
    array(
        'id' => $pref . 'city',
        'autocomplete' => $group_sufx . ' locality',
        'placeholder' => 'City',
        'label' => 'City',
        'class' => $input_span,
        'value' => !empty($city) ? $city : ''
    ),
    array(
        'id' => $pref . 'country',
        'label' => 'Country',
        'autocomplete' => $group_sufx . ' country',
        'class' => $input_span . ' input-medium',
        'type' => 'dropdown',
        'options' => $country_options,
        'value' => (!empty($country_id)) ? $country_id : '223'
    ),
    array(
        'id' => $pref . 'state',
        'label' => 'State',
        'autocomplete' => $group_sufx . ' region',
        'class' => $input_span . ' input-medium',
        'type' => 'dropdown',
        'options' => $zone_options,
        'value' => (!empty($state_id)) ? $state_id : 0
    ),
    array(
        'id' => $pref . 'zip',
        'label' => 'Zip Code',
        'autocomplete' => $group_sufx . ' postal-code',
        'placeholder' => 'Zip Code',
        'class' => $input_span,
        'value' => !empty($zip) ? $zip : ''
    ),
);
/* Testing to make bootstrap 2 compatable - NOT WORKING */
//$this->form_builder->init(array(
//    'default_control_label_class' => 'control-label',
//    'default_input_container_class' => 'control-group',
//    'default_form_control_class' => 'controls',
//    'bootstrap_required_input_class' => ''
//));
echo $this->form_builder->build_form_horizontal($form_options);
?>

<script>
    $(function() {
        $('#<?= $pref; ?>country').on('change', function() {
            $.post("/store/ajax_get_zones", {
                country_id: $('#<?= $pref; ?>country').val()
            }, function(e) {
                if (e)
                    $("#<?= $pref; ?>state").html(e).select2();
            })
        });
    });
</script>