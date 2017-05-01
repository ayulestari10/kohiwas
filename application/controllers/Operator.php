<?php
/**
 *
 */
class Operator extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $data = [
			'title'		=> 'Admin',
			'content'	=> 'operator_unit/dashboard'
		];
    $this->template($data,'operator');
  }
}

 ?>
