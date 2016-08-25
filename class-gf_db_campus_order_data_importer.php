<?php

namespace GF;

class GF_DB_CAMPUS_ORDER_DATA_IMPORTER {

    //id
    //form_id
    //source_platform
    //gravity_form
    //data
    //updated_at
    //created_at

    private $table_name = "wp_rg_campus_order_data_importer";

    function __construct() {
    }

    public function getMappedDataByFormIdAndSourcePlatFrom($form_id, $source_platform) {
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare("Select * from $this->table_name where form_id = $form_id and source_platform = '$source_platform'"  , ARRAY_A)
        );
        return $results;
    }

    public function processMapping($form_id, $source_platform, $gravity_form, $data) {
        if($id = $this->isMappingExist($form_id, $source_platform)) {
            if($this->update($data, $id)) {
                //echo "<br> successfully update";
                //return 1;
                return '<p>Mapping successfully updated</p>';
            } else {
                //return 2;
                return '<p>Mapping failed to update</p>';
            }
            // update the mapping fields
        } else {
            // add new mapping
            if($this->insert($form_id, $source_platform, $gravity_form, $data)){
                //echo "<br> successfully inserted";
                return '<p>Mapping successfully added</p>';
                //return 3;
            } else {
                //echo "<br> failed to insert";
                return '<p>Mapping failed to add</p>';
                //return 4;
            }
        }
    }

    public function isMappingExist($form_id, $source_platform) {
        // [form_id] => 6
        // [source_platform] => BGL Simple Fund Desktop
        //
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare("Select * from $this->table_name where form_id = $form_id and  source_platform = '$source_platform'"  , ARRAY_A)
        );
         //print_r($results);
        if(!empty($results)) {
            return $results[0]->id;
        } else {
            return false;
        }
    }

    public  function query() {
        global $wpdb;
        $results = $wpdb->get_results(
            $wpdb->prepare("Select * from $this->table_name where id > 0"  , ARRAY_A)
        );
        return $results;
    }

    public function insert($form_id, $source_platform, $gravity_form=null, $data) {
        global $wpdb;
        return $wpdb->insert(
            $this->table_name,
            array(
                'form_id' => $form_id,
                'source_platform' => $source_platform,
                'gravity_form' => $gravity_form,
                'data' => $data
            ),
            array(
                '%d',
                '%s',
                '%s',
                '%s'
            )
        );
    }

    /**
     * @note: Not sure for the update yet, Need to check this functionality
     * @param array $update_row
     * @param $row_id_name
     * @param $row_id_value
     * @return mixed
     */
    public function update($data, $id) {
        //print "<br> in update func";
        //print "<br>data = " . $data;
        //print "<br> id " . $id;

        global $wpdb;
        return $wpdb->update(
            $this->table_name,
            array( 'data' => $data ),
            array( 'id' => $id ),
            array( '%s' ),
            array( '%d' )
        );

    }

    public function delete_dual($form_id, $source_platform) {

        $fid = intval($form_id);
        $sp =   $source_platform;


        print " form id " . $fid . ' sp ' . $sp;

        global $wpdb;
        return $wpdb->delete( $this->table_name , array( 'form_id' => $fid, 'source_platform'=>$sp ), array( '%d', '%s' ) );
    }

    public function delete($id) {
        global $wpdb;
        return $wpdb->query($wpdb->prepare("DELETE FROM $this->table_name WHERE id = %d", $id));
    }

    public function mappEditedData($dbf_field, $data, $formFields) {
        /**
         * 1. Check if the bdf field is exist in the saved data with value if with value id then get it ex: nakuha nako ang number 3 nga id then gamiton ni sa pag set sa option to selected dropdown
         * 2. E check ang foreach nga loop daun daun gamiton nimo ang 3 nga nakuha sa value didto sa saved data daun e set nimo as selected ang drodown
         * 3. E return amg option
         * 4. E print ang data didto sa client or front end side
         */
        $data_arr  = json_decode($data, true);
        $fieldList = "";
        $saved_form_id = 0;
        // add underscode with string and convert characters to lower
        $dbf_field = str_replace(' ', '_', $dbf_field);
        $dbf_field = strtolower($dbf_field);
        foreach($data_arr as $dbf_field_name => $form_id) {
            // add underscode with string and convert characters to lower
            $dbf_field_name = str_replace(' ', '_', $dbf_field_name);
            $dbf_field_name = strtolower($dbf_field_name);
            //echo "print " . $dbf_field_name . '<br>';
            if($dbf_field_name == $dbf_field) {
                //print " matched  $dbf_field_name == $dbf_field <br>";
                $saved_form_id = $form_id;
                break;
            }
        }
        //print " saved_form_id = " . $saved_form_id;
        //echo "<h1> dbf field </h1>";
        //print $dbf_field;
        //echo "<h1> Data Array </h1>";
        //print_r($data_arr);
        // echo "<h1> Form Fields </h1>";
        // print_r($formFields);
        foreach($formFields as $field){
            if($saved_form_id == $field->id) {
                $fieldList .= '<option value="'.$field->id.'" selected>'.$field->label.' '.$field->type.'</option>';
            } else {
                $fieldList .= '<option value="'.$field->id.'">'.$field->label.' '.$field->type.'</option>';
            }
            //$fieldList .= "<br> id: " . $field->id . " field name: " .  $field->label . " " .  $field->type;
        }
        // print_r($fieldList);
        //echo "</pre>";
        //return "value";
        return $fieldList;
    }
}