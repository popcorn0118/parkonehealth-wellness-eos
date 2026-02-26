<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package parkonehealth-beauty
 */

get_header();

// 先撈熱門搜尋（所有地方共用）
$search_terms = get_option('popular_search_terms', []);

// 管理員處理刪除邏輯（這段建議放在 get_header() 之後就先執行）
if ( current_user_can('manage_options') ) {
    // 刪除全部熱門搜尋紀錄
    if ( isset($_GET['clear_search']) && $_GET['clear_search'] === '1' ) {
        delete_option('popular_search_terms');

        // 清除快取（for cloudway）
		clear_search_cache_all();

        echo "<script>window.location.href = '" . home_url('/search/') . "';</script>";
        exit;
    }

    // 刪除單一關鍵字
    if ( isset($_GET['delete_keyword']) ) {
        $term_to_delete = sanitize_text_field($_GET['delete_keyword']);
        if ( isset($search_terms[$term_to_delete]) ) {
            unset($search_terms[$term_to_delete]);
            update_option('popular_search_terms', $search_terms);

            // 清除快取（for cloudway）
			clear_search_cache_by_keyword( $term_to_delete );
            
        }
        echo "<script>window.location.href = '" . home_url('/search/') . "';</script>";
        exit;
    }
}


// ------------------------
// 接收查詢參數
// ------------------------
$page_query      = isset($_GET['page-query']) ? $_GET['page-query'] : 1;
$search_query    = isset($_GET['search-query']) ? sanitize_text_field($_GET['search-query']) : '';

// ------------------------
// 設定查詢參數
// ------------------------
$args_column_article_s = array(
    'post_type'      => array('post', 'case', 'faq', 'team', 'page'),
    'post_status'    => 'publish',
    'posts_per_page' => 10,
    'paged'          => $page_query,
    'order'          => 'desc',
    's'              => !empty($search_query) ? $search_query : '',
);

// ------------------------
// 執行查詢（取得文章列表）
// ------------------------
$column_article_list_s = get_posts($args_column_article_s);

// ------------------------
// 用 WP_Query 取得總筆數
// ------------------------
$args_count_only = array_merge($args_column_article_s, [
    'posts_per_page' => 1, // 只需要 1 筆
    'fields'         => 'ids',
]);
$count_query = new WP_Query($args_count_only);
$total_count = $count_query->found_posts;

// ------------------------
// 記錄熱門搜尋關鍵字（有結果才記）
// ------------------------
if ( !empty($search_query) && $total_count > 0 ) {
    if ( isset($search_terms[$search_query]) ) {
        $search_terms[$search_query]++;
    } else {
        $search_terms[$search_query] = 1;
    }
    update_option('popular_search_terms', $search_terms);

    // 清除快取（for cloudway）
	 clear_search_cache_by_keyword( $search_query );
}

// ------------------------
// 取得最大頁數
// ------------------------
$list      = new WP_Query($args_column_article_s);
$max_page  = $list->max_num_pages;
$showPage  = 5; // 每次顯示幾個頁碼
?>



<main class="article-list search-results-page animated-slow animated fadeInUp">

  <!-- 搜尋資訊 -->
  <div class="article-info">
    <?php if($total_count > 0 && !empty($search_query)): ?>
      <div class="total">共有<?php echo $total_count; ?>筆「<?php echo esc_html($search_query); ?>」的搜尋結果</div>
    <?php else: ?>
      <h3 class="no-search-results">您的關鍵字沒有搜尋結果，請輸入其他關鍵字重新搜尋。</h3>
      <!-- 搜尋關鍵字 -->
      <div class="article-search">
        <div class="warp search">
          <form method="get" class="search-query-box" action="<?php echo esc_url( home_url( '/search' ) ); ?>">
            <input type="text" value="<?php echo $search_query; ?>" name="search-query" class="search-query" placeholder="搜尋關鍵字..." required="required"/>
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
      
      
      <?php
        // 顯示熱門關鍵字區塊
        if ( !empty($search_terms) ) :
            arsort($search_terms); // 由大到小排序
            $popular_terms = array_slice($search_terms, 0, 10, true); // 只取前10筆
        ?>
            <div class="warp keyword">
                <h3 class="keyword-title">熱門關鍵字</h3>
                <ul class="keyword-list">
                    <?php foreach ( $popular_terms as $term => $count ) : ?>
                        <li class="item">
                            <a href="<?php echo home_url( '/search/?search-query=' . urlencode( $term ) ); ?>">
                                <?php echo esc_html( $term ); ?>
                            </a>
                            <!-- <small>(<?php //echo $count; ?>)</small> -->

                            <?php if ( current_user_can('manage_options') ): ?>
                                <a href="<?php echo esc_url( add_query_arg('delete_keyword', urlencode($term), home_url('/search/')) ); ?>"
                                  onclick="return confirm('確定要刪除「<?php echo esc_js($term); ?>」這個關鍵字？');"
                                  style="color: #cc0000; margin-left: 8px; font-size: 13px;">
                                    ✖
                                </a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?php if ( current_user_can('manage_options') ) : ?>
                    <p style="margin-top: 1em;">
                        <a href="<?php echo esc_url( add_query_arg('clear_search', '1', home_url('/search/')) ); ?>"
                          onclick="return confirm('確定要清除所有熱門搜尋紀錄？');"
                          style="color: red; font-weight: bold;">
                          清除所有熱門搜尋紀錄（只有管理員可見）
                        </a>
                    </p>
                <?php endif; ?>
            </div>
        <?php endif; ?>



    <?php endif; ?><!-- 搜尋資訊 -->
  </div>
      


  <!-- 文章列表 -->
  <div class="article-cont">     
    <?php if($total_count > 0 && !empty($search_query)): ?>
      
      <ul class="list">
        <?php 
          // 文章列表
          foreach ($column_article_list_s as $item):
              $title = $item->post_title;
              $name = $item->post_name;
              $type = $item->post_type;
              $id = $item->ID;
              $date = strtotime($item->post_date);
              $desc = !empty($item->post_excerpt) ? $item->post_excerpt : $item->post_content;
              $category = get_the_terms($id, 'case-type'); // get category
              $tags = get_the_terms($id, 'case-tag'); // get case-tag
              $img = wp_get_attachment_image_src( get_post_thumbnail_id( $item ), 'full' );

              if (!in_array($id, [3089])):
        ?>
          <li class="item">
            <div class="cont">
              <h3 class="title">
                <?php echo $title; ?>
              </h3>
              <div class="desc">
                <?php echo strip_tags($desc); ?>
              </div>
              <a class="read-btn btn" href="<?php echo esc_url( home_url( $type . '/' . $name ) ); ?>">了解更多</a>
            </div>
          </li>
        <?php 
            endif;
          endforeach;
        ?>

      </ul>


      <!-- 頁碼 -->
      <?php 
          $query_str = '&search-query=' . $search_query;
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
                          echo '<a href="'.get_page_link().'?page-query='.($page_query - 1) . $query_str . '"><svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2036 0.868601C10.0678 1.0038 9.99097 1.18723 9.98985 1.37886C9.98872 1.5705 10.0634 1.75481 10.1976 1.8916L13.5766 5.2766H0.71664C0.525022 5.2766 0.341251 5.35272 0.205756 5.48822C0.0702612 5.62371 -0.00585938 5.80748 -0.00585938 5.9991C-0.00585938 6.19072 0.0702612 6.37449 0.205756 6.50999C0.341251 6.64548 0.525022 6.7216 0.71664 6.7216H13.5706L10.1916 10.1066C10.0586 10.244 9.98475 10.4281 9.98587 10.6193C9.98699 10.8106 10.063 10.9938 10.1976 11.1296C10.3334 11.2635 10.5167 11.338 10.7074 11.3369C10.898 11.3357 11.0805 11.259 11.2146 11.1236L15.7936 6.5126C15.8562 6.44562 15.9069 6.36853 15.9436 6.2846C15.9814 6.19681 16.0004 6.10214 15.9996 6.0066C15.9997 5.8175 15.9258 5.63589 15.7936 5.5006L11.2146 0.8856C11.1499 0.817265 11.0721 0.762538 10.9859 0.72462C10.8997 0.686702 10.8069 0.666356 10.7127 0.664773C10.6186 0.66319 10.5251 0.680402 10.4376 0.715401C10.3502 0.750399 10.2707 0.802482 10.2036 0.868601Z" fill="#66AF8F"/></svg></a>';
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
                          echo '<a href="'.get_page_link().'?page-query='.($page_query + 1) . $query_str . '"><svg width="16" height="12" viewBox="0 0 16 12" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.2036 0.868601C10.0678 1.0038 9.99097 1.18723 9.98985 1.37886C9.98872 1.5705 10.0634 1.75481 10.1976 1.8916L13.5766 5.2766H0.71664C0.525022 5.2766 0.341251 5.35272 0.205756 5.48822C0.0702612 5.62371 -0.00585938 5.80748 -0.00585938 5.9991C-0.00585938 6.19072 0.0702612 6.37449 0.205756 6.50999C0.341251 6.64548 0.525022 6.7216 0.71664 6.7216H13.5706L10.1916 10.1066C10.0586 10.244 9.98475 10.4281 9.98587 10.6193C9.98699 10.8106 10.063 10.9938 10.1976 11.1296C10.3334 11.2635 10.5167 11.338 10.7074 11.3369C10.898 11.3357 11.0805 11.259 11.2146 11.1236L15.7936 6.5126C15.8562 6.44562 15.9069 6.36853 15.9436 6.2846C15.9814 6.19681 16.0004 6.10214 15.9996 6.0066C15.9997 5.8175 15.9258 5.63589 15.7936 5.5006L11.2146 0.8856C11.1499 0.817265 11.0721 0.762538 10.9859 0.72462C10.8997 0.686702 10.8069 0.666356 10.7127 0.664773C10.6186 0.66319 10.5251 0.680402 10.4376 0.715401C10.3502 0.750399 10.2707 0.802482 10.2036 0.868601Z" fill="#66AF8F"/></svg></a>';
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
