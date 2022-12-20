<?php
$requestMethod = $_SERVER["REQUEST_METHOD"];

header('Content-Type: application/json');

$con = new mysqli("MYSQL", "root", "", "Agregator");

$answer = array();

switch ($requestMethod) {

    case 'GET':
        if (empty(isset($_GET['blog_id']))) {
            $result = $con->query("SELECT * FROM Blogs;");
            while ($row = $result->fetch_assoc()) {
                $answer[] = $row;
            }
        } else {
            $query_result = $con->query("SELECT * FROM Blogs WHERE blog_id = " . $_GET['blog_id'] . ";");
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
        if (!empty($news_json->{'title'})) {
            $publisher_id = $news_json->{'bloger_id'};
            $title = $news_json->{'title'};
            $news_date = $news_json->{'blog_date'};
            $news_text = $news_json->{'blog_text'};
            if (!empty($result)) {
                http_response_code(409);

            } else {
                $stmt = $con->prepare("INSERT INTO Blogs (bloger_id,title,blog_date,blog_text) VALUES (?,?,?,?)");
                $stmt->bind_param('isss', $bloger_id,$title,$blog_date,$blog_text);
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
        if (empty(isset($_GET['news_id']))) {
            $answer["status"] = "Error. Need ID Param";
            http_response_code(422);
        } else {
            $query_result = $con->query("SELECT * FROM news WHERE news_id ='" . $_GET['news_id'] . "'");

            $result = $query_result->fetch_row();

            if (!empty($result)) {

                if (!empty($obj->{'publisher_id'}))
                    $con->query("UPDATE news SET publisher_id='" . $obj->{'publisher_id'} . "' WHERE news_id ='" . $_GET['news_id'] . "'");

                if (!empty($obj->{'title'}))
                    $con->query("UPDATE news SET title='" . $obj->{'title'} . "' WHERE news_id ='" . $_GET['news_id'] . "'");

                if (!empty($obj->{'news_date'}))
                    $con->query("UPDATE news SET news_date='" . $obj->{'news_date'} . "' WHERE news_id ='" . $_GET['news_id'] . "'");

                if (!empty($obj->{'news_text'}))
                    $con->query("UPDATE news SET news_text='" . $obj->{'news_text'} . "' WHERE news_id ='" . $_GET['news_id'] . "'");

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
        if (empty(isset($_GET['news_id']))) {
            http_response_code(422);

        } else {
            $query_result = $con->query("SELECT * FROM news WHERE news_id='" . $_GET['news_id'] . "'");
            $result = $query_result->fetch_row();

            if (!empty($result)) {
                $query_result = $con->query("DELETE FROM news WHERE news_id='" . $_GET['news_id'] . "'");
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
