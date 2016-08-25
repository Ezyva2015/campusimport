<?php


require ("../class-gfdbffields.php");


use GF\GF_DBF_FIELDS;



$gf_dbf_fields = new GF_DBF_FIELDS();


print_r($gf_dbf_fields->dbf_fields());
