<?php 
    require_once('db.php');
    header("Content-type: application/json");
    // Allow requests from any origin (for development)
    header("Access-Control-Allow-Origin: *");

    // Optional headers if needed (especially for POST, PUT, DELETE)
    header("Access-Control-Allow-Methods: GET, POST, DELETE, PUT, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");


    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET': {
            $sql = "SELECT * FROM `products` ORDER BY `id` DESC";
            $result  = $connection->query($sql);
            $products = [];
            while( $row = mysqli_fetch_assoc($result)) {
                array_push($products, $row);
            }
            echo json_encode($products);
        } break;

        case 'POST': {
            $input = json_decode(file_get_contents("php://input"), true);

            if (isset($input['title'])  &&  isset($input['price']) ) {
                
                $title = $input['title'];
                $price = $input['price'];

                $sql = " INSERT INTO products(`title`, `price`) VALUES ('$title', '$price');";
                $connection->query($sql);
                http_response_code(200);
                echo json_encode([
                    'status' => 200,
                    'message' => 'Product created successfully!'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(
                    [
                        'status' => 400,
                        'message' => 'Invalid Field'
                    ]
                );
            }
        } break;


        case 'PUT': {
            $input = json_decode(file_get_contents("php://input"), true);

            if(isset($input['id']) ) {
                $id = $input['id'];
                $title = $input['title'];
                $price = $input['price'];
                $sql = "UPDATE `products` SET `title` =  '$title',  `price` = '$price' WHERE `id` = '$id'";
                $connection->query($sql);
                http_response_code(200);
                echo json_encode([
                    'status' => 200,
                    'message' => 'Product updated successfully!'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(
                    [
                        'status' => 500,
                        'message' => 'Invalid Field'
                    ]
                );
            }
        } break;


        case 'DELETE': {
            $input = json_decode(file_get_contents("php://input"), true);

            if(isset($input['id']) ) {
                $id = $input['id'];
                $sql = "DELETE FROM `products` WHERE `id` = '$id';";
                $connection->query($sql);
                http_response_code(200);
                echo json_encode([
                    'status' => 200,
                    'message' => 'Product updated successfully!'
                ]);
            } else {
                http_response_code(400);
                echo json_encode(
                    [
                        'status' => 500,
                        'message' => 'Invalid Field'
                    ]
                );
            }
        } break;


    }