<?php
// 健檢方案內頁
// 導覽列
get_header();

$post_id = get_the_ID(); // 取得文章 ID
$plan_name = get_the_title($post_id); // 取得文章標題
$plan_info = get_plan_info_by_id($post_id); // 取得方案資訊

error_log(print_r($plan_info, true));

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
        <a href="javascript:void(0);" class="back-btn" data-fallback-url="<?php echo esc_url( home_url( '/choices/' ) ); ?>">返回上頁</a>
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
                    <button class="btn compare-btn add_to_plans_compare" type="button" data-pname="<?= $plan_name ?>" data-pid="<?= $post_id ?>">加入方案互比</button>
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
        
        <?php 
        if ( \Elementor\Plugin::instance()->db->is_built_with_elementor( get_the_ID() ) ) {
            // 是用 Elementor 編輯的頁面
            the_content(); 
        } else {
            // 不是 Elementor
            echo '<div class="cont">';
                the_content(); 
            echo '</div>';
        }
        
        ?>
    </div>


    <!-- 儀器 -->
    <?php
        $device_list = get_field('plan_checkup_device_list', $post_id);
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
                <table class="table responsive-table">
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
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>身體質量指數 / 身體成分分析</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>身體理學檢查綜合評估</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>醫師問診</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td rowspan="4">眼科</td>
                            <td>視力、眼壓、辨色力</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>眼底線、細隙燈檢查</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>免散瞳彩色數位眼底檢查</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>眼科醫師問診</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>心電圖檢測</td>
                            <td>靜態心電圖</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                        <tr>
                            <td>血液常規項目</td>
                            <td>紅血球、白血球、血色素、血小板、紅血球容積、平均紅血球體積、平均紅血球血色素量、平均紅血球色素濃度、白血球分類計數</td>
                            <td class="icon"><i class="icon-check"></i></td>
                            <td class="icon"><i class="icon-check"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 熱門加選 -->
    <?php
        $hot_additional_list = get_field('hot_additional_list', $post_id);
    ?>
    <?php if(!empty($hot_additional_list)): ?>
        <div class="checkup-plan-single-popular-add">
            <div class="cont">
                <h3 class="title">熱門加選</h3>
                <div class="desc">
                說明文字，位蝴次拍友抄三裏未候親室點愛法我何裝。給位找已夕條牙里那，意出雞生交助木央毛細兄見，要完巴定親娘親：免出頭弟畫止皮水向叫己，怕羊東間海冒路文坡陽胡未的安葉只貓反！去麻洋，村快下，四士員校里。
                </div>
                
                    <div class="table-wrapper">
                        <table class="table responsive-table">
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
            </div>
        </div>
    <?php endif; ?>

    <!-- 注意事項 -->
    <?php
        $precautions = get_field('precautions', $post_id);
    ?>
     <div class="checkup-plan-single-precautions">
        <div class="cont">
            <div class="left">
                <div class="desc no-br">
                    如已經了解方案內容，可以立即此預約方案；<br/>也可以利用互比功能，了解方案之間的不同。
                </div>
                <div class="btn-group">
                    <a href="<?php echo esc_url( home_url( '/health-check-up-appointment' ) ); ?>" class="btn booking-btn">立即預約方案</a>                    
                    <button class="btn compare-btn add_to_plans_compare" type="button" data-pname="<?= $plan_name ?>" data-pid="<?= $post_id ?>">加入方案互比</button>
                </div>
            </div>
            <?php if(!empty($precautions)): ?>
                <div class="right">
                    <h4 class="title">注意事項</h4>
                    <div class="desc">
                        <?php echo $precautions;  ?>
                    </div>
                </div>
            <?php endif; ?>
           
        </div>
    </div>

     <!-- 類似方案 -->
     <?php
        $similar_plan = get_field('similar_plan', $post_id);
        $plans_bg = get_field('hottest_plans_bg', 'option');
        // error_log(print_r($hottest_plans, true));	
        $count = 3;	// 只取前三筆
        $plans = array();
        for ($i = 0; $i < $count; $i++) {
            $plan = array();
            $_title = $similar_plan[$i]['plan']->post_title;
            $plan['tag_name'] = $similar_plan[$i]['tag_name'];
            $plan['title'] = $_title;
            $plan['detail'] = array();
            $plan['id'] = $similar_plan[$i]['plan']->ID;

            // 取得acf資料
            $checkup_price_gender_and_items = get_field("checkup_price_gender_and_items", $similar_plan[$i]['plan']->ID);
            foreach ($checkup_price_gender_and_items as $checkup_price_gender_and_item) {
                $_plan = array();
                $_gender = "";
                if ($checkup_price_gender_and_item['gender'] == 'male') {
                    $_gender = "男";
                } else if ($checkup_price_gender_and_item['gender'] == 'female') {
                    $_gender = "女";
                }

                $_plan['sex'] = $_gender;
                $_plan['price'] = number_format($checkup_price_gender_and_item['price']);
                array_push($plan['detail'], $_plan);
            }
            array_push($plans, $plan);
        }
    ?>
    <div class="checkup-plan-single-plans">
        <div class="cont">
            <h2 class="title">類似方案</h2>
            
            <div class="list plan-card">
                <?php
                $bg_keys = ['img_bg_1', 'img_bg_2', 'img_bg_3'];
                foreach ($plans as $index => $plan):
                    $bg_key = $bg_keys[$index] ?? 'img_bg_1'; // 防呆：超出3筆時仍抓第一張
                    $bg_url = $plans_bg[$bg_key]['link'];
                ?>
                <div class="plan">
                    <h4 class="sub-title"><?= $plan['tag_name']; ?></h4>
                    <h3 class="title"><?= $plan['title']; ?></h3>
                    <div class="line"></div>
                    <?php
                    foreach ($plan['detail'] as $detail):
                    ?>
                        <ul class="list">
                            <li class="sex"><?= $detail['sex']; ?>性</li>
                            <li class="price">NT$<?= $detail['price']; ?></li>
                        </ul>
                    <?php
                    endforeach;
                    ?>
                    <button type="button" class="program_content" data-pname="<?= $plan['title']; ?>" data-pid="<?= $plan['id']; ?>">了解方案內容</button>                    
                    <button class="add_to_plans_compare" type="button" data-pname="<?= $plan_name ?>" data-pid="<?= $post_id ?>">加入方案互比</button>
                    <div class="img" style="background-image: url(<?php echo $bg_url; ?>);"></div>
                </div>
            <?php
            endforeach;
            ?>
        </div>
            
        </div>
    </div>


</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    jQuery(function($){
        var plan_name = "<?= $plan_name; ?>"; // 使用 json_encode 以避免特殊字元問題
        var plan_id = <?= $post_id; ?>;
        console.log(plan_name, plan_id);

        // 點擊加入方案互比
        $('.add_to_plans_compare').on('click', function() {
            
            $.ajax({
                url: '<?= admin_url('admin-ajax.php'); ?>',
                type: 'post',
                dataType: 'json',
                cache: false,
                async: false,
                data: {
                    action: 'add_to_plans_compare',
                    pname: plan_name,
                    pid: plan_id,
                },
                success: function(response) {                    
                    if (response.success) {
                        Swal.fire({
                            title: '方案已加入互比',
                            text: '您可以在方案互比頁面查看已加入的方案。',
                            icon: 'success',
                            showCancelButton: true,
                            confirmButtonText: '前往方案互比',
                            confirmButtonColor: '#79895F',
                            cancelButtonText: '繼續瀏覽'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = '<?= site_url('choices'); ?>';
                            }
                        });
                    } else {
                        //alert(response.message);
                        Swal.fire({
                            title: '加入失敗',
                            text: response.message,
                            icon: 'error',
                            showCancelButton: true,
                            confirmButtonText: '前往方案互比',
                            confirmButtonColor: '#79895F',
                            cancelButtonText: '繼續瀏覽'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.href = '<?= site_url('choices'); ?>';
                            }
                        });
                    }
                }
            });
        });

        // 點擊了解方案內容
        $('.program_content').on('click', function() {
            var plan_id = $(this).data('pid');
            window.location.href = "<?php echo esc_url( home_url( '/single-checkup-plan/' ) ); ?>" + plan_name;
        });
    });
</script>

<?php

// 頁尾
get_footer();

?>