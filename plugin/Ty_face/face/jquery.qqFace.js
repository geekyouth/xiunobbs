// QQ表情插件
(function($){  
	$.fn.qqFace = function(options){
		var defaults = {
			id : 'facebox',
			path : 'face/',
			assign : 'content',
			tip : 'em_'
		};
		var option = $.extend(defaults, options);
		var assign = $('#'+option.assign);
		var id = option.id;
		var path = option.path;
		var tip = option.tip;
		if(assign.length<=0){
			return false;
		}
		
		$(this).click(function(e){
			var strFace, labFace;
			
			if(!option.floatContent){
				if($('#'+id).length<=0){
					strFace = '<div id="'+id+'" style="position:absolute;display:none;z-index:1000;" class="qqFace">' +
							'<table border="0" cellspacing="0" cellpadding="0"><tr>';
	
					var oneRem = window.getComputedStyle(document.documentElement)["fontSize"]; // 1rem的值
					oneRem = oneRem.substring(0, oneRem.length - 2);
					var mediaWidth = $('.card.card-postlist').parent().width();
					var oneRowCount = Math.floor(mediaWidth / (oneRem * 2.5));
	
					for(var i=1; i<=59; i++){
						labFace = '['+tip+i+']';
						strFace += '<td><img style="width: 2.5rem; max-width: none;" src="'+path+i+'.gif" onclick="$(\'#'+option.assign+'\').insertAtCaret(\'' + labFace + '\');" /></td>';
						if( i % oneRowCount == 0 ) strFace += '</tr><tr>';
					}
					strFace += '</tr></table>';
					strFace += '<div style="width: 100%; height: 3rem;"></div>';
					strFace += '</div>';
				}

				// 原来没有option.floatSize
				$(this).parent().append(strFace);
				var offset = $(this).position();
				var top = offset.top + $(this).outerHeight();
				$('#'+id).css('top',top);
				// $('#'+id).css('left',offset.left);
				$('#'+id).css('right', 0);
				$('#'+id).show();
				e.stopPropagation();
			} else {
				if($('#'+id).length<=0){
					strFace = '<div id="'+id+'" style="position:absolute; display:none; z-index:1000; width: 100%; padding: 0;" class="qqFace">' +
							'<div border="0" cellspacing="0" cellpadding="0" style="display: flex; flex-wrap: wrap; ">';
					
					var oneDivWidth = $('#sqFloatReply').width() / 10; // 单个表情的宽度，一行10个表情
	
					for(var i=1; i<=59; i++){
						labFace = '['+tip+i+']';
						strFace += '<div style="width: ' + oneDivWidth + 'px; height: ' + oneDivWidth + 'px;"><img style="width: 100%; height: 100%; max-width: none;" src="'+path+i+'.gif" facetext="' + labFace + '" /></div>';
					}
					strFace += '</div></div>';
				}
				
				var sqFaceClick = function(e) {
					var labFace = $(e.target).attr('facetext');
					var originVal = $('#'+option.assign).val();
					$('#'+option.assign).val(originVal + labFace);
					e.stopPropagation();
				}

				$('#sqFloatReply').append(strFace);
				$('#'+id).find('img').on('click', sqFaceClick); // 注册点击事件
				$('#'+id).css('position', 'relative');
				$('#'+id).show();
				e.stopPropagation();
			}
		});

		$(document).click(function(){
			$('#'+id).hide();
			$('#'+id).remove();
		});
	};

})(jQuery);

jQuery.extend({ 
unselectContents: function(){ 
	if(window.getSelection) 
		window.getSelection().removeAllRanges(); 
	else if(document.selection) 
		document.selection.empty(); 
	} 
}); 
jQuery.fn.extend({ 
	selectContents: function(){ 
		$(this).each(function(i){ 
			var node = this; 
			var selection, range, doc, win; 
			if ((doc = node.ownerDocument) && (win = doc.defaultView) && typeof win.getSelection != 'undefined' && typeof doc.createRange != 'undefined' && (selection = window.getSelection()) && typeof selection.removeAllRanges != 'undefined'){ 
				range = doc.createRange(); 
				range.selectNode(node); 
				if(i == 0){ 
					selection.removeAllRanges(); 
				} 
				selection.addRange(range); 
			} else if (document.body && typeof document.body.createTextRange != 'undefined' && (range = document.body.createTextRange())){ 
				range.moveToElementText(node); 
				range.select(); 
			} 
		}); 
	}, 

	setCaret: function(){ 
		if(!$.browser.msie) return; 
		var initSetCaret = function(){ 
			var textObj = $(this).get(0); 
			textObj.caretPos = document.selection.createRange().duplicate(); 
		}; 
		$(this).click(initSetCaret).select(initSetCaret).keyup(initSetCaret); 
	}, 

	insertAtCaret: function(textFeildValue){ 
		var textObj = $(this).get(0); 
		if(document.all && textObj.createTextRange && textObj.caretPos){ 
			var caretPos=textObj.caretPos; 
			caretPos.text = caretPos.text.charAt(caretPos.text.length-1) == '' ? 
			textFeildValue+'' : textFeildValue; 
		} else if(textObj.setSelectionRange){ 
			var rangeStart=textObj.selectionStart; 
			var rangeEnd=textObj.selectionEnd; 
			var tempStr1=textObj.value.substring(0,rangeStart); 
			var tempStr2=textObj.value.substring(rangeEnd); 
			textObj.value=tempStr1+textFeildValue+tempStr2; 
			textObj.focus(); 
			var len=textFeildValue.length; 
			textObj.setSelectionRange(rangeStart+len,rangeStart+len); 
			textObj.blur(); 
		}else{ 
			textObj.value+=textFeildValue; 
		} 
	} 
});