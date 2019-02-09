jQuery(document).ready(function(){
        $("#uploader_block").fadeToggle() ;
        $("#results").fadeToggle() ;
        $("#upload_tips").fadeToggle() ;
        $("#hide_dir").fadeToggle() ;
    })

function showfiles(id){
	 // не мешало бы проверить - открыт ли блок с файлами 		  
	  var ajax_path = $("#f_ajax_path").val();
	  var data = 'type=get_files&dir=' + id; 
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
  var content = document.getElementById("comments_content");
  var img_code = document.getElementById(id).value;
  
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
       showfiles('');
       var files_block = document.getElementById("files_block");
       files_block.style.display = "block";
       $("#show_dir").fadeOut() ;
       $("#hide_dir").fadeIn() ;
       }	       
       
function hidedir(){
       var files_block = document.getElementById("files_block");
      files_block.style.display = "none";
       $("#hide_dir").fadeOut() ;
       $("#show_dir").fadeIn() ;       
       }	
              
function showsettings(){
       $("#upload_settings").fadeToggle() ;
       }
       
function showresuts(){
       $("#upl_message").fadeToggle() ;
       $("#upload_tips").fadeToggle() ;
       }
       

			
     