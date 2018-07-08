$(function () {
	$.nette.init();
});

$(document).ready(function () {

	$("#node").keypress(function (e) {
		if (e.which == 13 && $(this).val() != '') {
			$.nette.ajax({
				type: "POST",
				url : $("#node").attr("data-link"),
				data: {
					"todo-value": $("#node").val()
				}
			});
			$(this).val(null);
		}
	});

	$(document).on('keypress', 'input[type=text]', function (e) {

		var li = $(this).parent();
		if (e.which === 13) {
			if ($(this).val() !== li.attr("data-value") && $(this).val() !== '') {
				$.nette.ajax({
					type: "POST",
					url : $(this).attr("data-link"),
					data: {
						"todo-value" : $(this).val(),
						"todo-id"    : li.attr("data-id"),
						"todo-toggle": "label"
					}
				});
				li.attr('data-value', $(this).val());
				li.find('label').text($(this).val());
				$(this).val(li.attr("data-value"));
			} else {
				$.nette.ajax({
					type: "POST",
					url : $(this).attr("data-link"),
					data: {
						"todo-id"    : li.attr("data-id"),
						"todo-toggle": "label"
					}
				});
			}
		}
	});

	$(document).on('focusout', '.edit-input', function () {
		$.nette.ajax({
			type: "POST",
			url : $(this).attr("data-link-edit"),
			data: {
				"todo-id": null,
			}
		});
	});
        
         $('#snippet-todo-wholeList').sortable({

             items: 'li',
             cursor: 'move',
             update: function(event, ui){
                 var item = ui.item,
                     order = $('#snippet-todo-wholeList').sortable('toArray');
                 $.nette.ajax({
			type: "POST",
			url :  $('#snippet-todo-toDoList').attr("data-link"),
			data:{ 
                            "todo-order": order
                        }
		});     
             }
         });
        
});