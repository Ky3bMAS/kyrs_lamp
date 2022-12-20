<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];

header('Content-Type: application/json');

$con = new mysqli("MYSQL", "root", "", "Agregator");

$answer = array();

switch ($requestMethod) {

    case 'GET':
        if (empty(isset($_GET['bloger_id']))) {
            $result = $con->query("SELECT * FROM Bloger;");
            while ($row = $result->fetch_assoc()) {
                $answer[] = $row;
            }
        } else {
            $query_result = $con->query("SELECT * FROM Bloger WHERE bloger_id = " . $_GET['bloger_id'] . ";");
            $result = $query_result->fetch_assoc();
            $answer = $result;
        }
        if (!empty($result)) {
            http_response_code(200);
            echo json_encode($answer);
        } else {
            http_response_code(204);
        }
        break;

    case 'POST':
        $json = file_get_contents('php://input');
        $news_json = json_decode($json);
        if (!empty($news_json->{'name'}) && !empty($news_json->{'address'})) {
            $bloger_name = $news_json->{'name'};
            $bloger_address = $news_json->{'address'};

            if (!empty($result)) {
                http_response_code(409);

            } else {
                $stmt = $con->prepare("INSERT INTO Bloger (name,address) VALUES (?,?)");
                $stmt->bind_param('ss', $bloger_name,$bloger_address );
                $stmt->execute();
                http_response_code(201);
            }

        } else {
            http_response_code(422);
        }

        break;


    case 'PATCH':
        $json = file_get_contents('php://input');
        $obj = json_decode($json);
        if (empty(isset($_GET['bloger_id']))) {
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM Bloger WHERE bloger_id ='" . $_GET['bloger_id'] . "'");

            $result = $query_result->fetch_row();

            if (!empty($result)) {

                if (!empty($obj->{'name'}))
                    $con->query("UPDATE Bloger SET name='" . $obj->{'name'} . "' WHERE bloger_id ='" . $_GET['bloger_id'] . "'");

                if (!empty($obj->{'address'}))
                    $con->query("UPDATE Bloger SET address='" . $obj->{'address'} . "' WHERE bloger_id ='" . $_GET['bloger_id'] . "'");

                $answer["status"] = "Success. User updated.";
                http_response_code(200);

            } else {
                $answer["status"] = "Error. User not found.";
                http_response_code(404);
            }
        }
        echo json_encode($answer);
        break;

    case 'DELETE':
        if (empty(isset($_GET['bloger_id']))) {
            http_response_code(422);

        } else {
            $query_result = $con->query("SELECT * FROM Bloger WHERE bloger_id='" . $_GET['bloger_id'] . "'");
            $result = $query_result->fetch_row();

            if (!empty($result)) {
                $query_result = $con->query("DELETE FROM Bloger WHERE bloger_id='" . $_GET['bloger_id'] . "'");
                http_response_code(200);
            } else {
                http_response_code(204);
            }
        }
        break;

    default:
        http_response_code(405);
        break;
}
?>
