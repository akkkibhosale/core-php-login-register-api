<?php
header('Content-type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: token, Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!empty($_POST['emailID']) && !empty($_POST['userPassword'])) {
        $emailID = $_POST['emailID'];
        $userPassword = $_POST['userPassword'];
        try {
            require 'DBConnect.php';
            // checking for valid user details 
            $SELECT__USER__DATA = "SELECT * FROM `users` WHERE users.email_id=:emailID";
            $select__user__statement = $con->prepare($SELECT__USER__DATA);
            $select__user__statement->bindParam(':emailID', $emailID, PDO::PARAM_STR);
            $select__user__statement->execute();
            $user__flag = $select__user__statement->rowCount();
            if ($user__flag > 0) {
                $user__data = $select__user__statement->fetch(PDO::FETCH_ASSOC);
                if (password_verify($userPassword, $user__data['password'])) {
                    $user__object = array(
                        "fullName" => $user__data['full_name'],
                        "emailID" => $user__data['email_id'],
                        "userName" => $user__data['username'],
                        "token" => base64_encode(uniqid())
                    );
                    http_response_code(200);
                    $server__response__success = array(
                        "code" => http_response_code(200),
                        "status" => true,
                        "message" => "User Verified",
                        "userData" => $user__object
                    );
                    echo json_encode($server__response__success);
                } else {
                    http_response_code(404);
                    $server__response__error = array(
                        "code" => http_response_code(404),
                        "status" => false,
                        "message" => "Opps!! Incorrect Login Credentials"
                    );
                    echo json_encode($server__response__error);
                }
            } else {
                http_response_code(404);
                $server__response__error = array(
                    "code" => http_response_code(404),
                    "status" => false,
                    "message" => "Opps!! Incorrect Login Credentials"
                );
                echo json_encode($server__response__error);
            }
        } catch (Exception $ex) {
            http_response_code(404);
            $server__response__error = array(
                "code" => http_response_code(404),
                "status" => false,
                "message" => "Opps!! Something Went Wrong! " . $ex->getMessage()
            );
            echo json_encode($server__response__error);
        }
    } else {
        http_response_code(404);
        $server__response__error = array(
            "code" => http_response_code(404),
            "status" => false,
            "message" => "Invalid API parameters! Please contact the administrator or refer to the documentation for assistance."
        );
        echo json_encode($server__response__error);
    }
} else {
    http_response_code(404);
    $server__response__error = array(
        "code" => http_response_code(404),
        "status" => false,
        "message" => "Bad Request"
    );
    echo json_encode($server__response__error);
}
