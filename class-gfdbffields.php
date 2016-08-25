<?php


namespace GF;

class GF_DBF_FIELDS {

    function __construct() {
    }

    public function dbf_fields() {
        $gfFields = array(

            // Start
            'Reference',
            'Fund Name',
            'State Law To Govern The Fund',
            'Fund Address',
                'Fund Address 1 Hidden',
                'Fund Address 2 Hidden',
                'Fund Address 3 Hidden',
                'Fund Address 4 Hidden',
                'Fund Address 5 Hidden',

            'Trustee Meeting Address Select',
            'Trustee Meeting Address Text',
                'Trustee Metting Address 1 Hidden',
                'Trustee Metting Address 2 Hidden',
                'Trustee Metting Address 3 Hidden',
                'Trustee Metting Address 4 Hidden',
                'Trustee Metting Address 5 Hidden',

            // Trustee members
            'How many Members will the Fund have',

                // Member 1
                'Title 1',
                'Given Names 1',
                'Family Name 1',
                'Gender 1',
                'Date of Birth 1',
                'TFN 1',
                'Member 1 Residential Address',
                'Member 1 Address Search',

                    'Member 1 Address 1 Hidden',
                    'Member 1 Address 2 Hidden',
                    'Member 1 Address 3 Hidden',
                    'Member 1 Address 4 Hidden',
                    'Member 1 Address 5 Hidden',

                // Member 2
                'Title 2',
                'Given Names 2',
                'Family Name 2',
                'Gender 2',
                'Date of Birth 2',
                'TFN 2',
                'Member 2 Residential Address',
                'Member 2 Address Search',

                    'Member 2 Address 1 Hidden',
                    'Member 2 Address 2 Hidden',
                    'Member 2 Address 3 Hidden',
                    'Member 2 Address 4 Hidden',
                    'Member 2 Address 5 Hidden',

                // Member 3
                'Title 3',
                'Given Names 3',
                'Family Name 3',
                'Gender 3',
                'Date of Birth 3',
                'TFN 3',
                'Member 3 Residential Address',
                'Member 3 Address Search',

                    'Member 3 Address 1 Hidden',
                    'Member 3 Address 2 Hidden',
                    'Member 3 Address 3 Hidden',
                    'Member 3 Address 4 Hidden',
                    'Member 3 Address 5 Hidden',

                // Member 4
                'Title 4',
                'Given Names 4',
                'Family Name 4',
                'Gender 4',
                'Date of Birth 4',
                'TFN 4',
                'Member 4 Residential Address',
                'Member 4 Address Search',

                    'Member 4 Address 1 Hidden',
                    'Member 4 Address 2 Hidden',
                    'Member 4 Address 3 Hidden',
                    'Member 4 Address 4 Hidden',
                    'Member 4 Address 5 Hidden',


            // TRUSTEE TYPE DATA
            'Trustee Type',
                // Non-Member Trustee Individual
                'Individual Title',
                'Individual Trustee 2 - Given Names',
                'Individual Trustee 2 - Family Name',
                'Individual Gender',

                // Corporate Trustee
                'Corporate Trustee Name',
                'Corporate Trustee ACN',
                'Corporate Date of Incorporation',
                'Corporate Trustee Registered Address',

                //Directors
                'Does the Company have an additional Director who is not a Member of the Fund?'
        );


        return  $gfFields;
    }
}

