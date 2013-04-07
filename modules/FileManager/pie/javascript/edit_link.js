String.prototype.phpimageeditor_add_editlink_endswith = function(str) 
{
	return (this.match(str+"$")==str);
}

function phpimageeditor_add_editlink(pathToEditor, pathToPlugin, hostPath, editImageText, language)
{
	var isJoomla3 = (parseFloat(PIE_SYSTEM_VERSION) >= 3);
	
	var mediamanagerForm = document.getElementById("mediamanager-form");
	if (mediamanagerForm != null)
	{
		var trs = mediamanagerForm.getElementsByTagName("tr");
		var modeDetailed = (trs.length > 0);
		
		if (modeDetailed)
		{
			var isTableHeader = true;
			
			for(var i=0;i<trs.length;i++)
			{
				if (isTableHeader)
				{
					var th = document.createElement("th");
					th.appendChild(document.createTextNode(editImageText));
					trs[i].appendChild(th);
				}
				else 
				{
					var td = document.createElement("td");
					
					var imageSrcDetailed = "";
					var links = trs[i].getElementsByTagName("a");
					var foundDetailedImage = false;

					for(var c=0;c<links.length;c++)
					{
						if (links[c].className == 'img-preview')
						{
							imageSrcDetailed = phpimageeditor_urlencode(links[c].href.replace(hostPath,'../../../')); 
							
							if (phpimageeditor_file_is_image(imageSrcDetailed))
							{
								if (trs[i].innerHTML.indexOf('folderup_16.png') == -1 && trs[i].innerHTML.indexOf('folder_sm.png') == -1)
								{
									td.innerHTML = '<div><a style="color: #0B55C4; background-position: 5px 0; background-image: url('+pathToPlugin+'lite/shared/images/edit.gif); background-repeat: no-repeat; padding-bottom: 4px; padding-left: 22px;" href="'+pathToEditor+imageSrcDetailed+'&language='+language+'" target="_blank">'+editImageText+'</a></div>';
									foundDetailedImage = true;
									break;
								}
							}
						}
					}
					
					if (!foundDetailedImage)
						td.innerHTML = "&nbsp;";

					trs[i].appendChild(td);
				}
				
				isTableHeader = false;			
			}
		}
		else
		{
			var imgClassName = isJoomla3 ? 'imgOutline thumbnail height-80 width-80 center' : 'imgOutline';
			var mainTagName = isJoomla3 ? 'li' : 'div';

			var e = mediamanagerForm.getElementsByTagName(mainTagName);
			
			for(var i=0;i<e.length;i++)
			{
				if (e[i].className == imgClassName && e[i].innerHTML.indexOf('folderup_32.png') == -1 && e[i].innerHTML.indexOf('folder.png') == -1)
				{
					var images = e[i].getElementsByTagName("img");
					var imageSrc = "";
					
					if (images.length > 0)
					{
						imageSrc = phpimageeditor_urlencode(images[0].src.replace(hostPath,'../../../'));
		
						if (phpimageeditor_file_is_image(imageSrc)) {
							
                            if (isJoomla3) {
                                
                                var divs = e[i].getElementsByTagName("div");
                                divs[2].innerHTML = ''; //Remove image name.
                                
                                e[i].style.overflow = 'hidden';
                                
                                e[i].innerHTML = '<a style="cursor: pointer; text-decoration: underline; color: #0088CC; background-position: 5px 0; background-image: url('+pathToPlugin+'lite/shared/images/edit.gif); background-repeat: no-repeat; display: block; font-size: 11px; padding-left: 22px;" href="'+pathToEditor+imageSrc+'&language='+language+'" target="_blank">'+editImageText+'</a>' + e[i].innerHTML;
                               
                            } else
                            	e[i].innerHTML += '<a style="color: #0B55C4; background-position: 5px 0; background-image: url('+pathToPlugin+'lite/shared/images/edit.gif); background-repeat: no-repeat; padding-bottom: 4px; padding-left: 22px; display: block;" href="'+pathToEditor+imageSrc+'&language='+language+'" target="_blank">'+editImageText+'</a>';
						}
					}
				}
			}
		}
	}
	else
	{
		var itemElements = document.getElementsByTagName(isJoomla3 ? "li" : "div");
		var itemClass = isJoomla3 ? 'imgOutline thumbnail height-80 width-80 center' : 'item';
		var uls = isJoomla3 ? document.getElementsByTagName('ul') : null;
		var foundManager = false;
		var foundItem = false;
		var managerElement = null;

		for(var i=0;i<itemElements.length;i++)
		{
			if (isJoomla3) {

				if (uls.length > 0 && uls[0].className == 'manager thumbnails') {
					foundManager = true;
					managerElement = uls[0];
				}

				if (itemElements[i].className == itemClass)
					foundItem = true;
				
			} else {	

				if (itemElements[i].className == 'manager')
				{
					foundManager = true;
					managerElement = itemElements[i];			
				}
				else if (itemElements[i].className == 'item')
					foundItem = true;
				
			}	
				
			if (foundManager && foundItem)
				break;
		}		
			
		if (foundManager && foundItem)
		{
			for(var i=0;i<itemElements.length;i++)
			{
				if (itemElements[i].className == itemClass)
				{
					var imagesArticle = itemElements[i].getElementsByTagName("img");
					var imageSrcArticle = "";
					
					if (imagesArticle.length > 0 && imagesArticle[0].src.indexOf('folder.gif') == -1 && phpimageeditor_file_is_image(imagesArticle[0].src))
					{
						imageSrcArticle = phpimageeditor_urlencode(imagesArticle[0].src.replace(hostPath,'../../../'));
						
						if (isJoomla3) {

                            var divs = itemElements[i].getElementsByTagName("div");
                            divs[1].innerHTML = ''; //Remove image name.
                            
                            itemElements[i].style.overflow = 'hidden';
							
							itemElements[i].innerHTML = '<a style="font-size: 11px; cursor: pointer; text-decoration: none; color: #0088CC; display: block; line-height: 15px; height: auto;" href="'+pathToEditor+imageSrcArticle+'&language='+language+'" target="_blank">'+editImageText+'</a>' + itemElements[i].innerHTML;
						}
						else
							itemElements[i].innerHTML = '<a style="color: #0B55C4; position: absolute; top: 0; left: 0; display: block; line-height: 15px; height: auto;" href="'+pathToEditor+imageSrcArticle+'&language='+language+'" target="_blank">'+editImageText+'</a>' + itemElements[i].innerHTML;
					}
				}
			}
		}
	}
}

function phpimageeditor_urlencode(str) 
{
    // http://kevin.vanzonneveld.net
    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // %          note: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // *     example 1: urlencode('Kevin van Zonneveld!');
    // *     returns 1: 'Kevin+van+Zonneveld%21'
    // *     example 2: urlencode('http://kevin.vanzonneveld.net/');
    // *     returns 2: 'http%3A%2F%2Fkevin.vanzonneveld.net%2F'
    // *     example 3: urlencode('http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a');
    // *     returns 3: 'http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a'
                                     
    var histogram = {}, histogram_r = {}, code = 0, tmp_arr = [];
    var ret = str.toString();
    
    var replacer = function(search, replace, str) {
        var tmp_arr = [];
        tmp_arr = str.split(search);
        return tmp_arr.join(replace);
    };
    
    // The histogram is identical to the one in urldecode.
    histogram['!']   = '%21';
    histogram['%20'] = '+';
    
    // Begin with encodeURIComponent, which most resembles PHP's encoding functions
    ret = encodeURIComponent(ret);
    
    for (search in histogram) {
        replace = histogram[search];
        ret = replacer(search, replace, ret) // Custom replace. No regexing
    }
    
    // Uppercase for full PHP compatibility
    return ret.replace('/(\%([a-z0-9]{2}))/g', function(full, m1, m2) {
        return "%"+m2.toUpperCase();
    });
    
    return ret;
}

function phpimageeditor_urldecode(str) 
{
    // Decodes URL-encoded string  
    // 
    // version: 1103.1210
    // discuss at: http://phpjs.org/functions/urldecode    // +   original by: Philip Peterson
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: AJ
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // +      input by: travc
    // +      input by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Lars Fischer
    // +      input by: Ratheous    // +   improved by: Orlando
    // +      reimplemented by: Brett Zamir (http://brett-zamir.me)
    // +      bugfixed by: Rob
    // +      input by: e-mike
    // +   improved by: Brett Zamir (http://brett-zamir.me)    // %        note 1: info on what encoding functions to use from: http://xkr.us/articles/javascript/encode-compare/
    // %        note 2: Please be aware that this function expects to decode from UTF-8 encoded strings, as found on
    // %        note 2: pages served as UTF-8
    // *     example 1: urldecode('Kevin+van+Zonneveld%21');
    // *     returns 1: 'Kevin van Zonneveld!'    // *     example 2: urldecode('http%3A%2F%2Fkevin.vanzonneveld.net%2F');
    // *     returns 2: 'http://kevin.vanzonneveld.net/'
    // *     example 3: urldecode('http%3A%2F%2Fwww.google.nl%2Fsearch%3Fq%3Dphp.js%26ie%3Dutf-8%26oe%3Dutf-8%26aq%3Dt%26rls%3Dcom.ubuntu%3Aen-US%3Aunofficial%26client%3Dfirefox-a');
    // *     returns 3: 'http://www.google.nl/search?q=php.js&ie=utf-8&oe=utf-8&aq=t&rls=com.ubuntu:en-US:unofficial&client=firefox-a'
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

function phpimageeditor_file_is_image(filePath)
{
	filePath = phpimageeditor_urldecode(filePath);
	filePath = filePath.toLowerCase();
	//Exclude files that not are images.
	//In Joomla 1.6, 1.7 media/media/images/ contains not image mimetype thumbnails.
	//In Joomla 1.5 administrator/components/com_media/images/ contains not image mimetype thumbnails.
	return (filePath.indexOf('media/media/images/') == -1 && filePath.indexOf('administrator/components/com_media/images/') == -1 && (filePath.phpimageeditor_add_editlink_endswith("jpg") || filePath.phpimageeditor_add_editlink_endswith("jpeg") || filePath.phpimageeditor_add_editlink_endswith("gif") || filePath.phpimageeditor_add_editlink_endswith("png")));
}