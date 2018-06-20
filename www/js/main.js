    $(function () {
	$.nette.init();
});

$(document).ready(function(){

    $("#node").keypress(function(e){
        if(e.which == 13 && $(this).val() != '') {
             $.nette.ajax({
                type: "POST",
		url: $("#node").attr("data-link"),
                data: {
                    value: $("#node").val()
                    }
		})
            $(this).val(null);
        }
    });

    $("#snippet--wholeList").on('click','button', function(){
             $.nette.ajax({
                type: "POST",
		url: $(this).attr("data-link"),
                data: {
                    id: $(this).attr("data-id")
                    }
		})
    });

    $(document).on('dblclick','label.singleNode', function () {
        var li = $(this).parent();
        li.find('label').hide();
        li.find('input[type="text"]').show().focus();
    });

    $(document).on('focusout','input[type=text]', function() {
        
        var li = $(this).parent();
        $(this).hide();
        
        if($(this).val() !== '' && $(this).val() !== li.attr("data-value")){
                
            $.nette.ajax({
                type: "POST",
		url: $(this).attr("data-link"),
                data: {
                    value: $(this).val(),
                    id: li.attr("data-id")
                    }
		}); 
            li.attr('data-value', $(this).val());
            li.find('label').text($(this).val()); 
            }
        li.find('label').show();
    });
});