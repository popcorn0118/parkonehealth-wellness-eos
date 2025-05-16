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

<!-- <h1><?php //echo $plan_name; ?></h1>
<img src="<?php //echo $thumbnail; ?>" alt="" style="width: 30%; height: auto;">
<div>
    <h3>方案內容</h3>
    <p><?php //echo $plan_info['content']; ?></p>
</div> -->
<?php

// echo "<h3>標籤</h3>";
// foreach($plan_info['checkup_parts'] as $checkup_part) {
//     $_checkup_part_title = $checkup_part->post_title;
//     echo "<div>";    
//     echo "<p>{$_checkup_part_title}</p>";
//     echo "</div>";
// }

// echo "<h3>性別</h3>";
// echo "<div>";
// foreach($plan_info['info'] as $key => $value) {
//     $_gender = "";
//     if($value['gender'] == 'female') {
//         $_gender = "女";
//     } else if ($value['gender'] == 'male'){
//         $_gender = "男";
//     }
//     $_price = number_format($value['price']);
//     echo "<p>{$_gender}性</p>";
//     echo "<p>NT{$_price}</p>";
// }
// echo "</div>";

// // 健檢裝置
// echo "<h3>健檢裝置</h3>";
// echo "<div>";
// foreach($plan_info['checkup_devices'] as $checkup_device_obj) {
//     $device_name = $checkup_device_obj->post_title;
//     $device_thumbnail = get_the_post_thumbnail_url($checkup_device_obj->ID, 'full');
//     $device_post_content = get_post($device_post_id);
//     echo "<div>";
//     echo "<h4>{$device_name}</h4>";
//     echo "<img src='{$device_thumbnail}' alt='' style='width: 30%; height: auto;'>";
//     echo "<p>{$device_post_content->post_content }</p>";
//     echo "</div>";
// }
// echo "</div>";

?>


<!-- 內容開始 -->
<main class="checkup-plan-single animated-slow animated fadeInUp">
    <div class="breadcrumbs">
        <div class="cont">
            <a href="javascript:window.history.back()" class="back-btn">返回上頁</a>
            <span class="line"></span>
            <span>健檢方案</span>
            <span> / </span>
            <span><?php echo $plan_name; ?></span>
        </div>
    </div>

    <div class="checkup-plan-single-header">
        <div class="cont">
            <div class="info">
                <h1 class="plan-name"><?php echo $plan_name; ?></h1>
                <div class="gender">
                    <?php
                        foreach($plan_info['info'] as $key => $value) {
                            $_gender = "";
                            if($value['gender'] == 'female') {
                                $_gender = "女";
                            } else if ($value['gender'] == 'male'){
                                $_gender = "男";
                            }
                            $_price = number_format($value['price']);
                    ?>
                        <div class="item">
                            <span><?php echo $_gender; ?>性</span>
                            <strong>NT<?php echo $_price; ?></strong>
                        </div>
                    <?php
                        }
                    ?>
                </div>
                <div class="tags">
                    <?php
                        foreach($plan_info['checkup_parts'] as $checkup_part) {
                            $_checkup_part_title = $checkup_part->post_title;
                        
                    ?>
                        <a class="tag" href="<?php echo esc_url( home_url( '/checkup_body_parts/' ) . $checkup_part->post_title ); ?>">
                            <?php echo $_checkup_part_title; ?>
                        </a>
                    <?php
                        }
                    ?>
                </div>
                <div class="suitable">
                    <h4 class="title">適合對象</h4>
                    <div class="desc">
                        30歲以上男性、木共父吉封申功掃
                    </div>
                </div>
                <div class="btn-group">
                    <a href="<?php echo esc_url( home_url( '/health-check-up-appointment' ) ); ?>" class="btn booking-btn">立即預約方案</a>
                    <!-- <a href="" class="btn compare-btn">加入方案互比</a> -->
                    <button class="btn compare-btn" type="button">加入方案互比</button>
                </div>
            </div>
           
            <div class="img">
                <div class="img-warp" style="background-image:url(<?php echo $thumbnail; ?>)">
                    <!-- <img src="<?php //echo $thumbnail; ?>" alt="<?php //echo $plan_name; ?>"> -->
                </div>
            </div>

        </div>
    </div>
    
    <!-- Elementor 內容 -->
    <div class="checkup-plan-single-cont">
        <?php the_content(); ?>
    </div>


    <!-- 儀器 -->
    <?php
        $device_list = get_field('plan_checkup_device_list');
    ?>
    <div class="checkup-plan-single-instrument">
        <div class="cont">
            <ul class="list">
                <?php 	

                foreach ($device_list as $item): 
                    $title = $item->post_title;
                    $desc = $item->post_content;
                    $img = wp_get_attachment_image_src( get_post_thumbnail_id( $item ), 'full' );
                    $img_default = '';

                ?>
                    <li class="item">			
                        <div class="img">
                            <img src="<?php echo !empty($img[0]) ? $img[0] : $img_default; ?>" alt="<?php echo $title; ?>">
                        </div>
                        <div class="info">
                            <h3 class="title"><?php echo $title; ?></h3>
                            <div class="desc">
                                <?php echo $desc; ?>
                            </div>
                        </div>
                    </li>	


                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- 項目詳細說明 -->
    <div class="checkup-plan-single-details">
        <div class="cont">
            <h3 class="title">項目詳細說明</h3>
            <div class="table-wrapper">
                <table class="responsive-table">
                    <thead>
                        <tr>
                            <th>類別</th>
                            <th>項目</th>
                            <th>男生</th>
                            <th>女生</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td rowspan="4">一般身體質量</td>
                            <td>身高、體重、腰圍、血壓、脈搏、呼吸、體溫</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>身體質量指數 / 身體成分分析</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>身體理學檢查綜合評估</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>醫師問診</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td rowspan="4">眼科</td>
                            <td>視力、眼壓、辨色力</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>眼底線、細隙燈檢查</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>免散瞳彩色數位眼底檢查</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>眼科醫師問診</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>心電圖檢測</td>
                            <td>靜態心電圖</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>血液常規項目</td>
                            <td>紅血球、白血球、血色素、血小板、紅血球容積、平均紅血球體積、平均紅血球血色素量、平均紅血球色素濃度、白血球分類計數</td>
                            <td><i class="icon-check"></i></td>
                            <td><i class="icon-check"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 熱門加選 -->
    <?php
        $hot_additional_list = get_field('hot_additional_list');
    ?>
     <div class="checkup-plan-single-popular-add">
        <div class="cont">
            <h3 class="title">熱門加選</h3>
            <div class="desc">
            說明文字，位蝴次拍友抄三裏未候親室點愛法我何裝。給位找已夕條牙里那，意出雞生交助木央毛細兄見，要完巴定親娘親：免出頭弟畫止皮水向叫己，怕羊東間海冒路文坡陽胡未的安葉只貓反！去麻洋，村快下，四士員校里。
            </div>
            <?php if(!empty($hot_additional_list)): ?>
                <div class="table-wrapper">
                    <table class="responsive-table">
                        <thead>
                            <tr>
                                <th>類別</th>
                                <th>項目</th>
                                <th>男生</th>
                                <th>女生</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                foreach ($hot_additional_list as $item): 
                                    $post = $item['hot_additional_item_name'];
                                    $title = $post->post_title;
                                    $price = $item['hot_additional_item_price'];
                            ?>
                                <tr>
                                    <td><?php echo $title; ?></td>
                                    <td>NT$<?php echo $price; ?></td>
                                    <td><i class="icon-check"></i></td>
                                    <td><i class="icon-check"></i></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php

// 頁尾
get_footer();

?>