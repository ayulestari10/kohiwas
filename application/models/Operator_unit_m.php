<?php
    /**
     *
     */
    class Operator_unit_m extends MY_Model
    {

      function __construct()
      {
        parent::__construct();
        $this->data['table_name'] = 'operator_unit';
        $this->data['primary_key'] = 'no_pegawai';
      }
    }

 ?>
