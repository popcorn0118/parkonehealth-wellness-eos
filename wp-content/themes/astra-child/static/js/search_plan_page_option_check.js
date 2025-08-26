// search_plan_page_option_check.js
jQuery(function ($) {
    // 檢查 search_plan_ajax 物件是否存在，以確保腳本已正確本地化
    if (typeof search_plan_ajax === 'undefined') {
        console.error('search_plan_ajax 變數未定義，請檢查 wp_localize_script 是否正確執行。');
        return;
    }

    // 檢查選擇的健檢項目是否有設定性別, 等待 1 秒後才執行
    var th_id = null;
    $('input[name="parts_of_body[]"]').on('change', function () {
        clearTimeout(th_id);
        th_id = setTimeout(function () {
            let parts_of_body = $('input[name="parts_of_body[]"]:checked');
            if (parts_of_body.length === 0) {
                return;
            }

            $.ajax({
                url: search_plan_ajax.ajax_url,
                type: 'post',
                dataType: 'json',
                data: {
                    action: 'check_body_part_gender',
                    parts: parts_of_body.map(function () { return this.value; }).get(),
                    nonce: search_plan_ajax.nonce
                },
                success: function (response) {
                    if (response.success) {
                        if (response.data.gender.length === 1 && response.data.gender[0] === 'female') {
                            $('input[name="gender[]"]').each(function () {
                                if ($(this).val() === '男生') {
                                    $(this).prop('checked', false);
                                    $(this).prop('disabled', true);
                                    $(this).addClass('disabled');
                                }

                                if ($(this).val() === '女生') {
                                    $(this).prop('disabled', false);
                                    $(this).removeClass('disabled');
                                }
                            });
                        } else if (response.data.gender.length === 1 && response.data.gender[0] === 'male') {
                            $('input[name="gender[]"]').each(function () {
                                if ($(this).val() === '女生') {
                                    $(this).prop('checked', false);
                                    $(this).prop('disabled', true);
                                    $(this).addClass('disabled');
                                }

                                if ($(this).val() === '男生') {
                                    $(this).prop('disabled', false);
                                    $(this).removeClass('disabled');
                                }
                            });
                        } else {
                            $('input[name="gender[]"]').each(function () {
                                $(this).prop('disabled', false);
                                $(this).removeClass('disabled');
                            });
                        }
                    } else {
                        console.error('AJAX 請求失敗:', response.data);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX 請求錯誤:', status, error);
                }
            });
        }, 1000);
    });
});