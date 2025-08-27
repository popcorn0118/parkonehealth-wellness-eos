jQuery(function ($) {
    $("input[name='gender']").on("change", function () {
        var selectedGender = $(this).val();
        console.log("Selected gender: " + selectedGender);
        let _disable_gender = "male";
        if (selectedGender === "男性") {
            _disable_gender = "female";
        }

        let part_list = $("a.part");
        part_list.each(function () {
            let part_gender = $(this).data("gender");
            // 把 part_gender 轉成陣列
            if(part_gender != _disable_gender) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});