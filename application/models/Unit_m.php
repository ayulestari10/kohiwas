<?php
    /**
     *
     */
    class Unit_m extends MY_Model
    {

      function __construct()
      {
        parent::__construct();
        $this->data['primary_key'] = 'id_unit';
        $this->data['table_name'] = 'unit';
      }
    }

 ?>
