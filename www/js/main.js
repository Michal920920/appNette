$(function () {
	$.nette.init();
});

$(document).ready(function () {


nodesSortable();
$(document).ajaxComplete(nodesSortable);
$(document).ajaxComplete(boxSubnodesSortable);

$(document).on("click",".hideSubnodes", function(){
    subnodesClear();           
}); 

$(document).on("click",".showSubnodes", function(){
    
   var div = $(this).parent(); 
   
   $.nette.ajax({
            type: "POST",
            url :  $(this).attr("data-link"),
            data:{
                "todo-id": div.attr("data-id")
            },success:function(){
                $(document).ajaxComplete(subnodesSortable);
            }
	});      
                
    let subnodesSortable = function(){
       var todo = div.attr('data-id');
       $('.toDoListSub'+todo).sortable({
           items: '.subnodeList',
           cursor: 'move',
              update: function(event, ui){
                    var item = ui.item,
                        order = $('.toDoListSub'+todo).sortable('toArray',{attribute: 'data-id'});
                    $.nette.ajax({
                           type: "POST",
                           url :  $('.nodes').attr("data-link"),
                           data:{ 
                               "todo-order": order,
                               "todo-table": 'subnodes',
                               "todo-id": div.attr('data-id')
                               }
                            });
                    }
               });
           }; 
                
});

function nodesSortable(){
        var sub = $('.subnodeList').parent().attr("data-id");
             $('.nodes').sortable({
             items: '.nodeList',
             cursor: 'move',
             update: function(event, ui){
                 var item = ui.item,
                 order = $('.nodes').sortable('toArray',{attribute: 'data-id'});
                 $.nette.ajax({
			type: "POST",
			url :  $('.nodes').attr("data-link"),
			data:{ 
                            "todo-order": order,
                            "todo-table": 'nodes',
                            "todo-id": sub
                        }
		});     
             }
         });
         
 }; 
 
function subnodesClear(){
          $.nette.ajax({
			type: "POST",
			url :  $('.hideSubnodes').attr("data-link"),
			data:{
                            "todo-id": null
                        }
                        });
                        
 };
 
function getEditableNode(node){
              $('#boxNodeEdit').attr("value", node.attr("data-value"));
              $('#boxNodeEdit').attr("data-id", node.attr("data-id"));
              $.nette.ajax({
			type: "POST",
			url : $('#nodeEdit').attr("data-link"),
                        data:{
                            "todo-id": node.attr("data-id"),
                            "todo-value": node.attr("data-value")
                        }
                });
     }

$(document).on('click','#nodeEdit',function(){
     var node = $(this).parent();
     getEditableNode(node);
     $(document).ajaxComplete(editBox);
        });
 

$(document).on('click','#newNode', function(){
     newBox();
        });
    
function newBox(){
                $('#newBox').dialog({
                title: 'Nový úkol',
                width: 400,
                height: 400,
                modal: true,
                close: boxSubnodesClear,
                resizable: true,
                buttons:[
                    {
                        text: 'uložit',
                        click: function(){
                            var values = $('#boxSubnodes div input').map(function(){
                            return $(this).val();
                                }).get();
                            $.nette.ajax({
				type: "POST",
				url : $(this).attr("data-link"),
				data: {
					"todo-value": $("#boxNodeNew").val(),
                                        "todo-date": $("#boxDateNew").val(),
                                        "todo-subValue": values
				}
			});
                            boxSubnodesClear();
                            $(this).dialog('close');        
                        }
                    }
                        
            ]
            });
};

 function editBox(){
        $('#editBox').dialog({
                title: 'Editovat úkol',
                width: 400,
                height: 400,
                modal: true,
                resizable: false,
                close: closeFunction,
                buttons:[
                    {
                        text: 'uložit',
                        click: function(){
                            var values = $('#boxSubnodes div input').map(function(){
                            return $(this).val();
                                }).get();
                            $.nette.ajax({
				type: "POST",
				url : $(this).attr("data-link"),
				data: {
					"todo-value": $("#boxNodeEdit").val(),
                                        "todo-date": $("#boxDateEdit").val(),
                                        "todo-subValue": values,
                                        "todo-id": $("#boxNodeEdit").attr('data-id')
				}
			});
                        $(this).dialog('close');
                        }
                    }
                        
            ] 
            });
    };
    

        
$(document).on('click', '#boxAddSubnodeNew', function(){
    var id = 0;
    $('.boxSubnodes').each(function() {
    id = Math.max(this.id, id);
    });
    
    if(id){
        id = Number(id) + 1;
        var input = $('<div class="boxSubnode"><input type="text" class="boxSubnodeInput" id="'+id+'"/><div class="boxMoveSubnode">↕</div><div class="boxDeleteSubnode">x</div></div>');
    }else{
        var input = $('<div class="boxSubnode"><input type="text" class="boxSubnodeInput" id="1"/><div class="boxMoveSubnode">↕</div><div class="boxDeleteSubnode">x</div></div>');
    }
    
    
    $('#boxSubnodes').append(input);
    boxSubnodesSortable();
});

$(document).on('click', '#boxAddSubnodeEdit', function(){
    var boxSubnode = $('<div class="boxSubnode"><input type="text" class="boxSubnodeInput"/><div class="boxMoveSubnode">↕</div><div class="boxDeleteSubnode">x</div></div>');
    $('#boxSubnodes:last-child').append(boxSubnode);
     boxSubnodesSortable();
    
});


$(document).on('click', '.boxDeleteSubnode', function(){
   
    $(this).parent().remove();
});

  function closeFunction(){
     $(this).dialog('destroy').remove();
 };       

function boxSubnodesClear(){
       $('#boxSubnodes').children().remove();
         
 }; 
 
 function boxSubnodesSortable(){
             $('#boxSubnodes').sortable({
             items: '.boxSubnode',
             cursor: 'move'
         });
         
 }; 
        
 });
