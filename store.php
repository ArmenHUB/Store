<?php

namespace functions;

use core\Z_Error;

use core\Z_UserAuthorisation;

use store\Z_Specification;

use store\Z_Store;





require_once "mail_send.php";

//require_once "errors.php";

require_once "core/Z_Error.php";

require_once "core/Z_UserAuthorisation.php";

require_once "store/Z_Store.php";

require_once "store/Z_Specification.php";



// from JavaScript

$all_data = file_get_contents('php://input');

$income_data = json_decode($all_data);

$params = $income_data->params;

$command = $income_data->command;

$answer = [

    "user_id" => intval($income_data->user_id),

    "host_id" => intval($income_data->host_id),

    "token" => strval($income_data->token),

    "lang_id" => intval($income_data->lang_id),

    "info" => '',

    "error_code" => 0,

    "error_text" =>'',

    "command" => strval($income_data->command)

];



if (is_numeric($answer['host_id']) && is_numeric($answer['user_id'])) {

    if ($command !== "get_time") {

        $ua = new Z_UserAuthorisation($answer['user_id'], strval($answer['token']), intval($answer['host_id']));

        $answer['error'] = $ua->lastError();

    } else {

        $answer['error'] = 0;

    }

}

if ($answer['error'] === 0) {

   // $contacts = new Z_Store();

  //  $contacts->queryBool("CREATE EXTENSION IF NOT EXISTS tablefunc;");

    $store = new Z_Store();

    $specifications = new Z_Specification();

    switch ($command) {

        case "get_store":

            $answer['info'] = [

                "categories" => [

                    "title" => 'Store',

                    "list" => $store->getStoreTree($income_data->lang_id)

                ],

                "table" =>  $store->getStoreProductsList($income_data->user_id,$income_data->lang_id,$params->category_id,$params->filters,$params->sort,$params->param_filters,$params->page),

                "filters" => [
                    "title" => 'Filters',
                  "list" => $store->getStoreParametersList($answer['lang_id'],$params->category_id,$params->param_filters)
                  //  "list" => $store->parameterValueList($answer['lang_id'],$params->category_id,$params->param_filters,1)
                ]

            ];

            break;

        case "get_store_tree":
            $answer['info'] = [
                "categories" => [
                    "title" => 'Store',
                    "list" => $store->getStoreTree($income_data->lang_id)
                ]
            ];
            break;
        case "get_store_table":
            $answer['info'] = [
                "table" =>  $store->getStoreProductsList($income_data->user_id,$income_data->lang_id,$params->category_id,$params->filters,$params->sort,$params->param_filters,$params->page),
            ];
            break;
        case "get_store_filters":
            $answer['info'] = [
                "filters" => [
                    "title" => 'Filters',
                    "list" => $store->parameterList($answer['lang_id'],$params->category_id,$params->param_filters)
                ]
            ];
            break;
        case "get_filter_value_list":
            $answer['info'] = $store->parameterValueList($answer['lang_id'],$params->category_id,$params->param_filters,$params->param_id,$params->search_type_id);
            break;
        case "update_table_settings":
            $answer['info'] = $store->UpdateSettings($answer['user_id'],$params->settings);
            break;

        case "get_specification_list":

            $answer['info'] = [

                "specification_list" => [

                    "title" => 'Specifications',

                    "list" => $specifications->getSpecificationList($answer['user_id'])

                ]

            ];

            break;

        case "get_specification_table":

            $answer['info'] = [

                "pagination" => $specifications->pagination($answer['user_id'],$answer['lang_id'],$params->archive,$params->filters,$params->sort,$params->page),

                "settings" => $specifications->getUserSettings($answer['user_id']),

                "thead" => $specifications->getSpecificationProperties($answer['lang_id']),

                "tbody" => $specifications->getAllSpecifications($answer['user_id'],$answer['lang_id'],$params->archive,$params->filters,$params->sort,0)

            ];

            break;

        case "save_specification":

            $answer['info'] = $specifications->saveSpecification($answer['user_id'],$answer['lang_id'],$params->name,$params->customer,$params->products);

            break;

        case "update_specification":

               $answer['info'] = $specifications->EditSpecification($answer['user_id'],$answer['lang_id'],$params->name,$params->customer,$params->products,$params->specification_id);

            break;



        case "get_specification_info":

            $answer['info'] = [

                "table" => [
                    "thead" => $store->getStoreProperties($income_data->lang_id),
                    "tbody" =>$specifications->getSpecificationInfo($income_data->lang_id,$params->specification_id,$income_data->user_id),
                    "pagination" => [],
                    "settings" => []
                ]

            ];

            break;

        case "specification_to_archive":

            $answer['info'] = $specifications->SpecificationToArchive($answer['user_id'],$params->specification_id,$params->archive);

            break;

        case "sharing":

            $answer['info'] = [
                "accesses" => $specifications->getAccessNames($answer['lang_id']),
                "sharing_list" => $specifications->SharingList($income_data->lang_id,$answer['user_id']),
                "access_user_list" => $specifications->HasAccessList($income_data->user_id,$params->specification_id,$answer['lang_id'])
            ];

            break;

        case "add_access":

            $answer['info'] = $specifications->addAccess($answer['user_id'],$params->specification_id,$params->contact_id,$params->access);

            break;

        case "remove_access":

            $answer['info'] = $specifications->RemoveAccess($answer['user_id'],$params->specification_id,$params->contact_id,$params->access);

            break;

    }

}

if($answer['error_code']>0){

//    $error = new Z_Error();

//    $answer['error_text'] = $error->getErrorText($answer['error'], $answer['lang_id']);

}

echo json_encode($answer);
