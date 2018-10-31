$(function () {
	$.nette.init();
});

$(document).ready(function () {


nodesSortable();
$(document).ajaxComplete(nodesSortable);
$(document).ajaxComplete(boxSubnodesSortable);
hoverEnabled = true;

$(document).on("click",".hideSubnodes", function(){
    subnodesClear();           
}); 

var subnodesSortable;

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
                
     subnodesSortable = function(){
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


$(document).on('click','#nodeEdit',function(){
     var node = $(this).parent();
     getEditableNode(node);
     $(document).ajaxComplete(editBox);
        });
        
 
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

 function editBox(){
        $('#editBox').dialog({
                title: 'Editovat úkol',
                width: 400,
                height: 400,
                modal: true,
                resizable: false,
                close: closeFunction,
                buttons:[{
                        text: 'uložit',
                        click: function(){
                            var values = $('.boxSubnodeInput').map(function(){
                            return $(this).val();
                                }).get();
                            if($("#boxNodeEdit").val() == 0){
                                alert('Prosím, vyplňte pole Název');
                            }else{
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
                             $(document).ajaxComplete(subnodesSortable);
                             $(this).dialog('close');   
                            }
                        }
                    }] 
            });
    };    

$(document).on('click','#newNode', function(){
        $('#newBox').dialog({
                title: 'Nový úkol',
                width: 400,
                minHeight: 400,
                modal: true,
                close: boxSubnodesClear,
                resizable: true,
                buttons:[{
                        text: 'uložit',
                        id: 'saveButton',
                        click: function(){
                            var values = $('.boxSubnodeInput').map(function(){
                            return $(this).val();
                                }).get();
                            if($("#boxNodeNew").val() == 0){
                                alert('Prosím, vyplňte pole Název');
                            }else{
                                $.nette.ajax({
                                    type: "POST",
                                    url : $(this).attr("data-link"),
                                    data: {
                                            "todo-value": $("#boxNodeNew").val(),
                                            "todo-date": $("#boxDateNew").val(),
                                            "todo-subValue": values
                                    }
			});
                            $(document).ajaxComplete(subnodesSortable);
                            $(this).dialog('close');   
                            }
                        }
                    }]
            });
        });



$(document).on('click', '#boxAddSubnodeNew', function(){
        var input = $(
                '<div class="boxSubnode">\n\
                    <textarea class="boxSubnodeInput"></textarea>\n\
                    <div class="boxMoveSubnode">↕</div>\n\
                    <div class="boxDeleteSubnode">x</div>\n\
                </div>');
    
    $('#newBox #boxSubnodes').append(input);
    boxSubnodesSortable();
});

$(document).on('click', '#boxAddSubnodeEdit', function(){
    var boxSubnode = $(
            '<div class="boxSubnode">\n\
                <textarea class="boxSubnodeInput"></textarea>\n\
                <div class="boxMoveSubnode">↕</div>\n\
                <div class="boxDeleteSubnode">x</div>\n\
            </div>');
    
    $('#editBox #boxSubnodes:last-child').append(boxSubnode);
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
 
 $(document).on('input',"#boxNodeEdit, #boxNodeNew", function() {
   
    if ($(this).val().length>=30) {
                        $(this).before('<div id="lenghtError">Limit 30 znaků</div>');
            		$('#lenghtError').delay(2000).fadeOut();
    }else if($(this).val().length==0){
        $(this).before('<div id="lenghtError">Toto pole je povinné</div>');
            		$('#lenghtError').delay(2000).fadeOut();
    }else{
        $('#lenghtError').remove();
    }

});

var delay=300, setTimeoutConst;

$(document).on('mouseenter','.nodeList',function(){

   var delButton =  $(this).find('#nodeDelete');
   var editButton =  $(this).find('#nodeEdit');
   $(this).find('.showSubnodes').fadeTo(70, 0.7);
           setTimeoutConst = setTimeout(function(){
               if(hoverEnabled){
                       console.log('hello');
                    delButton.fadeTo(150, 0.8);
                    editButton.fadeTo(150, 0.8);
               }
         }, delay);
    });
    
$(document).on('mouseleave','.nodeList',function(){
    var delButton =  $(this).find('#nodeDelete');
    var editButton =  $(this).find('#nodeEdit');
    $(this).find('.showSubnodes').fadeTo(70, 0.5);
    clearTimeout(setTimeoutConst);
               if(hoverEnabled){
                    delButton.fadeTo(150, 0);
                    editButton.fadeTo(150, 0);
               }
    });    
});