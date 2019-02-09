jQuery(document).ready(function(){
    
    	var tree = dhtmlXTreeFromHTML("treeboxbox_tree"); 
			tree.checkbox = true;
			tree.setOnClickHandler(tonclick);
			
        $("#uploader_block").fadeToggle() ;
        $("#files_block").fadeToggle() ;
        $("#upload_settings").fadeToggle() ;
        $("#results").fadeToggle() ;
		
		$("#f_sort_type").change(function(){
		  showfiles('');
		});
		
		showfiles('');		

    })


function showfiles(id){
	 // не мешало бы проверить - открыт ли блок с файлами 		  
	  var ajax_path = $("#f_ajax_path").val();
      var sort_type = document.getElementById("f_sort_type").value	  
	  var data = 'type=get_files&dir=' + id  + '&s_t=' + sort_type; 
	  var result = $("#files_list");
	  
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  result.html(json.error_description);
					}
					else{
					  files = json.resp;
						result.html(files);
					}
				},
			error:
				function(){
					result.html('Ошибка');
				}
		});
}
    
function tonclick(id){
				if(id == 'uploads')
				{ 
				  id = '';
			    document.getElementById("f_current_dir").value = '';
			  }
			  else
			  {
			     id = id + '/';
			     document.getElementById("f_current_dir").value = id;
			  }
	 
	 showfiles(id);
	  $("#files_tips").fadeOut() ;
	 
};    


function addimg(id) {
  var content = document.getElementById("f_content");
  var img_code = document.getElementById("img_code" + id).value;
  
if (document.selection) {
			content.focus();
			sel = document.selection.createRange();
			sel.text = img_code;
			content.focus();
}
else if (content.selectionStart || content.selectionStart == '0') {
			var startPos = content.selectionStart;
			var endPos = content.selectionEnd;
			var cursorPos = endPos;
			var scrollTop = content.scrollTop;
			if (startPos != endPos) {
				 content.value = content.value.substring(0, startPos)
							  + img_code
							  + content.value.substring(startPos, endPos)
							  + content.value.substring(endPos, content.value.length);
				cursorPos = startPos + t.length
			}
			else {
				content.value = content.value.substring(0, startPos)
								  + img_code
								  + content.value.substring(endPos, content.value.length);
				cursorPos = startPos + t.length;
			}
			content.focus();
			content.selectionStart = cursorPos;
			content.selectionEnd = cursorPos;
			content.scrollTop = scrollTop;
		}
		else {
			content.value += img_code;
		}
}	

function showuploader(){
       $("#uploader_block").fadeToggle() ;
       }	
       
function showdir(){
       $("#files_block").fadeToggle() ;
       }	       

function showsettings(){
       $("#upload_settings").fadeToggle() ;
       }
       
function showresuts(){
       $("#upl_message").fadeToggle() ;
       }
       


     