<?php
// 部位檢索內頁
// 導覽列
get_header();

$post_id = get_the_ID(); // 取得文章 ID
$post_title = get_the_title($post_id); // 取得文章標題
$post_excerpt = get_the_excerpt();

// 取得文章精選圖片
$thumbnail = get_the_post_thumbnail_url($post_id, 'full');
?>



<!-- 內容開始 -->
<main class="checkup-body-parts-single animated-slow animated fadeInUp">
    <div class="breadcrumbs">
        <div class="cont">
        <a href="javascript:void(0);" class="back-btn" data-fallback-url="<?php echo esc_url( home_url( '/parts-search/' ) ); ?>">返回上頁</a>
            <span class="line"></span>
            <span>部位檢所</span>
            <span>/</span>
            <span><?php echo $post_title; ?></span>
        </div>
    </div>

    <div class="checkup-body-parts-singles-header">
        <div class="cont">
            <div class="info">
                <div class="cont">
                    <h1 class="title"><?php echo $post_title; ?></h1>
                    <div class="excerpt"><?php echo $post_excerpt; ?></div>
                </div>
                
                <?php
                    $suggestion = get_field('suggestion', $post_id);
                ?>
                <div class="suggestion">
                    <?php if(!empty($suggestion)): ?>
                        <div class="warp">
                            <h3 class="title">健檢建議</h3>
                            <div class="desc">
                                <?php echo $suggestion;  ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
           
            <div class="img">
                <img src="<?php echo $thumbnail; ?>" alt="<?php echo $post_title; ?>"/>
            </div>

        </div>
    </div>
    
    <!-- Elementor 內容 -->
    <div class="checkup-body-parts-single-cont">
        
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



     <!-- 相關方案 -->
     <?php
        $related_plans = get_field('related_plans', $post_id);
        $plans_bg = get_field('hottest_plans_bg', 'option');
        // error_log(print_r($hottest_plans, true));	
        $count = 3;	// 只取前三筆
        $plans = array();
        for ($i = 0; $i < $count; $i++) {
            $plan = array();
            $_title = $related_plans[$i]['plan']->post_title;
            $plan['tag_name'] = $related_plans[$i]['tag_name'];
            $plan['title'] = $_title;
            $plan['detail'] = array();
            $plan['id'] = $related_plans[$i]['plan']->ID;

            // 取得acf資料
            $checkup_price_gender_and_items = get_field("checkup_price_gender_and_items", $related_plans[$i]['plan']->ID);
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
    <div class="checkup-body-parts-single-plans">
        <div class="cont">
            <h2 class="title">相關方案</h2>
            
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
                    <button type="button" data-pname="<?= $plan['title']; ?>" data-pid="<?= $plan['id']; ?>" class="add_to_plans_compare">加入方案互比</button>
                    <div class="img" style="background-image: url(<?php echo $bg_url; ?>);"></div>
                </div>
            <?php
            endforeach;
            ?>
        </div>
            
        </div>
    </div>


</main>

<?php

// 頁尾
get_footer();

?>