<?php
/**
 *
 */
class Admin extends MY_Controller
{

  public function __construct()
  {
    parent::__construct();
  }

  public function index()
  {
    $data = [
			'title'		=> 'Admin',
			'content'	=> 'admin/dashboard'
		];
    $this->template($data);
  }
}

 ?>
