<?php
include '../db.php';

$data = json_decode(file_get_contents("php://input"), true);

$sql = "UPDATE events SET 
event_type='{$data['type']}',
event_date='{$data['date']}',
event_time='{$data['time']}',
venue='{$data['venue']}',
guests='{$data['guests']}',
client_name='{$data['client']}',
client_contact='{$data['contact']}',
package_type='{$data['package']}',
budget='{$data['budget']}',
services='{$data['services']}'
WHERE id='{$data['id']}'";

if($conn->query($sql)){
    echo "Event Updated!";
}else{
    echo "Error!";
}
?>