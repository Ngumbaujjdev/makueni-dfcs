<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST['locationId'])) {
    $app = new App;
    $locationId = $_POST['locationId'];

    $query = "SELECT h.hotel_id, h.hotel_name 
              FROM hotels h
              WHERE h.location_id = '{$locationId}'
              ORDER BY h.hotel_name ASC";

    $hotels = $app->select_all($query);

    if ($hotels) {
        foreach ($hotels as $hotel) {
            echo '<option value="' . $hotel->hotel_id . '">' . $hotel->hotel_name . '</option>';
        }
    } else {
        echo '<option value="">No hotels found for the selected location</option>';
    }
} else {
    echo '<option value="">Please select a location first</option>';
}
