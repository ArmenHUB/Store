<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";

//get send data //
 $all_data = file_get_contents('php://input');
 $income_data = json_decode($all_data);
 $params = $income_data->params;
 $is_logged_normaly = false;
 $answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
 if (checkUser($income_data->user_id, $income_data->token)) {
     $is_logged_normaly = true;
 }
 if ($is_logged_normaly || $params->command === "login") {
     switch ($command) {
         case "logotype":
             $answer = [
                 "logotype" => "images/logo2.png",
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Modules loaded normally"
             ];
             break;
         case "modules":
             $user_id = 1;
             $token='erfdf454fgf';
             $answer = moduleList($user_id);
//            if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//            $answer = [
//                "list" => [
//                    ["id" => 1, "name" => "Contacts"],
//                    ["id" => 2, "name" => "Store"],
//                    ["id" => 3, "name" => "OrgChart"],
//                ],
//                "token" => $token,
//                "user_id" => 1,
//                "responseText" => "Modules loaded normally",
//            ];
             break;
         case "user_info":
             $user_id = 1;
             $token='erfdf454fgf';
             $answer = userInfo($user_id);
//            $answer = [
//                "user_id" => 1,
//                "user_photo" => "images/logo2.png",
//                "username" => "admin",
//                "name" => "Vasilij",
//                "lastname" => "Pupkin",
//                "company" => "Z-Soft",
//                "photo" => "images/no-photo",
//                "token" => $token,
//                "responseText" => "User info loaded normally"
//            ];
             break;
         case "module_tree":
             $answer = [
                 "tree" => [
                     ["id" => 1, "parent" => 0, "name" => "list item 1", "status" => "open", "selected" => 0, "count" => 7, "all" => 25],
                     ["id" => 2, "parent" => 0, "name" => "list item 2", "status" => "close", "selected" => 1, "count" => 15, "all" => 15],
                     ["id" => 3, "parent" => 1, "name" => "list item 3", "status" => "close", "selected" => 0, "count" => 1, "all" => 37],
                     ["id" => 4, "parent" => 3, "name" => "list item 4", "status" => "close", "selected" => 0, "count" => 0, "all" => 89]
                 ],
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Tree loaded normally"
             ];
             break;
         case "filters":
             $answer = [
                 "filters" => [
                     ["id" => 1, "value" => "aaa", "name" => "Name", "type" => "text", "search" => "text", "list" => []],
                     ["id" => 2, "value" => "bbb", "name" => "Description", "type" => "text", "search" => "text", "list" => []],
                     ["id" => 3, "value" => ["min" => 10, "max" => 20], "name" => "Diagonal", "type" => "number", "search" => "range", "list" => ["min" => 32, "max" => 55], "unit" => "inch"],
                     ["id" => 4, "value" => [2, 1], "name" => "Digital tuner bands", "type" => "text", "search" => "select", "list" => [["id" => 1, "value" => "DVB-T"], ["id" => 2, "value" => "DVB-C"], ["id" => 3, "value" => "DVB-T2"]]]
                 ],
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Filters loaded normally"
             ];
             break;
         case "catalog":
             if ($_REQUEST["params"]) {
                 $answer = [
                     "catalog" => [
                         ["1",  '<label><input type="checkbox"></label>', "TS-002", "Product Type", "Vendor name", "Ergo LE32CT5500AK", "TV production by Ergo 32` 2", 35, 20000],
                         ["2",  '<label><input type="checkbox"></label>', "TS-003", "Product Type", "Vendor name", "Ergo LE43CT2000AK", "TV production by Ergo 43` 1", 18, 10000],
                         ["3",  '<label><input type="checkbox"></label>', "TS-004", "Product Type", "Vendor name", "Ergo LE43CT3500AK", "TV production by Ergo 43` 2", 17, 3000],
                         ["4",  '<label><input type="checkbox"></label>', "TS-005", "Product Type", "Vendor name", "Ergo LE43CT5500AK", "TV production by Ergo 43` 3", 5, 4000],
                         ["5",  '<label><input type="checkbox"></label>', "TS-006", "Product Type", "Vendor name", "Ergo LE55CT2000AK", "TV production by Ergo 55`", 95, 2000],
                         ["6",  '<label><input type="checkbox"></label>', "TS-007", "Product Type", "Vendor name", "Haier LE24K6000S", "TV production company Haier 24`", 45, 121000],
                         ["7",  '<label><input type="checkbox"></label>', "TS-008", "Product Type", "Vendor name", "Haier LE32B8500T", "TV production company Haier 32` 1", 12, 140000],
                         ["8",  '<label><input type="checkbox"></label>', "TS-009", "Product Type", "Vendor name", "Haier LE32K5500T", "TV production company Haier 32` 2", 9, 186000],
                         ["9",  '<label><input type="checkbox"></label>', "TS-010", "Product Type", "Vendor name", "Haier LE32K6000S", "TV production company Haier 32` 3", 56, 267000],
                         ["10", '<label><input type="checkbox"></label>', "TS-011", "Product Type", "Vendor name", "Haier LE40U5000TF", "TV production company Haier 40`", 18, 234000],
                         ["11", '<label><input type="checkbox"></label>', "TS-012", "Product Type", "Vendor name", "Haier LE43K6000SF", "TV production company Haier 43` 1", 5, 175000],
                         ["12", '<label><input type="checkbox"></label>', "TS-013", "Product Type", "Vendor name", "Haier LE43K6500U", "TV production company Haier 43` 2", 37, 257000],
                         ["13", '<label><input type="checkbox"></label>', "TS-014", "Product Type", "Vendor name", "Haier LE50K5500TF", "TV production company Haier 50`", 1, 159000],
                     ],
                     "token" => $token,
                     "user_id" => 1,
                     "responseText" => "Catalog loaded normally 1111"
                 ];

             } else {
                 $answer = [
                     "catalog" => [
                         ["1", '<label><input type="checkbox"></label>', "TS-002", "Product Type", "Vendor name", "Ergo LE32CT5500AK", "TV production by Ergo 32` 2", 35, 20000],
                         ["2", '<label><input type="checkbox"></label>', "TS-003", "Product Type", "Vendor name", "Ergo LE43CT2000AK", "TV production by Ergo 43` 1", 18, 10000],
                         ["3", '<label><input type="checkbox"></label>', "TS-004", "Product Type", "Vendor name", "Ergo LE43CT3500AK", "TV production by Ergo 43` 2", 17, 3000],
                         ["4", '<label><input type="checkbox"></label>', "TS-005", "Product Type", "Vendor name", "Ergo LE43CT5500AK", "TV production by Ergo 43` 3", 5, 4000],
                         ["5", '<label><input type="checkbox"></label>', "TS-006", "Product Type", "Vendor name", "Ergo LE55CT2000AK", "TV production by Ergo 55`", 95, 2000],
                         ["6", '<label><input type="checkbox"></label>', "TS-007", "Product Type", "Vendor name", "Haier LE24K6000S", "TV production company Haier 24`", 45, 121000],
                         ["7", '<label><input type="checkbox"></label>', "TS-008", "Product Type", "Vendor name", "Haier LE32B8500T", "TV production company Haier 32` 1", 12, 140000],
                         ["8", '<label><input type="checkbox"></label>', "TS-009", "Product Type", "Vendor name", "Haier LE32K5500T", "TV production company Haier 32` 2", 9, 186000],
                         ["9", '<label><input type="checkbox"></label>', "TS-010", "Product Type", "Vendor name", "Haier LE32K6000S", "TV production company Haier 32` 3", 56, 267000],
                         ["10", '<label><input type="checkbox"></label>', "TS-011","Product Type",  "Vendor name", "Haier LE40U5000TF", "TV production company Haier 40`", 18, 234000],
                         ["11", '<label><input type="checkbox"></label>', "TS-012","Product Type",  "Vendor name", "Haier LE43K6000SF", "TV production company Haier 43` 1", 5, 175000],
                         ["12", '<label><input type="checkbox"></label>', "TS-013","Product Type",  "Vendor name", "Haier LE43K6500U", "TV production company Haier 43` 2", 37, 257000],
                         ["13", '<label><input type="checkbox"></label>', "TS-014","Product Type",  "Vendor name", "Haier LE50K5500TF", "TV production company Haier 50`", 1, 159000],
                     ],
                     "token" => $token,
                     "user_id" => 1,
                     "responseText" => "Catalog loaded normally"
                 ];
             }
             break;
         case "catalog_header":
             $answer = [
                 "headers" => [
                     ["name" => "N", "type" => "none", "list" => [], 'column' => 'none'],
                     ["name" => "Action", "type" => "none", "list" => [], 'column' => 'none'],
                     ["name" => "Part number", "type" => "text", "list" => [], 'column' => 'pn'],
                     ["name" => "Type", "type" => "none", "list" => [], 'column' => 'none'],
                     ["name" => "Vendor", "type" => "none", "list" => [], 'column' => 'vn'],
                     ["name" => "Name", "type" => "text", "list" => [], 'column' => 'name'],
                     ["name" => "Description", "type" => "text", "list" => [], 'column' => 'description'],
                     ["name" => "Count", "type" => "range", "list" => ["min" => 1, "max" => 95], 'column' => 'count'],
                     ["name" => "Price", "type" => "range", "list" => ["min" => 121000, "max" => 382000], 'column' => 'price'],
                 ],
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Catalog loaded normally",
                 "type" => "catalog"
             ];
             break;
         case "selected_header":
             $answer = [
                 "headers" => [
                     ["name" => "N", "type" => "none", "list" => [], 'column' => 'none'],
                     ["name" => "Part number", "type" => "text", "list" => [], 'column' => 'pn'],
                     ["name" => "Type", "type" => "none", "list" => [], 'column' => 'none'],
                     ["name" => "Vendor", "type" => "noneVendor name", "list" => [], 'column' => 'vn'],
                     ["name" => "Name", "type" => "text", "list" => [], 'column' => 'name'],
                     ["name" => "Description", "type" => "text", "list" => [], 'column' => 'description'],
                     ["name" => "Count", "type" => "none", "list" => [], 'column' => 'count'],
                     ["name" => "Price", "type" => "range", "list" => ["min" => 121000, "max" => 382000], 'column' => 'price'],
                 ],
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Catalog loaded normally",
                 "type" => "selected"
             ];
             break;
         case "login":
             $result = login($params->username, md5($params->password));
             if (gettype($result) == 'integer') { // return error number
                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
             } else { // return correct answer - array
                 $answer = ["token" => $result["token"], "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
             }
//             $answer = [
//                 "token" => $token,
//                 "user_id" => 1,
//                 "responseText" => "Unknown user"
//             ];
             break;
         case "none":
             $answer = [
                 "catalog" => [],
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Catalog is empty"
             ];
             break;
         case "import_files":
             $answer = [
                 "token" => $token,
                 "user_id" => 1,
                 "responseText" => "Import ok"
             ];
             break;
         case "logout":
              $result = logout($income_data->user_id);
             if ($result == 0) { // correctly logout
                 $answer = ["token" => T_LOGOUT, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => []];
             } else { // returned error number
                 $answer = ["token" => T_ERROR, "user_id" => $income_data->user_id, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
             }
             break;
     }
 }
 if ($answer['error'] > 0) {
     $answer['error'] = getError($answer['error'], $income_data->lang_id);
 }
 echo json_encode($answer);

 /**
  * @param $user_id
  * @return int
  */
function logout($user_id)
{
    $con = new Z_MySQL();
    if ($con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id}")) {
        return 0;
    }
    return 7;
}

 /**
  * @param $user_id
  * @param $token
  * @return bool
  */
function checkUser($user_id, $token)
{
    $con = new Z_MySQL();
    $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
    $answer = $con->queryNoDML("SELECT `loggedUsers`.`lastAction` AS 'lastAction' FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id} AND `loggedUsers`.`token` = '{$token}'")[0]["lastAction"];
    $cur_date = new DateTime($cur_time);
    $last_date = new DateTime($answer);
    if ($answer != "") {
        if ($last_date->getTimestamp() + LOG_OFF_DELAY > $cur_date->getTimestamp() || LOG_OFF_DELAY === 0) {
            $con->queryDML("UPDATE `loggedUsers` SET `lastAction`='{$cur_time}' WHERE `loggedUsers`.`userID` = {$user_id}");
            return true;
        } else {
            $con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id}");
        }
    }
    return false;
}

/**
 * @brief create session key
 * @return string -  random generated string
 */
function createToken()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}

/**
 * @param $username
 * @param $password
 * @param $host
 * @return array|int
 */
function login($username, $password)
{
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE `username` = '{$username}' AND `password` = '{$password}'")[0];
    if ((int)$data["userID"] > 0) {
        $user_id = (int)$data["userID"];
        //$usertype = (int)$data["userTypeID"];
        $token = createToken();
        $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
        $data1 = $con->queryNoDML("SELECT * FROM `loggedUsers` WHERE `userID` = '{$user_id}'");
        if($data1){
            $cur_date = new DateTime($cur_time);
            $last_date = new DateTime($data1[1]['lastAction']);
            if ($last_date->getTimestamp() + LOG_OFF_DELAY > $cur_date->getTimestamp() || LOG_OFF_DELAY === 0){
                return 5;
            }
            else{
                $con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id}");
            }
        }
        else{
            if ($con->queryDML("INSERT INTO `loggedUsers`(`userID`, `lastAction`, `token`) VALUES ('{$user_id}', '$cur_time', '$token')")) {
               //m return ["token" => $token, "user_id" => $user_id, "user_type_id" => $usertype];
            } else {
                return 5;
            }
        }
    }
    return 2;
}

/**
 * @param $user_id
 * @param $token
 * @return array|int
 */
function moduleList($user_id){
    if (gettype($user_id) != "integer") {
        return 7;
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `modules`.`moduleID` AS module_id, `moduleNames`.`name` AS module_name, `icons`.`icon` AS icon_name FROM `modules` INNER JOIN `moduleNames` ON  `modules`.`moduleNameID` = `moduleNames`.`moduleNameID` INNER JOIN `icons` ON `icons`.`iconID` = `modules`.`moduleIconID`");
    if ($data) {
        return $data;
    } else {
        return 9;
    }

}
//$user_id = 1;
//$token = "dsgf45dgb65r";
//$a = moduleList($user_id,$token);
//print_r($a);
//SELECT `modules`.`moduleID` AS module_id, `moduleNames`.`name` AS module_name, `icons`.`icon` AS icon_name FROM `modules` INNER JOIN `moduleNames` ON  `modules`.`moduleNameID` = `moduleNames`.`moduleNameID` INNER JOIN `icons` ON `icons`.`iconID` = `modules`.`moduleIconID`

/**
 * @param $user_id
 * @return array|int
 */
function userInfo($user_id){
    if (gettype($user_id) != "integer") {
        return 7;
    }
    $array_send = array();
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `users`.`userID` AS user_id,`users`.`username` AS username,`userParamNames`.`text` AS user_param_name,`userParamValues`.`text` AS user_param_value FROM `users` INNER JOIN `userInfo` ON `userInfo`.`userID` = `users`.`userID` INNER JOIN `userParamValues` ON `userParamValues`.`userParamValueID` = `userInfo`.`userParamValueID` INNER JOIN `userParamNames` ON `userParamNames`.`userParamNameID` = `userInfo`.`userParamNameID` WHERE `userInfo`.`userID` = '$user_id'");
    if ($data) {
        foreach ($data as $key => $value) {
            $user_param_name = $value["user_param_name"];
            $array_send['user_id'] = $value["user_id"];
            $array_send['username'] = $value["username"];
            $array_send[$user_param_name] = $value["user_param_value"];
        }
        return $array_send;
    } else {
        return 9;
    }
}
//$user_id = 1;
//print_r(userInfo($user_id));
