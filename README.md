Codeigniter Bootstrap 3 Form Builder
======================

Do you want to write forms 60% faster? 
---------------------
Or type 60% less? 
---------------------
Are you using Codegniter and Bootstrap 3?
---------------------
Then this is the plugin for you!
---------------------

CodeIgniter library to build form's styled with Bootstrap 3.
It's got 3 steps:
*	Load Libraries
* 	Open Form
*	Echo out the output of your chosen function
* 	Close your form
* 	Enjoy Easy form building


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
echo $this->form_builder->build_form_horizontal(
        array(
    array(
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
    array(
        'id' => 'submit',
        'type' => 'submit'
    )
        ), $defaults_object_or_array_from_db);
?>
```
            
Produces:
```
<form action="" method="post" accept-charset="utf-8" class="form-horizontal col-md-12">
	<input id="id" type="hidden" name="id" value="33">
         <div class="form-group">
        <label class="col-md-2 control-label" for="name">Color</label>
        <div class="col-md-9">
            <div class="input-group">
                <span class="input-group-addon">color: #</span>
                <input type="text" name="color" value="" id="color" placeholder="Item Color" help="this is a help block" class="form-control">
                <span class="input-group-addon">;</span>
            </div>
            <span class="help-block">this is a help block</span>
        </div>
    </div>
     <div class="form-group">
         <label class="col-md-2 control-label" for="name">Published</label>
         <div class="col-md-9">
             <select name="published" id="published"class=" valid">
                 <option value="1">Published</option>
                 <option value="2">Disabled</option>
             </select>
         </div>
     </div>
     <div class="form-group">
         <label class="col-md-2 control-label" for="name">Description</label>
         <div class="col-md-9">
             <textarea name="description" cols="40" rows="10" id="description" class="form-control wysihtml5" placeholder="Item Description (HTML or rich text)" >HTML</textarea>
         </div>
     </div>
     <div class="form-group">
     	 <label class="col-md-2 control-label" for="name"></label>
     	 <input type="submit" name="submit" value="Submit" class="form-control btn btn-primary">
     </div>
 </form>   
 ```    
 
 ![ScreenShot](https://raw.github.com/wallter/codeigniter_bootstrap_form_builder/master/images/form_render_screen_shot.png)

4. Close The Form
==============
```
<?= $this->form_builder->close_form(); ?>
```
