console.log(parts_options_list);
jQuery(function ($) {
    $('input[type="radio"]').on('change', function () {
        let _gender = $("[name='gender']:checked").val();
        let _age = $("[name='age']:checked").val();        
        $(".s_part").hide();
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
                        $(".s_part[data-part='"+part.post_title+"']").show();
                    });
                }
            });
        }
        return;
    });
});