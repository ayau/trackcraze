function initializeNews(get){
	$("#addpostsubmit").live("click",function(){
		var $whitelist = '<b><i><strong><em><a>',
    		newPostText = strip_tags(cleanHREF($("#addposttextbox").val()), $whitelist);
    		URLtext = escape(newPostText);
    		//INSERT REMOVE ATTR DISABLE BUTTON BEFORE SAVE IS DONE
			 if (newPostText.length > 0) {
            // prevent multiple submissions by disabling button until save is successful
            $("#addpostsubmit").attr("disabled", true); //CHANGE THESE STUFF
            $.ajax({//NEEEEEEEEDDD SPLIT ID. LOOK BELOW
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: "action=addPost&post=" + URLtext+"&get="+get, //URL TEXT IS USED HERE!!!!!!!!!!!!!!!!!!!!!!!!!
    			success: function(r){
    				t=r.split(",");
    				newPostText = replaceURL(newPostText);
    				$("#posts").prepend("<div id="+t[0]+" class='postcontent' style='display:none'><div class='postHeader'><div class='delete sp' hidden></div><div class='miniphoto'>"+t[2]+"</div><div class='postname'>"+t[1]+"</div><div class='posttext'>"+newPostText+"<p class='agotext'>"+t[3]+"</p><a class='postcomment'>comment</a></div></div><div class='kudos'></div><div class='comments'></div><div class='break'></div></div>");//no kudos. You can't kudos your own post
    				$('.url').each( function(){
    					if ($(this).attr('href').substring(0,4)!='http'){
        				$(this).attr('href', 'http://' + $(this).attr('href'));
    				}
    			})
    				$("#"+t[0]).fadeIn('slow');
    				document.getElementById('boardCon').style.height = eval($(document).height() - $(window).height()+300)+'px';
                  $("#addposttextbox").val("");
                 		$.ajax({//NEEEEEEEEDDD SPLIT ID. LOOK BELOW
       						type: "POST",
       						url: "db-interaction/users.php",
       						data: "action=newsupdate&content="+t[0]+
       						"&newstype=0"+"&postto="+get,
       						success: function(){
       							$("#addpostsubmit").removeAttr("disabled");
       						},
      						error: function(){
       						}
      					});
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
        } else {
        	$("#addposttextbox").val("");
        }
        return false; // prevent default form submission
	});

	 function replaceURL(text)//www.google.com/ DOESN"T WORK OTHERS WORK. ALSO COMMENT OUT TEMP!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!"
    {    	
      var exp = /((https?:\/\/[\da-z-]+\.)|(www\.))([\da-z-]+\.)(com|net|org|biz|info|org|gov|mobi|us|co|jp|hk|co\.uk|ca)(\/[\w\.-]*)*/g;
      url =  text.replace(exp,function(m){
      	if(m.substring(0,4)=='http'){
      		return "<a href=\""+m+"\">"+m+"</a>";
  		}else {
  			return "<a href=\"http://"+m+"\">"+m+"</a>";
  		}
  		});
  		return url;
    }
        
	$("#addpostcancel").live("click",function(){
		$("#addposttextbox").val("");
	})
	$(".postcontent").live("mouseover", function(){
		$(this).find(".postHeader").prev().show();
		$(this).css('background','#e1f0fd');
	});
	$(".postcontent").live("mouseout", function(){
		$(this).find(".postHeader").prev().hide();
		$(this).css('background','none');
	});
	$(".commentitem").live("mouseover", function(){
		$(this).find(".delete").show();
		$(this).css('background','#bed5fd');
	});
	$(".commentitem").live("mouseout", function(){
		$(this).find(".delete").hide();
		$(this).css('background','none');
	});
	
	$("#stoptrack").hover(function(){
		$(this).text("Untrack");
		$(this).addClass("red");
	}, function(){
		$(this).text("Tracking");
		$(this).removeClass("red");
	});
	
	$("#stoptrack").live("click",function(){
		$(this).before("<div style='color:#DE1818'>Don't worry, we won't tell anyone..</div>");
		$(this).remove();
	})
	
	$(".acceptTR").live("click",function(){
		id = $(this).parent().attr('class');
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"acceptTR",
    				"UID":id
				},
    				
    			success: function(r){
					$.ajax({
       						type: "POST",
       						url: "/db-interaction/users.php",
       						data: "action=newsupdate&content="+id+
       						"&newstype=20",
       						success: function(){
       							$("."+id+"tag").children().remove();
    							$("."+id+"tag").text('');
								$("."+id+"tag").append(r+" is now tracking your progress.");
       						},
      						error: function(){
       						}
      					});
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});
	$(".ignoreTR").live("click",function(){
		id = $(this).parent().attr('class');
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"ignoreTR",
    				"UID":id
				},
    				
    			success: function(r){
    				$("."+id+"tag").children().remove();
    				$("."+id+"tag").text('');
					$("."+id+"tag").append("You have successfully ignored "+r+". We'll notify him/her that you don't like him/her. haha jkjk. No hard feelings.");
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		});	
	var currentPage=0;
	var donescrolling=false;
	var doneloading=true;
	loadPosts();
	function loadPosts() {
		doneloading= false;
   		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"getPosts",
    				"UID":get,
    				"Page":currentPage
				},
    			success: function(r){
    				r=replaceURL(r);
    				$("#posts").append(r);
    				$("#overlay").hide();//or fadeout
    				if (r!=""){
    				currentPage++;
    				document.getElementById('boardCon').style.height = eval($(document).height() - $(window).height()+300)+'px';
				}else{
					donescrolling=true;
				}
				doneloading=true;
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		}

	//$("#tweets").scroll(function() {
   // We check if we're at the bottom of the scrollcontainer
   //if ($(this)[0].scrollHeight - $(this).scrollTop() == $(this).outerHeight()) {
 
      // If we're at the bottom, show the overlay and retrieve the next page
    //  currentPage++;
    //  $("#overlay").fadeIn();
    //  loadPosts();
  // }
//});

	$(window).scroll(function(){
        if  ($(window).scrollTop() == $(document).height() - $(window).height()&&donescrolling==false&&doneloading==true){
        	//document.getElementById('overlay').style.top = eval($(document).height()-45)+'px'; 
        	document.getElementById('overlay').style.height= eval($('#boardCon').height())+'px';
        	$("#overlay").fadeIn();
           loadPosts();
        }
	});
	
	$(".postcomment").live("click",function(){
		$(this).parent().parent().parent().find('.commentbox').prev().show();
		$(this).parent().parent().parent().find('.commentbox').remove();
		$(this).parent().append("<div class='commentbox'>Comment<textarea class='addpostcomment' placeholder='Remember, be nice!' cols='80' rows='1' autocomplete='off' ></textarea><input id='commentsubmit' style='margin-top:2px' class='small box' type='button' value='Post it!'/><input id='commentcancel' style='margin-left:5px; margin-top:2px' class='small grey box' type='button' value='Cancel'/></div>");
		$(".addpostcomment").autogrow(); 
        $(".addpostcomment").charCounter(150);
        $(".addpostcomment").select();
        $(this).hide();
	})
	$("#commentcancel").live("click",function(){
		$(this).parent().prev().show();
		$(this).parent().remove();
		});
	$("#commentsubmit").live("click",function(){
		var $whitelist = '<b><i><strong><em><a>',
    		text = strip_tags(cleanHREF($(this).prev().prev().val()), $whitelist);
    		//URLtext = escape(text);
        if (text.length > 0) {
        	$("#commentsubmit").attr("disabled", true);
		thiscache = $(this);
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"addComment",
    				"type":0,
    				"pid":$(this).parent().parent().parent().attr("id"),
    				"text":text
				},
    			success: function(r){
    				thiscache.parent().parent().next().next().append(r);
    				thiscache.parent().prev().show();
					thiscache.parent().remove(); 
					$("#"+$(r).attr('id')).each(function(){
					$(this)[0].onmouseover=function(){
						$(this).find(".delete").show();
						$(this).css('background','#bed5fd');
					}
					$(this)[0].onmouseout=function(){
						$(this).find(".delete").hide();
						$(this).css('background','none');
					}
				}); 
				$("#commentsubmit").removeAttr("disabled");  				
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});	
		}else {
        	$(this).prev().prev().val("");
        }
		});
	$('.expandcomment').live("click",function(){
		thiscache=$(this);
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"expandcomments",
    				"pid":thiscache.parent().parent().attr('id'),
    				"pby":thiscache.attr('postby'),
    				"ecount":parseInt(thiscache.attr('expand'))+2
				},
    			success: function(r){
    				thiscache.after(r);
    				thiscache.remove();
    				document.getElementById('boardCon').style.height = eval($(document).height() - $(window).height()+300)+'px';
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
	})
	$(".delete").live("click",function(){
		thiscache=$(this);
		if (parseInt(thiscache.parent().attr('id'))==thiscache.parent().attr('id')){
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"deletePost",
    				"pid":thiscache.parent().attr('id')
				},
    			success: function(){
    				thiscache.parent().remove();//animation remove?
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		}else{
		$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"deleteComment",
    				"type":0,
    				"cid":parseInt(thiscache.parent().attr('id'))
				},
    			success: function(){
    				thiscache.parent().remove();
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		}
		});
		$(".kudos2u").live("click",function(){
			thiscache=$(this);
			$.ajax({
    			type: "POST",
    			url: "/db-interaction/news.php",
    			data: {
    				"action":"givekudos",
    				"type":0,
    				"pid":$(this).parent().parent().attr('id')
				},
    			success: function(){
    				if(thiscache.parent().next().children().length==0){
						thiscache.parent().next().append("<div class='kudostext'>Kudos to you Sir!<div class='kudoscount'>x1</div></div>");
						thiscache.remove();
					}else{						thiscache.parent().next().find(".kudoscount").text("x"+eval(parseInt(thiscache.parent().next().find(".kudoscount").text().substring(1))+1));
					thiscache.remove();
					}
    			},
    			error: function(){
    			    // should be some error functionality here
    			}
    		});
		})


}
function cleanHREF(str) {
 return str.replace(/\<a(.*?)href=['"](javascript:)(.+?)<\/a>/gi, "Naughty!");
}
function strip_tags(str, allowed_tags) {
 // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Luke Godfrey
    // +      input by: Pul
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // +      input by: Alex
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Marc Palau
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +      input by: Brett Zamir (http://brettz9.blogspot.com)
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Eric Nagel
    // +      input by: Bobby Drake
    // +   bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: strip_tags('<p>Kevin</p> <br /><b>van</b> <i>Zonneveld</i>', '<i><b>');
    // *     returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
    // *     example 2: strip_tags('<p>Kevin <img src="someimage.png" onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
    // *     returns 2: '<p>Kevin van Zonneveld</p>'
    // *     example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>", "<a>");
    // *     returns 3: '<a href='http://kevin.vanzonneveld.net'>Kevin van Zonneveld</a>'
    // *     example 4: strip_tags('1 < 5 5 > 1');
    // *     returns 4: '1 < 5 5 > 1'
    var key = '', allowed = false;
    var matches = [];
    var allowed_array = [];
    var allowed_tag = '';
    var i = 0;
    var k = '';
    var html = '';

    var replacer = function(search, replace, str) {
        return str.split(search).join(replace);
    };

    // Build allowes tags associative array
    if (allowed_tags) {
        allowed_array = allowed_tags.match(/([a-zA-Z]+)/gi);
    }

    str += '';

    // Match tags
    matches = str.match(/(<\/?[\S][^>]*>)/gi);

    // Go through all HTML tags
    for (key in matches) {
        if (isNaN(key)) {
            // IE7 Hack
            continue;
        }

        // Save HTML tag
        html = matches[key].toString();

        // Is tag not in allowed list? Remove from str!
        allowed = false;

        // Go through all allowed tags
        for (k in allowed_array) {
            // Init
            allowed_tag = allowed_array[k];
            i = -1;

            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+'>');}
            if (i != 0) { i = html.toLowerCase().indexOf('<'+allowed_tag+' ');}
            if (i != 0) { i = html.toLowerCase().indexOf('</'+allowed_tag)   ;}

            // Determine
            if (i == 0) {
                allowed = true;
                break;
            }
        }

        if (!allowed) {
            str = replacer(html, "", str); // Custom replace. No regexing
        }
    }

    return str;
}
function trackingPage(){
	var counter = 0;
	$("table").find("input").each(function(){
		var thiscache = $(this).parent().parent();
		if($(this).attr('checked')==true){//Count how many top tracks there are
			counter = counter + 1;
		}
		$(this).click(function(){
			if ($(this).attr('checked')==false){//USER IS UNCHECKING A TOP TRACK
				counter = counter - 1;
				$.ajax({
					type: "POST",
					url: "db-interaction/users.php",
					data: "action=changetoptrack&toptrack=0&trackee="+thiscache.attr('id'),
					success: function(){
					},
					error: function(){
    				}
    			});
			}
			else if ($(this).attr('checked')==true){//User is making someone a top track
				if(counter==5){
					$(this).attr('checked',false);
					thiscache.find(".toptrackerror").text("5 top tracks only please").show();
					t = setTimeout(function(){thiscache.find(".toptrackerror").fadeOut(1000)},3000);
				}
				else{
					counter = counter + 1;
					$.ajax({
						type: "POST",
						url: "db-interaction/users.php",
						data: "action=changetoptrack&toptrack=1&trackee="+thiscache.attr('id'),
						success: function(){
						},
						error: function(){
	    				}
	    			});
				}
			}
		});
	});
}