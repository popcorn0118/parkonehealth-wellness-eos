<?php 

/* =================================

  相關文章
  "<?php require get_theme_file_path( 'inc/custom-related-articles.php' ); ?>"
  "[astra_custom_layout id=2412]"

 * ================================== */


global $post;
$page_id = $post->ID;
$page_type = $post->post_type;
$is_page_type = array(
  'category' => array('post' => 'category', 'case' => 'case-type'),
  'tags' => array('post' => 'post_tag', 'case' => 'case-tag'),
  'list' => array('post' => 'article', 'case' => 'case'),
  'id' => array('post' => 2375, 'case' => 2441),
);


$page_title = $post->post_title;
$parent_title = get_post($is_page_type['id'][$page_type])->post_title;
$category = get_the_terms($page_id, $is_page_type['category'][$page_type]); // get category
$tags = get_the_terms($page_id, $is_page_type['tags'][$page_type]); // get post_tag
$date = strtotime($post->post_date);


?>

<section class="related-articles single animated-slow animated fadeInUp">
      <div class="ast-container">
        <h3 class="title">其他<?php echo $parent_title; ?></h3>
        <ul class="related-articles-ul">
        <?php
            $relevant_args = array(
                'post_type' => $page_type,
                'post_status'		=> 'publish',
                "order"     => "desc",
                'posts_per_page' => -1,
                'posts_per_page' => 3
            );
            
            $relevant_column_article_list = get_posts($relevant_args);
            
        ?>
          <?php 
            // 相關文章
            foreach ($relevant_column_article_list as $item):
                $relevant_title = $item->post_title;
                $relevant_name = $item->post_name;
                $relevant_date = strtotime($item->post_date);
                $relevant_desc = !empty($item->post_excerpt) ? $item->post_excerpt : $item->post_content;
                $relevant_terms_category = get_the_terms($item->ID, $is_page_type['category'][$page_type]); // get category
                $relevant_img = wp_get_attachment_image_src( get_post_thumbnail_id( $item ), 'full' );
            ?>
              <li class="related-articles-li">
                <div class="img" style="background-image: url(<?php echo $relevant_img[0]; ?>);"></div>
                <div class="date"><?php echo date("Y.m.d", $date); ?></div>
                <a class="title" href="<?php echo esc_url( home_url( 'post/'.$relevant_name ) ); ?>">
                  <?php echo $relevant_title; ?>
                </a>
                <div class="meta">
                  <ul class="categories">
                    <?php foreach ($relevant_terms_category as $key => $cat):?>
                      <li>
                        <a href="<?php echo esc_url( home_url( $is_page_type['list'][$page_type] . '?' . $is_page_type['category'][$page_type] . '=' ) ) . $cat->name; ?>"><?php echo $cat->name; ?></a>
                      </li>
                      <?php
                          if ($key + 1 != count($relevant_terms_category)) {
                              echo '<li>、</li>';
                          }
                      ?>
                    <?php endforeach; ?>
                  </ul>
                </div>
                <div class="desc">
                  <?php echo $relevant_desc; ?>
                </div>
              </li>
            <?php endforeach; ?>
        </ul>
      </div>
    </section>