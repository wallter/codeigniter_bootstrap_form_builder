Codeigniter Bootstrap Form Builder
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

	<form id="item_form" name="item_form" method="post" class="col-md-7 form-horizontal" action="">
	
2. Echo out your form
==============

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
