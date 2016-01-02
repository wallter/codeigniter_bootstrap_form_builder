Codeigniter Bootstrap 3 Form Builder
======================

### Do you want to write forms 60% faster? 
### Or type 60% less? 
### Are you using Codegniter and Bootstrap 3?
### Then this is the plugin for you!

CodeIgniter library to build form's styled with Bootstrap 3.
It's got 5 steps:
* Load Libraries
* Open Form
* Echo out the output of your chosen function
* Close your form
* Enjoy Easy form building


1. Load Libraries
==============

Load the Codeigniter form helper, then load the form_builder library.

```
$this->load->helper('form');
$this->load->library('form_builder');
```

2. Open Your form
==============

```
<?= $this->form_builder->open_form(array('action' => '')); ?>
```
	
3. Echo out your form
==============

```
<?
/* Prepare variables */
$defaults_object_or_array_from_db = NULL;

$item = new stdClass;
$item->id = 33;
$item->description = '';

$years = range(intval(date('Y')), intval(date('Y')) + 20);
$months = array_map(function ($n) {
	return str_pad($n, 2, '0', STR_PAD_LEFT);
}, range(1, 12));

$exp_month_options = array_combine($months, $months);
$cc_exp_month = '05';

$exp_year_options = array_combine($years, $years);
$cc_exp_year = intval(date('Y')) + 5;

$input_span = 'pull-left ';

/* Build form */
echo $this->form_builder->build_form_horizontal(
		array(
				array(/* HIDDEN */
						'id' => 'id',
						'type' => 'hidden',
						'value' => $item->id
				),
				array(/* INPUT */
						'id' => 'color',
						'placeholder' => 'Item Color',
						'input_addons' => array(
								'pre' => 'color: #',
								'post' => ';'
						),
						'help' => 'this is a help block'
				),
				array(/* DROP DOWN */
						'id' => 'published',
						'type' => 'dropdown',
						'options' => array(
								'1' => 'Published',
								'2' => 'Disabled'
						)
				),
				array(/* TEXTAREA */
						'id' => 'description',
						'type' => 'textarea',
						'class' => 'wysihtml5',
						'placeholder' => 'Item Description (HTML or rich text)',
						'value' => html_entity_decode($item->description)
				),
				array(/* COMBINE */
						'id' => 'expiration_date',
						'type' => 'combine', /* use `combine` to put several input inside the same block */
						'elements' => array(
								array(
										'id' => 'cc_exp_month',
										'label' => 'Expiration Date',
										'autocomplete' => 'cc-exp-month',
										'type' => 'dropdown',
										'options' => $exp_month_options,
										'class' => $input_span . 'required input-small',
										'required' => '',
										'data-items' => '4',
										'pattern' => '\d{1,2}',
										'style' => 'width: auto;',
										'value' => (isset($cc_exp_month) ? $cc_exp_month : '')
								),
								array(
										'id' => 'cc_exp_year',
										'label' => 'Expiration Date',
										'autocomplete' => 'cc-exp-year',
										'type' => 'dropdown',
										'options' => $exp_year_options,
										'class' => $input_span . 'required input-small',
										'required' => '',
										'data-items' => '4',
										'pattern' => '\d{4}',
										'style' => 'width: auto; margin-left: 5px;',
										'value' => (isset($cc_exp_year) ? $cc_exp_year : '')
								)
						)
				),
				array(/* DATE */
						'id' => 'date',
						'type' => 'date'
				),
				array(/* CHECKBOX */
						'id' => 'checkbox_group',
						'label' => 'Checkboxes',
						'type' => 'checkbox',
						'options' => array(
								array(
										'id' => 'checkbox1',
										'value' => 1
										// If no label is set, the value will be used
								),
								array(
										'id' => 'checkbox2',
										'value' => 2,
										'label' => 'Two'
								)
						)
				),
				array(/* RADIO */
						'id' => 'radio_group',
						'label' => 'Radio buttons',
						'type' => 'radio',
						'options' => array(
								array(
										'id' => 'radio_button_yes',
										'value' => 1,
										'label' => 'Yes'
								),
								array(
										'id' => 'radio_button_no',
										'value' => 0,
										'label' => 'No'
								)
						)
				),
				array(/* SUBMIT */
						'id' => 'submit',
						'type' => 'submit'
				)
		), $defaults_object_or_array_from_db);

echo $this->form_builder->close_form();
?>
```

4. Close The Form
==============
```
<?= $this->form_builder->close_form(); ?>
```
            
Produces:
==============
```
<form action="" class="form-horizontal col-sm-12" autocomplete="on" enctype="multipart/form-data" method="post" accept-charset="utf-8">
	<input type="hidden" name="id" value="33" />
	<div class="form-group">
		<label for="color" class="col-sm-2 control-label">Color</label>
		<div class="col-sm-9">
			<div class="input-group">
				<span class="input-group-addon">color: #</span>
				<input type="text" name="color" value="" id="color" placeholder="Item Color" class="form-control"  />
				<span class="input-group-addon">;</span>
			</div>
			<span class="help-block">this is a help block</span>
		</div>
	</div>
	<div class="form-group">
		<label for="published" class="col-sm-2 control-label">Published</label>
		<div class="col-sm-9">
			<select name="published" id="published" class="valid form-control">
				<option value="1">Published</option>
				<option value="2">Disabled</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="description" class="col-sm-2 control-label">Description</label>
		<div class="col-sm-9">
			<textarea name="description" cols="40" rows="10" id="description" class="form-control wysihtml5" placeholder="Item Description (HTML or rich text)" ></textarea>
		</div>
	</div>
	<div class="form-group">
		<label for="expiration_date" class="col-sm-2 control-label">Expiration Date</label>
		<div class="col-sm-9">
			<select name="cc_exp_month" id="cc_exp_month" label="Expiration Date" autocomplete="cc-exp-month" class="pull-left required input-small valid form-control" required="" data-items="4" pattern="\d{1,2}" style="width: auto;">
				<option value="01">01</option>
				<option value="02">02</option>
				<option value="03">03</option>
				<option value="04">04</option>
				<option value="05" selected="selected">05</option>
				<option value="06">06</option>
				<option value="07">07</option>
				<option value="08">08</option>
				<option value="09">09</option>
				<option value="10">10</option>
				<option value="11">11</option>
				<option value="12">12</option>
			</select>
			<select name="cc_exp_year" id="cc_exp_year" label="Expiration Date" autocomplete="cc-exp-year" class="pull-left required input-small valid form-control" required="" data-items="4" pattern="\d{4}" style="width: auto; margin-left: 5px;">
				<option value="2016">2016</option>
				<option value="2017">2017</option>
				<option value="2018">2018</option>
				<option value="2019">2019</option>
				<option value="2020">2020</option>
				<option value="2021" selected="selected">2021</option>
				<option value="2022">2022</option>
				<option value="2023">2023</option>
				<option value="2024">2024</option>
				<option value="2025">2025</option>
				<option value="2026">2026</option>
				<option value="2027">2027</option>
				<option value="2028">2028</option>
				<option value="2029">2029</option>
				<option value="2030">2030</option>
				<option value="2031">2031</option>
				<option value="2032">2032</option>
				<option value="2033">2033</option>
				<option value="2034">2034</option>
				<option value="2035">2035</option>
				<option value="2036">2036</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label for="date" class="col-sm-2 control-label">Date</label>
		<div class="col-sm-9">
			<input type="date" name="date" value="" id="date" class="form-control"  />
		</div>
	</div>
	<div class="form-group">
		<label for="checkbox_group" class="col-sm-2 control-label">Checkboxes</label>
		<div class="col-sm-9">
			<label class="checkbox-inline">
				<input type="checkbox" name="checkbox_group" value="1" id="checkbox_group" label="1"  />
				1
			</label>
			<label class="checkbox-inline">
				<input type="checkbox" name="checkbox_group" value="2" id="checkbox_group" label="Two"  />
				Two
			</label>
		</div>
	</div>
	<div class="form-group">
		<label for="radio_group" class="col-sm-2 control-label">Radio buttons</label>
		<div class="col-sm-9">
			<label class="radio-inline">
				<input type="radio" name="radio_group" value="1" id="radio_group" label="Yes"  />
				Yes
			</label>
			<label class="radio-inline">
				<input type="radio" name="radio_group" value="0" id="radio_group" label="No"  />
				No
			</label>
		</div>
	</div>
	<div class="form-group">
		<label for="submit" class="col-sm-2 control-label">
		</label>
		<div class="col-sm-9">
			<input type="submit" name="submit" value="Submit"  class="btn btn-primary" name="submit" value="" />
		</div>
	</div>
</form>
 ```    
 
 ![ScreenShot](https://raw.github.com/wallter/codeigniter_bootstrap_form_builder/master/images/form_render_screen_shot.png)
