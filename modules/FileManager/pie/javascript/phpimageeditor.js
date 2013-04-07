var objCrop = null;

function ajax_post()
{
	if ($('#loading').css('display') == 'none') 
	{
		$('#width').attr('disabled','disabled');
		$('#height').attr('disabled','disabled');
		$('#keepproportions').attr('disabled','disabled');
		$('#btnRotateLeft').attr('disabled','disabled');
		$('#btnRotateRight').attr('disabled','disabled');
		$('#croptop').attr('disabled','disabled');
		$('#cropbottom').attr('disabled','disabled');
		$('#cropleft').attr('disabled','disabled');
		$('#cropright').attr('disabled','disabled');
		$('#grayscale').attr('disabled','disabled');
		$('#btnupdate').attr('disabled','disabled');
		$('#btnsave').attr('disabled','disabled');
		$('#btnundo').attr('disabled','disabled');
		$('#brightness_slider_track').slider('disable');
		$('#contrast_slider_track').slider('disable');
		$('#loading').css('display','block');
		$('#loading_bar').width(0);
		$('#loading_bar').animate({width: document.getElementById('loading').offsetWidth-30}, document.getElementById('loading').offsetWidth*10);
		
		$.ajax({timeout: AjaxPostTimeoutMs, type: "POST", url: FormAction, data: "grayscaleval="+$('#grayscaleval').val()+"&keepproportionsval="+$('#keepproportionsval').val()+"&width="+$('#width').val()+"&widthoriginal="+$('#widthoriginal').val()+"&height="+$('#height').val()+"&heightoriginal="+$('#heightoriginal').val()+"&rotate="+$('#rotate').val()+"&croptop="+$('#croptop').val()+"&cropleft="+$('#cropleft').val()+"&cropright="+$('#cropright').val()+"&cropbottom="+$('#cropbottom').val()+"&contrast="+$('#contrast').val()+"&brightness="+$('#brightness').val()+"&actiontype="+$('#actiontype').val()+"&panel="+$('#panel').val()+"&language="+$('#language').val()+"&actions="+$('#actions').val()+"&widthlast="+$('#widthlast').val()+"&heightlast="+$('#heightlast').val()+"&userid="+$('#userid').val()+"&contrastlast="+$('#contrastlast').val()+"&brightnesslast="+$('#brightnesslast').val()+"&widthlastbeforeresize="+$('#widthlastbeforeresize').val()+"&heightlastbeforeresize="+$('#heightlastbeforeresize').val()+"&cropkeepproportionsval="+$('#cropkeepproportionsval').val()+"&cropkeepproportionsratio="+$('#cropkeepproportionsratio').val()+"&isajaxpost=true",     	
		success: function(data)
		{	
			$('#phpImageEditor').html(data);
			activate_form();
			phpimageeditor_crop_activator(parseInt($('#panel').val()));
			phpimageeditor_init();
			$('#loading_bar').stop();
			$('#loading').css('display','none');
		},
	    error: function(XMLHttpRequest, textStatus, errorThrown)
	    {
			activate_form();
			$('#ulJsErrors').html("");
			adderror(TextAnUnexpectedError);
			$('#divJsErrors').css('display','block');
			$('#ulJsErrors').css('display','block');
			phpimageeditor_crop_activator(parseInt($('#panel').val()));
			$('#loading_bar').stop();
			$('#loading').css('display','none');
	    }
		});
		
	}
}

function isinteger(s)
{
    var i;

    if (isempty(s))
    if (isinteger.arguments.length == 1) return 0;
    else return (isinteger.arguments[1] == true);

    for (i = 0; i < s.length; i++)
    {
       var c = s.charAt(i);

       if (!isdigit(c)) return false;
    }

    return true;
}

function focus_on_enter(element, event)
{
	if(event.keyCode == 13)
		element.focus();
}

function reload_mouse_crop()
{
	objCrop.destroy();
	objCrop = $.Jcrop('#image',{onChange: set_crop_values,onSelect: set_crop_values, aspectRatio: $("input#cropkeepproportions").attr('checked') ? $("#cropkeepproportionsratio").val() : 0});		
	$(".jcrop-holder").css("display", "none");
	$("#image").css("display", "block");			
}

function update_width(InputWidth, EditForm)
{
	if (isinteger(InputWidth.val()))
	{
		var Width = parseInt($('#width').val());
		var Height = parseInt($('#height').val());
		
		$('#image').css('width',Width+'px');
		
		if ($("input#keepproportions").attr('checked'))
		{
			$('#image').css('height', get_proportional_height(Width, EditForm) + "px");
			$('#height').val(get_proportional_height(Width, EditForm));
		}
		
		update_mouse_resizer();
		reload_mouse_crop();
	}
}

function update_mouse_resizer()
{
	$('#imageResizerKeepProportions').css('width',$('#width').val()+'px');	
	$('#imageResizerKeepProportions').css('height',$('#height').val()+'px');
	$('#imageResizerNoProportions').css('width',$('#width').val()+'px');	
	$('#imageResizerNoProportions').css('height',$('#height').val()+'px');
}

function update_height(InputHeight, EditForm)
{
	if (isinteger(InputHeight.val()))
	{
		var Height = parseInt($('#height').val());
		var Width = parseInt($('#width').val());
		
		$('#image').css('height',$('#height').val()+'px');
		
		if ($("input#keepproportions").attr('checked'))
		{
			$('#image').css('width',get_proportional_width(Height, EditForm)+'px');
			$('#width').val(get_proportional_width(Height, EditForm));
		}
		
		update_mouse_resizer();
		reload_mouse_crop();
	}
}

function isempty(s)
{
	return ((s == null) || (s.length == 0))
}

function isdigit (c)
{
	return ((c >= "0") && (c <= "9"))
}

function isintegerinrange(s, a, b)
{   
	if (isempty(s))
         if (isintegerinrange.arguments.length == 1) return false;
         else return (isintegerinrange.arguments[1] == true);

      // Catch non-integer strings to avoid creating a NaN below,
      // which isn't available on JavaScript 1.0 for Windows.
      if (!isinteger(s, false)) return false;

      // Now, explicitly change the type to integer via parseInt
      // so that the comparison code below will work both on
      // JavaScript 1.2 (which typechecks in equality comparisons)
      // and JavaScript 1.1 and before (which doesn't).
      var num = parseInt (s);
      return ((num >= a) && (num <= b));
}

function validate_form()
{
	var sendForm = true;
	
	var width = $('#width').val();
	var height = $('#height').val();
	var cropleft = $('#cropleft').val();
	var cropright = $('#cropright').val();
	var croptop = $('#croptop').val();
	var cropbottom = $('#cropbottom').val();
	
	$('#divJsErrors').css('display','none');
	$('#ulJsErrors').css('display','none');
	
	$('#ulJsErrors').html("");

	if (width == "")
	{
		adderror("\"" + TextWidth + "\" " + TextIsRequired)
		sendForm = false;
	}
	
	if (height == "")
	{
		adderror("\"" + TextHeight + "\" " + TextIsRequired)
		sendForm = false;
	}
	
	if (!sendForm)
	{
		$('#divJsErrors').css('display','block');
		$('#ulJsErrors').css('display','block');

		return sendForm;
	}
	
	if (!isinteger(width))
	{
		adderror("\"" + TextWidth + "\" " + TextMustBeNumeric)
		sendForm = false;
	}
	
	if (!isinteger(height))
	{
		adderror("\"" + TextHeight + "\" " + TextMustBeNumeric)
		sendForm = false;
	}
	
	if (!sendForm)
	{
		$('#divJsErrors').css('display','block');
		$('#ulJsErrors').css('display','block');
		return sendForm;
	}

	width = parseInt(width);
	height = parseInt(height);

	if (!isintegerinrange(width, 1, ImageMaxWidth))
	{
		adderror("\"" + TextWidth + "\" " + TextNotInRange + ": 1 - " + ImageMaxWidth)
		sendForm = false;
	}
	
	if (!isintegerinrange(height, 1, ImageMaxHeight))
	{
		adderror("\"" + TextHeight + "\" " + TextNotInRange + ": 1 - " + ImageMaxHeight)
		sendForm = false;
	}
		
	
	if ($('#textlist').val() == '-1' && $('#text').val() != '')
	{
		if (!isintegerinrange(textposx, 0, width))
		{
			adderror("\"" + TextStartPositionX + "\" " + TextNotInRange + ": 1 - " + width)
			sendForm = false;
		}
		
		if (!isintegerinrange(textposy, 0, height))
		{
			adderror("\"" + TextStartPositionY + "\" " + TextNotInRange + ": 1 - " + height)
			sendForm = false;
		}
	}
	
	if (!sendForm)
	{
		$('#divJsErrors').css('display','block');
		$('#ulJsErrors').css('display','block');
	}

	return sendForm;
}

function adderror(ErrorText)
{
	$('#ulJsErrors').html($('#ulJsErrors').html()+'<li>'+ErrorText+'</li>');
}

function get_proportional_width(Height, EditForm)
{
	var HeightOriginal = parseInt($("#heightlastbeforeresize").val());
	var WidthOriginal = parseInt($("#widthlastbeforeresize").val());
	
	return Math.round((WidthOriginal/HeightOriginal)*Height);
}

function get_proportional_height(Width, EditForm)
{
	var HeightOriginal = parseInt($("#heightlastbeforeresize").val());
	var WidthOriginal = parseInt($("#widthlastbeforeresize").val());
	
	return Math.round((HeightOriginal/WidthOriginal)*Width);
}

function remove_px(Value)
{
	return Value.replace("px","");
}

function activate_form()
{
	$('#width').removeAttr('disabled');
	$('#height').removeAttr('disabled');
	$('#keepproportions').removeAttr('disabled');
	$('#btnRotateLeft').removeAttr('disabled');
	$('#btnRotateRight').removeAttr('disabled');
	$('#croptop').removeAttr('disabled');
	$('#cropbottom').removeAttr('disabled');
	$('#cropleft').removeAttr('disabled');
	$('#cropright').removeAttr('disabled');
	$('#grayscale').removeAttr('disabled');
	$('#btnupdate').removeAttr('disabled');
	$('#btnsave').removeAttr('disabled');
	$('#btnundo').removeAttr('disabled');
	$('#brightness_slider_track').slider('enable');
	$('#contrast_slider_track').slider('enable');
	
	if ($('#actions').val() == '')
	{
		$('#btnundo').attr('disabled','disabled');
		$('#btnsave').attr('disabled','disabled');
	}
	else
	{
		$('#btnundo').removeAttr('disabled');
		$('#btnsave').removeAttr('disabled');
	}	
}


function set_crop_values(c)
{
	if (isinteger($("#height").val()) && isinteger($("#width").val()))
	{
		$("#croptop").val(c.y);
		$("#cropbottom").val(parseInt($("#height").val()) - (c.y + c.h));
		$("#cropleft").val(c.x);
		$("#cropright").val(parseInt($("#width").val()) - (c.x + c.w));
		$("#cropwidth").html(c.w);
		$("#cropheight").html(c.h);
	}
}

function phpimageeditor_resize_activator(selectedIndex)
{
	if (selectedIndex == MenuResize)
	{
		if ($('#keepproportionsval').val() == "1")
		{
			$("#imageResizerKeepProportions").css("display", "block");
			$("#imageResizerNoProportions").css("display", "none");
		}
		else
		{
			$("#imageResizerKeepProportions").css("display", "none");
			$("#imageResizerNoProportions").css("display", "block");
		}
	}
	else
	{
		$("#imageResizerKeepProportions").css("display", "none");
		$("#imageResizerNoProportions").css("display", "none");
	}
}

function phpimageeditor_panelfade(selectedIndex)
{
	for (i = 0; i < 4; i++)
	{
		if ($('#panel_'+i) != null)
		{
			$("#panel_"+i).css('opacity','0.0');
	
			if (i == selectedIndex)
			{
				$("#menuitem_"+i).removeClass("not-selected");				
				$("#menuitem_"+i).addClass("selected");
				$("#panel_"+i).css('display','block');
				$("#panel_"+i).fadeTo("normal", 1.0);
			}
			else
			{
				$("#menuitem_"+i).removeClass("selected");
				$("#menuitem_"+i).addClass("not-selected");				
				$("#panel_"+i).css('display','none');
			}
		}
	}
	
	if (selectedIndex != MenuCrop)
	{
		$("#cropleft").val("0");
		$("#cropright").val("0");
		$("#croptop").val("0");
		$("#cropbottom").val("0");
		$("#cropwidth").html("0");
		$("#cropheight").html("0");
	}
	
	phpimageeditor_resize_activator(selectedIndex);
	phpimageeditor_crop_activator(selectedIndex);
		
	$("#panel").val(selectedIndex);
}

function phpimageeditor_crop_activator(selectedIndex)
{
	if (objCrop != null)
	{	
		if (selectedIndex != MenuCrop)
		{
			objCrop.release();
			objCrop.disable();
			$(".jcrop-holder").css("display", "none");
			$("#image").css("display", "block");
		}
		else
		{
			objCrop.release();
			objCrop.enable();
			$(".jcrop-holder").css("display", "block");
			$("#image").css("display", "block");
		}
	}
}

function phpimageeditor_init()
{
    objCrop = $.Jcrop('#image',{onChange: set_crop_values,onSelect: set_crop_values, aspectRatio: $("input#cropkeepproportions").attr('checked') ? $("input#cropkeepproportionsratio").val() : 0});
    
    $("#imageResizerKeepProportions").resizable(
    {
        aspectRatio: parseFloat($("input#widthlastbeforeresize").val()) / parseFloat($("input#heightlastbeforeresize").val()),
        stop: function(event,ui)
        {        
            var resize_width = parseInt(remove_px($("#imageResizerKeepProportions").css("width")));
            var resize_height = parseInt(remove_px($("#imageResizerKeepProportions").css("height")));
                        
            $("#image").css("width", resize_width+"px");
            $("#image").css("height", resize_height+"px");
            $("#width").val(resize_width);
            $("#height").val(resize_height);

            update_mouse_resizer();

            $("#imageResizerKeepProportions").css("opacity", "0.0");
                        
            reload_mouse_crop();
        },
        start: function(event,ui)
        {        
            $("#imageResizerKeepProportions").css("opacity", "0.5");
        }
    });
                
    $("#imageResizerNoProportions").resizable(
    {
        aspectRatio: false,
        stop: function(event,ui)
        {        
            var resize_width = parseInt(remove_px($("#imageResizerNoProportions").css("width")));
            var resize_height = parseInt(remove_px($("#imageResizerNoProportions").css("height")));
                        
            $("#image").css("width", resize_width+"px");
            $("#image").css("height", resize_height+"px");
            $("#width").val(resize_width);
            $("#height").val(resize_height);
                        
            update_mouse_resizer();

            $("#widthlastbeforeresize").val(resize_width);
            $("#heightlastbeforeresize").val(resize_height);
            $("#imageResizerKeepProportions").resizable('option','aspectRatio',parseFloat($('#widthlastbeforeresize').val())/parseFloat($('#heightlastbeforeresize').val()));

            $("#imageResizerNoProportions").css("opacity", "0.0");

            reload_mouse_crop();
        },
        start: function(event,ui)
        {        
            $("#imageResizerNoProportions").css("opacity", "0.5");
        }
    });
    
    $("#contrast_slider_track").slider({value: parseInt($("#contrast").val()) + ContrastMax, min: 1, max: ((ContrastMax*2)+1), step: 1,
    	stop: function(event,ui)
        {        
            if (validate_form())
                ajax_post();
        },
        slide: function(event, ui) 
        {
            $("#contrast").val(parseInt(ui.value)-((ContrastMax)+1));
        }
    });
    
    $("#brightness_slider_track").slider({value: (parseInt($("input#brightness").val())+BrightnessMax),min: 1,max: ((BrightnessMax*2)+1),step: 1,
    	stop: function(event,ui)
        {        
            if (validate_form())
                ajax_post();
        },
        slide: function(event, ui) 
        {
            $("#brightness").val(parseInt(ui.value)-(BrightnessMax+1));
        }
    });

    $("#grayscale").click(function()
    {
        if (validate_form())
        {
            $("#grayscale").attr('checked') ? $('#grayscaleval').val('1') : $('#grayscaleval').val('0');
            ajax_post();
        }
    });
                
    $("#btnupdate").click(function()
    {
        if (validate_form())
        {
        	$("#actiontype").val(ActionUpdate);                                      
            ajax_post();
        }
    });
                
    $("#btnundo").click(function()
    {
        if (validate_form())
        {
            $("#actiontype").val(ActionUndo);                                    
            ajax_post();
        }
    });

    $("#btnsave").click(function()
    {
        if (validate_form())
        {
            $("#actiontype").val(ActionSaveAndClose);                                    
            ajax_post();
        }
    });

    $("#btnRotateLeft").click(function()
    {
        if (validate_form())
        {
            $("#rotate").val(ActionRotateLeft);
            ajax_post();
        }
    });

    $("#btnRotateRight").click(function()
    {
        if (validate_form())
        {
            $("#rotate").val(ActionRotateRight);
            ajax_post();
        }
    });
                
    $("#btnRotateLeft_text").click(function()
    {
        if (validate_form())
        {
            $("#rotatetext").val(ActionRotateLeftText);
            ajax_post();
        }
    });

    $("#btnRotateRight_text").click(function()
    {
        if (validate_form())
        {
            $("#rotatetext").val(ActionRotateRightText);
            ajax_post();
        }
    });

    $("#btnDelete_text").click(function()
    {
        if (validate_form())
        {
            $("#deletetext").val(ActionDeleteText);
            ajax_post();
        }
    });

    $("form#" + FormId).submit(function()
    {
        if (validate_form())
        {
            $("#actiontype").val(ActionSaveAndClose);
            return true;
        }
        return false;
    });
    
    $("#textlist").change(function()
    {
        if (validate_form())
        {
            $("#actiontype").val(ActionChangeText);
            ajax_post();
        }
    });
                
    $("#width").keydown(function(event)
    {
        focus_on_enter($("input#btnupdate"), event);
    });
                
    $("#width").keyup(function()
    {
        update_width($(this),$("form#" + FormId));
    });
                
    $("#height").keydown(function(event)
    {
        focus_on_enter($("input#btnupdate"), event);
    });
                
    $("#height").keyup(function()
    {
        update_height($(this),$("form#" + FormId));
    });
                
    $("#keepproportions").click(function()
    {
        if ($(this).attr('checked'))
        {
            $('#keepproportionsval').val('1');
            $('#imageResizerKeepProportions').css('display','block');
            $('#imageResizerNoProportions').css('display','none');  
        }
        else
        {
            $('#keepproportionsval').val('0'); 
            $('#imageResizerKeepProportions').css('display','none');
            $('#imageResizerNoProportions').css('display','block'); 
        }
    });
         

    $("#cropkeepproportions").click(function()
    {
        if ($(this).attr('checked'))
        {
            $('#cropkeepproportionsval').val('1');
        }
        else
        {
            $('#cropkeepproportionsval').val('0'); 
        }
    });
    
    $("#menuitem_" + MenuResize).click(function()
    {
        if ($('#panel').val() != MenuResize)
        {
            phpimageeditor_panelfade(MenuResize);
        }
    });
                
   $("#menuitem_" + MenuRotate).click(function()
   {
       if ($('#panel').val() != MenuRotate)
       {
            phpimageeditor_panelfade(MenuRotate);
        }
    });
            
    $("#menuitem_" + MenuCrop).click(function()
    {
        if ($('#panel').val() != MenuCrop)
        {
            phpimageeditor_panelfade(MenuCrop);
        }
    });
            
    if ($("#menuitem_" + MenuEffects) != null)
    {
        $("#menuitem_" + MenuEffects).click(function()
        {
            if ($('#panel').val() != MenuEffects)
            {
                phpimageeditor_panelfade(MenuEffects);
            }
        });
    }

    $('#cropkeepproportions').change(function(e) 
    {
        if (objCrop != null)
        {
            if (this.checked && parseFloat($("#cropwidth").html()) != 0 && parseFloat($("#cropheight").html()) != 0)
            {   
                var aspectRatio = parseFloat($("#cropwidth").html()) / parseFloat($("#cropheight").html());
                objCrop.setOptions({ aspectRatio: aspectRatio });
                $("#cropkeepproportionsratio").val(aspectRatio);
            }
            else if (this.checked && parseFloat($("#cropwidth").html()) == 0 && parseFloat($("#cropheight").html()) == 0)
            {
                objCrop.setOptions({ aspectRatio: $("#cropkeepproportionsratio").val() });
            }   
            else
            {   
                objCrop.setOptions({ aspectRatio: 0 });
            }
        }
    });

    $("input#width").numeric();
    $("input#height").numeric();
                
    var selectedIndex = parseInt($('#panel').val());
    for (i = 0; i < 4; i++)
    {
        if ($('#panel_'+i) != null)
        {
            if (i == selectedIndex)
            {
                $("#panel_"+i).css('opacity','1.0');
                $("#panel_"+i).css('display','block');
            }
            else
            {
                $("#panel_"+i).css('opacity','0.0');
                $("#panel_"+i).css('display','none');
            }
        }
    }

	phpimageeditor_crop_activator(selectedIndex);
    phpimageeditor_resize_activator(selectedIndex);
}

$(document).ready(function()
{
	phpimageeditor_init();
});