<?php
    /**
     *
     */
    class User_m extends MY_Model
    {

      function __construct()
      {
        parent::__construct();
        $this->data['primary_key'] = 'username';
        $this->data['table_name'] = 'user';
      }
    }

 ?>
