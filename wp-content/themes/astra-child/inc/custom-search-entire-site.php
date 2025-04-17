<?php 

/* =================================

  Header的搜尋點開 > 全站搜尋
  "<?php require get_theme_file_path( 'inc/custom-search-entire-site.php' ); ?>"
  "[astra_custom_layout id=1130]"

 * ================================== */

 // 先撈熱門搜尋（所有地方共用）
$search_terms = get_option('popular_search_terms', []);

// 管理員處理刪除邏輯（這段建議放在 get_header() 之後就先執行）
if ( current_user_can('manage_options') ) {
    // 刪除全部熱門搜尋紀錄
    if ( isset($_GET['clear_search']) && $_GET['clear_search'] === '1' ) {
        delete_option('popular_search_terms');
        echo "<script>window.location.href = '" . home_url('/search/') . "';</script>";
        exit;
    }

    // 刪除單一關鍵字
    if ( isset($_GET['delete_keyword']) ) {
        $term_to_delete = sanitize_text_field($_GET['delete_keyword']);
        if ( isset($search_terms[$term_to_delete]) ) {
            unset($search_terms[$term_to_delete]);
            update_option('popular_search_terms', $search_terms);
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
}

// ------------------------
// 取得最大頁數
// ------------------------
$list      = new WP_Query($args_column_article_s);
$max_page  = $list->max_num_pages;
$showPage  = 5; // 每次顯示幾個頁碼
?>
<div class="search-entire-site">
	<div class="cont">
		<div class="ast-container">
			<div class="search-close-btn">✖&nbsp;&nbsp;關閉搜尋</div>
			<!-- 搜尋關鍵字 -->
			<div class="warp search">
				<h3 class="search-title">搜尋</h3>
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
								<small>(<?php echo $count; ?>)</small>

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
		</div>
		
	</div>
</div>

	