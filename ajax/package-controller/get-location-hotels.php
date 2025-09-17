<?php
include "../../../config/config.php";
include "../../../libs/App.php";

if (isset($_POST['locationId'])) {
    $app = new App;
    $locationId = $_POST['locationId'];
    $packageId = $_POST['packageId'] ?? null;

    // Get currently selected hotels if in edit mode
    if ($packageId) {
        $query = "SELECT hotel_id FROM package_hotel_mappings WHERE package_id = '{$packageId}'";
        $result = $app->select_all($query);
        $selectedHotels = array_map(function ($row) {
            return $row->hotel_id;
        }, $result);
    } else {
        $selectedHotels = [];
    }

    // Get hotels for the location
    $query = "SELECT * FROM hotels WHERE location_id = '{$locationId}' ORDER BY hotel_name ASC";
    $hotels = $app->select_all($query);

    if ($hotels) {
        foreach ($hotels as $hotel) {
            $selected = in_array($hotel->hotel_id, $selectedHotels) ? 'selected' : '';
            echo "<option value='{$hotel->hotel_id}' {$selected}>{$hotel->hotel_name}</option>";
        }
    }
}
