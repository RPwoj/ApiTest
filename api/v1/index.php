<?php
require_once('/xampp/htdocs/api-project/db-conn.php');

$data = file_get_contents('php://input');
$decodedData = json_decode($data, true);
$getQuerya = mysqli_query($conn, "SELECT * FROM posts");

$response = array(
    'status' => 'success',
    'server' => 'none',
    'data' => $decodedData,
    'chosen-id' => 0,
    'last-id' => 0,
    'is-wrong' => false,
    'is-zero' => false,
    'is-ok'> false,
    'asasa' => 'hohoh',
);


if($_SERVER['REQUEST_METHOD'] == 'GET') {
    $postID = 0;
    $response['server'] = 'gecik';

    if (isset($_GET['post-id'])) {
        $postID = $_GET['post-id'];
        $sqlCheck = $conn->query('SELECT id FROM posts WHERE id=(SELECT max(id) FROM posts)');
        $sqlCheckRes = mysqli_fetch_assoc($sqlCheck);
        $response['chosen-id'] = $postID;
        $response['last-id'] = $sqlCheckRes['id'];
        $getQuery;
        if ($postID > $sqlCheckRes['id']) {
            $response['data'] = 'nie ma takiego id';
        } elseif ($postID == 0) {
            $getAllInfo = [];
            $getQuery = $conn->query('SELECT * FROM posts');
            while ($row = mysqli_fetch_assoc($getQuery)) {
                array_push($getAllInfo, $row);
            }
             $response['data'] = $getAllInfo;
        } else {
            $getQuery = $conn->query('SELECT title FROM posts WHERE id  =' . $postID);
            while ($row = mysqli_fetch_assoc($getQuery)) {
                $response['data'] = $row;
            }
        }
    }

    if (isset($_GET['id-to-change'])) {
        $postID = $_GET['id-to-change'];
        $getQuery = $conn->query('SELECT * FROM posts WHERE id=' . $postID);
        while ($row = mysqli_fetch_assoc($getQuery)) {
            $response['data'] = $row;
        }
    }
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $postID = 0;
    $response['server'] = $_SERVER['REQUEST_METHOD'];
    $response['data'] = $decodedData;

    $reqTitle = $decodedData['title'];
    $reqContent = $decodedData['content'];
    $reqPostImage = NULL; 
    $reqDateCreated = $decodedData['dateCreated'];

    $preparedInputText =  strtolower($decodedData['title']);
    $sqlCs = $conn->query('SELECT title FROM posts WHERE title="' . $preparedInputText . '"');
    // $sqlCs = $conn->query('SELECT title FROM posts WHERE id=2');
    $sqlCskRes = mysqli_fetch_assoc($sqlCs);
    if(!$sqlCskRes) {
        $sql = ('INSERT INTO posts (title, content, post_image, date_created) VALUES ("' . $reqTitle . '","' . $reqContent . '","' . $reqPostImage . '","' . $reqDateCreated . '")');

        if ($conn->query($sql) === TRUE) {
            $response['info-work'] = 'DZIALA';
          } else {
            $response['info-work'] =  'nie dziala';
          }

    } else {
        $response['data'] = 'ZAJETE';
    }
}

if($_SERVER['REQUEST_METHOD'] == 'PUT') {
    $idToUpdate = $decodedData['id'];
    $newTitle = $decodedData['title'];
    $response['data'] = $newTitle;

    $sqla = ('UPDATE posts SET title = "' . $newTitle . '" WHERE id = ' . $idToUpdate . ';');

    if ($conn->query($sqla) === TRUE) {
        $response['info-work'] = 'DZIALA';
      } else {
        $response['info-work'] =  'nie dziala';
      }
}

if($_SERVER['REQUEST_METHOD'] == 'DELETE') {
    $idToDelete = $decodedData['id'];
    $response['data'] = $idToDelete;

    $sqla = ('DELETE FROM posts WHERE id=' . $idToDelete . ';');

    if ($conn->query($sqla) === TRUE) {
        $response['info-work'] = 'DZIALA';
      } else {
        $response['info-work'] =  'nie dziala';
      }
}


echo json_encode($response);

