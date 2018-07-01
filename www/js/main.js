    $(function () {
	$.nette.init();
});

$(document).ready(function(){
    
    $("#node").keypress(function(e){
        if(e.which == 13 && $(this).val() != ''){
            $.nette.ajax({
                type: "POST",
		url: $("#node").attr("data-link"),
                data: {
                    "todo-value": $("#node").val()
                    }
		});
            $(this).val(null);
        }
    });

    $("#snippet-todo-wholeList").on('click','.nodeDelete', function(){
        
        var li = $(this).parent();
            $.nette.ajax({
                type: "POST",
		url: $(this).attr("data-link"),
                data: {
                    "todo-id": li.attr("data-id")
                    }
		});
    });
    
    $(document).on('click', ".nodeDone",  function(){
     
        var li = $(this).parent();
        if(li.find('label').hasClass('singleNodedone')){
            $.nette.ajax({
                type: "POST",
                url: $(this).attr("data-link"),
                data: {
                    "todo-id": li.attr("data-id"),
                    "todo-done": ''
                      }
		});
        }else if(li.find('label').hasClass('singleNode')){
            $.nette.ajax({
                type: "POST",
                url: $(this).attr("data-link"),
                data: {
                    "todo-id": li.attr("data-id"),
                    "todo-done": "done"
                      }
		});
        }else{
            li.find('label').attr('class','singleNode');
        }
    });
    
    $(document).on('dblclick','label.singleNode', function () {
        
        var li = $(this).parent();
        $.nette.ajax({
            type: "POST",
            url: $(this).attr("data-link"),
            data: {
                "todo-id": li.attr("data-id"),
                "todo-toggle": "input"
                    },
                    success: function(){
                        $(checkForChanges);
                        }
                    });
        });
        
    $(document).on('keypress','input[type=text]', function(e) {
        
        var li = $(this).parent();
        if(e.which === 13){
            if($(this).val() !== li.attr("data-value") && $(this).val() !== ''){
                $.nette.ajax({
                    type: "POST",
                    url: li.find('label').attr("data-link"),
                    data: {
                        "todo-value": $(this).val(),
                        "todo-id": li.attr("data-id"),
                        "todo-toggle": "label"
                        }
                    });
                li.attr('data-value', $(this).val());
                li.find('label').text($(this).val()); 
                $(this).val(li.attr("data-value"));
            }else{
                $.nette.ajax({
                    type: "POST",
                    url: li.find('label').attr("data-link"),
                    data: {
                        "todo-id": li.attr("data-id"),
                        "todo-toggle": "label"
                        }
                    });
                }
        }
    });

    $(document).on('focusout','.edit-input', function() {
        
        var li = $(this).parent();
                $.nette.ajax({
                    type: "POST",
                    url: li.find('label').attr("data-link"),
                    data: {
                        "todo-id": li.attr("data-id"),
                        "todo-toggle": "label"
                        }
                    });
    });
    
    function checkForChanges(){
        
        if ($('input[type=text]').hasClass('edit-input'))
            $('.edit-input').focus();
        else
            setTimeout(checkForChanges, 500);
        };
});