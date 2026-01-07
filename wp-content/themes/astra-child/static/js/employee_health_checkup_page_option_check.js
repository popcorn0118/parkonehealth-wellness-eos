jQuery(function($){
    $("#last_period").hide();

    // radio button change event
    $("[name='gender']").on('change',function(){
        var gender = $(this).val();
        console.log(gender);
        if(gender == '女性'){
            $("#last_period").show();
        } else {
            $("#last_period").hide();
        }
    });
});