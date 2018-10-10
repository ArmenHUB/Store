<?php
// noCash //
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

require_once('config.php');
require_once('check-access.php');
require_once('user-session.php');
// DB Connect //
$db = array("host" => DB_HOSTNAME, "user" => DB_USERNAME, "pass" => DB_PASSWORD, "name" => DB_NAME);

$connection = new mysqli($db["host"], $db["user"], $db["pass"], $db["name"]);
mysqli_set_charset($connection, "utf8");
$connection->select_db($db["name"]);

// Call Command //
$lang_id = (isset($_REQUEST['lang_id']) ? $_REQUEST["lang_id"] : null);

$user_id = (isset($_REQUEST['user_id']) ? $_REQUEST["user_id"] : null);
$token = (isset($_REQUEST['token']) ? $_REQUEST["token"] : null);

$command = (isset($_REQUEST["command"]) ? $_REQUEST["command"] : null);

$answer = -1;
// check is user login ok
if ($command !== "login") {
//    if ($user_id == null || $user_id < 0) {
//        $answer = [
//            "token" => "-1",
//            "responseText" => "Unknown user"
//        ];
//    } else if (!checkSession($token, $user_id, $connection)) {
//        $answer = [
//            "token" => "-1",
//            "responseText" => "Session expired"
//        ];
//    }
}
// check command and call appropriate function
//$command = "modules";
if ($answer == -1) {
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
            $answer = moduleList($user_id,$token);
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
           //print_r($answer['list']);
            break;
        case "user_info":
            $answer = [
                "user_id" => 1,
                "user_photo" => "images/logo2.png",
                "username" => "admin",
                "name" => "Vasilij",
                "lastname" => "Pupkin",
                "company" => "Z-Soft",
                "photo" => "images/no-photo",
                "token" => $token,
                "responseText" => "User info loaded normally"
            ];
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
            $answer = [
                "token" => $token,
                "user_id" => 1,
                "responseText" => "Unknown user"
            ];
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
    }
}
//$connection->close();
//INSERT INTO `modules` VALUES ('1','1','2','0','0','0','0')
echo json_encode($answer);
/**
 * @param $user_id
 * @param $token
 * @return array|int
 */
function moduleList($user_id,$token){
    if (gettype($user_id) != "integer") {
        return 7;
    }
    if ($token == "") {
        return 9;
    }
    $list_modules = array();
    $i = 0;
    global $connection;
    $sql = "SELECT `modules`.`moduleID` AS module_id, `moduleNames`.`name` AS module_name, `icons`.`icon` AS icon_name FROM `modules` INNER JOIN `moduleNames` ON  `modules`.`moduleNameID` = `moduleNames`.`moduleNameID` INNER JOIN `icons` ON `icons`.`iconID` = `modules`.`moduleIconID`";
    $result = $connection->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $list_modules[$i]['id'] = $row["module_id"];
            $list_modules[$i]['name'] = $row["module_name"];
            $i++;
        }
        $send_array = array('user_id' => $user_id, 'token' => $token,'responseText' => 'Modules loaded normally' ,'list' => $list_modules);
        return $send_array;
    } else {
        return 7;
    }
    $connection->close();
}
//$user_id = 1;
//$token = "dsgf45dgb65r";
//$a = moduleList($user_id,$token);
//print_r($a);
//SELECT `modules`.`moduleID` AS module_id, `moduleNames`.`name` AS module_name, `icons`.`icon` AS icon_name FROM `modules` INNER JOIN `moduleNames` ON  `modules`.`moduleNameID` = `moduleNames`.`moduleNameID` INNER JOIN `icons` ON `icons`.`iconID` = `modules`.`moduleIconID`
