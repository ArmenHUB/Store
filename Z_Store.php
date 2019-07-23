<?php



namespace store;



use \core\Z_Param;



require_once __DIR__."/../core/Z_Param.php";

class Z_Store extends Z_Param

{

    /* @params Object Z_Params */

    private $param;

    public $c1 = array();

    public $common_category_ids = array();

    public $pr_id = array();

    /**

     * @param $langID    integer current user lang id

     * @return array    [[category_id,parent_id,category_name,count_products],]

     */

    public function getStoreTree($langID){



        $category_list = $this->queryNoDML("SELECT   store_category_structure.category_id AS id,

                                                           store_category_structure.parent_id AS parent_id,

                                                           store_category_names.text AS name

                                                           FROM store_category_structure

                                                           INNER JOIN store_category_names ON store_category_names.category_name_id = store_category_structure.category_name_id

                                                           WHERE store_category_names.lang_id = {$langID}");


        $i=0;

        foreach ($category_list AS $key=>$value){

            $category_id = $value['id'];

            $count = $this->getCategoryCountProducts($category_id,$langID);

            $category_list[$i]['total'] = $count;

            $i++;

            $this->common_category_ids = array();

        }

        return $category_list;

    }



    /**

     * @param $category_id

     * @return array count products by category_id

     */

    public function getCategoryCountProducts($category_id,$langID){

        array_push($this->common_category_ids, $category_id);

        $this->getParentsCategory($category_id);

        $count_products = $this->queryNoDML("SELECT

                                                    count(store_category_products.product_id) AS count_products

                                                    FROM store_category_structure

                                                    LEFT JOIN store_category_products ON store_category_products.category_id = store_category_structure.category_id

                                                    WHERE store_category_structure.category_id IN(" . implode(',', $this->common_category_ids) . ")");

        return $count_products[0]['count_products'];

    }



    /**

     * @param $category_id

     */

    public function getParentsCategory($category_id)

    {

        $category_parents = $this->queryNoDML("SELECT store_category_structure.category_id AS category_id

                                                       FROM store_category_structure

                                                       WHERE store_category_structure.parent_id = {$category_id}");

        if ($category_parents) {

            foreach ($category_parents AS $key1 => $value1) {

                $cat_id = $value1['category_id'];

                array_push($this->common_category_ids, $cat_id);

                $this->getParentsCategory($cat_id);

            }





        }

    }





    /**

     * @param $langID

     * @param $category_id

     * @param $filters

     * @param $sort

     * @return array

     */

    public function getStoreProductsList($user_id,$langID, $category_id, $filters, $sort,$params_filters,$page)

    {
        $this->pr_id = array();

        $filter_arr = json_decode(json_encode($filters), true);

        $sort_arr = json_decode(json_encode($sort), true);

        $param_filters_arr = json_decode(json_encode($params_filters), true);





        //ok

        $filter = "";

        $cat_id = "";

        $sorting = "";

        $param_filter = "";

        $find_param = "";

        $param_counts = "";

        $param_counts_max = "";

        $as = "";



        if(count($filter_arr) > 0) {

//            foreach ($filter_arr as $filter_name => $filter_value) {

//               switch($filter_name){

//                   case "vendor": $filter .= " AND products.vendor_id = {$filter_value} "; break;

//                   case "part_number": $filter .= " AND products.part_number ILIKE '{$filter_value}%' "; break;

//                   case "name":  $filter .= " AND product_names.text ILIKE '{$filter_value}%' "; break;

//               }

//            }





            foreach ($filter_arr as $key => $value) {

                $filter_name = $value['name'];

                $filter_value = $value['value'];

                $search_type_id = $value['search_type'];

//               switch($search_type_id){

//                   case "1": $filter .= " AND {$filter_name} = {$filter_value} "; break;

//                   case "2": $filter .= " AND {$filter_name} ILIKE '{$filter_value}%' "; break;

//                   case "4": $filter .= " AND {$filter_name} = {$filter_value} "; break;

//              }

                switch($filter_name){

                    case "vendor": $filter .= " AND store_products.vendor_id = {$filter_value} "; break;

                    case "part_number": $filter .= " AND store_products.part_number ILIKE '%{$filter_value}%' "; break;

                    case "name":  $filter .= " AND store_product_names.text ILIKE '%{$filter_value}%' "; break;

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



       $num =1;

        if(count($param_filters_arr) > 0){

            $param_filter.= " WHERE ";

            $lastElement = end($param_filters_arr);

            foreach ($param_filters_arr as $key => $value) {

                end($param_filters_arr);

                $param_id = $value['param_id'];

                     $param_counts.= ",p{$num}";

                     $param_counts_max .= ", MAX(p{$num}) AS pr{$num}";

                     $as .= ",pr{$num}";

                $search_type_id = $value['search_type_id'];

                switch($search_type_id){

                    case "6":

                        $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN COALESCE(store_values.number::text) END AS p{$num}";



                        $param_value_min = $value['param_value_min'];

                        $param_value_max = $value['param_value_max'];

                        if($value == $lastElement){

                            $param_filter.= " pr{$num}::integer BETWEEN '{$param_value_min}' AND '{$param_value_max}' AND pr{$num} IS NOT NULL";

                        }else{

                            $param_filter.= " pr{$num}::integer BETWEEN '{$param_value_min}' AND '{$param_value_max}' AND pr{$num} IS NOT NULL AND";

                        }



                        break;

                    case "4":
                        $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN array_agg(COALESCE(store_values.text,store_values.number::text,store_values.timestamp::text,store_values.json,store_values.unit)) END AS p{$num}";

                        $param_value = $value['param_value'];

                        if($value == $lastElement){

                            $param_filter .= " '{$param_value}' = ANY(pr{$num}) ";

                        }else{

                            $param_filter .= " '{$param_value}' = ANY(pr{$num}) AND";

                        }

                        break;

                }

                $num++;

            }

        }



             $offset = 0;

             if(!empty($page)){

                 $offset = ($page-1) * 20;

             }



        if ($category_id !== 0) {

            array_push($this->common_category_ids, $category_id);

            $this->getParentsCategory($category_id);

            $cat_id .= " AND store_category_products.category_id IN(" . implode(',',$this->common_category_ids).") ";

        }



        //return $param_counts."/".$param_counts_max."/".$as;



        $products_list = $this->queryNoDML("SELECT count(*) OVER() AS count,json_agg(product_id::integer) OVER() As ids,vendor,part_number,name,product_id FROM

                                                         (SELECT vendor,part_number,name,product_id {$param_counts_max} FROM

                                                         (SELECT vendor,part_number,name,product_id {$param_counts} FROM

                                                         (SELECT store_vendors.text AS vendor,

                                                          store_products.part_number AS part_number,

                                                          store_product_names.text AS name,

                                                          store_product_data.product_id AS product_id

                                                          {$find_param}

                                                         FROM store_products

                                                         LEFT JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id

                                                         LEFT JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id

                                                         LEFT JOIN store_category_products ON store_category_products.product_id = store_products.product_id

                                                         LEFT JOIN store_category_structure ON store_category_structure.category_id = store_category_products.category_id

                                                         LEFT JOIN store_product_data ON store_product_data.product_id = store_products.product_id

                                                         LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                         WHERE

CASE

 WHEN  store_product_names.lang_id  ={$langID} THEN  store_product_names.lang_id = {$langID}

WHEN store_product_names.lang_id = 2 AND NOT EXISTS (SELECT product_name_id FROM store_product_names WHERE product_name_id = store_product_data.product_id AND lang_id = {$langID}) THEN  store_product_names.lang_id = 2

END



                                                         AND store_vendors.lang_id = {$langID}

                                                         {$cat_id}

                                                         {$filter}

                                                         GROUP BY

                                                         store_vendors.text,

                                                         store_products.part_number,

                                                         store_product_names.text,

                                                         store_product_data.product_id,

                                                         store_product_data.param_id,

                                                         store_values.number

                                                         ORDER BY store_product_data.product_id ) a

                                                         GROUP BY vendor,part_number,name,product_id {$param_counts}) b

                                                         GROUP BY vendor,part_number,name,product_id

                                                         ORDER BY product_id ) c {$param_filter} {$sorting} LIMIT 20 OFFSET {$offset}");



        // bad code(

        $child_array = array();

        $parent_array = array();

        $send_array = array();

        $i = 1;

        if($products_list){
            foreach ($products_list AS $key=>$value){
                $count = $value['count'];
                foreach ($value AS $key1=>$value1){
                    if($i > 2){
                        $child_array['id'] = $i;

                        $child_array['value'] = $value1;

                        $child_array['alias'] = $key1;

                        array_push($parent_array,$child_array);
                    }

                    $i++;

                    $child_array = array();

                }

                array_push($send_array,$parent_array);

                $parent_array = array();

                $i = 1;

            }
        }




      //  return $send_array;

//        $products_ids = $this->queryNoDML("SELECT product_id FROM
//
//                                                         (SELECT product_id {$param_counts_max} FROM
//
//                                                         (SELECT product_id {$param_counts} FROM
//
//                                                         (SELECT
//
//                                                          store_product_data.product_id AS product_id
//
//                                                          {$find_param}
//
//                                                         FROM store_products
//
//                                                         LEFT JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id
//
//                                                         LEFT JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id
//
//                                                         LEFT JOIN store_category_products ON store_category_products.product_id = store_products.product_id
//
//                                                         LEFT JOIN store_category_structure ON store_category_structure.category_id = store_category_products.category_id
//
//                                                         LEFT JOIN store_product_data ON store_product_data.product_id = store_products.product_id
//
//                                                         LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
//
//                                                         WHERE
//
//CASE
//
// WHEN  store_product_names.lang_id  ={$langID} THEN  store_product_names.lang_id = {$langID}
//
//WHEN store_product_names.lang_id = 2 AND NOT EXISTS (SELECT product_name_id FROM store_product_names WHERE product_name_id = store_product_data.product_id AND lang_id = {$langID}) THEN  store_product_names.lang_id = 2
//
//END
//
//
//
//                                                         AND store_vendors.lang_id = {$langID}
//
//                                                         {$cat_id}
//
//                                                         {$filter}
//
//                                                         GROUP BY
//
//                                                         store_vendors.text,
//
//                                                         store_product_names.text,
//
//                                                         store_product_data.product_id,
//
//                                                         store_product_data.param_id,
//
//                                                         store_values.number
//
//                                                         ORDER BY store_product_data.product_id ) a
//
//                                                         GROUP BY product_id {$param_counts}) b
//
//                                                         GROUP BY product_id
//
//                                                         ORDER BY product_id ) c {$param_filter}");






        if($products_list){
//            foreach($products_ids AS $key=>$product){
//                $product_id = intval($product['product_id']);
//                array_push($this->pr_id,$product_id);
//            }


            $json = $products_list[0]['ids'];
            $array_json = json_decode($json);

            for ($i1 = 0; $i1 < count($array_json);$i1++){
                array_push($this->pr_id,intval($array_json[$i1]));
            }

        }




        $product_count = $count;


        $page_count = 0;

        if(ceil($product_count / 20) > 0){

            $page_count = ceil(($product_count) / 20);

        }else{

            $page_count = 0;

        }

        $settings = $this->getUserSettings($user_id);
        $thead = $this->getStoreProperties($langID);

     return  [

            "title" => 'Products',

            "settings" => $settings,

            "thead" => $thead,

            "tbody" =>$send_array,

            "pagination" => ["page_count"=>$page_count, "page"=>$page, "count_all"=>$product_count]

        ];

    }

    /**

     * @param $langID

     * @return array EXP ['id'=>'1', 'name'=> 'vendor', 'search_type_id'=> '4', 'search_template'=> '<select></select>', 'value_list'=> ['value'=> '1', 'name'=> 'ITK']];

     */

    public function getStoreProperties($langID){



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

                                       WHERE names.lang_id = {$langID} AND store_properties.content = 'table'

                                       ORDER BY store_properties.property_id");



        // add value_list

        $search_type_id = $this->queryNoDML("SELECT search_types.search_type_id FROM search_types WHERE template = '<select></select>'")[0]['search_type_id'];

        $i=0;

        foreach ($properties AS $key=>$value){

            if($value['search_type_id'] == $search_type_id){

                switch($value['alias']){

                    case "vendor": $properties[$i]['value_list'] = $this->getTableSearchListVendors($langID); break;

                }

            }else{

                $properties[$i]['value_list'] = [];

            }

            $i++;

        }

        return  $properties;

    }



    /**

     * @param $langID

     * @return array $vendors_list; EXP. array ('id'=>'1', 'name'=>'Molex');

     */

    public function getTableSearchListVendors($langID)

    {

        $vendors_list = $this->queryNoDML("SELECT store_vendors.vendor_id AS value,

                                                         store_vendors.text AS name

                                                         FROM store_vendors

                                                         WHERE store_vendors.lang_id = {$langID}

                                                  ");

        return $vendors_list;

    }



    /**

     * @param $user_id

     * @return array $user_settings

     */

    public function getUserSettings($user_id){



        $user_settings = $this->queryNoDML("SELECT 
                                                         user_settings.settings AS settings
                                                         FROM user_settings
                                                         WHERE user_settings.user_id = {$user_id} AND alias = 'store'
                                                  ");

        $myJSON = json_encode($user_settings);

        return $myJSON;

    }



    /**

     * @param $user_id

     * @param $settings

     * @return bool

     */

    public function UpdateSettings($user_id,$settings){

        $json_settings = json_encode($settings);

        $update_settings = $this->queryNoDML(" ");

        if($update_settings){

            return TRUE;

        }

    }


    public function getStoreParametersList($langID,$category_id,$params_filters){



        $param_filters_arr = json_decode(json_encode($params_filters), true);





        $var_ids = "";

        $param_values_array = array();

        $param_values_array1 = array();


        $sel_value = "";

        if($category_id !== 0){

            $this->getParentsCategory($category_id);

            $var_ids .= " AND store_category_products.category_id IN(" . implode(',', $this->common_category_ids) . ") ";

        }


       $parameter_list = $this->getProductParam($langID,$category_id);


        if(empty($this->pr_id)){
            $pr_id = "";
        }else{
            $pr_id = "  AND store_product_data.product_id IN(" . implode(',', $this->pr_id) . ")";
        }


        foreach ($param_filters_arr AS $k=>$f){

            array_push($param_values_array,$f['param_id']);

        }

        $parameters_list = array();

        foreach ($parameter_list AS $key=>$value){

             $pm_id = $value['param_id'];

            if(in_array($pm_id, $param_values_array)) {

                $selected_value = " COALESCE(store_values.text,

                                    store_values.number::text,

                                    store_values.timestamp::text,

                                    store_values.json,

                                     store_values.unit) AS param_value,";

                foreach ($param_filters_arr AS $l=>$l1){

                    $id = $l1['param_id'];

                    if($id == $pm_id){

                        if($l1['search_type_id'] !== 6){

                            $val = $l1['param_value'];

                            $sel_value .= " AND COALESCE(store_values.text,

                                    store_values.number::text,

                                    store_values.timestamp::text,

                                    store_values.json,

                                     store_values.unit) = '{$val}'";

                        }


                    }

                }

            }else{

                $selected_value = "";

            }

            $p_list = $this->queryNoDML("SELECT distinct on (store_params.param_id)

                                                     store_params.param_id AS id,

                                                     param_names.text AS param_name,

                                                     {$selected_value}

                                                     store_params.alias AS data_alias,

                                                     search_types.template AS template,

                                                     store_product_data.product_id AS product_id,

                                                     store_product_data.value_id AS value_id,

                                                     store_params.search_type_id AS search_type_id

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id 

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN search_types ON store_params.search_type_id = search_types.search_type_id

                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_values.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$pm_id}

                                                      {$var_ids}

                                                     {$pr_id}

                                                     {$sel_value}

                                                     GROUP BY store_params.param_id,

                                                     param_names.text,

                                                     search_types.template,

                                                     store_product_data.product_id,

                                                     store_product_data.value_id,

                                                     store_params.search_type_id,

                                                     store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit

                                                     ORDER BY store_params.param_id");


           $sel_value = "";

            array_push($parameters_list,$p_list[0]);



        }


        foreach ($parameters_list AS $key=>$value){

           $param_id = $value['id'];

           $search_type_id = $value['search_type_id'];

           if($search_type_id == '6'){

               foreach ($param_filters_arr AS $n=>$v){

                   array_push($param_values_array1,$v['param_id']);

               }



               $values = array();



               if(in_array($param_id, $param_values_array1)){

                  $prr_id = $this->filterNotLast($langID,$category_id,$params_filters,$param_id);

                     // return $prr_id;

                   $min_value = $this->queryNoDML("SELECT distinct(min_value) min_value FROM (SELECT 

                                                     COALESCE(

                                                     store_values.number::integer

                                                      ) AS min_value

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$param_id}

                                                     {$var_ids}

                                                    {$prr_id}

                                                     AND store_values.lang_id = {$langID}

                                                     GROUP BY  store_values.number::integer) AS a ORDER BY min_value");



               }else{

                   $min_value = $this->queryNoDML("SELECT distinct(min_value) min_value FROM (SELECT 

                                                     COALESCE(

                                                     store_values.number::integer

                                                      ) AS min_value

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$param_id}

                                                     {$var_ids}

                                                    {$pr_id}

                                                     AND store_values.lang_id = {$langID}

                                                     GROUP BY  store_values.number::integer) AS a ORDER BY min_value");

               }





               foreach ($min_value AS $key1=>$value1){

                   $number = $value1['min_value'];
                   array_push($values,$number);
               }



               $param_value_min=0;

               $param_value_max=0;

               if(count($param_filters_arr) > 0){

                   foreach ($param_filters_arr AS $n1=>$v1){

                       if($v1['search_type_id'] == 6 && $v1['param_id'] == $param_id){

                           $param_value_min = $v1['param_value_min'];

                           $param_value_max = $v1['param_value_max'];



                       }

                   }

               }



               if($param_value_min == 0 && $param_value_max == 0){

                   $param_value_min = min($values);

                   $param_value_max = max($values);

               }


               $parameters_list[$key]['list'] = ['min'=>min($values), 'max' =>max($values), 'value_list'=> $values, 'selected_min'=> $param_value_min, 'selected_max'=>$param_value_max];

           }else{

               foreach ($param_filters_arr AS $n=>$v){

                     array_push($param_values_array1,$v['param_id']);

               }

               if(in_array($param_id, $param_values_array1)) {

                $prr_id = $this->filterNotLast($langID,$category_id,$params_filters,$param_id);



                   $parameters_list[$key]['list'] =  $this->queryNoDML("SELECT * FROM (SELECT 



                                                     COALESCE(store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit) AS param_value

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_values.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$param_id}

                                                      {$var_ids}

                                                     {$prr_id}

                                                     GROUP BY store_params.param_id,store_params.param_id,store_product_data.product_id,store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit) a WHERE param_value IS NOT NULL GROUP BY param_value

                                                     ");





               }else{

                   $parameters_list[$key]['list'] =  $this->queryNoDML("SELECT * FROM (SELECT 



                                                     COALESCE(store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit) AS param_value

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_values.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$param_id}

                                                      {$var_ids}

                                                      {$pr_id}

                                                     GROUP BY store_params.param_id,store_params.param_id,store_product_data.product_id,store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit) a WHERE param_value IS NOT NULL GROUP BY param_value

                                                     ");


               }


           }
        }

        return  $parameters_list;

    }

    public function filterNotLast($langID,$category_id,$params_filters,$param_id_current){



        $param_filters_arr = json_decode(json_encode($params_filters), true);





        $var_ids = "";

        $param_filter = "";

        $find_param = "";

        $param_counts = "";

        $param_counts_max = "";

        $as = "";

        $products_id = array();

        $pr_id = "";

        if($category_id !== 0){

            $this->getParentsCategory($category_id);

            $var_ids .= " AND store_category_products.category_id IN(" . implode(',', $this->common_category_ids) . ") ";

        }



        $num =1;

        if(count($param_filters_arr) > 0){

            foreach ($param_filters_arr as $key1 => $value1){

                $param_id = $value1['param_id'];

                if($param_id == $param_id_current){

                    unset($param_filters_arr[$key1]);

                }

            }



            $param_filter.= " WHERE ";

            $lastElement = end($param_filters_arr);

            foreach ($param_filters_arr as $key => $value) {

                end($param_filters_arr);

                $param_id = $value['param_id'];

                $param_counts.= ",p{$num}";

                $param_counts_max .= ", MAX(p{$num}) AS pr{$num}";

                $as .= ",pr{$num}";

                $search_type_id = $value['search_type_id'];

                switch($search_type_id){

                    case "6":

                        $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN COALESCE(store_values.number::text) END AS p{$num}";



                        $param_value_min = $value['param_value_min'];

                        $param_value_max = $value['param_value_max'];

                        if($value == $lastElement){

                            $param_filter.= " pr{$num}::integer BETWEEN '{$param_value_min}' AND '{$param_value_max}' AND pr{$num} IS NOT NULL";

                        }else{

                            $param_filter.= " pr{$num}::integer BETWEEN '{$param_value_min}' AND '{$param_value_max}' AND pr{$num} IS NOT NULL AND";

                        }



                        break;

                    case "4":

                        $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN array_agg(COALESCE(store_values.text,store_values.number::text,store_values.timestamp::text,store_values.json,store_values.unit)) END AS p{$num}";



                        $param_value = $value['param_value'];

                        if($value == $lastElement){

                            $param_filter .= " '{$param_value}' = ANY(pr{$num}) ";

                        }else{

                            $param_filter .= " '{$param_value}' = ANY(pr{$num}) AND";

                        }

                        break;

                }

                $num++;

            }

        }





        $category_products  = $this->queryNoDML("SELECT product_id FROM 

                                                         (SELECT product_id {$param_counts_max} FROM

                                                         (SELECT product_id {$param_counts} FROM 

                                                         (SELECT 

                                                          store_product_data.product_id AS product_id

                                                          {$find_param}

                                                         FROM store_products

                                                         LEFT JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id

                                                         LEFT JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id

                                                         LEFT JOIN store_category_products ON store_category_products.product_id = store_products.product_id

                                                         LEFT JOIN store_category_structure ON store_category_structure.category_id = store_category_products.category_id

                                                         LEFT JOIN store_product_data ON store_product_data.product_id = store_products.product_id

                                                         LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                         WHERE
CASE 

 WHEN  store_product_names.lang_id  ={$langID} THEN  store_product_names.lang_id = {$langID}

WHEN store_product_names.lang_id = 2 AND NOT EXISTS (SELECT product_name_id FROM store_product_names WHERE product_name_id = store_product_data.product_id AND lang_id = {$langID}) THEN  store_product_names.lang_id = 2

END

                                                         AND store_values.lang_id = {$langID}

                                                         AND store_vendors.lang_id = {$langID}


                                                         {$var_ids}

                                                         GROUP BY

                                                         store_vendors.text,

                                                         store_products.part_number,

                                                         store_product_names.text,

                                                         store_product_data.product_id,

                                                         store_product_data.param_id,

                                                         store_values.number

                                                         ORDER BY store_product_data.product_id ) a 

                                                         GROUP BY product_id {$param_counts}) b  

                                                         GROUP BY product_id 

                                                         ORDER BY product_id ) c {$param_filter}");



        if($category_products){
            foreach($category_products AS $key=>$product){

                $product_id = $product['product_id'];

                array_push($products_id,$product_id);

            }
            $pr_id .= "  AND store_product_data.product_id IN(" . implode(',', $products_id) . ") ";

        }

        return $pr_id;

    }

    public function getProductParam($langID,$category_id){

        $find_param = "";

        $var_ids = "";

   //     $var_ids1 = "";

        $param = "";

        $j = array();

        $j1 = array();



        if($category_id !== 0){

            $this->getParentsCategory($category_id);

            $var_ids .= "WHERE store_category_products.category_id IN(" . implode(',', $this->common_category_ids) . ") ";

            $all_params = $this->queryNoDML("SELECT store_params.param_id AS param_id

                                                FROM store_params

                                                INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                {$var_ids}

                                                GROUP BY store_params.param_id ORDER BY store_params.param_id");

//            foreach ($all_params AS $key=>$value){
//                $param_id = $value['param_id'];
//                array_push($j1,$param_id);
//            }
//            return  $j1;

            return  $all_params;

        }else{
            $all_params = $this->queryNoDML("SELECT store_params.param_id AS param_id

                                                FROM store_params

                                                INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                {$var_ids}

                                                GROUP BY store_params.param_id ORDER BY store_params.param_id");
        }

        $num = 1;

        if($all_params){

            foreach ($all_params AS $key=>$value){
                $param_id = $value['param_id'];
                $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN COALESCE(store_values.text,store_values.number::text,store_values.timestamp::text,store_values.json,store_values.unit) END AS p{$param_id}";
                $param .= ",count(p{$param_id})*100/(SELECT count(store_products.product_id) FROM store_products) AS p{$param_id}";
            }

            $g  = $this->queryNoDML("SELECT count(*) {$param} FROM (SELECT

                                                  store_product_data.product_id AS product_id

                                                  {$find_param}

                                                         FROM store_products

                                                         LEFT JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id

                                                         LEFT JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id

                                                         LEFT JOIN store_category_products ON store_category_products.product_id = store_products.product_id

                                                         LEFT JOIN store_category_structure ON store_category_structure.category_id = store_category_products.category_id

                                                         LEFT JOIN store_product_data ON store_product_data.product_id = store_products.product_id

                                                         LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                         WHERE

CASE

 WHEN  store_product_names.lang_id  ={$langID} THEN  store_product_names.lang_id = {$langID}

WHEN store_product_names.lang_id = 2 AND NOT EXISTS (SELECT product_name_id FROM store_product_names WHERE product_name_id = store_product_data.product_id AND lang_id = {$langID}) THEN  store_product_names.lang_id = 2

END



                                                         AND store_vendors.lang_id = {$langID}

                                                  GROUP BY

                                                  store_product_data.product_id,

                                                  store_product_data.param_id,

                                                  store_values.text,

                                                  store_values.number::text,

                                                  store_values.timestamp::text,

                                                  store_values.json,

                                                  store_values.unit,

                                                  store_vendors.text,

                                                  store_products.part_number,

                                                  store_product_names.text

                                                  ORDER BY store_product_data.product_id) a ");


            foreach ($g AS $key=>$procent){
                foreach ($procent AS $key=>$value){
                    if($key !== 'count'){
                        $param_id = ltrim($key, 'p');
                        if($value > 60 && $category_id == 0){
                            $j['param_id'] = $param_id;
                            $j1[$num] = $j;
                           // array_push($j1,$param_id);
                        }else if($category_id !== 0){
                            $j['param_id'] = $param_id;
                            $j1[$num] = $j;
                        }
                    }
                    $num++;
                }
            }


        }

       return $j1;

    }

    public function getProductParam1($langID,$category_id){

        $find_param = "";

        $var_ids = "";

        //     $var_ids1 = "";

        $param = "";

        $j = array();

        $j1 = array();



        if($category_id !== 0){

            $this->getParentsCategory($category_id);

            $var_ids .= "WHERE store_category_products.category_id IN(" . implode(',', $this->common_category_ids) . ") ";

            $all_params = $this->queryNoDML("SELECT store_params.param_id AS param_id
                                                FROM store_params
                                                INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id
                                                {$var_ids}
                                                GROUP BY store_params.param_id ORDER BY store_params.param_id");
            $i=0;
            foreach ($all_params AS $key=>$value){
                $param_id = $value['param_id'];
                if($i<10){
                    array_push($j1,$param_id);
                }
               $i++;
            }
            return  $j1;



        }else{
            $all_params = $this->queryNoDML("SELECT store_params.param_id AS param_id

                                                FROM store_params

                                                INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id

                                                {$var_ids}

                                                GROUP BY store_params.param_id ORDER BY store_params.param_id");
        }

        $num = 1;

        if($all_params){

            foreach ($all_params AS $key=>$value){
                $param_id = $value['param_id'];
                $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN COALESCE(store_values.text,store_values.number::text,store_values.timestamp::text,store_values.json,store_values.unit) END AS p{$param_id}";
                $param .= ",count(p{$param_id})*100/(SELECT count(store_products.product_id) FROM store_products) AS p{$param_id}";
            }

            $g  = $this->queryNoDML("SELECT count(*) {$param} FROM (SELECT

                                                  store_product_data.product_id AS product_id

                                                  {$find_param}

                                                         FROM store_products

                                                         LEFT JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id

                                                         LEFT JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id

                                                         LEFT JOIN store_category_products ON store_category_products.product_id = store_products.product_id

                                                         LEFT JOIN store_category_structure ON store_category_structure.category_id = store_category_products.category_id

                                                         LEFT JOIN store_product_data ON store_product_data.product_id = store_products.product_id

                                                         LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                         WHERE

CASE

 WHEN  store_product_names.lang_id  ={$langID} THEN  store_product_names.lang_id = {$langID}

WHEN store_product_names.lang_id = 2 AND NOT EXISTS (SELECT product_name_id FROM store_product_names WHERE product_name_id = store_product_data.product_id AND lang_id = {$langID}) THEN  store_product_names.lang_id = 2

END



                                                         AND store_vendors.lang_id = {$langID}

                                                  GROUP BY

                                                  store_product_data.product_id,

                                                  store_product_data.param_id,

                                                  store_values.text,

                                                  store_values.number::text,

                                                  store_values.timestamp::text,

                                                  store_values.json,

                                                  store_values.unit,

                                                  store_vendors.text,

                                                  store_products.part_number,

                                                  store_product_names.text

                                                  ORDER BY store_product_data.product_id) a ");


            foreach ($g AS $key=>$procent){
                foreach ($procent AS $key=>$value){
                    if($key !== 'count'){
                        $param_id = ltrim($key, 'p');
                        if($value > 60 && $category_id == 0){
//                            $j['param_id'] = $param_id;
//                            $j1[$num] = $j;
                             array_push($j1,$param_id);
                        }else if($category_id !== 0){
                            $j['param_id'] = $param_id;
                            $j1[$num] = $j;
                        }
                    }
                    $num++;
                }
            }


        }

        return $j1;

    }

    public function getProductIds($langID, $category_id,$params_filters)

    {

        $param_filters_arr = json_decode(json_encode($params_filters), true);

        $cat_id = "";

        $param_filter = "";

        $find_param = "";

        $param_counts = "";

        $param_counts_max = "";

        $as = "";

        $pr_id = "";

        $num =1;

        if(count($param_filters_arr) > 0){

            $param_filter.= " WHERE ";

            $lastElement = end($param_filters_arr);

            foreach ($param_filters_arr as $key => $value) {

                end($param_filters_arr);

                $param_id = $value['param_id'];

                $param_counts.= ",p{$num}";

                $param_counts_max .= ", MAX(p{$num}) AS pr{$num}";

                $as .= ",pr{$num}";

                $search_type_id = $value['search_type_id'];

                switch($search_type_id){

                    case "6":

                        $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN COALESCE(store_values.number::text) END AS p{$num}";



                        $param_value_min = $value['param_value_min'];

                        $param_value_max = $value['param_value_max'];

                        if($value == $lastElement){

                            $param_filter.= " pr{$num}::integer BETWEEN '{$param_value_min}' AND '{$param_value_max}' AND pr{$num} IS NOT NULL";

                        }else{

                            $param_filter.= " pr{$num}::integer BETWEEN '{$param_value_min}' AND '{$param_value_max}' AND pr{$num} IS NOT NULL AND";

                        }



                        break;

                    case "4":
                        $find_param .=  ", CASE WHEN store_product_data.param_id = {$param_id} THEN array_agg(COALESCE(store_values.text,store_values.number::text,store_values.timestamp::text,store_values.json,store_values.unit)) END AS p{$num}";

                        $param_value = $value['param_value'];

                        if($value == $lastElement){

                            $param_filter .= " '{$param_value}' = ANY(pr{$num}) ";

                        }else{

                            $param_filter .= " '{$param_value}' = ANY(pr{$num}) AND";

                        }

                        break;

                }

                $num++;

            }

        }




        if ($category_id !== 0) {

            array_push($this->common_category_ids, $category_id);

            $this->getParentsCategory($category_id);

            $cat_id .= " AND store_category_products.category_id IN(" . implode(',',$this->common_category_ids).") ";

        }




        $products_list = $this->queryNoDML("SELECT json_agg(product_id::integer) OVER() As ids FROM

                                                         (SELECT product_id {$param_counts_max} FROM
                                                         (SELECT product_id {$param_counts} FROM
                                                         (SELECT 
                                                          store_product_data.product_id AS product_id
                                                          {$find_param}
                                                         FROM store_products

                                                         LEFT JOIN store_vendors ON store_vendors.vendor_id = store_products.vendor_id

                                                         LEFT JOIN store_product_names ON store_product_names.product_name_id = store_products.product_name_id

                                                         LEFT JOIN store_category_products ON store_category_products.product_id = store_products.product_id

                                                         LEFT JOIN store_category_structure ON store_category_structure.category_id = store_category_products.category_id

                                                         LEFT JOIN store_product_data ON store_product_data.product_id = store_products.product_id

                                                         LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                         WHERE

CASE

 WHEN  store_product_names.lang_id  ={$langID} THEN  store_product_names.lang_id = {$langID}

WHEN store_product_names.lang_id = 2 AND NOT EXISTS (SELECT product_name_id FROM store_product_names WHERE product_name_id = store_product_data.product_id AND lang_id = {$langID}) THEN  store_product_names.lang_id = 2

END



                                                         AND store_vendors.lang_id = {$langID}

                                                         {$cat_id}

                                                         GROUP BY
                                                         store_product_data.product_id,

                                                         store_product_data.param_id,

                                                         store_values.number

                                                         ORDER BY store_product_data.product_id ) a

                                                         GROUP BY product_id {$param_counts}) b

                                                         GROUP BY product_id

                                                         ORDER BY product_id ) c {$param_filter} LIMIT 1");



        if($products_list){
            $json = $products_list[0]['ids'];
            $array_json = json_decode($json);
            $pr_id = "  AND store_product_data.product_id IN(" . implode(',', $array_json) . ")";
        }

          return $pr_id;

    }

    public function parameterList($langID,$category_id,$params_filters){
        $param_filters_arr = json_decode(json_encode($params_filters), true);

        $var_ids = "";
        if($category_id !== 0){
            $this->getParentsCategory($category_id);
            $var_ids .= " AND store_category_products.category_id IN(" . implode(',', $this->common_category_ids) . ") ";
        }

        if(count($param_filters_arr) > 0) {
            $pr_id = $this->getProductIds($langID, $category_id,$params_filters);
            $p_list = $this->queryNoDML("SELECT distinct on (store_params.param_id)
                                                     store_params.param_id AS id,
                                                     param_names.text AS param_name,
                                                     store_params.alias AS data_alias,
                                                     search_types.template AS template,
                                                     store_product_data.product_id AS product_id,
                                                     store_product_data.value_id AS value_id,
                                                     store_params.search_type_id AS search_type_id
                                                     FROM store_params
                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id 
                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                     INNER JOIN search_types ON store_params.search_type_id = search_types.search_type_id
                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id
                                                     WHERE
                                                     param_names.lang_id = {$langID}
                                                     AND store_values.lang_id = {$langID}
                                                     {$var_ids}
                                                     {$pr_id}
                                                     GROUP BY store_params.param_id,
                                                     param_names.text,
                                                     search_types.template,
                                                     store_product_data.product_id,
                                                     store_product_data.value_id,
                                                     store_params.search_type_id,
                                                     store_values.text,
                                                     store_values.number::text,
                                                     store_values.timestamp::text,
                                                     store_values.json,
                                                     store_values.unit
                                                     ORDER BY store_params.param_id");
            return $p_list;
        }else{
            $parameter_list = $this->getProductParam1($langID,$category_id);

            $p_list = $this->queryNoDML("SELECT distinct on (store_params.param_id)
                                                     store_params.param_id AS id,
                                                     param_names.text AS param_name,
                                                     store_params.alias AS data_alias,
                                                     search_types.template AS template,
                                                     store_product_data.product_id AS product_id,
                                                     store_product_data.value_id AS value_id,
                                                     store_params.search_type_id AS search_type_id
                                                     FROM store_params
                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id 
                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                     INNER JOIN search_types ON store_params.search_type_id = search_types.search_type_id
                                                     WHERE
                                                     param_names.lang_id = {$langID}
                                                     AND store_values.lang_id = {$langID}
                                                     AND store_product_data.param_id IN(" . implode(',', $parameter_list) . ")
                                                     GROUP BY store_params.param_id,
                                                     param_names.text,
                                                     search_types.template,
                                                     store_product_data.product_id,
                                                     store_product_data.value_id,
                                                     store_params.search_type_id,
                                                     store_values.text,
                                                     store_values.number::text,
                                                     store_values.timestamp::text,
                                                     store_values.json,
                                                     store_values.unit
                                                     ORDER BY store_params.param_id");

            return $p_list;
        }

    }

    public function parameterValueList($langID,$category_id,$params_filters,$param_id_current,$search_type_id){

        $param_filters_arr = json_decode(json_encode($params_filters), true);


        $var_ids = "";
        if($category_id !== 0){
            $this->getParentsCategory($category_id);
            $var_ids .= " AND store_category_products.category_id IN(" . implode(',', $this->common_category_ids) . ") ";
        }

        if(count($param_filters_arr) > 0) {
            $prr_id = $this->filterNotLast($langID,$category_id,$params_filters,$param_id_current);

            $p_list = $this->queryNoDML("SELECT distinct on (store_params.param_id)

                                                     store_params.param_id AS id,

                                                     param_names.text AS param_name,

                                                     store_params.alias AS data_alias,

                                                     search_types.template AS template,

                                                     store_product_data.product_id AS product_id,

                                                     store_product_data.value_id AS value_id,

                                                     store_params.search_type_id AS search_type_id

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN search_types ON store_params.search_type_id = search_types.search_type_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_values.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$param_id_current}

                                                     GROUP BY store_params.param_id,

                                                     param_names.text,

                                                     search_types.template,

                                                     store_product_data.product_id,

                                                     store_product_data.value_id,

                                                     store_params.search_type_id,

                                                     store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit

                                                     ORDER BY store_params.param_id");


                if($search_type_id == 4){
                    $value_list =  $this->queryNoDML("SELECT * FROM (SELECT
                                                     COALESCE(store_values.text,
                                                     store_values.number::text,
                                                     store_values.timestamp::text,
                                                     store_values.json,
                                                     store_values.unit) AS param_value
                                                     FROM store_params
                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id
                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                     WHERE
                                                     param_names.lang_id = {$langID}
                                                     AND store_values.lang_id = {$langID}
                                                     AND store_product_data.param_id = {$param_id_current}
                                                     {$prr_id}
                                                     GROUP BY store_params.param_id,store_params.param_id,store_product_data.product_id,store_values.text,
                                                     store_values.number::text,
                                                     store_values.timestamp::text,
                                                     store_values.json,
                                                     store_values.unit) a WHERE param_value IS NOT NULL GROUP BY param_value
                                                     ");
                   // $list = ["value_list"=>$value_list, "param_id"=>$param_id_current];
                    $p_list[0]['list'] =  $value_list;
                    return $p_list;
                }else{
                    $sel_min = 0;
                    $sel_max = 0;
                    foreach ($params_filters AS $k =>$v){
                       $p_id = $v['param_id'];
                       if($p_id == $param_id_current){
                           $param_value_min = $v['param_value_min'];
                           $param_value_max = $v['param_value_max'];
                           $sel_min = $param_value_min;
                           $sel_max = $param_value_max;
                       }
                    }
                    $values = array();
                    $value = $this->queryNoDML("SELECT distinct(min_value) min_value FROM (SELECT 
                                                     COALESCE(
                                                     store_values.number::integer
                                                      ) AS min_value
                                                     FROM store_params
                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id
                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id
                                                     WHERE
                                                     param_names.lang_id = {$langID}
                                                     AND store_product_data.param_id = {$param_id_current}
                                                     AND store_values.lang_id = {$langID}
                                                     {$prr_id}
                                                     GROUP BY  store_values.number::integer) AS a ORDER BY min_value");

                    foreach ($value AS $key1=>$value1){
                        $number = $value1['min_value'];
                        array_push($values,$number);
                    }
                    $count = intval(count($value))-1;
                    $min_value = $value[0]['min_value'];
                    $max_value = $value[$count]['min_value'];
                    $list =['min'=>$min_value, 'max' =>$max_value, 'value_list'=> $values, 'selected_min'=>$sel_min, 'selected_max'=>$sel_max, "param_id"=>$param_id_current];
                    $p_list[0]['list'] = $list;
                    return $p_list;
                }





        }else{

            $p_list = $this->queryNoDML("SELECT distinct on (store_params.param_id)

                                                     store_params.param_id AS id,

                                                     param_names.text AS param_name,

                                                     store_params.alias AS data_alias,

                                                     search_types.template AS template,

                                                     store_product_data.product_id AS product_id,

                                                     store_product_data.value_id AS value_id,

                                                     store_params.search_type_id AS search_type_id

                                                     FROM store_params

                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id

                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id

                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id

                                                     INNER JOIN search_types ON store_params.search_type_id = search_types.search_type_id

                                                     WHERE

                                                     param_names.lang_id = {$langID}

                                                     AND store_values.lang_id = {$langID}

                                                     AND store_product_data.param_id = {$param_id_current}

                                                     GROUP BY store_params.param_id,

                                                     param_names.text,

                                                     search_types.template,

                                                     store_product_data.product_id,

                                                     store_product_data.value_id,

                                                     store_params.search_type_id,

                                                     store_values.text,

                                                     store_values.number::text,

                                                     store_values.timestamp::text,

                                                     store_values.json,

                                                     store_values.unit

                                                     ORDER BY store_params.param_id");


            if($search_type_id == 4){
                $value_list =  $this->queryNoDML("SELECT * FROM (SELECT
                                                     COALESCE(store_values.text,
                                                     store_values.number::text,
                                                     store_values.timestamp::text,
                                                     store_values.json,
                                                     store_values.unit) AS param_value
                                                     FROM store_params
                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id
                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                     WHERE
                                                     param_names.lang_id = {$langID}
                                                     AND store_values.lang_id = {$langID}
                                                     AND store_product_data.param_id = {$param_id_current}
                                                     GROUP BY store_params.param_id,store_params.param_id,store_product_data.product_id,store_values.text,
                                                     store_values.number::text,
                                                     store_values.timestamp::text,
                                                     store_values.json,
                                                     store_values.unit) a WHERE param_value IS NOT NULL GROUP BY param_value
                                                     ");

                $p_list[0]['list'] = $value_list;
                return $p_list;
            }else{
                $sel_min = 0;
                $sel_max = 0;
                $values = array();
                $value = $this->queryNoDML("SELECT distinct(min_value) min_value FROM (SELECT 
                                                     COALESCE(
                                                     store_values.number::integer
                                                      ) AS min_value
                                                     FROM store_params
                                                     LEFT JOIN param_names ON store_params.param_name_id = param_names.param_name_id
                                                     INNER JOIN store_product_data ON store_product_data.param_id = store_params.param_id
                                                     LEFT JOIN store_values ON store_values.value_id = store_product_data.value_id
                                                     INNER JOIN store_category_products ON store_category_products.product_id = store_product_data.product_id
                                                     WHERE
                                                     param_names.lang_id = {$langID}
                                                     AND store_product_data.param_id = {$param_id_current}
                                                     AND store_values.lang_id = {$langID}
                                                     GROUP BY  store_values.number::integer) AS a ORDER BY min_value");

                foreach ($value AS $key1=>$value1){
                    $number = $value1['min_value'];
                    array_push($values,$number);
                }
                $count = intval(count($value))-1;
                $min_value = $value[0]['min_value'];
                $max_value = $value[$count]['min_value'];
                $list =['min'=>$min_value, 'max' =>$max_value, 'value_list'=> $values, 'selected_min'=>$sel_min, 'selected_max'=>$sel_max, "param_id"=>$param_id_current];
                $p_list[0]['list'] = $list;
                return $p_list;
            }

         //   }

        }


    }

}







//$f = array();
//$f1 = array();
//echo "<pre>";
//$obj = new Z_Store();
//print_r($obj->getStoreParametersList(2));
//print_r($obj->getParentsCategory(5));
//print_r($obj->getCategoryParents(3));
//print_r($obj->getParentsCategory1(3));
//print_r($obj->getStoreTree(1));
//print_r($obj->getCategoryCountProducts(1));

//print_r($obj->getStoreProperties(2));

//print_r($obj->getStoreProductsList(5,1, 0, [], [],[],0));

//print_r($obj->getProductParam(1));

//print_r($obj->getCategoryCountProducts(2));

//print_r($obj->getStoreParametersList(1,0,['param_id'=>'2','param_value'=>'MultiMode','search_type_id'=>'4']));
//print_r($obj->parameterValueList(1,0,[],2,4));

//print_r($obj->getProductIds(1, 11,[]));
//print_r($obj->getProductParam1(1,4));
//echo "</pre>";
