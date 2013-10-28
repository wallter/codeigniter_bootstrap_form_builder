Codeigniter Bootstrap 3 Form Builder
======================

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

	$this->load->helper('form');
	$this->load->library('form_builder');

2. Open Your form
==============

	<?= $this->form_builder->open_form(array('action' => '')); ?>
	
3. Echo out your form
==============

	<?=
            $this->form_builder->build_form_horizontal(
                    array(
                array( /* INPUT */
                    'id' => 'name',
                    'placeholder' => 'Item Name',
                ),
                array( /* DROP DOWN */
                            'id' => 'published',
                            'type' => 'dropdown',
                            'options' => array(
                                '1' => 'Published',
                                '2' => 'Disabled'
                            )
                        ),
                array( /* TEXTAREA */
                    'id' => 'description',
                    'type' => 'textarea',
                    'class' => 'wysihtml5',
                    'placeholder' => 'Item Description (HTML or rich text)',
                    'value' =>html_entity_decode($item->description)
                )
                    ), $defaults_object_or_array_from_db);
            ?>

4. Close The Form
==============

	<?= $this->form_builder->close_form(); ?>
