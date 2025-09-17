<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST['hotelId'])) {
    $app = new App;
    $hotelId = $_POST['hotelId'];

    $query = "SELECT * FROM hotels WHERE hotel_id = '{$hotelId}'";
    $hotels = $app->select_all($query);

    if ($hotels && count($hotels) > 0) {
        echo json_encode($hotels[0]); // Return first hotel from results
    } else {
        echo json_encode(null);
    }
}
