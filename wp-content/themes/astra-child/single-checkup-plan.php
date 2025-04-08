<?php
// 導覽列
get_header();

$post_id = get_the_ID(); // 取得文章 ID
$plan_name = get_the_title($post_id); // 取得文章標題
$plan_info = get_plan_info_by_id($post_id); // 取得方案資訊

// 取得文章精選圖片
$thumbnail = $plan_info['thumbnail'];
// var_dump($thumbnail);
?>
<h1><?php echo $plan_name; ?></h1>
<img src="<?php echo $thumbnail; ?>" alt="" style="width: 30%; height: auto;">
<div>
    <h3>方案內容</h3>
    <p><?php echo $plan_info['content']; ?></p>
</div>
<?php

echo "<h3>標籤</h3>";
foreach($plan_info['checkup_parts'] as $checkup_part) {
    $_checkup_part_title = $checkup_part->post_title;
    echo "<div>";    
    echo "<p>{$_checkup_part_title}</p>";
    echo "</div>";
}

echo "<h3>性別</h3>";
echo "<div>";
foreach($plan_info['info'] as $key => $value) {
    $_gender = "";
    if($value['gender'] == 'female') {
        $_gender = "女";
    } else if ($value['gender'] == 'male'){
        $_gender = "男";
    }
    $_price = number_format($value['price']);
    echo "<p>{$_gender}性</p>";
    echo "<p>NT{$_price}</p>";
}
echo "</div>";

// 健檢裝置
echo "<h3>健檢裝置</h3>";
echo "<div>";
foreach($plan_info['checkup_devices'] as $checkup_device_obj) {
    $device_name = $checkup_device_obj->post_title;
    $device_thumbnail = get_the_post_thumbnail_url($checkup_device_obj->ID, 'full');
    $device_post_content = get_post($device_post_id);
    echo "<div>";
    echo "<h4>{$device_name}</h4>";
    echo "<img src='{$device_thumbnail}' alt='' style='width: 30%; height: auto;'>";
    echo "<p>{$device_post_content->post_content }</p>";
    echo "</div>";
}
echo "</div>";

// 頁尾
get_footer();

?>