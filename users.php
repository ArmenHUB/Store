<?php
/**
 * Created by PhpStorm.
 * User: suren.danielyan
 * Date: 17/01/2019
 * Time: 12:22
 */

require_once "config.php";
require_once "z_mysql.php";
require_once "z_config.php";

/**
 * @brief Generate key/token/password
 * @param $length int created key/password length default value 12
 * @param $include_symbol bool is need include symbols in key/password default value false
 * @return string key/password
 */
function createToken($length = 12, $include_symbol = false)
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $symbols = '!@#$%^&*()_+~,.;[]{}:|<>?-';
    if($include_symbol === true){
        $alphabet .= $symbols;
    }
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < $length; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

/**
 * @param $user_id
 * @param $token
 * @param $con Z_MySQL
 * @return bool
 */
function checkUser($user_id, $token, $con)
{
    $logged = $con->queryNoDML("SELECT `userID` AS 'user_id', `lastAction` AS `last_action` FROM `loggedUsers` WHERE `userID` = {$user_id} AND `token` = '{$token}'")[0];
    $is_logged = false;
    $is_in_time = false;
    if($logged['user_id']>0){
        $is_logged = true;
    }
    $last = new DateTime($logged['last_action']);
    $now = new DateTime($con->getNow());
    if(LOG_OFF_DELAY==0 || $last->getTimestamp()+LOG_OFF_DELAY>$now->getTimestamp()){
        $is_in_time = true;
    }

    if ($is_logged && $is_in_time) {
        $update = $con->queryDML("UPDATE `loggedUsers` SET `lastAction`=NOW()  WHERE `userID`={$user_id} AND `token`='{$token}'");
        return true;
    }
    $con->queryDML("DELETE FROM `loggedUsers` WHERE `userID`={$user_id}");
    return false;
}


/**
 * @param $user_id
 * @param $token
 * @return array|int
 */
function LogoTypeByUser($user_id){

    $con = new Z_MySQL();
    $array_send = array();
    $user_param_name = LOGOTYPE;
    $data = $con->queryNoDML("SELECT `userParamNames`.`text` AS user_param_name,`userParamValues`.`text` AS user_param_value FROM `users` INNER JOIN `userInfo` ON `userInfo`.`userID` = `users`.`userID` INNER JOIN `userParamValues` ON `userParamValues`.`userParamValueID` = `userInfo`.`userParamValueID` INNER JOIN `userParamNames` ON `userParamNames`.`userParamNameID` = `userInfo`.`userParamNameID` WHERE `userInfo`.`userID` = '$user_id' AND `userParamNames`.`userParamNameID` =  '$user_param_name'");
    if($data){
        foreach ($data as $key => $value) {
            $user_param_name = $value["user_param_name"];
            $array_send[$user_param_name] = $value["user_param_value"];
        }
        return $array_send;
    }else {
        return 7;
    }
}

function ModuleList($user_id,$lang_id){
    $con = new Z_MySQL();
    $array_send = array();
    $data = $con->queryNoDML("SELECT `modules`.`moduleID` AS module_id, `moduleNames`.`name` AS module_name, `icons`.`icon` AS icon_name,`moduleNames`.`name` AS 'module_name', `moduleNames`.`alias` AS 'data_alias' FROM `modules`
                              INNER JOIN `moduleNames` ON  `modules`.`moduleNameID` = `moduleNames`.`moduleNameID`
                              INNER JOIN `icons` ON `icons`.`iconID` = `modules`.`moduleIconID` INNER JOIN `moduleAccess` ON `modules`.`moduleID` = `moduleAccess`.`moduleID` WHERE
                               `moduleNames`.`langID` = '{$lang_id}' AND `moduleAccess`.`userID` = '{$user_id}'");
    if($data){
        return $data;
    }else{
        return 9;
    }
}

function UserInfo($user_id){
    $con = new Z_MySQL();
    $array_send = array();
    $data = $con->queryNoDML("SELECT `users`.`userID` AS user_id,`users`.`username` AS username,`userParamNames`.`text` AS user_param_name,`userParamValues`.`text` AS user_param_value FROM `users`
            INNER JOIN `userInfo` ON `userInfo`.`userID` = `users`.`userID` INNER JOIN `userParamValues` ON `userParamValues`.`userParamValueID` = `userInfo`.`userParamValueID`
            INNER JOIN `userParamNames` ON `userParamNames`.`userParamNameID` = `userInfo`.`userParamNameID` WHERE `userInfo`.`userID` = '{$user_id}'");

    if($data){
        foreach ($data as $key => $value){
            $user_param_name = $value["user_param_name"];
            $array_send['user_id'] = $value["user_id"];
            $array_send['username'] = $value["username"];
            $array_send[$user_param_name] = $value["user_param_value"];
        }
        return $array_send;
    }else{
        return 9;
    }

}

function LangList($lang_id){
    $con = new Z_MySQL();

    $lang_list = $con->queryNoDML("SELECT `languages`.`langID` AS 'lang_id',
                                    `languages`.`shortName` AS 'short_name',
                                    `languages`.`flag` AS 'flag', `languages`.`name` AS 'name' FROM `languages`");
    if($lang_list){
        return $lang_list;
    }else{
        return FALSE;
    }
}


function CategoryList($lang_id){
    $con = new Z_MySQL();

    $category_list = $con->queryNoDML("SELECT `categories`.`categoryID` AS 'category_id', `categories`.`categoryParentID` AS 'parent_id', `categoryNames`.`text` AS 'category_name'
                                       FROM `categories` INNER JOIN `categoryNames` ON `categoryNames`.`categoryNameID` = `categories`.`categoryNameID`
                                       WHERE `categoryNames`.`langID` = '{$lang_id}'");

    if($category_list){
        return $category_list;
    }else{
        return FALSE;
    }
}


function ProductList($category_id,$lang_id,$name_filter,$part_number_filter,$available_filter){
    $con = new Z_MySQL();
    $param_names = array(PRODUCT_PARAM_NAME,PRODUCT_PARAM_DESC,PRODUCT_PARAM_COUNT);

    if($category_id == '0'){
        if($part_number_filter == "" && $name_filter !== ""){
                            $products = $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number` 
                                                           FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         WHERE 
                                         CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                         AND `productNames`.`name` LIKE '%$name_filter%' 
                                         AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'");
            return $products;

        }else if($name_filter == "" && $part_number_filter !== ""){
            $products =  $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number`
                                                           FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                           WHERE
                                          CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                        AND `part_number` LIKE '%$part_number_filter%'
                                        AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'");
            return $products;
        }else if($name_filter !== "" && $part_number_filter !== ""){
            $products = $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number`
                                                           FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         WHERE
                                          CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                         AND `productNames`.`name` LIKE '%$name_filter%'
                                         AND `products`.`part_number` LIKE '%$part_number_filter%'
                                          AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'");
            return $products;
        }else{
                $products =  $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number` FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         WHERE
                                         CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                         AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'
                                         ");
                return $products;
        }
    }else{
        if($part_number_filter == "" && $name_filter !== ""){

            $products = $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number` 
                                                           FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         INNER JOIN `category_products` ON `category_products`.`productID` = `products`.`productID`
                                         WHERE 
                                         CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                         AND `productNames`.`name` LIKE '%$name_filter%' 
                                         AND `category_products`.`categoryID` = '{$category_id}'
                                         AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'");
            return $products;
        }else if($name_filter == "" && $part_number_filter !== ""){
            $products =  $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number`
                                                           FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         INNER JOIN `category_products` ON `category_products`.`productID` = `products`.`productID`
                                           WHERE
                                          CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                        AND `part_number` LIKE '%$part_number_filter%'
                                        AND `category_products`.`categoryID` = '{$category_id}'
                                        AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'");
            return $products;
        }else if($name_filter !== "" && $part_number_filter !== ""){
            $products = $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number`
                                                           FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         INNER JOIN `category_products` ON `category_products`.`productID` = `products`.`productID`
                                         WHERE
                                          CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                         AND `productNames`.`name` LIKE '%$name_filter%'
                                         AND `products`.`part_number` LIKE '%$part_number_filter%'
                                         AND `category_products`.`categoryID` = '{$category_id}'
                                         AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'");
            return $products;
        }else{
            $products =  $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number` FROM `products`
                                         INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                         INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                         INNER JOIN `category_products` ON `category_products`.`productID` = `products`.`productID`
                                         WHERE
                                         CASE
                                          WHEN $available_filter = '0' THEN `products`.`count` > 0 OR `products`.`count` = 0
                                          WHEN $available_filter = '1' THEN `products`.`count` > 0 
                                          WHEN $available_filter = '2' THEN `products`.`count` = 0
                                         END
                                         AND `category_products`.`categoryID` = '{$category_id}'
                                         AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'
                                         ");
            return $products;
        }
    }

}

function ProductsSort($products_id,$lang_id,$sort_by,$sort){
    $con = new Z_MySQL();
    $send_array = array();
    $param_names = array(PRODUCT_PARAM_NAME,PRODUCT_PARAM_DESC,PRODUCT_PARAM_COUNT);
    if($sort == 'DESC'){
        $products = $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number` FROM `products`
                                                           INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                                           INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                                           WHERE `products`.`productID` IN(".implode(',',$products_id).")
                                                           AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'
                                                           ORDER BY `$sort_by` DESC");
        return $products;
    }else{
        $products = $con->queryNoDML("SELECT `products`.`productID` AS 'product_id',
                                                          `productNames`.`name` AS 'name',
                                                          `productDescription`.`name` AS 'description',
                                                          `products`.`count` AS 'count',
                                                          `products`.`part_number` FROM `products`
                                                           INNER JOIN `productNames` ON `productNames`.`productNameID` = `products`.`productNameID`
                                                           INNER JOIN `productDescription` ON `productDescription`.`productDescID` = `products`.`productDescID`
                                                           WHERE `products`.`productID` IN(".implode(',',$products_id).")
                                                           AND `productNames`.`langID` = '1' AND `productDescription`.`langID` = '1'
                                                           ORDER BY `$sort_by`");
        return $products;
    }

}
