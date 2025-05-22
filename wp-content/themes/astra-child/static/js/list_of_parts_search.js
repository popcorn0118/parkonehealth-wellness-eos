console.log(parts_options_list);
jQuery(function ($) {
    $('input[type="radio"]').on('change', function () {
        let _gender = $("[name='gender']:checked").val();
        let _age = $("[name='age']:checked").val();        
        // $(".part_tag").hide();
        $(".part").removeClass("suggested");

        if (_gender === '男性') {
            $(".preview-img img.male").addClass("show");
            $(".preview-img img.female").removeClass("show");
        } else if (_gender === '女性') {
            $(".preview-img img.female").addClass("show");
            $(".preview-img img.male").removeClass("show");
        }

        if (_gender == undefined || _age == undefined) {
            return;
        } else {
            switch(_gender){
                case '男性':
                    _gender = 'male';
                    break;
                case '女性':
                    _gender = 'female';
                    break;
            }
            switch(_age){
                case '30歲以下':
                    _age = '30';
                    break;                
                case '31~60歲':
                    _age = '31-60';
                    break;
                case '60歲以上':
                    _age = '60';
                    break;                
            }
            parts_options_list.forEach(function (item) {
                if(_age == item.age && _gender == item.gender){
                    item.parts.forEach(function (part) {
                        $(".part_tag[data-part='"+part.post_title+"']").closest(".part").addClass("suggested");
                    });
                }
            });
        }
        return;
    });




    $(".part").on("mouseenter", function () {
        const $this = $(this);
        const title = $this.data("title");
        const excerpt = $this.data("excerpt");
        const img = $this.data("img");
        const img_male = $this.data("img-male");
        const img_female = $this.data("img-female");
        const gender = $("[name='gender']:checked").val();
        
        $(".preview-title").text(title);
        $(".preview-excerpt").text(excerpt);
        $(".preview-btn").attr("href", '/checkup_body_parts/' + title);
        $(".preview-btn").text('了解相關方案');
        if (title) {
            $(".preview-info").addClass('show');
        } else {
            $(".preview-info").removeClass('show');
        }

        // 更新圖片 src 和 alt
        $(".preview-img img.male").attr({ src: img_male, alt: img_male });
        $(".preview-img img.female").attr({ src: img_female, alt: img_female });
        // 預設先全部移除 .show
        $(".preview-img img").removeClass("show");
        // 根據性別顯示對應圖片，若未選則套預設邏輯
        if (gender === '男性' && img_male) {
            $(".preview-img img.male").addClass("show");
        } else if (gender === '女性' && img_female) {
            $(".preview-img img.female").addClass("show");
        } else if (img_male) {
            $(".preview-img img.male").addClass("show");
        } else if (img_female) {
            $(".preview-img img.female").addClass("show");
        }
        
      });
});