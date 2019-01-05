<?php
/**
 * This file is used to subscribe to DashCore Newsletter, is not a Test List.
 **/
include('./integration/MailChimp.php');
include('./models/SubscribeModel.php');

use \integration\MailChimp;
use \models\SubscribeModel;

define("LIST_ID", "0b892e81bd"); // DashCore Newsletter ListId
define("API_KEY", "829bc0293b68085b939769d5fdd27ca0-us13");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!empty($_POST['Subscribe'])) {
        $chimp = new MailChimp(API_KEY);
        $model = new SubscribeModel($_POST['Subscribe']);

        if ($model->isValid()) {
            $fields = [
                "FNAME" => $model->fname ? $model->fname : "Guest",
                "LNAME" => $model->lname ? $model->lname : "Guest"
            ];

            $result = $chimp->subscribeToList(LIST_ID, $model->email, $fields);

            if ($result->result) {
                echo json_encode([
                    "result" => true,
                    "message" => "Please check your email to confirm your subscription."
                ]);
            } else {
                echo json_encode([
                    "result" => false,
                    "message" => $result->error,
                    "errors" => ["email" => $result->error]
                ]);
            }
        } else {
            echo json_encode([
                "result" => false,
                "message" => "We found an unhandled error, please try again.",
                "errors" => $model->errors
            ]);
        }
    }
}
