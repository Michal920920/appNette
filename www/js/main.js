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
                    "todo-value": $("#node").val()
                    }
		})
            $(this).val(null);
        }
    });

    $("#snippet-todo-wholeList").on('click','button', function(){
             $.nette.ajax({
                type: "POST",
		url: $(this).attr("data-link"),
                data: {
                    "todo-id": $(this).attr("data-id")
                    }
		})
    });

    $(document).on('dblclick','label.singleNode', function () {
        var li = $(this).parent();
        li.find('label').hide();
        li.find('input[type="text"]').show().focus();
    });
    
    $(document).on('keypress','input[type=text]', function(e) {
        if(e.which == 13){
            var li = $(this).parent();
            $(this).hide();
        
            if($(this).val() !== '' && $(this).val() !== li.attr("data-value")){
                
                $.nette.ajax({
                    type: "POST",
                    url: $(this).attr("data-link"),
                    data: {
                        "todo-value": $(this).val(),
                        "todo-id": li.attr("data-id")
                        }
                    }); 
                li.attr('data-value', $(this).val());
                li.find('label').text($(this).val()); 
            }
            $(this).val(li.attr("data-value"));
            li.find('label').show();
        }

    });

    $(document).on('focusout','input[type=text]', function() {
        
        var li = $(this).parent();
        $(this).hide();
        li.find('label').show();
        $(this).val(li.attr("data-value"));
    });
});