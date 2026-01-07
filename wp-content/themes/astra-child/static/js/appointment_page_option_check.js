jQuery(function ($) {
    var breakfast_item_list = $("#breakfast_item_list");
    var meal_type = $("#meal_type");
    var multi_select_list = $("#multi_select_list");
    var total_price_field = $("#total_price_field");
    var total_price = $("#total_price");
    var plan_price = 0;
    var hot_additional_list = $("#hot_additional_list");
    var div_meal_replacement = $("#div_meal_replacement");
    var div_meal_replacement_payment_method = $("#div_meal_replacement_payment_method");
    var div_constipate = $("#div_constipate");

    $("#client_period_item").hide();
    $("[name='client_period']").hide();
    $("#label_client_period").hide();
    breakfast_item_list.hide();
    meal_type.hide();
    multi_select_list.hide();
    total_price_field.hide();
    hot_additional_list.hide();
    div_meal_replacement.hide();
    div_meal_replacement_payment_method.hide();
    div_constipate.hide();
    total_price.val('0');


    // GET AJAX 取得方案資訊
    function ajax_get_plan_info(plan_name, gender) {
        let url = appointment_page_ajax.get_planinfo_url + plan_name + '/' + gender;
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function (data) {
                console.log("ajax_get_plan_info success:", data);
                // 處理供餐選項顯示
                if (data.enable_breakfast == 'yes') {
                    breakfast_item_list.show();
                } else {
                    breakfast_item_list.hide();
                }

                if (data.meal_type_display == 'yes') {
                    meal_type.show();
                } else {
                    meal_type.hide();
                }

                // 如果有 multi_select 為 array，表示有多選項目
                multi_select_list.empty();
                if (data.multi_select && Array.isArray(data.multi_select)) {
                    multi_select_list.show();
                    var count = 1;
                    data.multi_select.forEach(function (item) {
                        // console.log("Adding multi_select item:", item);
                        var title = item.length + "選1:";
                        var _html = '<div><label id="label_multi_select">' + title + '</label><br>';
                        item.forEach(function (sub_item) {
                            _html += '<input type="radio" name="multi_select_' + count + '" value="' + sub_item.item_name + '"> ' + sub_item.item_name + '<br />';
                        });
                        _html += '</div>';
                        multi_select_list.append(_html);
                        count++;
                    });
                } else {
                    // 清除內容後隱藏
                    multi_select_list.hide();
                }

                // 如果有熱門加選 hot_additional_list 為 array，表示有多選項目                
                hot_additional_list.empty();
                if (data.hot_additional_list && Array.isArray(data.hot_additional_list)) {
                    var _html = '<div><label>熱門加選</label><br>';
                    data.hot_additional_list.forEach(function (item) {
                        _html += '<input type="checkbox" class="hot_additional_item" name="hot_additional_item" data-additional-price="' + item.item_price + '" value="' + item.item_name + '"> ' + item.item_name + ' (+' + item.item_price + ')<br />';
                    });
                    _html += '</div>';
                    hot_additional_list.append(_html);
                    hot_additional_list.show();
                }

                // 顯示總價
                if (data.price === undefined || data.price === null || data.price === "") {
                    total_price.val('0');
                    total_price_field.hide();
                } else {
                    plan_price = parseInt(data.price);
                    total_price_field.show();
                    total_price.val(data.price);
                }

                if(data.constipate === true){
                    div_constipate.show();
                } else {
                    div_constipate.hide();
                }

                if(data.meal_replacement_and_laxative === true){
                    div_meal_replacement_payment_method.show();
                } else {
                    div_meal_replacement_payment_method.hide();
                }

                if(data.meal_plrd === true){
                    div_meal_replacement.show();
                } else {
                    div_meal_replacement.hide();
                }
            }
        });
    };

    $(document).on('change', ".hot_additional_item", function () {
        var additional_total = 0;
        $(".hot_additional_item:checked").each(function () {
            var item_price = parseInt($(this).data('additional-price'));
            if (!isNaN(item_price)) {
                additional_total += item_price;
            }
        });
        var final_total = plan_price + additional_total;
        total_price.val(final_total);
    });

    $("[name='gender']").change(function (e) {
        var selectedGender = $(this).val();
        if (selectedGender === "女性") {
            $("[name='client_period']").show();
            $("#label_client_period").show();
            $("#client_period_item").show();
        } else {
            $("[name='client_period']").hide();
            $("#label_client_period").hide();
            $("#client_period_item").hide();
        }

        var selectedPlan = $("[name='appointment_plan']:checked").val();
        if (selectedPlan !== undefined) {
            ajax_get_plan_info(selectedPlan, selectedGender);
        }
    });

    $("[name='appointment_plan']").change(function (e) {
        // 確認有選擇性別
        var selectedGender = $("[name='gender']:checked").val();
        if (selectedGender !== undefined) {
            var selectedPlan = $(this).val();
            ajax_get_plan_info(selectedPlan, selectedGender);
        }
    });

    // 為方案選項的 label 添加連結功能
    $(".wpcf7-form-control-wrap[data-name='appointment_plan']").find(".wpcf7-list-item-label").each(function(elm) {
        $(this).css("cursor", "pointer");
        $(this).click(function() {
            var plan_name = $(this).text().trim();
            var site_url = appointment_page_ajax.site_url;
            var plan_url = site_url + "/checkup-plan/" + encodeURIComponent(plan_name) + "/";
            window.open(plan_url, "_blank");
        });
    });
});