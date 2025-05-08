<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package parkonehealth-beauty
 */

get_header();


$category_query  = isset($_GET['category']) ? $_GET['category']: null;
$page_query = isset($_GET['page-query']) ? $_GET['page-query']: 1 ;
$search_query = isset($_GET['search-query']) ? $_GET['search-query']: null ;
$year_query = $_GET['year-query'] ;

$is_category_query = (empty($category_query) || ($category_query == 'all')) ? null : $category_query;

?>

<?php
  //文章列表
  $tax_query = array(
      array(
          'taxonomy' => 'category',
          'field' => 'slug',
          'terms' => $is_category_query,
      )
  );

  $tag_query = isset($_GET['post_tag']) ? $_GET['post_tag'] : null;
  $args_column_article_s = array(
      'post_type' => 'post',
      'post_status' => 'publish',
      'posts_per_page' => 10,
      'paged' => $page_query,
      'order' => 'desc',
      's' => !empty($search_query) ? $search_query : "",
      'tag' => !empty($tag_query) ? $tag_query : '',
      'tax_query' => $is_category_query ? array_replace([], $tax_query, ['relation' => 'AND']) : '',
      'date_query' => array(
          'relation' => 'OR',
          array('year' => (empty($year_query) || ($year_query == 'all')) ? null : $year_query),
      ),
  );
  $column_article_list_s = get_posts($args_column_article_s); //文章列表
  
  $args_count_only = array_merge($args_column_article_s, [
    'posts_per_page' => 1, // 只要1筆就好
    'fields' => 'ids',     // 不需要整篇文章內容，只要 ID
  ]);

  $count_query = new WP_Query($args_count_only);
  $total_count = $count_query->found_posts;
  
  // 取得最大頁碼
  $list = new WP_Query( $args_column_article_s );
  $max_page = $list->max_num_pages;
  //一次顯示n頁頁碼
  $showPage = 5; 
?>




<main class="article-list animated-slow animated fadeInUp">

  <!-- 搜尋 -->
  <div class="article-search">
    
    <!-- 選擇年度 -->
    <div class="warp dropdown-warp year">
      <div class="dropdown-title">
        <?php 
          if (!empty($year_query)) {
            if($year_query == 'all') {
              echo '全部年度';
            } else {
              echo $year_query;
            }
          } else {
            echo '選擇年度';
          }
        ?>
        <div class="dropdown-btn">
          <svg width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8099 6.00244L5.7809 10.5394C5.92307 10.7092 6.00098 10.9235 6.00098 11.1449C6.00098 11.3664 5.92307 11.5807 5.7809 11.7504C5.71605 11.8283 5.63487 11.891 5.5431 11.934C5.45134 11.977 5.35124 11.9993 5.2499 11.9993C5.14856 11.9993 5.04846 11.977 4.9567 11.934C4.86493 11.891 4.78375 11.8283 4.7189 11.7504L0.218901 6.60944C0.0814613 6.44405 0.0044047 6.23681 0.000406265 6.0218C-0.00359217 5.8068 0.0657044 5.59683 0.196899 5.42644L4.7159 0.250444C4.78055 0.172256 4.86166 0.10931 4.95346 0.0661011C5.04525 0.0228925 5.14545 0.000488281 5.2469 0.000488281C5.34835 0.000488281 5.44855 0.0228925 5.54034 0.0661011C5.63214 0.10931 5.71325 0.172256 5.7779 0.250444C5.92007 0.420182 5.99798 0.634533 5.99798 0.855944C5.99798 1.07735 5.92007 1.29171 5.7779 1.46144L1.8099 6.00244Z" fill="#4B4B4B"/></svg>
        </div>
      </div>
      <?php 
        $years = $wpdb->get_col("SELECT DISTINCT YEAR(post_date) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' ORDER BY post_date DESC");
      ?>
        <?php if ($years): ?>
          <div class="dropdown-list">
            <ul>
              <li class="item<?php echo $year_query == 'all' ? ' active' : ''?>">
                <a href="<?php echo esc_url( home_url( 'article?category=' ) ) . $category_query . '&search-query=' . $search_query . '&year-query=all'; ?>">全部年度</a>
              </li>
              <?php foreach ($years as $year): ?>
                  <li class="item<?php echo $year_query == $year ? ' active' : ''?>">
                    <a href="<?php echo esc_url( home_url( 'article?category=' ) ) . $category_query . '&search-query=' . $search_query . '&year-query=' . $year; ?>"><?php echo esc_html($year); ?></a>
                  </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>
    </div>

    <?php
        //文章分類
        $article_categories = get_terms( array( 
            'taxonomy' => 'category',
            'parent'   => 0
        ) );
    ?>
    <div class="warp dropdown-warp category">
      <div class="dropdown-title">
        <?php 
          if (!empty($category_query) && $category_query != 'all') {
            $term = get_term_by('slug', $category_query, 'category');
            echo $term ? $term->name : $category_query; 
          } elseif ($category_query === 'all') {
            echo '全部分類';
          } else {
            echo '選擇分類';
          }
        ?>
        <div class="dropdown-btn">
          <svg width="6" height="12" viewBox="0 0 6 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.8099 6.00244L5.7809 10.5394C5.92307 10.7092 6.00098 10.9235 6.00098 11.1449C6.00098 11.3664 5.92307 11.5807 5.7809 11.7504C5.71605 11.8283 5.63487 11.891 5.5431 11.934C5.45134 11.977 5.35124 11.9993 5.2499 11.9993C5.14856 11.9993 5.04846 11.977 4.9567 11.934C4.86493 11.891 4.78375 11.8283 4.7189 11.7504L0.218901 6.60944C0.0814613 6.44405 0.0044047 6.23681 0.000406265 6.0218C-0.00359217 5.8068 0.0657044 5.59683 0.196899 5.42644L4.7159 0.250444C4.78055 0.172256 4.86166 0.10931 4.95346 0.0661011C5.04525 0.0228925 5.14545 0.000488281 5.2469 0.000488281C5.34835 0.000488281 5.44855 0.0228925 5.54034 0.0661011C5.63214 0.10931 5.71325 0.172256 5.7779 0.250444C5.92007 0.420182 5.99798 0.634533 5.99798 0.855944C5.99798 1.07735 5.92007 1.29171 5.7779 1.46144L1.8099 6.00244Z" fill="#4B4B4B"/></svg>
        </div>
      </div>
      <div class="dropdown-list">
        <ul>
          <li class="item<?php echo $category_query == 'all' ? ' active' : ''?>"><a href="<?php echo esc_url( home_url( 'article?category=all' ) ) . '&search-query=' . $search_query . '&year-query=' . $year_query; ?>">全部分類</a></li>
          <?php foreach ($article_categories as $item): ?>
            <li class="item<?php echo $category_query == $item->name ? ' active' : ''; ?>">
                <a href="<?php echo esc_url( home_url( 'article?category=' ) ) . $item->slug . '&search-query=' . $search_query . '&year-query=' . $year_query; ?>"><?php echo $item->name; ?></a>
            </li>
          <?php endforeach ;?>
        </ul>
      </div>
      
    </div>

    <!-- 搜尋關鍵字 -->
    <div class="warp search">
      <form method="get" class="search-query-box" action="<?php echo esc_url( home_url( '/article' ) ); ?>">
        <input type="text" value="<?php echo $search_query; ?>" name="search-query" class="search-query" placeholder="搜尋關鍵字..." />
        <input type="text" value="<?php echo $category_query; ?>" name="category" class="category" placeholder="搜尋分類" hidden />
        <input type="text" value="<?php echo $year_query; ?>" name="year-query" class="year-query" placeholder="搜尋年份" hidden />
        <button type="submit" class="search-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="24.97" height="24.97" viewBox="0 0 24.97 24.97">
                <g id="Icon_feather-search" data-name="Icon feather-search" transform="translate(-3 -3)">
                    <path id="Path_118" data-name="Path 118" d="M23.477,13.988A9.488,9.488,0,1,1,13.988,4.5,9.488,9.488,0,0,1,23.477,13.988Z" fill="none" stroke="#272727" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
                    <path id="Path_119" data-name="Path 119" d="M30.134,30.134l-5.159-5.159" transform="translate(-4.286 -4.286)" fill="none" stroke="#272727" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"/>
                </g>
            </svg>
        </button>
      </form> 
    </div>
    
  </div>

  <!-- 搜尋資訊 -->
  <div class="article-info">
    <?php
      echo '<div class="total">';
      if (!empty($search_query)) {
          echo '共有' . $total_count . '筆「'. esc_html($search_query) .'」的搜尋結果';
      } else {
          echo '共有' . $total_count . '筆資料';
      }
      echo '</div>';
    ?>

    <?php if(!empty($category_query) || !empty($search_query)):?>
      <a class="clear-all" href="<?php echo esc_url( home_url( 'article' ) ); ?>">清除全部篩選詞</a>
    <?php endif; ?>
  </div>


  <!-- 文章列表 -->
  <div class="article-cont">     
      
    <ul class="list">
      <?php 
        // 文章列表
        foreach ($column_article_list_s as $item):
            $title = $item->post_title;
            $name = $item->post_name;
            $date = strtotime($item->post_date);
            $desc = !empty($item->post_excerpt) ? $item->post_excerpt : $item->post_content;
            $category = get_the_terms($item->ID, 'category'); // get category
            $tags = get_the_terms($item->ID, 'post_tag'); // get post_tag
            $img = wp_get_attachment_image_src( get_post_thumbnail_id( $item ), 'full' );
      ?>
        <li class="item">
          <?php if(!empty($img[0])): ?>
              <div class="img" style="background-image:url(<?php echo $img[0]; ?>)">
                <!-- <img src="<?php //echo $img[0]; ?>" alt=""> -->
              </div>
            <?php endif; ?>
          <div class="cont">
            <div class="meta">
              <div class="date"><?php echo date("Y.m.d", $date); ?></div>
              <span class="line"></span>
              <ul class="categories">
                <?php foreach ($category as $key => $cat):?>
                  <li>
                    <?php echo $cat->name; ?>
                    <!-- <a href="<?php //echo esc_url( home_url( 'article?category=' ) ) . $cat->slug; ?>"><?php //echo $cat->name; ?></a> -->
                  </li>
                  <?php
                      if ($key + 1 != count($category)) {
                          echo '<li>、</li>';
                      }
                  ?>
                <?php endforeach; ?>
              </ul>
              <?php if(!empty($tags)): ?>
                <ul class="tags">
                  <li>、</li>
                  <?php foreach ($tags as $key => $tag):?>
                    <li>
                      <?php echo $tag->name; ?>
                    </li>
                    <?php
                        if ($key + 1 != count($tags)) {
                            echo '<li>、</li>';
                        }
                    ?>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
            </div>
            <h3 class="title">
              <?php echo $title; ?>
            </h3>
            <div class="desc">
              <?php echo strip_tags($desc); ?>
            </div>
            <a class="read-btn btn" href="<?php echo esc_url( home_url( 'article/'.$name ) ); ?>">了解更多
              <svg width="7" height="12" viewBox="0 0 7 12" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M-2.10598e-07 0.980724L1.04108 -1.10361e-07L6.71157 5.34495C6.80297 5.43059 6.87551 5.53244 6.92502 5.64462C6.97452 5.7568 7 5.87711 7 5.99861C7 6.12012 6.97452 6.24042 6.92502 6.3526C6.87551 6.46479 6.80297 6.56663 6.71157 6.65227L1.04108 12L0.000980942 11.0193L5.32314 6L-2.10598e-07 0.980724Z"/>
              </svg>
            </a>
          </div>
        </li>
      <?php endforeach ;?>

    </ul>


    <!-- 頁碼 -->
    <?php 
      if ($total_count != 0):

        $query_str = '&category=' . $category_query . '&search-query=' . $search_query;
    ?>
        <div class="page-number-warp">
            <ul class="page-number">
            <?php if($page_query != 1 && $max_page > 5): ?>
                <!-- 到第一頁 -->
                <li class="first-page page-item">
                    <?php
                      //echo '<a href="'.get_page_link().'?page-query=1' . $query_str . '">&lt;&lt;</a>';
                    ?>
                </li>
            <?php endif; ?>
            <?php if($page_query != 1): ?>
                <!-- 上一頁 -->
                <li class="previous page-item">
                    <?php
                        echo '<a href="'.get_page_link().'?page-query='.($page_query - 1) . $query_str . '"><svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2036 0.868601C10.0678 1.0038 9.99097 1.18723 9.98985 1.37886C9.98872 1.5705 10.0634 1.75481 10.1976 1.8916L13.5766 5.2766H0.71664C0.525022 5.2766 0.341251 5.35272 0.205756 5.48822C0.0702612 5.62371 -0.00585938 5.80748 -0.00585938 5.9991C-0.00585938 6.19072 0.0702612 6.37449 0.205756 6.50999C0.341251 6.64548 0.525022 6.7216 0.71664 6.7216H13.5706L10.1916 10.1066C10.0586 10.244 9.98475 10.4281 9.98587 10.6193C9.98699 10.8106 10.063 10.9938 10.1976 11.1296C10.3334 11.2635 10.5167 11.338 10.7074 11.3369C10.898 11.3357 11.0805 11.259 11.2146 11.1236L15.7936 6.5126C15.8562 6.44562 15.9069 6.36853 15.9436 6.2846C15.9814 6.19681 16.0004 6.10214 15.9996 6.0066C15.9997 5.8175 15.9258 5.63589 15.7936 5.5006L11.2146 0.8856C11.1499 0.817265 11.0721 0.762538 10.9859 0.72462C10.8997 0.686702 10.8069 0.666356 10.7127 0.664773C10.6186 0.66319 10.5251 0.680402 10.4376 0.715401C10.3502 0.750399 10.2707 0.802482 10.2036 0.868601Z"/></svg></a>';
                    ?>
                </li>
            <?php endif; ?>
            <?php
                for ($num = 0; $num < $max_page; $num++) {
                    $active = ($page_query == $num + 1) ? ' active' : '';
                    $pageNumHtml = '<li class="page-item' . $active . '"><a href="'.get_page_link().'?page-query='.($num + 1) . $query_str . '">' . ($num + 1) . '</a></li>';
                    if ($max_page > $showPage) {
                        if ($page_query <= 3) {
                            // 前三頁顯示方式
                            if ($num <= ($showPage - 1)) {
                                echo $pageNumHtml;
                            }
                        } else if ($page_query >= $max_page - 2) {
                            // 中間頁顯示方式
                            if ($num >= ($showPage - 1)) {
                                echo $pageNumHtml;
                            }
                        } else {
                            // 後三頁顯示方式
                            if (($page_query - ($num+1)) <= 2 && ($page_query - ($num+1)) >= -2) {
                                echo $pageNumHtml;
                            }
                        }
                    } else {
                        // 未滿$showPage頁直接顯示全部頁碼
                        echo $pageNumHtml;
                    }
                }
            ?>
            <?php if($page_query != $max_page): ?>
                <!-- 下一頁 -->
                <li class="next page-item">
                    <?php
                        echo '<a href="'.get_page_link().'?page-query='.($page_query + 1) . $query_str . '"><svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2036 0.868601C10.0678 1.0038 9.99097 1.18723 9.98985 1.37886C9.98872 1.5705 10.0634 1.75481 10.1976 1.8916L13.5766 5.2766H0.71664C0.525022 5.2766 0.341251 5.35272 0.205756 5.48822C0.0702612 5.62371 -0.00585938 5.80748 -0.00585938 5.9991C-0.00585938 6.19072 0.0702612 6.37449 0.205756 6.50999C0.341251 6.64548 0.525022 6.7216 0.71664 6.7216H13.5706L10.1916 10.1066C10.0586 10.244 9.98475 10.4281 9.98587 10.6193C9.98699 10.8106 10.063 10.9938 10.1976 11.1296C10.3334 11.2635 10.5167 11.338 10.7074 11.3369C10.898 11.3357 11.0805 11.259 11.2146 11.1236L15.7936 6.5126C15.8562 6.44562 15.9069 6.36853 15.9436 6.2846C15.9814 6.19681 16.0004 6.10214 15.9996 6.0066C15.9997 5.8175 15.9258 5.63589 15.7936 5.5006L11.2146 0.8856C11.1499 0.817265 11.0721 0.762538 10.9859 0.72462C10.8997 0.686702 10.8069 0.666356 10.7127 0.664773C10.6186 0.66319 10.5251 0.680402 10.4376 0.715401C10.3502 0.750399 10.2707 0.802482 10.2036 0.868601Z"/></svg></a>';
                    ?>
                </li>
            <?php endif; ?>
            <?php if($page_query != $max_page && $max_page > 5): ?>
                <!-- 到最後一頁 -->
                <li class="last-page page-item">
                    <?php
                       // echo '<a href="'.get_page_link().'?page-query='.$max_page . $query_str . '">&gt;&gt;</a>';
                    ?>
                </li>
            <?php endif; ?>
            </ul>
        </div>
    <?php endif; ?>

  </div>
    
</main>




<?php
get_footer();
