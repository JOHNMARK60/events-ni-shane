<?php
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'];

if($conn->query("DELETE FROM events WHERE id='$id'")){
    echo "Event Deleted!";
}else{
    echo "Error!";
}
?>