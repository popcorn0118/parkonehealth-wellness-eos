<?php

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
							// console.log(response);
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
	// error_log("import_csv called: ".var_export($_FILES, true));
	// exit();
	$csv_file = $_FILES['csv_file']['tmp_name'];
	$csv = getcsv($csv_file);

	// 將所有已存的方案內容清空, checkup-item, checkup-plan, health_checkup_set 都刪除
	// 刪除 checkup-item
	$checkup_item_args = array(
		'post_type' => 'checkup-item',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);
	$checkup_item_query = new WP_Query($checkup_item_args);
	if ($checkup_item_query->have_posts()) {
		while ($checkup_item_query->have_posts()) {
			$checkup_item_query->the_post();
			$checkup_item_id = get_the_ID();
			wp_delete_post($checkup_item_id, true);
		}
	}

	// 刪除 checkup-plan
	$checkup_plan_args = array(
		'post_type' => 'checkup-plan',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	);

	$checkup_plan_query = new WP_Query($checkup_plan_args);
	if ($checkup_plan_query->have_posts()) {
		while ($checkup_plan_query->have_posts()) {
			$checkup_plan_query->the_post();
			$checkup_plan_id = get_the_ID();
			wp_delete_post($checkup_plan_id, true);
		}
	}

	// 刪除 health_checkup_set 的 term
	$terms = get_terms(array(
		'taxonomy' => 'health_checkup_set',
		'hide_empty' => false,
	));

	foreach ($terms as $term) {
		wp_delete_term($term->term_id, 'health_checkup_set');
	}

	// 開始處理 csv 內容

	$checkup_plan_id_table = array();   // 對應x的位置
	// $checkup_plan_item_rel_table = array();
	// $check_table = array(); // 將 csv 轉換成 2d array table

	// $_check_item_id_list = array(0, 0);
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
								if (is_wp_error($term)) {
									// error_log("建立分類失敗: " . $term->get_error_message());
									$_checkup_item_term_id = 0;
								} else {
									$_checkup_item_term_id = $term['term_id'];
								}
							} else {
								$_checkup_item_term_id = $term['term_id'];
							}
							array_push($_ary_x_content_line, $_checkup_item_type);
							break;
						case 2:
							// 處理健檢項目
							$_check_item_name = $col;
							$_check_item_desc = $row[3];

							// error_log("處理 $col");

							$item_exists = false;
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
								if ($_check_item_name == $checkup_item_query->post->post_title) {
									$item_exists = true;
									$checkup_item_query->the_post();
									$checkup_item_id = get_the_ID();
								}
								// }
							}

							if (!$item_exists) {
								// 新增健檢項目
								$checkup_item_id = wp_insert_post(array(
									'post_title' => $_check_item_name,
									'post_type' => 'checkup-item',
									'post_status' => 'publish',
									'post_content' => $_check_item_desc,
								));
								// 更新 post cache
								// error_log("checkup_item $_check_item_name id: " . print_r($checkup_item_id, true));
							}
							array_push($_ary_x_content_line, $_check_item_name);
							// 將健檢項目與健檢項目類型關聯
							// error_log("_checkup_item_term_id: ".print_r($_checkup_item_term_id, true));
							// error_log("checkup_item_id: ".print_r($checkup_item_id, true));

							// 測試, 上線後要打開
							// break;
							wp_set_post_terms($checkup_item_id, $_checkup_item_term_id, 'health_checkup_set');
							// break;

							// 處理健檢項目與健檢專案的關聯
						case $x >= 4:
							if ($col != "") {
								$_col = explode(':', $col);
								if (count($_col) == 2) {
									// 多選一的項目
									$checkup_plan_id_table[$x]["info"]['checkup_item_multi_select_list'][$_col[1]][] = $checkup_item_id;
								} else {
									// 一般項目
									$checkup_plan_id_table[$x]["info"]["checkup_item_list"][] = $checkup_item_id;
								}
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
			'multi_select' =>  [],
		];
		// 處理 multi select 的項目
		if (isset($check_plan["info"]['checkup_item_multi_select_list'])) {
			foreach ($check_plan["info"]['checkup_item_multi_select_list'] as $ms_key => $ms_items) {
				// 將 multi select 的項目也加入總項目清單
				$_info['multi_select'][] = ['multi_select_item' => $ms_items];
			}
		}

		$acf_data_ary[$_check_plan_post_id][] = $_info;
	}
	error_log("4_acf_data_ary: " . var_export($acf_data_ary, true));

	// 測試, 上線後要打開
	// wp_send_json_error( );

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
		'multi_select_item_list' => array(),
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

	$multi_select_item_list = get_field('multi_select', $pid);
	foreach ($multi_select_item_list as $multi_select_item_obj) {
		$plan_info['multi_select_item_list'][] = $multi_select_item_obj;
	}

	// 取得熱門加選
	$hot_additional_list = get_field('hot_additional_list', $pid);
	foreach ($hot_additional_list as $hot_additional_obj) {
		$plan_info['hot_additional_list'][] = $hot_additional_obj;
	}

	// 取得是否供餐
	$plan_info['enable_breakfast'] = get_field('enable_breakfast', $pid);
	$plan_info['meal_type_display'] = get_field('meal_type_display', $pid);
	$plan_info['meal_plrd'] = get_field('meal_plrd', $pid);

	// 其他
	$plan_info['meal_replacement_and_laxative'] = get_field('meal_replacement_and_laxative', $pid);
	$plan_info['constipate'] = get_field('constipate', $pid);

	error_log("get_plan_info_by_id {$pid}: ".var_export($plan_info, true));
	return $plan_info;
}

// 透過 checkup-plan id 取得方案全部資訊, v2 版，未來要改用這個
function get_plan_info_by_id_v2($pid)
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
	$plan_info['info'] = $checkup_price_gender_and_items;
	foreach ($checkup_price_gender_and_items as $checkup_price_gender_and_item) {
		foreach ($checkup_price_gender_and_item['checkup_item_list'] as $item_id) {
			if (!key_exists($item_id, $plan_info['check_item_union_list'])) {
				$plan_info['check_item_union_list'][$item_id] = $checkup_price_gender_and_item['gender'];
			} else {
				$plan_info['check_item_union_list'][$item_id] = 'unisex';
			}
		}
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
	$plans_bg = get_field('hottest_plans_bg', 'option');
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
	<div class="hottest-plan plan-card">
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
						<!-- <li>詳細資訊</li> -->
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
	<script>
		jQuery(function($) {
			$('.program_content').click(function(e) {
				var pname = $(this).data('pname');
				var pid = $(this).data('pid');
				location.href = '<?= site_url('checkup-plan'); ?>/' + pname;
			});
		});
	</script>

	<?php

	return ob_get_clean();
}

add_shortcode('show_plan_compare', 'show_plan_compare');
function show_plan_compare()
{
	ob_start();
	wp_enqueue_script('jquery');
	$checkup_set_list = get_health_checkup_set_list(true);
	// error_log('$checkup_set_list: ' . var_export($checkup_set_list, true));
	$compare_plans_item_list = [];
	$_compare_plans_item_list = [];
	// if (isset($_COOKIE['plans_compare'])) {
	// 	$compare_plans = $_COOKIE['plans_compare'];
	// 	$compare_plans = json_decode(wp_unslash($compare_plans), true);
	// 	// error_log('$compare_plans: ' . print_r($compare_plans, true));
	// 	if (is_array($compare_plans)) {
	// 		foreach ($compare_plans as $pid => $pname) {
	// 			$plan_info = get_plan_info_by_id($pid);
	// 			error_log("{$pname} plan_info: " . print_r($plan_info, true));
	// 			$compare_plans_item_list[$pname] = $plan_info;

	// 			$_compare_plans_item_list[$pname] = $plan_info['check_item_union_list'];
	// 		}
	// 	}
	// } else {
	// 	$compare_plans = [];
	// }
	// $compare_plans_item_list = [];
	$show_plans = [];
	if (isset($_COOKIE['plans_compare'])) {
		$compare_plans = $_COOKIE['plans_compare'];
		$compare_plans = json_decode(wp_unslash($compare_plans), true);
		if (is_array($compare_plans)) {
			foreach ($compare_plans as $pid => $pname) {
				$plan_info = get_plan_info_by_id($pid);
				// error_log("{$pname} plan_info: " . print_r($plan_info, true));
				$show_plans[] = $plan_info;
				if (isset($plan_info['info'])) {
					foreach ($plan_info['info'] as $info) {
						$gender = ($info['gender'] === 'male') ? '男' : '女';
						$col_key = $pname . '-' . $gender; // 例如 "尊爵菁英-男"
						$compare_plans_item_list[$col_key] = $info['checkup_item_list'];
						$_compare_plans_item_list[$pname] = $plan_info['check_item_union_list'];
					}
				}
			}
		} else {
			$compare_plans = [];
		}
	}

	// error_log('$show_plans: ' . print_r($show_plans, true));


	// 組合成 table
	if (is_array($compare_plans) && count($compare_plans) > 0):
	?>
		<!-- 做直的比較表 -->
		<div id="compare" class="solution-comparison-results">
			<div class="cont">
				<div class="table-wrapper">
					<table class='table responsive-table'>
						<thead>
							<tr>
								<th class="th" rowspan="2">項目</th>
								<?php
								foreach ($_compare_plans_item_list as $pname => $union_list) {
									echo "<th class='th' colspan='2'>{$pname}</th>";
								}
								?>
							</tr>
							<tr><?php foreach ($show_plans as $plan_info):
									$_gender = ($plan_info['info']['gender'] === 'male') ? '男' : '女';
									if ($_gender === '女') {
										$__gender = '男';
									} else {
										$__gender = '女';
									}
									echo "<th>$_gender</th>";
									echo "<th>$__gender</th>";
								?>
								<?php endforeach; ?></tr>
						</thead>
						<tbody>
							<tr>
								<th>定價</th>
								<?php
								foreach ($show_plans as $plan_info) {
									foreach ($plan_info['info'] as $info) {
										$_price = $info['price'] ?? 0;
										echo "<td>NT$" . number_format($_price) . "</td>";
									}
								}
								// $_price = number_format($info['price']);
								// error_log("info: " . print_r($info, true));
								?>
							</tr>
							<?php
							foreach ($checkup_set_list as $_checkup_plan_id => $checkup_set):
							?>
								<tr>
									<th><?= $checkup_set['title']; ?></th>
									<?php
									foreach ($show_plans as $plan_info):
										$plan_name = $plan_info['plan_name'];
										foreach ($plan_info['info'] as $info) {

											// $_gender = ($info['gender'] === 'male') ? '男' : '女';
											// error_log("_gender: ".var_export($_gender, true));
											// $_price = number_format($info['price']);
											// error_log("info: " . print_r($info, true));
											$union_list = $plan_info['check_item_union_list'];
											// error_log("union_list: " . print_r($union_list, true));
											if (in_array($_checkup_plan_id, $union_list)) {
												echo '<td class="icon"><i class="icon-check"></i></td>';
											} else {
												echo "<td></td>";
											}
											// foreach($info['checkup_item_list'] as $checkup_item_id){
											// 	if( in_array($checkup_item_id, $union_list) ){
											// 		echo "<td>O</td>";
											// 	} else {
											// 		echo "<td></td>";
											// 	}
											// }

										}
									endforeach;
									// foreach ($_compare_plans_item_list as $pname => $union_list) {
									// 	$checkup_set_id = $_checkup_plan_id;
									// 	// $checkup_set_title = $checkup_set['title'];
									// 	if (in_array($checkup_set_id, $union_list)) {
									// 		echo "<td>O</td>";
									// 	} else {
									// 		echo "<td></td>";
									// 	}
									// }
									?>
								</tr>
							<?php
							endforeach;
							?>
							<tr>
								<th>了解方案</th>
								<?php
								foreach ($compare_plans as $pid => $pname) {
								?>
									<td colspan="2"><button type="button" class="describe_case" data-pname="<?= $pname; ?>" data-pid="<?= $pid; ?>">方案介紹</button></td>
								<?php
								}
								?>
							</tr>
							<tr>
								<th>線上預約</th>
								<?php
								foreach ($compare_plans as $pid => $pname) {
								?>
									<td colspan="2"><button type="button" class="reserve_this_case" data-pname="<?= $pname; ?>" data-pid="<?= $pid; ?>">預約此方案</button></td>
								<?php
								}
								?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- End of 做直的比較表 -->
	<?php endif; ?>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
								// location.reload();
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

				$(".describe_case").click(function() {
					var pname = $(this).data('pname');
					var pid = $(this).data('pid');
					location.href = '<?= site_url('checkup-plan'); ?>/' + pname;
				});

				$(".reserve_this_case").click(function() {
					var pname = $(this).data('pname');
					var pid = $(this).data('pid');
					location.href = '<?= site_url('checkup-plan'); ?>/' + pname + '/reserve';
				});

			});
		});
	</script>
	<?php
	return ob_get_clean();
}

add_shortcode('show_plan_compare_v2', 'show_plan_compare_v2');
function show_plan_compare_v2()
{
	ob_start();
	wp_enqueue_script('jquery');
	$checkup_set_list = get_health_checkup_set_list(true);
	// error_log('$checkup_set_list: ' . var_export($checkup_set_list, true));
	$compare_plans_item_list = [];
	$_compare_plans_item_list = [];

	// 20251120 排列新的勾選項目
	$show_plans = [];
	$compare_plans = [];
	if (isset($_COOKIE['plans_compare'])) {
		$compare_plans = $_COOKIE['plans_compare'];
		$compare_plans = json_decode(wp_unslash($compare_plans), true);
		if (is_array($compare_plans)) {
			foreach ($compare_plans as $pid => $pname) {
				$plan_info = get_plan_info_by_id_v2($pid);
				// error_log("{$pname} plan_info: " . print_r($plan_info, true));
				$show_plans[] = $plan_info;
				if (isset($plan_info['info'])) {
					foreach ($plan_info['info'] as $info) {
						$gender = ($info['gender'] === 'male') ? '男' : '女';
						$col_key = $pname . '-' . $gender; // 例如 "尊爵菁英-男"
						$compare_plans_item_list[$col_key] = $info['checkup_item_list'];
						$_compare_plans_item_list[$pname] = $plan_info['check_item_union_list'];
					}
				}
			}
		} else {
			$compare_plans = [];
		}
	}
	$compare_plans_count = count($show_plans) + 1;

	// 20251120 把方案重排一次
	$new_checkup_set_list = [];
	foreach ($checkup_set_list as $_checkup_plan_id => $checkup_set) {
		$_plan_terms = get_the_terms($_checkup_plan_id, 'health_checkup_set');
		// if(!key_exists($_plan_terms[0]->name, $new_compare_plans)){
		$new_checkup_set_list[$_plan_terms[0]->name][$_checkup_plan_id] = $checkup_set['title'];
		// }
	}
	// error_log('$new_checkup_set_list: ' . print_r($new_checkup_set_list, true));

	// error_log('$show_plans: ' . print_r($show_plans, true));


	// 組合成 table
	if (is_array($compare_plans) && count($compare_plans) > 0):
	?>
		<!-- 做直的比較表 -->
		<div id="plan_list_title" class="section-title">
			<h4 class="title">方案互比結果</h4>
			<p class='sub-title'>方案代號：
				<?php
				$i = 1;
				foreach ($compare_plans as $pid => $pname) {
					echo "<span class='sub-title'>{$i} : {$pname}</span>";
					// echo "<span class='sub-title'><img src='plan_{$i}.png'> : {$pname}</span>";
					$i++;
				}
				?>
			</p>
			<p> 性別代號 ：不拘性別 (Unisex)、男 (Male) 、女 (Female) </p>
			<!-- <p> 性別代號 ：不拘性別 <img src="unisex_icon.png">、男 <img src="male_icon.png"> 、女 <img src="female_icon.png"></p> -->
		</div>
		<div id="compare" class="solution-comparison-results">
			<div class="cont">
				<div class="table-wrapper">
					<table class='table responsive-table'>
						<thead>
							<tr>
								<th class="th">項目</th>
								<?php
								$i = 1;
								foreach ($_compare_plans_item_list as $pname => $union_list) {
									// echo "<th class='th'>{$pname}</th>";
									echo "<th class='th'>{$i}</th>";
									// echo "<th class='th'><img src='plan_{$i}.png'></th>";
									$i++;
								}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							// 跑全部的健檢項目, $term_name 為 健檢項目類型名稱, $checkup_set 為該類型底下的健檢項目
							foreach ($new_checkup_set_list as $term_name => $checkup_set) {
								echo "<tr><th class='plan_term_name' colspan='{$compare_plans_count}' style='background-color: #f0f0f0; text-align: left; padding-left: 10px;'>{$term_name}</th></tr>";
								// 跑該類型底下的健檢項目								
								foreach ($checkup_set as $_checkup_plan_id => $checkup_set_title) {
									echo "<tr><th>{$checkup_set_title}</th>";
									// 進行比對
									foreach ($show_plans as $plan_info) {
										$plan_name = $plan_info['plan_name'];
										$union_list = $plan_info['check_item_union_list'];
										// error_log("{$plan_name} {$_checkup_plan_id} union_list: " . print_r($union_list, true));
										if (key_exists($_checkup_plan_id, $union_list)) {
											echo "<td class='icon'>{$union_list[$_checkup_plan_id]}</td>";
										} else {
											echo "<td></td>";
										}
									}
									echo "</tr>";
								}
							}
							?>
							<tr>
								<th>了解方案</th>
								<?php
								foreach ($compare_plans as $pid => $pname) {
								?>
									<td><button type="button" class="describe_case" data-pname="<?= $pname; ?>" data-pid="<?= $pid; ?>">方案介紹</button></td>
								<?php
								}
								?>
							</tr>
							<tr>
								<th>線上預約</th>
								<?php
								foreach ($compare_plans as $pid => $pname) {
								?>
									<td><button type="button" class="reserve_this_case" data-pname="<?= $pname; ?>" data-pid="<?= $pid; ?>">預約此方案</button></td>
								<?php
								}
								?>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- End of 做直的比較表 -->
	<?php endif; ?>
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
								// location.reload();
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

				$(".describe_case").click(function() {
					var pname = $(this).data('pname');
					var pid = $(this).data('pid');
					location.href = '<?= site_url('checkup-plan'); ?>/' + pname;
				});

				$(".reserve_this_case").click(function() {
					var pname = $(this).data('pname');
					var pid = $(this).data('pid');
					location.href = '<?= site_url('checkup-plan'); ?>/' + pname + '/reserve';
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
	// $plans_compare = array();

	// 新增另一暫存用的 cookie: card_plan_name_list 從前端的方案卡片加入互比時使用
	if (isset($_COOKIE['card_plan_name_list'])) {
		$card_plan_name_list = json_decode(wp_unslash($_COOKIE['card_plan_name_list']), true);
	} else {
		$card_plan_name_list = [];
	}


	$plan_name_list = $_COOKIE['plan_name_list'] ?? "";
	$plan_name_list = json_decode(wp_unslash($plan_name_list), true);
	if (!is_array($plan_name_list)) {
		$plan_name_list = [];
	}

	if (!in_array($plan_id, array_keys($plan_name_list))) {
		$plan_name_list[$plan_id] = $pname;
		// 存入 cookie
		$plan_name_list = json_encode($plan_name_list, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		setcookie('plan_name_list', $plan_name_list, time() + 3600, '/');
	}

	// 存入 card_plan_name_list
	if (!in_array($plan_id, array_keys($card_plan_name_list))) {
		$card_plan_name_list[$plan_id] = $pname;
		// 存入 cookie
		$card_plan_name_list = json_encode($card_plan_name_list, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		setcookie('card_plan_name_list', $card_plan_name_list, time() + 3600, '/');
	}

	if (in_array($pname, $plan_name_list)) {
		$result['message'] = '已經加入過';
		echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		exit();
	}

	// if (count($plans_compare) >= 3) {
	// 	$result['message'] = '最多只能比較三筆方案';
	// 	echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	// 	exit();
	// }


	// 存入 cookie
	// $plans_compare[$plan_id] = $pname;
	// $plans_compare = json_encode($plans_compare, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	// setcookie('plans_compare', $plans_compare, time() + 3600, '/');
	$result['success'] = true;
	$result['message'] = "";
	echo json_encode($result, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
	exit();
}

add_action('wp_ajax_add_to_plans_compare_single', 'add_to_plans_compare_single');
add_action('wp_ajax_nopriv_add_to_plans_compare_single', 'add_to_plans_compare_single');
function add_to_plans_compare_single()
{
	$result = ['success' => false, 'message' => ''];
	// error_log("add_to_plans_compare: " . print_r($_POST, true));
	$pname = $_POST['pname'];
	$plan_id = $_POST['pid'];

	// 改成讀取 cookie
	if (isset($_COOKIE['plans_compare'])) {
		$plans_compare = json_decode(wp_unslash($_COOKIE['plans_compare']), true);
	} else {
		$plans_compare = [];
	}

	// 加入方案名稱列表
	if (!in_array($pname, $plans_compare)) {
		$plans_compare[$plan_id] = $pname;
		// 存入 cookie
		$plans_compare = json_encode($plans_compare, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT);
		setcookie('plans_compare', $plans_compare, time() + 3600, '/');
	}

	wp_send_json_success([
		'success' => true,
		'message' => '方案已加入互比',
		'plans_compare' => $plans_compare,
	]);
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

add_shortcode('plan_search_result', 'plan_search_result_fn');
function plan_search_result_fn()
{
	// 讀取 cookie
	$plans_compare = array();
	if (isset($_COOKIE['plans_compare'])) {
		$plans_compare = json_decode(wp_unslash($_COOKIE['plans_compare']), true);
	}
	// 讀取方案名稱列表
	$plan_name_list = array();
	if (isset($_COOKIE['plan_name_list'])) {
		$plan_name_list = json_decode(wp_unslash($_COOKIE['plan_name_list']), true);
	}

	foreach ($plans_compare as $pid => $pname) {
		if (!in_array($pname, $plan_name_list)) {
			$plan_name_list[$pid] = $pname;
		}
	}

	error_log("plans_compare: " . var_export($plans_compare, true));
	error_log("plan_name_list: " . var_export($plan_name_list, true));
	wp_enqueue_script('jquery');
	ob_start();
?>
	<div id="search_result" class="search-results">
		<div class="cont">
			<div class="table-warp">
				<h3 class="title">搜尋結果</h3>
				<table id="plan_results" class="form">
					<tbody>
						<?php
						if (is_array($plan_name_list) && count($plan_name_list) > 0) {
							foreach ($plan_name_list as $pid => $pname) {
								$plan_info = get_plan_info_by_id($pid);
								// error_log('$plan_info: ' . var_export($plan_info, true));
						?>
								<tr class='compare_row'>
									<td>
										<input type='checkbox' class='add_to_plan_compare' value="<?= $pname; ?>" data-pid='<?= $pid; ?>' id="<?= $pid; ?>">
									</td>
									<td>
										<label class='p-title' for='<?= $pid; ?>'>
											<span class='p-name'><?= $pname; ?></span>
											<?php if (in_array($pname, $plans_compare)): ?>
												<span class='plan-tag'>互比方案</span>
											<?php endif; ?>
										</label>

										<ul class="p-info">
											<?php
											foreach ($plan_info['info'] as $info):
												$_gender = '';
												if ($info['gender'] == 'female') {
													$_gender = '女性';
												} else if ($info['gender'] == 'male') {
													$_gender = '男性';
												}
											?>
												<li>
													<div class="info">
														<?= $_gender; ?>&nbsp;NT$<?= $info['price']; ?>
												</li>
			</div>

		<?php endforeach; ?>
		</ul>
		</td>
		<td>
			<div class="btn-group">
				<a href="" class="btn booking-btn">立即預約</a>
				<a href="<?= esc_url(home_url('/checkup-plan/' . $pname)); ?>" class="btn learn-btn">了解方案</a>
			</div>
		</td>
		</tr>
<?php
							}
						}
?>
<tr class="tfoot">
	<td colspan="3">
		<div class="info">
			<p>同時間只能最多選擇三個方案進行互比。</p>
			<button type='button' id='clear_all_compare_plans'>清除搜尋結果和互比方案<svg width="16" height="18" viewBox="0 0 16 18" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M3 18C2.45 18 1.97933 17.8043 1.588 17.413C1.19667 17.0217 1.00067 16.5507 1 16V3H0V1H5V0H11V1H16V3H15V16C15 16.55 14.8043 17.021 14.413 17.413C14.0217 17.805 13.5507 18.0007 13 18H3ZM5 14H7V5H5V14ZM9 14H11V5H9V14Z" fill="" />
				</svg></button>
		</div>
		<button type='button' id='btn_add_to_compare' class='compare-btn is-loading-btn'>方案互比</button>
	</td>
</tr>
</tbody>
</table>
		</div>
	</div>
	</div>

	<!-- sweet alert -->
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
	<script type="text/javascript">
		document.addEventListener('DOMContentLoaded', function() {
			function fillSearchForm(plan_search_data) {
				// 填入性別
				let gender = document.querySelectorAll('input[name="gender[]"]');
				gender.forEach((input) => {
					input.checked = plan_search_data.gender.includes(input.value);
				});
				let price = document.querySelectorAll('input[name="price[]"]');
				price.forEach((input) => {
					input.checked = plan_search_data.price.includes(input.value);
				});

				let parts_of_body = document.querySelectorAll('input[name="parts_of_body[]"]');
				parts_of_body.forEach((input) => {
					input.checked = plan_search_data.parts_of_body.includes(input.value);
				});
			}

			jQuery(function($) {
				// console.log("plan_search_result.js loaded");
				function show_search_result(search_result) {
					$(".result_row").remove();
					for (const key in search_result) {
						if (search_result.hasOwnProperty(key)) {
							const plans = search_result[key];
							let exists_plan_compare = $('.add_to_plan_compare[data-pid="' + plans["plan_id"] + '"]');
							if (exists_plan_compare.length > 0) {
								return;
							} else {
								$("#search_result").show();
								// 畫面移動到 #search_result
								// $('html, body').animate({
								// 	scrollTop: $("#search_result").offset().top
								// }, 500);
							}
							// console.log(key);
							let tr = $("<tr class='result_row'></tr>");
							let td = $("<td></td>");


							// let content_text = "<input type='checkbox' class='add_to_plan_compare' value=" + key + " data-pid='" + plans["plan_id"] + "'><label>" + key + "</label><span>[最多人選擇]</span>";

							let content_text = `
								<td>
									<input type="checkbox" class="add_to_plan_compare" value="${key}" data-pid="${plans["plan_id"]}" id="${plans["plan_id"]}">
								</td>
								<td>
									<label class="p-title" for="${plans["plan_id"]}">
										<span class="p-name">${key}</span>
										<span class="plan-tag outline">最多人選擇</span>
									</label>
								
							`;
							console.log(plans);

							content_text += '<ul class="p-info">';
							plans["info"].forEach(
								(plan, index) => {
									var _gender = '';
									if (plan["gender"] == 'female') {
										_gender = '女性';
									} else if (plan["gender"] == 'male') {
										_gender = '男性';
									}
									content_text += "<li><div class='info'>" + _gender + "&nbsp;NT$ " + plan["price"] + "</div></li>";
								});
							content_text += '</ul></td>';
							content_text += `<td>
								<div class="btn-group">
									<a href="<?= site_url('checkup-plan'); ?>/health-check-up-appointment/" class="btn booking-btn" target="plan_appointment">立即預約</a>
									<a href="<?= site_url('checkup-plan'); ?>/${plans["plan_name"]}/" class="btn learn-btn" target="know_plan">了解方案</a>
								</div>
							</td>`
							tr.html(content_text);
							// tr.append(td);
							// $("#result_th").after(tr);
							$("tbody > tr:first").before(tr);
						}
					}
				}

				<?php
				// 從 cookie 讀取
				// 將 plan_search_data 填入表單				
				if (isset($_COOKIE['plan_search_data'])) {
				?>
					let plan_search_data = JSON.parse('<?= $_COOKIE['plan_search_data']; ?>');
					fillSearchForm(plan_search_data);
				<?php
				}
				?>

				<?php if (!is_array($plans_compare) || count($plans_compare) == 0): ?>
					$("#search_result").hide();
				<?php endif; ?>

				<?php if (is_array($plan_name_list) && count($plan_name_list) > 0): ?>
					$("#search_result").show();
				<?php endif; ?>

				// 如果 sessionStroage 中有 plan_search_result				
				var plan_search_result = sessionStorage.getItem('plan_search_result');

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
							// show_search_result(response);
							// 我要強制條轉到 choices/#search_result 頁面
							location.href = '<?= site_url('choices#search_result'); ?>';
						}
					});
				});

				$("#btn_add_to_compare").on('mouseup', function() {
					// console.log($(".add_to_plan_compare"));
					let add_to_plan_compare = $(".add_to_plan_compare");
					let add_to_plan_compare_checked = add_to_plan_compare.filter(":checked");
					let err_msgs = [];

					// 沒有選擇方案
					if (add_to_plan_compare.length == 0) {
						err_msgs.push('請選擇方案');
					}

					// 最多3筆
					if (add_to_plan_compare_checked.length > 3) {
						err_msgs.push('最多只能比較三筆方案');
					}

					if (err_msgs.length > 0) {
						Swal.fire({
							icon: 'error',
							title: '錯誤',
							html: err_msgs.join('<br>')
						});
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
							action: 'clear_all_plan_compare',
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
									action: 'add_to_plans_compare_single',
									pname: pname,
									pid: $(this).data('pid'),
								},
								success: function(response) {
									// console.log(response);									
								}
							});
						}
					});

					location.href = '<?= site_url('choices#compare'); ?>';
				});

				$("#clear_all_compare_plans").on('mouseup', function() {
					// console.log('clear_all_compare_plans clicked');
					$.ajax({
						url: '<?= admin_url('admin-ajax.php'); ?>',
						type: 'post',
						dataType: 'json',
						cache: false,
						async: false,
						data: {
							action: 'clear_all',
						},
						success: function(response) {
							// console.log(response);
							if (response.success) {
								$("#search_result").hide();
								location.href = '<?= site_url('choices'); ?>';
							} else {
								alert(response.message);
							}
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
	error_log('plan_search_result: ' . var_export($postdata, true));
	// 處理性別
	$gender = "";

	if (!isset($postdata['gender'])) {
		$postdata['gender'] = array('女生', '男生');
	}
	foreach ($postdata['gender'] as $_gender) {
		if ($_gender == '女生') {
			$gender .= "'female',";
		} else if ($_gender == '男生') {
			$gender .= "'male',";
		}
	}
	$gender = rtrim($gender, ',');

	// 處理搜尋關鍵字
	// $keywords = array();
	// $parts = $postdata['parts_of_body'];
	// error_log('parts_of_body: '.var_export($parts, true));
	// foreach ($parts as $part) {
	// 	// 只取"｜"前面的部分
	// 	$_part = explode('｜', $part);
	// 	$keywords[] = $_part[0];
	// }

	$plan_id_list = array();

	global $wpdb;
	// 搜尋方案 - 性別
	$table = $wpdb->prefix . 'postmeta';
	$sql = "SELECT post_id FROM `{$table}` WHERE `meta_key` LIKE 'checkup_price_gender_and_items_%_gender' AND `meta_value` IN ({$gender});";
	error_log($sql);
	$gender_post_id_list = $wpdb->get_col($sql);
	error_log('gender_post_id_list: ' . var_export($gender_post_id_list, true));

	// 搜尋方案 - 價格, 選項有 : <2萬, 2-5萬, 5-11萬; 複選
	if (!isset($postdata['price'])) {
		$postdata['price'] = array('<2萬', '2-5萬', '5-11萬');
	}
	$price_choices = $postdata['price'];
	$max = null;
	$min = null;
	$sql_condition = "";
	foreach ($price_choices as $price_choice) {
		switch ($price_choice) {
			case '<2萬':
				$max = 20000;
				$min = 0;
				$sql_condition = "(`meta_value` BETWEEN {$min} AND {$max})";
				break;
			case '2-5萬':
				if ($min === null) {
					$min = 20000;
				} else if ($min < 20000) {
					$min = 20000;
				}
				$max = 50000;
				if ($sql_condition === "") {
					$sql_condition = "(`meta_value` BETWEEN {$min} AND {$max})";
				} else {
					$sql_condition .= " OR (`meta_value` BETWEEN {$min} AND {$max})";
				}
				break;
			case '5-11萬':
				if ($min === null) {
					$min = 50000;
				} else if ($min < 50000) {
					$min = 50000;
				}
				$max = 110000;
				if ($sql_condition === "") {
					$sql_condition = "(`meta_value` BETWEEN {$min} AND {$max})";
				} else {
					$sql_condition .= " OR (`meta_value` BETWEEN {$min} AND {$max})";
				}
				break;
		}
	}
	$table = $wpdb->prefix . 'postmeta';
	$sql = "SELECT post_id FROM `{$table}` WHERE `meta_key` LIKE 'checkup_price_gender_and_items_%_price' AND ({$sql_condition});";
	error_log($sql);
	$price_post_id_list = $wpdb->get_col($sql);
	error_log('price_post_id_list: ' . var_export($price_post_id_list, true));

	// 搜尋方案 - 健檢部位
	$checkup_part_id_list = array();

	// 如果沒有選擇任何部位, 預設選擇全部
	if (isset($postdata['parts_of_body'])) {
		$checkup_parts = $postdata['parts_of_body'];
		$checkup_part_keywords = "";
		foreach ($checkup_parts as $checkup_part) {
			// 只取"｜"前面的部分
			$_part = explode('｜', $checkup_part);
			$checkup_part_keywords .= "'" . $_part[0] . "',";
		}
		$checkup_part_keywords = rtrim($checkup_part_keywords, ',');

		$table = $wpdb->prefix . 'posts';
		$sql = "SELECT ID FROM `{$table}` WHERE `post_title` IN ({$checkup_part_keywords}) AND `post_type` = 'checkup_body_parts' AND `post_status` = 'publish';";
		$checkup_part_keywords_id_list = $wpdb->get_col($sql);
		// error_log('checkup_part_keywords_id_list: '.var_export($checkup_part_keywords_id_list, true));
		$table = $wpdb->prefix . 'postmeta';
		$sql = "SELECT post_id, meta_value FROM `{$table}` WHERE `meta_key` = 'checkup_parts'";
		$_checkup_part_list = $wpdb->get_results($sql);
		// error_log('_checkup_part_list: '.var_export($_checkup_part_list, true));
		foreach ($_checkup_part_list as $_checkup_part) {
			// 解開格式為 a:3:{i:0;s:3:"203";i:1;s:3:"205";i:2;s:3:"202";} 的資料
			// error_log(var_export($_checkup_part, true));
			$__checkup_part_list = unserialize($_checkup_part->meta_value);
			// error_log('$_checkup_part->post_id: '.var_export($_checkup_part->post_id, true));
			// error_log('__checkup_part_list: '.var_export($__checkup_part_list, true));
			if (is_array($__checkup_part_list)) {
				foreach ($checkup_part_keywords_id_list as $checkup_part_keywords_id) {
					if (in_array($checkup_part_keywords_id, $__checkup_part_list)) {
						$checkup_part_id_list[] = $_checkup_part->post_id;
						break;
					}
				}
			}
		}
	} else {
		// 沒有選擇任何部位, 預設選擇全部
		$table = $wpdb->prefix . 'posts';
		$sql = "SELECT ID FROM `{$table}` WHERE `post_type` = 'checkup-plan' AND `post_status` = 'publish';";
		$checkup_part_id_list = $wpdb->get_col($sql);
	}

	// error_log('checkup_part_id_list: '.var_export($checkup_part_id_list, true));

	// 找出 $gender_post_id_list, $price_post_id_list, $checkup_part_id_list 的交集
	$plan_id_list = array_intersect($gender_post_id_list, $price_post_id_list, $checkup_part_id_list);
	// error_log('plan_id_list: '.var_export($plan_id_list, true));

	$plan_list = array();
	$plan_name_list = array();
	foreach ($plan_id_list as $plan_id) {
		$plan = get_plan_info_by_id($plan_id);
		$plan_list[$plan['plan_name']] = $plan;
		if (!in_array($plan['plan_name'], $plan_name_list)) {
			$plan_name_list[$plan_id] = $plan['plan_name'];
		}
	}

	// 把 card_plan_name_list 整合進 plan_name_list
	if (isset($_COOKIE['card_plan_name_list']) && is_array(json_decode(wp_unslash($_COOKIE['card_plan_name_list']), true))) {
		$card_plan_name_list = json_decode(wp_unslash($_COOKIE['card_plan_name_list']), true);
		foreach ($card_plan_name_list as $pid => $pname) {
			if (!in_array($pname, $plan_name_list)) {
				$plan_name_list[$pid] = $pname;
			}
		}
	}

	setcookie('plan_name_list', '', time() - 3600, '/');
	setcookie('plan_name_list', json_encode($plan_name_list, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT), time() + 7200, '/');
	// error_log('plan_list: '.print_r($plan_list, true));
	header('Content-Type: application/json; charset=utf-8');
	exit(json_encode($plan_list, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
}

// // // 啟用健檢專案的 post_tag
// function add_tags_to_checkup_plan_type()
// {
// 	register_taxonomy_for_object_type('post_tag', 'checkup-plan'); // 'custom_post' 是你的 Post Type 名称
// }
// add_action('init', 'add_tags_to_checkup_plan_type');

// // ajax 移除全部方案互比
// add_action('wp_ajax_remove_plan_compare', 'remove_plan_compare');
// add_action('wp_ajax_nopriv_remove_plan_compare', 'remove_plan_compare');
// function remove_plan_compare()
// {
// 	delete_user_meta(get_current_user_id(), 'plans_compare');
// 	// 回傳成功
// 	exit(json_encode(['success' => true], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
// }

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

// 清除全部方案互比
add_action('wp_ajax_clear_all', 'clear_all');
add_action('wp_ajax_nopriv_clear_all', 'clear_all');
function clear_all()
{
	// 清除 cookie
	setcookie('plans_compare', '', time() - 3600, '/');
	setcookie('plan_search_data', '', time() - 3600, '/');
	setcookie('plan_name_list', '', time() - 3600, '/');
	setcookie('card_plan_name_list', '', time() - 3600, '/');
	// 清除 user meta
	// delete_user_meta(get_current_user_id(), 'plans_compare');
	// 回傳成功
	exit(json_encode(['success' => true], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
}

add_action('wp_ajax_clear_all_plan_compare', 'clear_all_plan_compare');
add_action('wp_ajax_nopriv_clear_all_plan_compare', 'clear_all_plan_compare');
function clear_all_plan_compare()
{
	// 清除 cookie
	setcookie('plans_compare', '', time() - 3600, '/');
	// 清除 user meta
	// delete_user_meta(get_current_user_id(), 'plans_compare');
	// 回傳成功
	exit(json_encode(['success' => true], JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES + JSON_PRETTY_PRINT));
}

// 列出檢索部位
add_shortcode('list_of_parts_search', 'list_of_parts_search');
function list_of_parts_search()
{

	// // Elementor 編輯器內就不執行內容避免 crash
	// if ( defined( 'ELEMENTOR_VERSION' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
	// 	return '<!-- parts search disabled in editor mode -->';
	// }

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

	echo '<div class="part-warp">';
	// 只要存 title 與 content 到陣列 parts 裡
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$_title = get_the_title();
			$_content = get_the_content();
			$_id = get_the_ID();
			// $parts[get_the_title()] = get_the_content();
			$excerpt = get_the_excerpt();
			$_link = get_permalink();
			// $image = get_the_post_thumbnail_url(get_the_ID(), 'full');
			$body_parts_img = get_field('body_parts_img', $_id);
			$image_male = $body_parts_img['male']['url'];
			$image_female = $body_parts_img['female']['url'];

			$image = !empty($image_male) ? $image_male : $image_female;

			$_part_gender = get_field("body_part_gender", $_id);
			if (!$_part_gender) {
				$_part_gender = ['male', 'female'];
			}

	?>
			<a href="<?= esc_url($_link); ?>" class="part"
				data-title="<?= esc_attr(get_the_title()); ?>"
				data-excerpt="<?= esc_attr($excerpt); ?>"
				data-img-male="<?= esc_url($image_male); ?>"
				data-img-female="<?= esc_url($image_female); ?>"
				data-gender="<?= esc_attr(json_encode($_part_gender)); ?>">

				<h4 class="part_title"><span><?= $_title; ?></span><span class="part_tag" data-part="<?= $_title; ?>">建議檢測項目</span></h4>
				<div class="part-arrow"></div>
				<!-- <p><?= $_content; ?></p> -->
			</a>
	<?php
		}
		wp_reset_postdata();
	}
	echo '</div>';

	$parts_options_list = get_field('parts_options_list', 'option');
	error_log(print_r($parts_options_list, true));
	wp_register_script('static/js/list_of_parts_search.js', get_stylesheet_directory_uri() . '/static/js/list_of_parts_search.js', array('jquery'), '1.0', true);
	wp_localize_script('static/js/list_of_parts_search.js', 'parts_options_list', $parts_options_list);
	wp_enqueue_script('static/js/list_of_parts_search.js');
	return ob_get_clean();
}



function part_preview_block()
{
	$img_url_male = get_stylesheet_directory_uri() . '/assets/img/default_male.png';
	$img_url_female = get_stylesheet_directory_uri() . '/assets/img/default_female.png';
	$html = '<div class="part-preview">
				<div class="preview-info">
					<h3 class="preview-title"></h3>
					<div class="preview-excerpt"></div>
					<a class="preview-btn" href=""></a>
				</div>
				<div class="preview-img">
					<img class="male show" src="' . esc_url($img_url_male) . '" alt="男性部位預設圖">
					<img class="female" src="' . esc_url($img_url_female) . '" alt="女性部位預設圖">
				</div>
		   </div>';

	return $html;
}
add_shortcode('part_preview', 'part_preview_block');

function show_parts_in_search_plan_page($tag, $unused)
{
	// 檢查欄位名稱是否為 'parts_of_body'，請根據你的表單欄位名稱修改
	if ($tag['name'] != 'parts_of_body') {
		return $tag;
	}

	$checkup_body_parts = array();
	$args = array(
		'post_type' => 'checkup_body_parts',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	);
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$checkup_body_parts[] = get_the_title() . '|' . get_the_title();
		}
	}
	wp_reset_postdata();

	// 將動態產生的選項設定回 Contact Form 7 的 $tag 物件
	$tag['raw_values'] = $checkup_body_parts;
	$tag['pipes']      = new WPCF7_Pipes($checkup_body_parts);
	$tag['labels']     = $tag['pipes']->collect_befores();
	$tag['values']     = $tag['pipes']->collect_afters();

	return $tag;
}
add_filter('wpcf7_form_tag', 'show_parts_in_search_plan_page', 10, 2);

// 20250822 檢查選擇的健檢項目是否有設定性別
add_action('wp_footer', 'search_plan_page_option_check');
function search_plan_page_option_check()
{
	if (is_page('choices')) {
		$js_mod_ts = fileatime(get_stylesheet_directory() . '/static/js/search_plan_page_option_check.js');
		wp_enqueue_script(
			'search_plan_page_option_check',
			get_stylesheet_directory_uri() . '/static/js/search_plan_page_option_check.js',
			array('jquery'),
			$js_mod_ts,
			true
		);

		// 讀取性別
		$checkup_body_parts = [];
		$args = array(
			'post_type' => 'checkup_body_parts',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'date',
			'order' => 'DESC',
		);
		$query = new WP_Query($args);
		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$_gender = get_field("body_part_gender", get_the_ID());
				if (!$_gender) {
					$gender = ['male', 'female'];
				} else {
					$gender = $_gender;
				}
				$checkup_body_parts[] = ["part_name" => get_the_title(), "gender" => $gender];
			}
		}
		wp_reset_postdata();

		wp_localize_script(
			'search_plan_page_option_check',
			'search_plan_ajax',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'checkup_body_parts' => $checkup_body_parts,
				'nonce'    => wp_create_nonce('search_plan_nonce_action'),
			)
		);
	}

	if (is_page('parts-search')) {
		$js_mod_ts = fileatime(get_stylesheet_directory() . '/static/js/parts_search_page_option_check.js');

		wp_enqueue_script(
			'parts_search_page_option_check',
			get_stylesheet_directory_uri() . '/static/js/parts_search_page_option_check.js',
			array('jquery'),
			$js_mod_ts,
			true
		);
	}

	// 預約頁面
	if (is_page('health-check-up-appointment')) {
		$js_mod_ts = filemtime(get_stylesheet_directory() . '/static/js/appointment_page_option_check.js');

		wp_enqueue_script(
			'appointment_page_option_check',
			get_stylesheet_directory_uri() . '/static/js/appointment_page_option_check.js',
			array('jquery'),
			$js_mod_ts,
			true
		);
		wp_localize_script(
			'appointment_page_option_check',
			'appointment_page_ajax',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'get_planinfo_url' => site_url('wp-json/parkone/v1/checkup-plan-price/'),
				'site_url' => site_url(),
				'nonce'    => wp_create_nonce('search_plan_nonce_action'),
			)
		);
	}

	// 勞工健檢頁面
	if (is_page('employee-health-checkup')) {
		$js_mod_ts = filemtime(get_stylesheet_directory() . '/static/js/employee_health_checkup_page_option_check.js');

		wp_enqueue_script(
			'employee_health_checkup_page_option_check',
			get_stylesheet_directory_uri() . '/static/js/employee_health_checkup_page_option_check.js',
			array('jquery'),
			$js_mod_ts,
			true
		);
	}
}

add_action('wp_ajax_check_body_part_gender', 'check_body_part_gender_callback');
add_action('wp_ajax_nopriv_check_body_part_gender', 'check_body_part_gender_callback');
function check_body_part_gender_callback()
{
	// 驗證 Nonce
	if (!check_ajax_referer('search_plan_nonce_action', 'nonce', false)) {
		wp_send_json_error('Nonce 驗證失敗。');
	}

	// 這裡放入你的業務邏輯，例如查詢資料庫
	$parts = $_POST['parts'];
	// 查詢 posttype = checkup_body_parts, title = $part 的 post_id
	global $wpdb;
	$sql = "SELECT ID FROM $wpdb->posts WHERE post_type = 'checkup_body_parts' AND post_title IN (" . implode(',', array_fill(0, count($parts), '%s')) . ")";
	$sql = $wpdb->prepare($sql, $parts);
	$post_id_list = $wpdb->get_col($sql);
	// error_log("post_id_list: ".var_export($post_id_list, true));
	if (!$post_id_list) {
		wp_send_json_error('找不到對應的健檢部位。');
	}

	$gender = [];
	foreach ($post_id_list as $pid) {
		// 查詢 post_id 的 acf 欄位
		$_gender = get_field("body_part_gender", $pid);
		if (!$_gender) {
			$gender = ['male', 'female'];
		} else {
			$gender = $_gender;
			if (count($_gender) == 1) {
				break;
			}
		}
	}

	// 返回成功的回應
	wp_send_json_success(array(
		'gender' => $gender
	));
}

// wp-admin 後台新增匯入 csv 健檢項目細項清單選單, 新增一個設定頁面, 內容只有一個選擇檔案與送出按鈕的表單
add_action('admin_menu', 'import_health_checkup_items_menu');
function import_health_checkup_items_menu()
{
	add_menu_page(
		'CSV匯入健檢項目細項清單',
		'CSV匯入健檢項目細項清單',
		'manage_options',
		'import-health-checkup-items_page',
		'import_health_checkup_items_page_callback',
		'dashicons-upload',
		81
	);
}


function import_health_checkup_items_page_callback()
{
	$ajax_url = admin_url('admin-ajax.php');
	?>
	<div class="wrap">
		<h1>CSV匯入健檢項目細項清單</h1>
		<form method="post" action="<?= $ajax_url; ?>" enctype="multipart/form-data" id="upload_csv_form">
			<input type="file" name="csv_file" accept=".csv" required>
			<?php submit_button('匯入'); ?>
		</form>
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
								// console.log(response);
								alert('上傳成功');
							}
						});
					});
				});
			});
		</script>
	</div>
<?php
}

// 在 checkup-item 列表加上所屬檢查項目(taxonomy=health_checkup_set), 要可以 sorting
add_filter('manage_checkup-item_posts_columns', 'add_health_checkup_set_column');
function add_health_checkup_set_column($columns)
{
	$columns['health_checkup_set'] = '所屬檢查項目';
	return $columns;
}

add_action('manage_checkup-item_posts_custom_column', 'show_health_checkup_set_column', 10, 2);
function show_health_checkup_set_column($column, $post_id)
{
	if ($column == 'health_checkup_set') {
		$terms = get_the_terms($post_id, 'health_checkup_set');
		if (!empty($terms) && !is_wp_error($terms)) {
			$term_names = wp_list_pluck($terms, 'name');
			echo implode(', ', $term_names);
		} else {
			echo '無';
		}
	}
}

// 讓所屬檢查項目欄位可以排序
add_filter('manage_edit-checkup-item_sortable_columns', 'health_checkup_set_sortable_column');
function health_checkup_set_sortable_column($columns)
{
	$columns['health_checkup_set'] = 'health_checkup_set';
	return $columns;
}

// 處理所屬檢查項目的排序查詢
add_action('pre_get_posts', 'health_checkup_set_orderby');
function health_checkup_set_orderby($query)
{
	if (!is_admin() || !$query->is_main_query()) {
		return;
	}

	if ($query->get('post_type') !== 'checkup-item') {
		return;
	}

	if ($query->get('orderby') == 'health_checkup_set') {
		$query->set('tax_query', array(
			array(
				'taxonomy' => 'health_checkup_set',
				'field'    => 'slug',
				'terms'    => get_terms(array(
					'taxonomy' => 'health_checkup_set',
					'fields'   => 'slugs',
				)),
			),
		));
		$query->set('orderby', 'term_order');
	}
}

function dynamic_select_health_plans($null, $options, $args)
{
	// error_log('dynamic_select_health_plans: ' . var_export($options, true));
	
	switch($options[0]){
		case 'health_checkup_appointment_plans':
			// 從資料庫取得健檢方案
			$plans = get_posts(array(
				'post_type'      => 'checkup-plan',
				'posts_per_page' => -1,
				'post_status'    => 'publish',
			));

			// 清空預設選項
			$options = array();

			// 將健檢方案加入選項
			foreach ($plans as $plan) {
				$options[] = $plan->post_title;
			}
			return $options;

		case 'health_checkup_appointment_gender':
			// 清空預設選項
			$options = array();
			$options['male'] = "男性";
			$options['female'] = "女性";
			return $options;

		case 'get_breakfast_item':
			global $wpdb;
			// 清空預設選項
			$options = array();
			// 使用 SQL 直接查詢 ACF 重複器欄位資料
			$sql = "SELECT `option_value` FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE 'options_breakfast_list_%_breakfast_meal_item'";
			$results = $wpdb->get_col($sql);

			if ($results) {
				foreach ($results as $value) {
					if (!empty($value)) {
						$options[$value] = $value;
					}
				}
			}

			return $options;

		case 'get_breakfast_drink_item':
			global $wpdb;
			// 清空預設選項
			$options = array();
			// 使用 SQL 直接查詢 ACF 重複器欄位資料
			$sql = "SELECT `option_value` FROM `{$wpdb->prefix}options` WHERE `option_name` LIKE 'options_breakfast_drink_%_breakfast_drink_item'";
			$results = $wpdb->get_col($sql);

			if ($results) {
				foreach ($results as $value) {
					if (!empty($value)) {
						$options[$value] = $value;
					}
				}
			}

			return $options;

		case 'get_meal_type':
			// "葷" "全素" "蛋奶素"
			// 清空預設選項
			$options = array();
			$options['meat'] = "葷";
			$options['vegetarian'] = "全素";
			$options['lacto_ovo_vegetarian'] = "蛋奶素";
			return $options;

		case 'get_clothes_size':
			// 清空預設選項
			$options = array();
			$options['S'] = "S";
			$options['M'] = "M";
			$options['L'] = "L";
			$options['XL'] = "XL";
			$options['2XL'] = "2XL";
			$options['3XL'] = "3XL";
			$options['5XL'] = "5XL";
			$options['7XL'] = "7XL";
			return $options;

		case 'get_meal_replacement_options':
			// 清空預設選項
			$options = array();
			$options['omnivore'] = "葷";
			$options['vegetarian'] = "素";
			return $options;

		case 'get_meal_payment_method_options':
			// 清空預設選項
			$options = array();
			$options['self_pickup'] = "自取（訂金付現）";
			$options['mail_wire'] = "郵寄（訂金匯款）";
			return $options;

		case 'get_constipate_options':
			// 清空預設選項
			$options = array();
			$options['yes'] = "是";
			$options['no'] = "否";
			return $options;

		case 'get_employee_health_checkup_list':
			// 清空預設選項
			$options = array();
			global $post;
			$post_id = $post->ID;
			// error_log('post: ' . var_export($post, true));
			// error_log('post_id: ' . var_export($post_id, true));
			$employee_health_checkup_list = get_field('employee_health_checkup_list', $post_id);
			// error_log('employee_health_checkup_list: ' . var_export($employee_health_checkup_list, true));
			if($employee_health_checkup_list){	
				foreach($employee_health_checkup_list as $item){
					$options[$item['employee_health_checkup_item']] = "{$item['employee_health_checkup_item']} {$item['employee_health_checkup_price']}元";
				}
			}
			break;
	}
	// error_log('dynamic_select_health_plans return default options: ' . var_export($options, true));

	return $options;
}
add_filter('wpcf7_form_tag_data_option', 'dynamic_select_health_plans', 10, 3);

// // 自訂設定訪客識別 Cookie
// function custom_set_visitor_cookie() {
//     $cookie_name = 'wp_visitor_id';

//     // 檢查 Cookie 是否已存在
//     if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {

//         // 1. 生成一個唯一的 ID
//         // 注意：這裡我們使用 PHP 的 uniqid()，但在更安全的應用中建議使用 UUID 庫
//         $visitor_id = uniqid( 'v', true ); 

//         // 2. 設定 Cookie 的過期時間 (例如：30 天)
//         $expiry = time() + ( 86400 * 1 ); // 86400 秒 = 1 天

//         // 3. 設置 Cookie
//         // 參數: 名稱, 值, 過期時間, 路徑 (網站根目錄), 網域, 安全 (HTTPS), HTTP-Only (防止 JS 讀取)
//         setcookie( $cookie_name, $visitor_id, $expiry, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true );
//     }
// }
// // 在 WordPress 載入時的早期階段執行
// add_action( 'init', 'custom_set_visitor_cookie' );

// CF7 送出表單 hook, 把資料存到資料表
add_action('wpcf7_submit', 'save_plan_search_data', 10, 2);
function save_plan_search_data($contact_form, $abort)
{
	$submission = WPCF7_Submission::get_instance();
	$uid = uniqid();

	$posted_data = $submission->get_posted_data();

	global $wpdb;
	// 存入資料表 wp_parkone_health_checkup_reservation
	$table = $wpdb->prefix . 'parkone_health_checkup_reservation';
	$data = array(
		'id' => $uid,
		// 'wp_user_id' => $wp_user_id,
		// 'visitor_id' => $visitor_id,
		'reservation_data' => maybe_serialize($posted_data),
		// 'submitted_at' => current_time('mysql'),
	);
	$wpdb->insert($table, $data);
}


// 預約相關部分, 開 rest api 查詢方案內容, 會用 get 變數帶 plan_name 與 gender 過來查詢價格
// 查詢網址範例: https://yourdomain.com/wp-json/parkone/v1/checkup-plan-price/微型悠活/male
add_action('rest_api_init', function () {
	register_rest_route(
		'parkone/v1',
		'/checkup-plan-price/(?P<plan_name>[^/]+)/(?P<gender>[^/]+)',
		array(
			'methods' => 'GET',
			'callback' => 'get_checkup_plan_price',
			'permission_callback' => '__return_true',
		)
	);
});

function get_checkup_plan_price($request)
{
	$params = $request->get_params();
	$plan_name = isset($params['plan_name']) ? urldecode($params['plan_name']) : '';
	$gender = isset($params['gender']) ? urldecode($params['gender']) : '';
	if (!empty($gender)) {
		if ($gender == "女性") {
			$gender = "female";
		} elseif ($gender == "男性") {
			$gender = "male";
		}
	}

	// 用 plan_name 找到 plan_id
	$plan_id = null;
	$args = array(
		'post_type'      => 'checkup-plan',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'title'          => $plan_name,
	);
	$plans = get_posts($args);
	if ($plans) {
		foreach ($plans as $plan) {
			if ($plan->post_title === $plan_name) {
				$plan_id = $plan->ID;
				break;
			}
		}
	}

	// error_log('plan_name: ' . var_export($plan_name, true));
	// error_log('gender: ' . var_export($gender, true));
	// error_log('plan_id: ' . var_export($plan_id, true));

	$price = null;
	if ($plan_id && $gender) {
		$plan_info = get_plan_info_by_id($plan_id);
		if (empty($plan_info["plan_name"])) {
			return rest_ensure_response(array(
				'error' => 'Invalid plan_id',
			));
		}
		// error_log('plan_info: ' . var_export($plan_info, true));
		$result = [];
		$multi_select = [];
		$plan_name = $plan_info['plan_name'];
		if ($plan_info) {
			foreach ($plan_info['info'] as $info) {
				if ($info['gender'] === $gender) {
					$price = $info['price'];

					// 處理多選一項目
					if (isset($info['multi_select'])) {
						foreach ($info['multi_select'] as $key => $multi_select_item_ary) {
							// $multi_select_item_obj 是 array
							foreach ($multi_select_item_ary as $multi_select_item_obj_ary) {
								// 取得 multi_select_item_obj 的 post_title 與 ID
								// error_log('multi_select_item_obj: ' . var_export($multi_select_item_obj_ary, true));
								foreach ($multi_select_item_obj_ary as $multi_select_item_obj_id) {
									$multi_select_item_obj = get_post($multi_select_item_obj_id);
									if ($multi_select_item_obj) {
										$multi_select[$key][] = array(
											'item_name' => $multi_select_item_obj->post_title,
											'item_id' => $multi_select_item_obj->ID,
										);
									}
								}
							}
						}
					}
					break;
				}
			}

			// 處理熱門加選項目
			$hot_additional_list = [];
			if (isset($plan_info['hot_additional_list'])) {
				foreach ($plan_info['hot_additional_list'] as $i => $hot_additional_item_ary) {
					$_hot_additional_item_price = $hot_additional_item_ary['hot_additional_item_price'];
					$_hot_additional_item_title = $hot_additional_item_ary['hot_additional_item_name']->post_title;
					$_hot_additional_item_id = $hot_additional_item_ary['hot_additional_item_name']->ID;
					$hot_additional_list[] = array(
						'item_name' => $_hot_additional_item_title,
						'item_id' => $_hot_additional_item_id,
						'item_price' => $_hot_additional_item_price,
					);
				}
			}

			$result = array(
				'plan_id' => $plan_id,
				'plan_name' => $plan_name,
				'gender' => $gender,
				'price' => $price,
			);

			if (!empty($multi_select) && is_array($multi_select)) {
				$result['multi_select'] = $multi_select;
			}

			if (!empty($hot_additional_list) && is_array($hot_additional_list)) {
				$result['hot_additional_list'] = $hot_additional_list;
			}

			if (isset($plan_info['enable_breakfast']) && $plan_info['enable_breakfast'] === 'yes') {
				$result['enable_breakfast'] = $plan_info['enable_breakfast'];
			}

			if (isset($plan_info['meal_type_display']) && $plan_info['meal_type_display'] === 'yes') {
				$result['meal_type_display'] = $plan_info['meal_type_display'];
			}			

			if(isset($plan_info['meal_plrd']) && !empty($plan_info['meal_plrd'])){
				$result['meal_plrd'] = $plan_info['meal_plrd'];
			}

			if(isset($plan_info['meal_replacement_and_laxative']) && !empty($plan_info['meal_replacement_and_laxative'])){
				$result['meal_replacement_and_laxative'] = $plan_info['meal_replacement_and_laxative'];				
			}

			if(isset($plan_info['constipate']) && !empty($plan_info['constipate'])){
				$result['constipate'] = $plan_info['constipate'];
			}
		}

		return rest_ensure_response(
			$result
		);
	}
	return rest_ensure_response(array(
		'error' => 'Missing plan_id or gender parameter',
	));
}

