<?php
/**
 * astra-child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package astra-child
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_ASTRA_CHILD_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {
	// 先取消 WordPress 預設的 jQuery
	wp_deregister_script('jquery');

	// 改用 jQuery 3.7.1
	wp_register_script(
		'jquery',
		get_stylesheet_directory_uri() . '/assets/js/jquery-3.7.1.min.js',
		array(), // 無依賴
		'3.7.1',
		true     // 放到底部 footer
	);
	wp_enqueue_script('jquery');


	//css
	wp_enqueue_style( 'astra-child-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_ASTRA_CHILD_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );









// use function WPvividGuzzleHttp\json_decode;

add_filter('manage_checkup-plan_posts_columns', 'my_checkup_plan_column');
function my_checkup_plan_column($columns)
{
	$columns['check_sex'] = '性別';
	$columns['plan_price'] = '價格';
	return $columns;
}

add_action('manage_checkup-plan_posts_custom_column', 'my_checkup_plan_column_content', 30, 3);
function my_checkup_plan_column_content($column_name, $post_id)
{
	switch ($column_name) {
		case 'check_sex':
			$check_sex = get_post_meta($post_id, 'check_sex', true);
			echo "<p>$check_sex</p>";
			break;

		case 'plan_price':
			$plan_price = get_field('plan_price', $post_id);
			echo "<p>$plan_price</p>";
			break;
		default:
			break;
	}
}

add_shortcode('check_plan', 'display_check_plan');
function display_check_plan()
{
	$check_plan_table = get_option('check_table');
	$checkup_plan_id_list = get_option('_checkup_plan_id_list');
	$multi_select_rule = array();
	ob_start();
?>
	<style>
		table {
			border-collapse: collapse;
			width: 100%;
		}

		th,
		td {
			border: 1px solid #ddd;
			padding: 8px;
			text-align: center;
		}

		th {
			background-color: #f2f2f2;
		}

		tfoot {
			background-color: #f2f2f2;
		}

		button {
			background-color: #4CAF50;
			color: white;
			padding: 8px 20px;
			border: none;
			cursor: pointer;
			border-radius: 5px;
		}

		button:hover {
			background-color: #45a049;
		}
	</style>
	<table class="table">
		<?php
		$has_rowspan = array();	// 用來記錄哪些欄位有 rowspan
		foreach ($check_plan_table as $y => $row) {
			switch ($y) {
				case 0:
		?>
					<thead>
						<tr>
							<?php
							$has_colspan = 0;
							foreach ($row as $x => $col) {
								// 如果有 colspan 就跳過
								if ($has_colspan > 0) {
									$has_colspan--;
									continue;
								}

								// 如果有 rowspan 就跳過
								// if (isset($has_rowspan[$x]['count']) && $has_rowspan[$x]['count'] > 0) {
								// 	$has_rowspan[$x]['count']--;
								// 	continue;
								// }

								// 用 regex 判斷結尾是否為 colspan 或 rowspan, 並取出數字
								$pattern = '/\|([c|r])s(\d+)$/';
								$reg_result = preg_match($pattern, $col, $matches);
								if ($reg_result) {
									$_col = preg_replace($pattern, '', $col);
									switch ($matches[1]) {
										case 'c':
											$colspan = $matches[2];
											$has_colspan = $colspan - 1;
							?>
											<th colspan="<?php echo $colspan; ?>"><?php echo $_col; ?></th>
										<?php
											break;
										case 'r':
											$rowspan = $matches[2];
											$has_rowspan[$x]['rowspan'] = $rowspan;
											$has_rowspan[$x]['count'] = $rowspan - 1;
										?>
											<th rowspan="<?php echo $rowspan; ?>"><?php echo $_col; ?></th>
									<?php
											break;
									}
								} else {
									?>
									<th><?php echo $col; ?></th>
							<?php
								}
							}
							?>
						</tr>
					</thead>
				<?php
					break;

				default:
				?>
					<tr>
						<?php

						foreach ($row as $x => $col) {
							// 如果有 rowspan 就跳過
							if (isset($has_rowspan[$x]['count']) && $has_rowspan[$x]['count'] > 0 && $y != $has_rowspan[$x]['start_y']) {
								$has_rowspan[$x]['count']--;
								continue;
							}

							// 用 regex 判斷結尾是否為 colspan 或 rowspan, 並取出數字
							$pattern = '/\|([c|r])s(\d+)$/';
							$reg_result = preg_match($pattern, $col, $matches);
							// var_dump($reg_result);
							if ($reg_result) {
								$_col = preg_replace($pattern, '', $col);
								switch ($matches[1]) {
									case 'c':
										$colspan = $matches[2];
										$has_colspan = $colspan - 1;
						?>
										<td colspan="<?php echo $colspan; ?>"><?php echo $_col; ?></td>
									<?php
										break;
									case 'r':
										$rowspan = $matches[2];
										$has_rowspan[$x]['rowspan'] = $rowspan;
										$has_rowspan[$x]['count'] = $rowspan - 1;
										$has_rowspan[$x]['start_y'] = $y;
									?>
										<td rowspan="<?php echo $rowspan; ?>"><?php echo $_col; ?></td>
									<?php
										break;
								}
							} else {
								// 處理多選一的項目
								if (strpos($col, ':') !== false) {
									$col = explode(':', $col);
									$_col = $col[0];
									$reg_pattern = '/(.+)(\d+)$/';
									$reg_result = preg_match($reg_pattern, $col[1], $matches);
									$class = $matches[1];
									$limit = $matches[2];
									if (!isset($multi_select_rule[$class])) {
										$multi_select_rule[$class] = $limit;
									}
									?>
									<td><input type='checkbox' class="<?= $class; ?>"><?php echo $_col; ?></td>
								<?php
								} else {
								?>
									<td><?php echo $col; ?></td>
						<?php
								}
							}
						}
						?>
					</tr>
				<?php
					break;
			}
		}

		echo "<tfoot><tr>";
		foreach ($checkup_plan_id_list as $checkup_plan_id) {
			if ($checkup_plan_id != 0) {
				?>
				<td>
					<button class="button" data-cpid="<?= $checkup_plan_id; ?>">選擇此方案</button>
				</td>
			<?php
			} else {
			?>
				<td></td>
		<?php
			}
		}
		echo "</tr></tfoot>";
		?>
	</table>
	<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
	<script>
		var multi_select_rule = <?= json_encode($multi_select_rule); ?>;
		document.addEventListener('DOMContentLoaded', function() {
			jQuery(document).ready(function($) {
				$("input").change(function() {
					var class_name = $(this).attr('class');
					var checked = $("." + class_name + ":checked").length;
					if (checked > multi_select_rule[class_name]) {
						alert("最多只能選擇 " + multi_select_rule[class_name] + " 項");
						$(this).prop('checked', false);
					}
				});
			});
		});
	</script>
<?php
	return ob_get_clean();
}

add_shortcode('upload_csv', 'upload_csv');
function upload_csv()
{
	$ajax_url = admin_url('admin-ajax.php');
	ob_start();
?>
	<form action="<?= $ajax_url; ?>" method="post" enctype="multipart/form-data" id="upload_csv_form">
		<input type="file" name="csv_file" accept=".csv">
		<input type="submit" value="上傳">
	</form>
	<!-- <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script> -->
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			jQuery(document).ready(function($) {
				$('#upload_csv_form').submit(function(e) {
					e.preventDefault();

					if ($('input[type="file"]').val() == '') {
						alert('請選擇檔案');
						return;
					}

					var formData = new FormData(this);
					formData.append('action', 'import_csv');
					$.ajax({
						url: $(this).attr('action'),
						type: $(this).attr('method'),
						data: formData,
						processData: false,
						contentType: false,
						dataType: 'json',
						cache: false,
						async: false,
						success: function(response) {
							console.log(response);
							alert('上傳成功');
						}
					});
				});
			});
		});
	</script>
<?php
	return ob_get_clean();
}

function getcsv($csv_file)
{
	$content = file_get_contents($csv_file);
	// 將內容轉換成 utf-8
	$content = mb_convert_encoding($content, 'UTF-8', 'BIG-5');

	// 用regex 將 \n 取代成 <br>, 但 \r\n 不要
	$content = preg_replace('/(?<!\r)\n/', '<br>', $content);
	// 寫入暫存檔
	file_put_contents("temp.csv", $content);

	// $temp_content = file_get_contents("temp.csv");
	// 讀取 csv 檔
	$csv = array_map('str_getcsv', file('temp.csv'));
	// 刪除暫存檔
	// unlink("temp.csv");
	return $csv;
}

add_action('wp_ajax_import_csv', 'import_csv');
function import_csv()
{
	$csv_file = $_FILES['csv_file']['tmp_name'];
	$csv = getcsv($csv_file);

	$checkup_plan_id_table = array();   // 對應x的位置
	$checkup_plan_item_rel_table = array();
	// $check_table = array(); // 將 csv 轉換成 2d array table

	$_check_item_id_list = array(0, 0);
	// $checkup_plan_id_table 的資料結構為
	// $checkup_plan_id_table[$x] = [
	//     'checkup_plan_post_id' => $post_id,
	//     'info' => [
	//          'gender' => $male,
	//			'checkup_plan_price' => 1000,
	//			'checkup_item_list' => [1, 2, 3, 4], // 健檢項目的 post_id => checkup-item
	//      ],
	// ];
	foreach ($csv as $y => $row) {
		switch (true) {
			case $y == 0:
				// 顯示頁面表格表頭
				// $check_table[$y] = $row;

				$_post_id = 0;
				// 處理健檢專案
				foreach ($row as $x => $col) {
					if ($x >= 4) {
						$checkup_plan_id_table[$x] = array();
						//查詢是否已有相同的專案存在, posttype = checkup-plan
						$args = array(
							's' => $col,
							'post_type' => 'checkup-plan',
							'post_status' => 'publish',
							'posts_per_page' => 1,
						);
						$query = new WP_Query($args);
						if ($query->have_posts()) {
							while ($query->have_posts()) {
								$query->the_post();
								$_post_id = get_the_ID();
							}
						} else {
							// 新增健檢專案
							$_post_id = wp_insert_post(array(
								'post_title' => $col,
								'post_type' => 'checkup-plan',
								'post_status' => 'publish',
							));

							// 更新 post cache
							// wp_cache_delete($post_id, 'posts');
						}
						$checkup_plan_id_table[$x] = ["checkup_plan_post_id" => $_post_id];
					}
				}
				break;

			// 處理性別
			case $y == 1:
				// $check_table[$y] = $_ary_x;
				$_gender = "";
				foreach ($row as $x => $col) {
					if ($x >= 4) {
						$gender = $col;
						$checkup_plan_id_table[$x]["info"] = ["gender" => $gender];
					}
				}
				break;

			// 處理價格
			case $y == 2:
				// $_price = 0;
				foreach ($row as $x => $col) {
					if ($x >= 4) {
						// 將內容不是數字的部分去掉
						$pattern = '/\D/';
						$price = preg_replace($pattern, '', $col);
						$checkup_plan_id_table[$x]["info"]["checkup_plan_price"] = $price;
					}
				}
				break;

			// 處理健檢項目
			case $y >= 3:
				$_ary_x_content_line = array();
				$_checkup_item_type = "";

				// 處理健檢項目
				$_checkup_item_term_id = 0;
				$checkup_item_id = 0;
				foreach ($row as $x => $col) {
					switch ($x) {
						case 0:
							// $_check_up_no = $col;
							array_push($_ary_x_content_line, $col);
							break;
						case 1:
							// 處理健檢項目的類型
							$_col = explode('|', $col);
							$_checkup_item_type = $_col[0];
							// 查詢是否已有相同的健檢項目存在 taxonomy = health_checkup_set
							$term = term_exists($_checkup_item_type, 'health_checkup_set');
							if (!$term) {
								$term = wp_insert_term($_checkup_item_type, 'health_checkup_set');
								$_checkup_item_term_id = $term->term_id;
							} else {
								$_checkup_item_term_id = $term['term_id'];
							}
							array_push($_ary_x_content_line, $_checkup_item_type);
							break;
						case 2:
							// 處理健檢項目
							$_check_item_name = $col;
							$_check_item_desc = $row[3];

							// 檢查是否有健檢項目
							$args = array(
								's' => $_check_item_name,
								'post_type' => 'checkup-item',
								'post_status' => 'publish',
								'posts_per_page' => 1,
							);
							$checkup_item_query = new WP_Query($args);
							if ($checkup_item_query->have_posts()) {
								// while ($checkup_item_query->have_posts()) {
								$checkup_item_query->the_post();
								$checkup_item_id = get_the_ID();
								// }
							} else {
								// 新增健檢項目
								$checkup_item_id = wp_insert_post(array(
									'post_title' => $_check_item_name,
									'post_type' => 'checkup-item',
									'post_status' => 'publish',
									'post_content' => $_check_item_desc,
								));
								// 更新 post cache								
								// error_log("checkup_item_id: ".print_r($checkup_item_id, true));
							}
							array_push($_ary_x_content_line, $_check_item_name);
							// 將健檢項目與健檢項目類型關聯
							// error_log("_checkup_item_term_id: ".print_r($_checkup_item_term_id, true));
							// error_log("checkup_item_id: ".print_r($checkup_item_id, true));							
							wp_set_post_terms($checkup_item_id, $_checkup_item_term_id, 'health_checkup_set');
							break;

						// 處理健檢項目與健檢專案的關聯
						case $x >= 4:
							if ($col != "") {
								$checkup_plan_id_table[$x]["info"]["checkup_item_list"][] = $checkup_item_id;
							}

							break;

							// default:
							// $checkup_plan_id_table[$x]["info"]["checkup_item_list"][] = $col;
							// break;
					}
				}
				break;
		}
	}
	// error_log("3_checkup_plan_id_table: " . var_export($checkup_plan_id_table, true));

	// 整理要 acf 更新的資料
	$acf_data_ary = [];
	foreach ($checkup_plan_id_table as $idx => $check_plan) {
		// error_log("check_plan: ".var_export($check_plan, true));
		$_check_plan_post_id = $check_plan["checkup_plan_post_id"];
		$_gender = "";
		if ($check_plan["info"]["gender"] == "男") {
			$_gender = "male";
		} else {
			$_gender = "female";
		}

		$_info = [
			'gender' => $_gender,
			'price' => $check_plan["info"]["checkup_plan_price"],
			'checkup_item_list' => $check_plan["info"]["checkup_item_list"],
		];

		$acf_data_ary[$_check_plan_post_id][] = $_info;
	}

	// acf 更新
	foreach ($acf_data_ary as $_post_id => $a_data) {
		update_field("checkup_price_gender_and_items", $a_data, $_post_id);
	}

	// update_option('_check_item_id_list', $_check_item_id_list);

	// foreach ($checkup_plan_item_rel_table as $checkup_plan_id => $checkup_item_ids) {
	// 	update_post_meta($checkup_plan_id, '健檢項目', $checkup_item_ids);
	// }

	// // 先把 $check_table 存在 option 裡, 之後要換到 postmeta
	// update_option('check_table', $check_table);

	return wp_send_json_success();
}

// 取得方案整合資訊, 整合方案名稱, 價格, 性別
function get_plan_info($plan_name)
{
	$args = array(
		's' => $plan_name,
		'post_type' => 'checkup-plan',
		'post_status' => 'publish',
		'posts_per_page' => 0,
	);

	$plan_info = array(
		'plan_name' => $plan_name,
		'info' => array(),
		'tags' => array(),
		'checkup_devices' => array(),
	);

	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$plan_id = get_the_ID();
		}
	}

	return $plan_info;
}

// 透過 checkup-plan id 取得方案全部資訊
function get_plan_info_by_id($pid)
{
	$plan_info = array(
		'plan_id' => $pid,
		'plan_name' => '',
		'content' => '',
		'thumbnail' => '',
		'info' => array(),
		'checkup_parts' => array(),
		'checkup_devices' => array(),
		'check_item_union_list' => array(),
	);

	if ($pid) {
		$plan_info['plan_name'] = get_the_title($pid);
		$plan_info['content'] = get_the_content($pid);
		$plan_info['thumbnail'] = get_the_post_thumbnail_url($pid, 'full');
	}

	$checkup_price_gender_and_items = get_field('checkup_price_gender_and_items', $pid);
	foreach ($checkup_price_gender_and_items as $checkup_price_gender_and_item) {
		$plan_info['info'][] = $checkup_price_gender_and_item;
		$plan_info['check_item_union_list'] = array_unique(array_merge($plan_info['check_item_union_list'], $checkup_price_gender_and_item['checkup_item_list']));
	}

	$plan_checkup_device_list = get_field('plan_checkup_device_list', $pid);
	foreach ($plan_checkup_device_list as $plan_checkup_device_obj) {
		$plan_info['checkup_devices'][] = $plan_checkup_device_obj;
	}

	$checkup_parts = get_field('checkup_parts', $pid);
	foreach ($checkup_parts as $checkup_part_obj) {
		$plan_info['checkup_parts'][] = $checkup_part_obj;
	}

	return $plan_info;
}

function get_plan_item_list($plan_name)
{
	$args = array(
		's' => $plan_name,
		'post_type' => 'checkup-plan',
		'post_status' => 'publish',
		'posts_per_page' => 0,
	);

	$plan_info = array(
		'plan_name' => $plan_name,
		'check_item_list' => array(),
	);

	$plan_item_list = [];

	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$plan_id = get_the_ID();
			$check_items = get_field('健檢項目', $plan_id);
			// error_log("健檢項目 {$plan_name}: ".print_r($check_items, true));
			foreach ($check_items as $check_item) {
				$check_item_post = get_post($check_item);
				if (!in_array($check_item, $plan_item_list)) {
					// $plan_item_list[$check_item] = $check_item_post->post_title;
					$plan_item_list[] = $check_item;
				}
			}
			// $check_item_id = $check_item->ID;
			// 	$check_item_title = $check_item->post_title;
			// 	$check_item_desc = get_field('checkup_item_desc', $check_item_id);
			// 	$check_item_type = get_the_terms($check_item_id, 'health_checkup_set');
			// 	$check_item_type = $check_item_type[0]->name;
			// 	$plan_info['check_item_list'][] = array(
			// 		'check_item_id' => $check_item_id,
			// 		'check_item_title' => $check_item_title,
			// 		'check_item_desc' => $check_item_desc,
			// 		'check_item_type' => $check_item_type,
			// 	);
			// }
		}
	}
	// error_log("整合健檢項目 {$plan_name}: ".print_r($plan_item_list, true));
	return $plan_item_list;
}

add_shortcode('get_hottest_plan', 'get_hottest_plan');
function get_hottest_plan()
{
	ob_start();
	$hottest_plans = get_field('hottest_plans', 'option');
	// error_log(print_r($hottest_plans, true));	
	$count = 3;	// 只取前三筆
	$plans = array();
	for ($i = 0; $i < $count; $i++) {
		$plan = array();
		$_title = $hottest_plans[$i]['plan']->post_title;
		$plan['tag_name'] = $hottest_plans[$i]['tag_name'];
		$plan['title'] = $_title;
		$plan['detail'] = array();
		$plan['id'] = $hottest_plans[$i]['plan']->ID;

		// 取得acf資料
		$checkup_price_gender_and_items = get_field("checkup_price_gender_and_items", $hottest_plans[$i]['plan']->ID);
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

	wp_enqueue_script('jquery');
?>
	<div class="hottest-plan">
		<?php
		foreach ($plans as $plan):
		?>
			<div class="plan">
				<h4><?= $plan['tag_name']; ?></h4>
				<h3><?= $plan['title']; ?></h3>
				<?php
				foreach ($plan['detail'] as $detail):
				?>
					<p>
					<ul>
						<li><?= $detail['sex']; ?>性</li>
						<li>價格: <?= $detail['price']; ?></li>
						<li>詳細資訊</li>
					</ul>
					</p>
				<?php
				endforeach;
				?>
				<button type="button" data-pname="<?= $plan['title']; ?>" data-pid="<?= $plan['id']; ?>" class="add_to_plans_compare">加入方案互比</button>
			</div>
		<?php
		endforeach;

		?>
	</div>


	<?php

	return ob_get_clean();
}

add_shortcode('show_plan_compare', 'show_plan_compare');
function show_plan_compare()
{
	ob_start();
	wp_enqueue_script('jquery');
	$checkup_set_list = get_health_checkup_set_list(true);
	$compare_plans_item_list = [];
	$_compare_plans_item_list = [];
	if (isset($_COOKIE['plans_compare'])) {
		$compare_plans = $_COOKIE['plans_compare'];
		$compare_plans = json_decode(wp_unslash($compare_plans), true);
		if (is_array($compare_plans)) {
			foreach ($compare_plans as $pid => $pname) {
				$plan_info = get_plan_info_by_id($pid);
				error_log('$plan_info: ' . print_r($plan_info, true));
				$compare_plans_item_list[$pname] = $plan_info;

				$_compare_plans_item_list[$pname] = $plan_info['check_item_union_list'];
			}
		}
	} else {
		$compare_plans = [];
	}

	// 組合成 table
	if (count($compare_plans) > 0):
	?>
		<!-- 做直的比較表 -->
		<hr />
		<table class='table'>
			<tr>
				<th>項目</th>
				<?php
				foreach ($_compare_plans_item_list as $pname => $union_list) {
					echo "<th>{$pname}</th>";
				}
				?>
			</tr>
			<?php
			foreach ($checkup_set_list as $_checkup_plan_id => $checkup_set):
			?>
				<tr>
					<th><?= $checkup_set['title']; ?></th>
					<?php
					foreach ($_compare_plans_item_list as $pname => $union_list) {
						$checkup_set_id = $_checkup_plan_id;
						// $checkup_set_title = $checkup_set['title'];
						if (in_array($checkup_set_id, $union_list)) {
							echo "<td>O</td>";
						} else {
							echo "<td></td>";
						}
					}
					?>
				</tr>
			<?php
			endforeach;
			?>
			<tr>
				<th><button type="button" id="btn_remove_from_compare">移除</button></th>
				<?php
				foreach ($compare_plans as $pid => $pname):
				?>
					<td><input type="checkbox" class="remove_from_compare" value="<?= $pname; ?>"></td>
				<?php
				endforeach;
				?>
			</tr>
			<tr>
				<th>項目</th>
				<?php
				foreach ($_compare_plans_item_list as $pname => $union_list) {
					echo "<th>{$pname}</th>";
				}
				?>
			</tr>
		</table>
		<!-- End of 做直的比較表 -->
	<?php endif; ?>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			jQuery(function($) {
				$('.add_to_plans_compare').click(function() {
					var pname = $(this).data('pname');
					var pid = $(this).data('pid');

					$.ajax({
						url: '<?= admin_url('admin-ajax.php'); ?>',
						type: 'post',
						dataType: 'json',
						cache: false,
						async: false,
						data: {
							action: 'add_to_plans_compare',
							pname: pname,
							pid: pid,
						},
						success: function(response) {
							// console.log(response);
							if (response.success) {
								location.reload();
							} else {
								alert(response.message);
							}
						}
					});
				});

				$("#btn_remove_from_compare").on('mouseup', function() {
					var remove_from_compare = $(".remove_from_compare:checked");
					var plans = [];
					remove_from_compare.each(function() {
						plans.push($(this).val());
					});

					$.ajax({
						url: '<?= admin_url('admin-ajax.php'); ?>',
						type: 'post',
						dataType: 'json',
						cache: false,
						async: false,
						data: {
							action: 'remove_from_compare',
							plans: plans,
						},
						success: function(response) {
							// console.log(response);
							location.reload();
						}
					});
				});
			});
		});
	</script>
<?php
	return ob_get_clean();
}

add_action('wp_ajax_add_to_plans_compare', 'add_to_plans_compare');
add_action('wp_ajax_nopriv_add_to_plans_compare', 'add_to_plans_compare');
function add_to_plans_compare()
{
	$result = ['success' => false, 'message' => ''];
	// error_log("add_to_plans_compare: " . print_r($_POST, true));
	$pname = $_POST['pname'];
	$plan_id = $_POST['pid'];
	$plans_compare = array();
	// 改成讀取 cookie
	if (!isset($_COOKIE['plans_compare'])) {
		setcookie('plans_compare', '', time() + 3600, '/');		
	} else {
		$plans_compare = json_decode(wp_unslash($_COOKIE['plans_compare']), true);
	}


	// if (!is_array($plans_compare)) {
	// 	$result['message'] = '請先選擇方案';
	// 	$plans_compare = array();
	// 	echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	// 	exit();
	// }

	if (in_array($pname, $plans_compare)) {
		$result['message'] = '已經加入過';
		echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		exit();
	}

	if (count($plans_compare) >= 3) {
		$result['message'] = '最多只能比較三筆方案';
		echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		exit();
	}


	$plans_compare[$plan_id] = $pname;
	// 存入 cookie
	$plans_compare = json_encode($plans_compare, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	setcookie('plans_compare', $plans_compare, time() + 3600, '/');
	// update_user_meta($user_id, 'plans_compare', $plans_compare);
	$result['success'] = true;
	$result['message'] = $plans_compare;
	echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	exit();
}

// 取得健檢項目, 依照 term id 排列
function get_health_checkup_set_list($all = false)
{
	if ($all) {
		$arg = array(
			'post_type' => 'checkup-item',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'order' => 'ASC',
		);

		$checkup_item_list = get_posts($arg);
		$health_checkup_set_list = array();
		foreach ($checkup_item_list as $checkup_item) {
			$_id = $checkup_item->ID;
			$_title = $checkup_item->post_title;
			$health_checkup_set_list[$_id] = [
				'title' => $_title,
			];
		}
		return $health_checkup_set_list;
	}
	$health_checkup_set_list = array();
	$health_checkup_item_id_list = get_field('方案互比健檢項目細項清單', 'option');
	foreach ($health_checkup_item_id_list as $health_checkup_item) {
		$_id = $health_checkup_item['方案互比健檢項目細項'];
		$checkup_item = get_post($_id);
		$health_checkup_set_list[$_id] = [
			'title' => $checkup_item->post_title,
		];
	}

	return $health_checkup_set_list;
}

add_shortcode('plan_search_result', 'plan_search_result');
function plan_search_result()
{
	wp_enqueue_script('jquery');
	ob_start();
	// $plans_compare = get_user_meta(get_current_user_id(), 'plans_compare', true);
	$plans_compare = $_COOKIE['plans_compare'];
	$plans_compare = json_decode(wp_unslash($plans_compare), true);
	// var_dump($plans_compare);
	// $compare_plans_item_list = [];
	// if (is_array($plans_compare)) {
	// 	foreach ($plans_compare as $pid => $pname) {
	// 		$plan_info = get_plan_info_by_id($pid);
	// 		$compare_plans_item_list[] = $plan_info['check_item_union_list'];
	// 	}
	// }
	// error_log("compare_plans_item_list: " . print_r($compare_plans_item_list, true));
?>
	<div id="search_result">
		<table id="plan_results">
			<tr id="result_th">
				<th>搜尋結果</th>
			</tr>
			<?php
			if (is_array($plans_compare) && count($plans_compare) > 0) {
				foreach ($plans_compare as $pid => $pname) {										
					$plan_info = get_plan_info_by_id($pid);
					error_log('$plan_info: ' . var_export($plan_info, true));
			?>
					<tr class='compare_row'>
						<td>
							<input type='checkbox' class='add_to_plan_compare' value="<?= $pname; ?>" data-pid='<?= $pid; ?>'>							
							<label><?= $pname; ?></label>
							<span>[互比方案]</span>
							<?php 
							foreach($plan_info['info'] as $info): 
								$_gender = '';
								if($info['gender'] == 'female') {
									$_gender = '女性';
								} else if ($info['gender'] == 'male') {
									$_gender = '男性';
								}
								?>
								
								<label><?= $_gender; ?></label>
								<label>價格: <?= $info['price']; ?></label>
							<?php endforeach; ?>
						</td>
					</tr>
			<?php
				}
			}
			?>
			<tr>
				<td><button type='button' id='btn_add_to_compare'>方案互比</button></td>
			</tr>
		</table>
	</div>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			jQuery(function($) {
				let form = $("form.wpcf7-form.init");
				$("#btn_search").on('click', function() {
					$.ajax({
						url: '<?= admin_url('admin-ajax.php'); ?>',
						type: 'post',
						dataType: 'json',
						cache: false,
						async: false,
						data: {
							action: 'get_plan_search_result',
							data: form.serialize(),
						},
						success: function(response) {
							// console.log(response);
							$(".result_row").remove();
							for (const key in response) {
								if (response.hasOwnProperty(key)) {
									const plans = response[key];
									let exists_plan_compare = $('.add_to_plan_compare[data-pid="' + plans["plan_id"] + '"]');
									if (exists_plan_compare.length > 0) {											
										return;
									}
									// console.log(key);
									let tr = $("<tr class='result_row'></tr>");
									let td = $("<td></td>");
									let content_text = "<input type='checkbox' class='add_to_plan_compare' value=" + key + " data-pid='" + plans["plan_id"] + "'><label>" + key + "</label><span>[最多人選擇]</span>";
									console.log(plans);

									plans["info"].forEach(
										(plan, index) => {
											var _gender = '';
											if(plan["gender"] == 'female') { _gender = '女性'; }
											else if (plan["gender"] == 'male') { _gender = '男性'; }
											content_text += "<label>" + _gender + "</label><label>價格: " + plan["price"] + "</label>";
										});
									td.html(content_text);
									tr.append(td);
									$("#result_th").after(tr);
								}
							}
						}
					});
				});

				$("#btn_add_to_compare").on('mouseup', function() {
					// console.log($(".add_to_plan_compare"));
					let add_to_plan_compare = $(".add_to_plan_compare");

					// 沒有選擇方案
					if (add_to_plan_compare.length == 0) {
						alert('請選擇方案');
						return;
					}
					console.log(add_to_plan_compare.length);
					// 最多3筆
					if (add_to_plan_compare.length > 3) {
						alert('最多只能比較三筆方案');
						return;
					}

					// 刪除原本的方案互比
					$.ajax({
						url: '<?= admin_url('admin-ajax.php'); ?>',
						type: 'post',
						dataType: 'json',
						cache: false,
						async: false,
						data: {
							action: 'remove_plan_compare',
						},
						success: function(response) {}
					});

					add_to_plan_compare.each(function() {
						if ($(this).prop('checked')) {
							let pname = $(this).val();
							// console.log(pname);
							$.ajax({
								url: '<?= admin_url('admin-ajax.php'); ?>',
								type: 'post',
								dataType: 'json',
								cache: false,
								async: false,
								data: {
									action: 'add_to_plans_compare',
									pname: pname,
									pid: $(this).data('pid'),
								},
								success: function(response) {
									// console.log(response);
									location.href = '<?= site_url('search_plan'); ?>';
								}
							});
						}
					});
				});
			});
		});
	</script>
	<?php
	return ob_get_clean();
}

add_action('wp_ajax_get_plan_search_result', 'get_plan_search_result');
add_action('wp_ajax_nopriv_get_plan_search_result', 'get_plan_search_result');
function get_plan_search_result()
{
	$postdata = array();
	parse_str($_POST['data'], $postdata);
	error_log(var_export($postdata, true));
	// 處理性別
	$gender = "";
	foreach($postdata['gender'] as $_gender){
		if($_gender == '女生'){
			$gender.= "'female',";
		} else if ($_gender == '男生'){
			$gender.= "'male',";
		}
	}
	$gender = rtrim($gender, ',');	

	// 處理搜尋關鍵字
	$keywords = array();
	$parts = $postdata['parts_of_body'];
	error_log(var_export($parts, true));
	foreach ($parts as $part) {
		// 只取"｜"前面的部分
		$_part = explode('｜', $part);
		$keywords[] = $_part[0];
	}

	$plan_id_list = array();

	global $wpdb;
	// 搜尋方案 - 性別
	$table = $wpdb->prefix . 'postmeta';
	$sql = "SELECT post_id FROM `{$table}` WHERE `meta_key` LIKE 'checkup_price_gender_and_items_%_gender' AND `meta_value` IN ({$gender});";
	$gender_post_id_list = $wpdb->get_col($sql);
	error_log('gender_post_id_list: '.var_export($gender_post_id_list, true));

	// 搜尋方案 - 價格, 選項有 : <2萬, 2-5萬, 5-11萬; 複選
	$price_choices = $postdata['price'];
	$max = null;
	$min = null;
	foreach($price_choices as $price_choice){
		switch($price_choice){
			case '<2萬':
				$max = 20000;
				$min = 0;
				break;
			case '2-5萬':
				if($min === null){
					$min = 20000;
				}
				$max = 50000;
				break;
			case '5-11萬':
				if($min === null){
					$min = 50000;
				}
				$max = 110000;
				break;			
		}
	}
	$table = $wpdb->prefix . 'postmeta';
	$sql = "SELECT post_id FROM `{$table}` WHERE `meta_key` LIKE 'checkup_price_gender_and_items_%_price' AND `meta_value` BETWEEN {$min} AND {$max};";
	error_log($sql);
	$price_post_id_list = $wpdb->get_col($sql);
	error_log('price_post_id_list: '.var_export($price_post_id_list, true));

	// 搜尋方案 - 健檢部位
	$checkup_part_id_list = array();
	$checkup_parts = $postdata['parts_of_body'];
	$checkup_part_keywords = "";
	foreach ($checkup_parts as $checkup_part) {
		// 只取"｜"前面的部分
		$_part = explode('｜', $checkup_part);
		$checkup_part_keywords .= "'".$_part[0]."',";
	}
	$checkup_part_keywords = rtrim($checkup_part_keywords, ',');

	$table = $wpdb->prefix . 'posts';
	$sql = "SELECT ID FROM `{$table}` WHERE `post_title` IN ({$checkup_part_keywords}) AND `post_type` = 'checkup_body_parts' AND `post_status` = 'publish';";
	$checkup_part_keywords_id_list = $wpdb->get_col($sql);
	error_log(var_export($checkup_part_keywords_id_list, true));
	$table = $wpdb->prefix . 'postmeta';
	$sql = "SELECT post_id, meta_value FROM `{$table}` WHERE `meta_key` = 'checkup_parts'";
	$_checkup_part_list = $wpdb->get_results($sql);
	error_log('_checkup_part_list: '.var_export($_checkup_part_list, true));
	foreach($_checkup_part_list as $_checkup_part){
		// 解開格式為 a:3:{i:0;s:3:"203";i:1;s:3:"205";i:2;s:3:"202";} 的資料
		// error_log(var_export($_checkup_part, true));
		$__checkup_part_list = unserialize($_checkup_part->meta_value);
		error_log('$_checkup_part->post_id: '.var_export($_checkup_part->post_id, true));
		error_log('__checkup_part_list: '.var_export($__checkup_part_list, true));
		if(is_array($__checkup_part_list)){
			foreach($checkup_part_keywords_id_list as $checkup_part_keywords_id){
				if(in_array($checkup_part_keywords_id, $__checkup_part_list)){
					$checkup_part_id_list[] = $_checkup_part->post_id;
					break;
				}
			}
		}
	}	
	error_log('checkup_part_id_list: '.var_export($checkup_part_id_list, true));
	
	// 找出 $gender_post_id_list, $price_post_id_list, $checkup_part_id_list 的交集
	$plan_id_list = array_intersect($gender_post_id_list, $price_post_id_list, $checkup_part_id_list);
	error_log('plan_id_list: '.var_export($plan_id_list, true));

	$plan_list = array();
	foreach ($plan_id_list as $plan_id) {
		$plan = get_plan_info_by_id($plan_id);
		$plan_list[$plan['plan_name']] = $plan;
	}
	error_log('plan_list: '.var_export($plan_list, true));
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($plan_list, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
}

// // // 啟用健檢專案的 post_tag
// function add_tags_to_checkup_plan_type()
// {
// 	register_taxonomy_for_object_type('post_tag', 'checkup-plan'); // 'custom_post' 是你的 Post Type 名称
// }
// add_action('init', 'add_tags_to_checkup_plan_type');

// ajax 移除全部方案互比
add_action('wp_ajax_remove_plan_compare', 'remove_plan_compare');
add_action('wp_ajax_nopriv_remove_plan_compare', 'remove_plan_compare');
function remove_plan_compare()
{
	delete_user_meta(get_current_user_id(), 'plans_compare');
	// 回傳成功
	exit(json_encode(['success' => true], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
}

// ajax 移除方案互比
add_action('wp_ajax_remove_from_compare', 'remove_from_compare');
add_action('wp_ajax_nopriv_remove_from_compare', 'remove_from_compare');
function remove_from_compare()
{
	$plans = $_POST['plans'];
	$plans_compare = json_decode(wp_unslash($_COOKIE['plans_compare']), true);

	if (is_array($plans_compare)) {
		foreach ($plans as $pname) {
			$pid = array_search($pname, $plans_compare);
			unset($plans_compare[$pid]);
		}
	}
	// 存入 cookie
	$plans_compare = json_encode($plans_compare, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	setcookie('plans_compare', $plans_compare, time() + 3600, '/');
	// 回傳成功
	exit(json_encode(['success' => true], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
}

// 列出檢索部位
add_shortcode('list_of_parts_search', 'list_of_parts_search');
function list_of_parts_search()
{
	ob_start();
	// 取得所有 posttype = body_parts 的 posts, 以上傳時間排序
	$args = array(
		'post_type' => 'checkup_body_parts',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	);
	$query = new WP_Query($args);

	// 只要存 title 與 content 到陣列 parts 裡
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$_title = get_the_title();
			$_content = get_the_content();
			// $parts[get_the_title()] = get_the_content();

	?>
			<div class="part">
				<h3><?= $_title; ?><span class="s_part" data-part="<?= $_title; ?>" style="color: darkred; display: none;">[推薦]</span></h3>
				<!-- <p><?= $_content; ?></p> -->
			</div>
<?php
		}
	}

	$parts_options_list = get_field('parts_options_list', 'option');
	error_log(print_r($parts_options_list, true));
	wp_register_script('static/js/list_of_parts_search.js', get_stylesheet_directory_uri() . '/static/js/list_of_parts_search.js', array('jquery'), '1.0', true);
	wp_localize_script('static/js/list_of_parts_search.js', 'parts_options_list', $parts_options_list);
	wp_enqueue_script('static/js/list_of_parts_search.js');
	return ob_get_clean();
}

?>