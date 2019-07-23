<?php





namespace store;



use \core\Z_Param;



require_once __DIR__."/../core/Z_Param.php";



class Z_Specification extends Z_Param

{

    /* @params Object Z_Params */

    private $param;



    /**

     * @param $user_id integer current user id

     * @return string contactsIDs who owner is this user

     * @ToDo add primary contact

     */

    protected function getYourContactID($user_id)

    {

        return $this->queryNoDML("SELECT  contact_person.contact_id  AS contacts_id FROM contact_person WHERE contact_person.person_id ={$user_id} LIMIT 1")[0]['contacts_id'];

    }



    /**

     * @param $alias string access name alias who is need to get

     * @return integer/bool integer if access exist bool if not

     */

    protected function getAccessByAlias($alias){

        return $this->queryNoDML("SELECT store_specification_accesses_names.access_id AS access FROM  store_specification_accesses_names WHERE alias = '{$alias}'")[0]['access'];

    }



    /**

     * @param $user_id    integer current user id

     * @param $action     string  access name

     * @return bool       true if has access or false

     */

    protected function hasAccessBySpecification($specification_id,$user_id, $action){



        $person_contact_id = $this->getYourContactID($user_id);

        if(intval($person_contact_id)){

            $full_access = $this->queryNoDML("SELECT store_specification_accesses_names.access_id AS access FROM  store_specification_accesses_names WHERE alias = 'full_access'")[0]['access'];

            $access = $this->getAccessByAlias($action);



            $permission = $this->queryNoDML("SELECT access_id AS access FROM store_specification_access WHERE specification_id = {$specification_id} AND contact_id = {$person_contact_id}");



            if($permission){

                foreach ($permission AS $key=>$value){

                    if($value['access'] == $access || $value['access'] == $full_access){

                        return true;

                    }

                }

                return false;



            }else{

                return false;

            }

        }

    }





    /**

     * @param $user_id

     * @return Json File, user settings

     */

    public function getUserSettings($user_id){

        $user_settings = $this->queryNoDML("SELECT 

                                                         user_settings.settings AS settings

                                                         FROM user_settings

                                                         WHERE user_settings.user_id = {$user_id} AND alias = 'specification'

                                                  ");

        $myJSON = json_encode($user_settings);

        return $myJSON;

    }





    /**

     * @param $user_id

     * @return  $specification_list EXP id=>1 name=>Draft A

     */

    public function getSpecificationList($user_id){


        $spec_ids = array();
        $access = array();

        $spec_list = $this->queryNoDML("SELECT specification_id AS id,name FROM store_specifications 
                                               WHERE is_archived = 0 ");


        foreach($spec_list AS $key=>$value){

            $specification_id = $value['id'];

            if($this->hasAccessBySpecification($specification_id,$user_id,"view")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"edit")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"remove")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"share")){

                $access = array(1);

            }

            if(empty($access)){

            }else{
                array_push($spec_ids,$specification_id);
                $access = array();
            }


        }

          $specification_list = $this->queryNoDML("SELECT specification_id AS id,name FROM store_specifications 
                                                          WHERE is_archived = 0 
                                                          AND store_specifications.specification_id 
                                                          IN(" . implode(',',$spec_ids).")");

          return $specification_list ;

    }





    /**

     * @param $langID  integer current user lang id

     * @return array   access names [[1,view,View],[2,edit,Edit]]

     */

    public function getAccessNames($langID)

    {

        $answer = $this->queryNoDML("SELECT store_specification_accesses_names.access_id AS access,

                                            store_specification_accesses_names.alias AS access_alias,

                                            names.text AS access_name

                                            FROM store_specification_accesses_names

                                            LEFT JOIN names ON names.name_id = store_specification_accesses_names.name_id AND names.lang_id = {$langID}");

        return $answer;

    }



    /**

     * @param $specification_id

     * @param $user_id

     * @return $specification_info

     */

    public function getSpecificationInfo($langID,$specification_id, $user_id){

            $answer = array();

            $access = array();

        $specification_info = $this->queryNoDML("SELECT name,customer FROM store_specifications WHERE specification_id = '{$specification_id}'");

        foreach ($specification_info AS $key=>$value){

            $answer[$key] = $value;

        }





            if($this->hasAccessBySpecification($specification_id,$user_id,"view")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"edit")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"remove")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"share")){

                $access = array(1);

            }



            if(empty($access)){

                $answer['products'] = [];

                return  $answer;

            }else{

                $products = $this->queryNoDML("SELECT store_vendors.text AS vendor,store_products.part_number AS part_number,store_product_names.text AS name,store_products.product_id As product_id, store_specification_products.quantity FROM store_products

                                                                                 INNER JOIN store_specification_products ON store_specification_products.product_id = store_products.product_id

                                                                                 INNER JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id

                                                                                 INNER JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id

                                                                                 WHERE

                                                                                 CASE 

                                                                                 WHEN  store_product_names.lang_id  ={$langID} 

                                                                                 THEN  store_product_names.lang_id = {$langID}

                                                                                 WHEN store_product_names.lang_id = 2 

                                                                                 AND NOT EXISTS

                                                                                (SELECT product_name_id

                                                                                 FROM store_product_names 

                                                                                 WHERE product_name_id = store_products.product_id 

                                                                                 AND lang_id = {$langID}) 

                                                                                 THEN  store_product_names.lang_id = 2

                                                                                END

                                                                                AND store_specification_products.specification_id = '{$specification_id}'

                                                                                AND store_vendors.lang_id = '{$langID}'");



                // bad code(

                $child_array = array();

                $parent_array = array();

                $send_array = array();

                $i = 1;

                foreach ($products AS $key=>$value){

                    $prod_id = $value['product_id'];
                    foreach ($value AS $key1=>$value1){



                        $child_array['id'] = $i;

                        $child_array['value'] = $value1;

                        $child_array['alias'] = $key1;

                        array_push($parent_array,$child_array);





                        $i++;

                        $child_array = array();

                    }

                  // array_push($send_array,$parent_array);
                    $send_array[$prod_id] = $parent_array;

                    $parent_array = array();

                    $i = 1;

                }





                $answer['products'] = $send_array;

                return  $answer;

            }

    }

    public function getAllSpecifications($user_id,$lang_id,$archive,$filters,$sort,$page){



        $filter = "";

        $sorting = "";

        $filter_arr = json_decode(json_encode($filters), true);

        $sort_arr = json_decode(json_encode($sort), true);



    //    $filter_arr = [["name"=>"customer","value"=>"ABC","search_type"=>4],["name"=>"creation_date","value_min"=>"12.11.2019","value_max"=>"15.11.2019","search_type"=>7]];



        if(count($filter_arr) > 0) {

            $filter .= " WHERE";

            foreach ($filter_arr as $key => $value) {

                $search_type_id = $value['search_type'];



                reset($filter_arr);

                if ($key === key($filter_arr)){

                    switch ($search_type_id) {

                        case "4":

                            $filter_name = $value['name'];

                            $filter_value = $value['value'];

                            $filter .= "  {$filter_name} = {$filter_value} ";

                            break;

                        case "7":

                            $filter_name = $value['name'];

                            $value_min = $value['value_min'];

                            $value_max = $value['value_max'];

                            $filter .= " {$filter_name} BETWEEN to_date('{$value_min}','YYYY-MM-DD')  
                                                         AND to_date('{$value_max}','YYYY-MM-DD') ";

                            break;

                        default:

                            $filter_name = $value['name'];

                            $filter_value = $value['value'];

                            $filter .= "  {$filter_name}::text LIKE '%{$filter_value}%' ";

                    }

                }else{

                    switch ($search_type_id) {

                        case "4":

                            $filter_name = $value['name'];

                            $filter_value = $value['value'];

                            $filter .= " AND {$filter_name} = {$filter_value} ";

                            break;

                        case "7":
                            $filter_name = $value['name'];

                            $value_min = $value['value_min'];

                            $value_max = $value['value_max'];

                            $filter .= " AND  {$filter_name} BETWEEN to_date('{$value_min}','YYYY-MM-DD')  
                                                         AND to_date('{$value_max}','YYYY-MM-DD')  ";

                            break;

                        default:

                            $filter_name = $value['name'];

                            $filter_value = $value['value'];

                            $filter .= " AND {$filter_name}::text LIKE '%{$filter_value}%' ";

                    }

                }



            }

        }







        if(count($sort_arr) > 0){

            $sorting .= " ORDER BY ";

            foreach ($sort_arr as $sort_name => $sort_type) {

                end($sort_arr);

                if($sort_name === key($sort_arr)){

                    $sorting .= " {$sort_name} {$sort_type}";

                }else{

                    $sorting .= " {$sort_name} {$sort_type},";

                }

            }

        }



        if($archive == 0){

            $specification_list = $this->queryNoDML("SELECT specification_id AS id,name FROM store_specifications WHERE is_archived = 0");

        }else{

            $specification_list = $this->queryNoDML("SELECT specification_id AS id,name FROM store_specifications");

        }





        $spec_ids = array();
        $access = array();




        foreach($specification_list AS $key=>$value){

            $specification_id = $value['id'];

            if($this->hasAccessBySpecification($specification_id,$user_id,"view")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"edit")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"remove")){

                $access = array(1);

            }else if($this->hasAccessBySpecification($specification_id,$user_id,"share")){

                $access = array(1);

            }

            if(empty($access)){

            }else{
                array_push($spec_ids,$specification_id);
                $access = array();
            }


        }

        $offset = 0;
        if(!empty($page)){
            $offset = ($page-1) * 20;
        }

        $get_all_specification = $this->queryNoDML("SELECT visible_id,customer,creation_date,edit_date,name,is_archived,draft_id FROM ( SELECT store_specifications.specification_id AS draft_id,
                                                           store_specifications.visible_id AS visible_id,

                                                           store_specifications.customer AS customer,

                                                           store_specifications.creation_date  AS creation_date,

                                                           store_specifications.edit_date AS edit_date,

                                                           store_specifications.name AS name,

                                                           store_specifications.is_archived AS is_archived

                                                           FROM store_specifications WHERE  store_specifications.specification_id IN(" . implode(',',$spec_ids).") ) a {$filter} {$sorting} LIMIT 20 OFFSET {$offset}");


        // bad code(

        $child_array = array();

        $parent_array = array();

        $send_array = array();

        $i = 1;

        foreach ($get_all_specification AS $key=>$value){

            foreach ($value AS $key1=>$value1){



                $child_array['id'] = $i;

                $child_array['value'] = $value1;

                $child_array['alias'] = $key1;

                array_push($parent_array,$child_array);





                $i++;

                $child_array = array();

            }

            array_push($send_array,$parent_array);

            $parent_array = array();

            $i = 1;

        }

        return $send_array;

    }

    public function pagination($user_id,$lang_id,$archive,$filters,$sort,$page){
         $filter = "";

         $sorting = "";

         $filter_arr = json_decode(json_encode($filters), true);

         $sort_arr = json_decode(json_encode($sort), true);



         //    $filter_arr = [["name"=>"customer","value"=>"ABC","search_type"=>4],["name"=>"creation_date","value_min"=>"12.11.2019","value_max"=>"15.11.2019","search_type"=>7]];



         if(count($filter_arr) > 0) {

             $filter .= " WHERE";

             foreach ($filter_arr as $key => $value) {

                 $search_type_id = $value['search_type'];



                 reset($filter_arr);

                 if ($key === key($filter_arr)){

                     switch ($search_type_id) {

                         case "4":

                             $filter_name = $value['name'];

                             $filter_value = $value['value'];

                             $filter .= "  {$filter_name} = {$filter_value} ";

                             break;

                         case "7":

                             $filter_name = $value['name'];

                             $value_min = $value['value_min'];

                             $value_max = $value['value_max'];

                             $filter .= " {$filter_name} BETWEEN to_date('{$value_min}','YYYY-MM-DD')  
                                                         AND to_date('{$value_max}','YYYY-MM-DD') ";

                             break;

                         default:

                             $filter_name = $value['name'];

                             $filter_value = $value['value'];

                             $filter .= "  {$filter_name}::text LIKE '%{$filter_value}%' ";

                     }

                 }else{

                     switch ($search_type_id) {

                         case "4":

                             $filter_name = $value['name'];

                             $filter_value = $value['value'];

                             $filter .= " AND {$filter_name} = {$filter_value} ";

                             break;

                         case "7":
                             $filter_name = $value['name'];

                             $value_min = $value['value_min'];

                             $value_max = $value['value_max'];

                             $filter .= " AND  {$filter_name} BETWEEN to_date('{$value_min}','YYYY-MM-DD')  
                                                         AND to_date('{$value_max}','YYYY-MM-DD')  ";

                             break;

                         default:

                             $filter_name = $value['name'];

                             $filter_value = $value['value'];

                             $filter .= " AND {$filter_name}::text LIKE '%{$filter_value}%' ";

                     }

                 }



             }

         }







         if(count($sort_arr) > 0){

             $sorting .= " ORDER BY ";

             foreach ($sort_arr as $sort_name => $sort_type) {

                 end($sort_arr);

                 if($sort_name === key($sort_arr)){

                     $sorting .= " {$sort_name} {$sort_type}";

                 }else{

                     $sorting .= " {$sort_name} {$sort_type},";

                 }

             }

         }



         if($archive == 0){

             $specification_list = $this->queryNoDML("SELECT specification_id AS id,name FROM store_specifications WHERE is_archived = 0");

         }else{

             $specification_list = $this->queryNoDML("SELECT specification_id AS id,name FROM store_specifications");

         }





         $spec_ids = array();
         $access = array();




         foreach($specification_list AS $key=>$value){

             $specification_id = $value['id'];

             if($this->hasAccessBySpecification($specification_id,$user_id,"view")){

                 $access = array(1);

             }else if($this->hasAccessBySpecification($specification_id,$user_id,"edit")){

                 $access = array(1);

             }else if($this->hasAccessBySpecification($specification_id,$user_id,"remove")){

                 $access = array(1);

             }else if($this->hasAccessBySpecification($specification_id,$user_id,"share")){

                 $access = array(1);

             }

             if(empty($access)){

             }else{
                 array_push($spec_ids,$specification_id);
                 $access = array();
             }


         }




         $all_spec = $this->queryNoDML("SELECT visible_id,customer,creation_date,edit_date,name,is_archived,draft_id FROM ( SELECT store_specifications.specification_id AS draft_id,
                                                           store_specifications.visible_id AS visible_id,

                                                           store_specifications.customer AS customer,

                                                           store_specifications.creation_date  AS creation_date,

                                                           store_specifications.edit_date AS edit_date,

                                                           store_specifications.name AS name,

                                                           store_specifications.is_archived AS is_archived

                                                           FROM store_specifications WHERE  store_specifications.specification_id IN(" . implode(',',$spec_ids).") ) a {$filter} {$sorting} ");
         $draft_count = count($all_spec);
         if(ceil($draft_count / 20) > 0){
             $page_count = ceil(($draft_count) / 20);
         }else{
             $page_count = 0;
         }
         return ["page_count"=>$page_count, "page"=>$page, "count_all"=>$draft_count];
     }

    public function getSpecificationProperties($langID){



        //properties

        $properties = $this->queryNoDML("SELECT

                                       store_properties.property_id AS id,

                                       names.text AS name,

                                       search_types.search_type_id AS search_type_id,

                                       search_types.template AS search_template,

                                       store_properties.alias AS alias

                                       FROM store_properties_search_types

                                       LEFT JOIN store_properties ON store_properties.property_id = store_properties_search_types.property_id

                                       LEFT JOIN search_types ON search_types.search_type_id = store_properties_search_types.search_type_id

                                       LEFT JOIN names ON store_properties.name_id = names.name_id

                                       WHERE names.lang_id = {$langID} AND store_properties.content = 'specification_table'

                                       ORDER BY store_properties.property_id");



        // add value_list

        $i=0;

        foreach ($properties AS $key=>$value){

            if($value['search_type_id'] == 4){

                switch($value['alias']){

                    case "is_archived": $properties[$i]['value_list'] = [["value"=>0,"name"=>"not_archived"],["id"=>1,"name"=>"archived"]]; break;

                }

            }else{

                $properties[$i]['value_list'] = [];

            }

            $i++;

        }

        return  $properties;

    }

    protected function IdGenerator(){

        $pattern = "1234567890";

        $id = $pattern{rand(0,10)};

        for($i = 1; $i < 5; $i++)

        {

            $id .= $pattern{rand(0,10)};

        }



        return $id;

    }

    public function saveSpecification($user_id,$lang_id,$name,$customer,$products){




        $products_spec = json_decode(json_encode($products), true);



        // ADD SPECIFICATION

        $visible_id = $this->IdGenerator();

        $cur_date = $this->queryNoDML("SELECT to_char(CURRENT_TIMESTAMP , 'YYYY-MM-DD') AS cur_date")[0]['cur_date'];
        $save_specification = $this->queryDML("INSERT INTO store_specifications 

                                                      VALUES(nextval('store_specifications_specification_id_seq'::regclass),

                                                      '{$visible_id}','{$name}','{$cur_date}',NULL,0,'{$customer}')

                                                       RETURNING specification_id");





          // ADD SPECIFICATION-PRODUCTS

           if($save_specification){

             $specification_id = $save_specification['return_data'];

              foreach ($products_spec AS $key=>$value){

               $product_id = $value['product_id'];

               $quantity = $value['qty'];

               $save_specification_products = $this->queryDML("INSERT INTO store_specification_products

                                                      VALUES('{$specification_id}','{$product_id}','{$quantity}')");

               }

           }else{

               return FALSE;

           }



        // ADD FULL ACCESSES

        $person_contact_id = $this->getYourContactID($user_id);

        $full_access = $this->queryNoDML("SELECT store_specification_accesses_names.access_id AS access

                                              FROM store_specification_accesses_names

                                              WHERE store_specification_accesses_names.alias

                                              IN ('full_access')");



                 $access = $full_access[0]['access'];

           $add_full_access= $this->queryDML("INSERT INTO store_specification_access

                                    VALUES ({$specification_id}, {$person_contact_id}, {$access})");



             if($add_full_access){

                 return TRUE;

             }

             return FALSE;



         }

    public function EditSpecification($user_id,$lang_id,$name,$customer,$products,$specification_id){



        $access = array();

        $products_spec = json_decode(json_encode($products), true);



        if($this->hasAccessBySpecification($specification_id,$user_id,"edit")) {

            $access = array(1);

        }

//        }else if($this->hasAccessBySpecification($specification_id,$user_id,"remove")){

//            $access = array(1);

//        }





        if(empty($access)){

            return FALSE;

        }else{

            //UPDATE SPECIFICATION

            $update_spec = $this->queryDML("UPDATE store_specifications SET name = '{$name}',customer = '{$customer}',edit_date = CURRENT_TIMESTAMP WHERE specification_id = '{$specification_id}'");



            //EMPTY SPECIFICATION_PRODUCTS

            $delete_specification = $this->queryDML("DELETE FROM store_specification_products WHERE specification_id = '{$specification_id}'");

            //  $delete_specification = TRUE;

            // ADD SPECIFICATION-PRODUCTS

            if($delete_specification){

                foreach ($products_spec AS $key=>$value){

                    $product_id = $value['product_id'];

                    $quantity = $value['qty'];

                    $save_specification_products = $this->queryDML("INSERT INTO store_specification_products

                                                      VALUES('{$specification_id}','{$product_id}','{$quantity}')");

                }

                return TRUE;

            }else{

                return FALSE;

            }

        }



    }

    public function SpecificationToArchive($user_id,$specification_id,$archive){

            $to_archive = $this->queryDML("UPDATE store_specifications SET is_archived = {$archive},edit_date = CURRENT_TIMESTAMP WHERE specification_id = '{$specification_id}'");

            return TRUE;

    }

    public function SharingList($langID,$user_id){

        $contact_id = $this->getYourContactID($user_id);

        return $this->queryNoDML("SELECT contact_id, name FROM(SELECT DISTINCT

                                                            contacts.contact_id,

                                                            (CASE WHEN person_name.text IS NOT NULL THEN

                                                                               CONCAT(name.text,CONCAT('(',person_name.text,')'))

                                                                           ELSE

                                                                               name.text

                                                                           END) AS name

                                                        FROM contacts

                                                        LEFT JOIN contact_data ON  contact_data.contact_id = contacts.contact_id AND contact_data.param_id = 1 

                                                        LEFT JOIN (SELECT * FROM (SELECT  contact_values.value_id AS value_id,

                                                                                          contact_values.text AS text

                                                                                  FROM contact_data

                                                                                           LEFT JOIN contact_values ON contact_values.value_id = contact_data.value_id

                                                                                           LEFT JOIN languages      ON languages.lang_id       = {$langID} OR contact_values.lang_id = languages.lang_id

                                                                                  ORDER BY languages.priority) AS t

                                                                   GROUP BY t.value_id,t.text) AS name ON name.value_id = contact_data.value_id

                                                        LEFT JOIN contact_person ON contact_person.contact_id = contacts.contact_id

                                                        LEFT JOIN person_data    ON person_data.person_id = contact_person.person_id AND person_data.param_id = 33

                                                        LEFT JOIN (SELECT * FROM (SELECT  contact_values.value_id AS value_id,

                                                                                          contact_values.text AS text

                                                                                  FROM person_data

                                                                                           LEFT JOIN contact_values ON contact_values.value_id = person_data.value_id

                                                                                           LEFT JOIN languages      ON languages.lang_id       = {$langID} OR contact_values.lang_id = languages.lang_id

                                                                                  ORDER BY languages.priority) AS t

                                                                   GROUP BY t.value_id,t.text) AS person_name ON person_name.value_id = person_data.value_id

                                                        LEFT JOIN contact_types ON contact_types.contact_type_id = contacts.contact_type_id

                                                        WHERE contacts.contact_type_id = 6                                                        

                                                        ORDER BY contacts.contact_id

                                                      ) AS temp WHERE contact_id NOT IN ({$contact_id})");

    }

    public function addAccess($user_id,$specification_id,$contact_id,$access){



        $accesses = array();

        if($this->hasAccessBySpecification($specification_id,$user_id,"share")) {

            $accesses = array(1);

        }



        if(empty($accesses)){

            return FALSE;

        }else{

            $this->queryDML("DELETE FROM store_specification_access WHERE specification_id = {$specification_id} AND contact_id = {$contact_id}");

            $add_access = $this->queryDML("INSERT INTO store_specification_access

                                                   VALUES ({$specification_id}, {$contact_id}, {$access})");

            if($add_access){

                return TRUE;

            }else{

                return FALSE;

            }

        }



    }

    public function RemoveAccess($user_id,$specification_id,$contact_id,$access){



        $accesses = array();

        if($this->hasAccessBySpecification($specification_id,$user_id,"share")){

            $accesses = array(1);

        }



        if(empty($accesses)){

            return FALSE;

        }else{

            $delete_access = $this->queryDML("DELETE FROM store_specification_access 

                                                  WHERE specification_id = {$specification_id} 

                                                  AND contact_id = {$contact_id} 

                                                  AND access_id = {$access}");

            if($delete_access){

                return TRUE;

            }else{

                return FALSE;

            }

        }

    }

    public function HasAccessList($user_id,$specification_id,$langID){
        $contact_id = $this->getYourContactID($user_id);
        return $this->queryNoDML("SELECT contact_id, name,access,access_id FROM(SELECT DISTINCT
                                                            contacts.contact_id,
                                                            (CASE WHEN person_name.text IS NOT NULL THEN
                                                                               CONCAT(name.text,CONCAT('(',person_name.text,')'))
                                                                           ELSE
                                                                               name.text
                                                                           END) AS name,
                                                        names.text AS access,
                                                        store_specification_access.access_id AS access_id
                                                        FROM contacts
                                                        LEFT JOIN contact_data ON  contact_data.contact_id = contacts.contact_id AND contact_data.param_id = 1 
                                                        INNER JOIN store_specification_access ON store_specification_access.contact_id = contacts.contact_id
                                                        INNER JOIN store_specification_accesses_names ON store_specification_accesses_names.access_id = store_specification_access.access_id
                                                        INNER JOIN names ON names.name_id = store_specification_accesses_names.name_id
                                                        LEFT JOIN (SELECT * FROM (SELECT  contact_values.value_id AS value_id,
                                                                                          contact_values.text AS text
                                                                                  FROM contact_data
                                                                                           LEFT JOIN contact_values ON contact_values.value_id = contact_data.value_id
                                                                                           LEFT JOIN languages      ON languages.lang_id       = {$langID} OR contact_values.lang_id = languages.lang_id
                                                                                  ORDER BY languages.priority) AS t
                                                                   GROUP BY t.value_id,t.text) AS name ON name.value_id = contact_data.value_id
                                                        LEFT JOIN contact_person ON contact_person.contact_id = contacts.contact_id
                                                        LEFT JOIN person_data    ON person_data.person_id = contact_person.person_id AND person_data.param_id = 33
                                                        LEFT JOIN (SELECT * FROM (SELECT  contact_values.value_id AS value_id,
                                                                                          contact_values.text AS text
                                                                                  FROM person_data
                                                                                           LEFT JOIN contact_values ON contact_values.value_id = person_data.value_id
                                                                                           LEFT JOIN languages      ON languages.lang_id       = {$langID} OR contact_values.lang_id = languages.lang_id
                                                                                  ORDER BY languages.priority) AS t
                                                                   GROUP BY t.value_id,t.text) AS person_name ON person_name.value_id = person_data.value_id
                                                        LEFT JOIN contact_types ON contact_types.contact_type_id = contacts.contact_type_id
                                                        WHERE contacts.contact_type_id = 6
                                                         AND store_specification_access.specification_id = {$specification_id}                                                       
                                                        ORDER BY contacts.contact_id
                                                      ) AS temp WHERE contact_id NOT IN ({$contact_id})");

    }
}

//
//$products = array(["product_id"=> 1, "qty"=>20],[]);
//$obj = new Z_Specification();
//
//echo "<pre>";
//print_r($obj->getSpecificationInfo(1,68, 6));
//echo "</pre>";
//echo $obj->saveSpecification(1,2,'DRAFTS','Lanar Service LLC',3);

//echo "<pre>";

//print_r($obj->getAllSpecifications(6,1));

//echo "</pre>";

//echo "<pre>";

//print_r($obj->hasAccessBySpecification(1,5, "view"));

//echo "</pre>";
