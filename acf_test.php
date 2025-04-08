<?php
require_once('wp-load.php');

$post_id = 92;
$field_name = "checkup_price_gender_and_items";

$checkup_price_gender_and_items = get_field($field_name, $post_id);
echo "<pre>", var_dump($checkup_price_gender_and_items), "</pre>";
// error_log(var_export($checkup_price_gender_and_items, true));

// $a_data = [];
// $a_data[] = [
//     'gender' => 'female',
//     'price' => '8800',
//     'checkup_item_list' => [
//         ['checkup_items' => 67],
//         ['checkup_items' => 102],
//         ['checkup_items' => 103],
//         ['checkup_items' => 104],
//         ['checkup_items' => 105],
//         ['checkup_items' => 106],
//         ['checkup_items' => 107],
//         ['checkup_items' => 108],
//         ['checkup_items' => 109],
//         ['checkup_items' => 111],
//         ['checkup_items' => 112],
//         ['checkup_items' => 113],
//         ['checkup_items' => 114],
//         ['checkup_items' => 115],
//         ['checkup_items' => 119],
//         ['checkup_items' => 120],
//         ['checkup_items' => 121],
//         ['checkup_items' => 122],
//         ['checkup_items' => 123],
//         ['checkup_items' => 125],
//         ['checkup_items' => 129],
//         ['checkup_items' => 131],
//         ['checkup_items' => 132],
//         ['checkup_items' => 144],
//         ['checkup_items' => 145],
//         ['checkup_items' => 146],
//         ['checkup_items' => 147],
//         ['checkup_items' => 148],
//         ['checkup_items' => 150],
//         ['checkup_items' => 156],
//         ['checkup_items' => 157],
//         ['checkup_items' => 158],
//         ['checkup_items' => 162],
//         ['checkup_items' => 176],
//         ['checkup_items' => 177],
//         ['checkup_items' => 178],
//         ['checkup_items' => 180],
//     ],
// ];

// $a_data[] = [
//     'gender' => 'male',
//     'price' => '8800',
//     'checkup_item_list' => [
//         ['checkup_items' => 67],
//         ['checkup_items' => 102],
//         ['checkup_items' => 103],
//         ['checkup_items' => 104],
//         ['checkup_items' => 105],
//         ['checkup_items' => 106],
//         ['checkup_items' => 107],
//         ['checkup_items' => 108],
//         ['checkup_items' => 109],
//         ['checkup_items' => 111],
//         ['checkup_items' => 112],
//         ['checkup_items' => 113],
//         ['checkup_items' => 114],
//         ['checkup_items' => 115],
//         ['checkup_items' => 119],
//         ['checkup_items' => 120],
//         ['checkup_items' => 121],
//         ['checkup_items' => 122],
//         ['checkup_items' => 123],
//         ['checkup_items' => 125],
//         ['checkup_items' => 129],
//         ['checkup_items' => 131],
//         ['checkup_items' => 132],
//         ['checkup_items' => 144],
//         ['checkup_items' => 145],
//         ['checkup_items' => 146],
//         ['checkup_items' => 147],
//         ['checkup_items' => 148],
//         ['checkup_items' => 150],
//         ['checkup_items' => 156],
//         ['checkup_items' => 157],
//         ['checkup_items' => 158],
//         ['checkup_items' => 162],
//         ['checkup_items' => 176],
//         ['checkup_items' => 177],
//         ['checkup_items' => 178],
//         ['checkup_items' => 180],
//     ],
// ];

// update_field($field_name, $a_data, $post_id);
?>