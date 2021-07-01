jQuery(document).ready( function() {
	var buttonfunction = 1;//This is an important variable which sets up the dynamic button
	
	function postToChat(){//If we call this function, it will post the message to the chat box via AJAX/PHP
		jQuery.ajax({
			type : "post",
		   url : myAjax.ajaxurl,
		   data : {
			 action : "postChat",
			 Message : document.getElementById("Message").value
		   }
	   });
	 document.getElementById("Message").value = "";
	}

	function changeName(){//If we call this function, it will change our name and alert us of our new name
		var newname = document.getElementById("forminput").value;
		if(newname != ""){
			jQuery.ajax({
   				type : "post",
  				url : myAjax.ajaxurl,
  				data : {
            		action : "changeName",
					name : newname
  				},
				success : function(response){
					alert("Your username will now appear as " + response + ".");
				}
  			});
	    	document.getElementById("forminput").value = "";
			window.location.reload();
    	}
	}

	function revertMultiButton(){//When called, we essentially reset the Multifunction button
		jQuery.ajax({
			type : "post",
		   	url : myAjax.ajaxurl,
		   	data : { action : "getForumPosts" },
			success : function(response){
				if(response != ""){
					document.getElementById("ForumField").innerHTML = response;
			 	}
		 	}
	   	});
	 	document.getElementById('Multifunction').value = "Post";
	 	buttonfunction = 1;
	}

	jQuery("#button").click( function(e) {//If we click the button attached to the chat box, it will always and only always post the message to chat
   		postToChat();
	})
	
	jQuery("#Multifunction").click( function(e) {//Dynamic Button functionality here
		switch(buttonfunction){
			case 0://In case 0, we are in the forum submission page, if we click this button we will go to the forum listing page and change the text from submit to post
				if(document.getElementById("PostTitle").value != "" && document.getElementById("description").value != ""){
					jQuery.ajax({
   						type : "post",
  						url : myAjax.ajaxurl,
  						data : {
							action : "postToForums",
							PostTitle : document.getElementById("PostTitle").value,
							Subject : document.getElementById("subject").value,
							Experience : document.getElementById("Exp").value,
							NegReason : document.getElementById("Reason").value,
							Description : document.getElementById("description").value
						}
  					});
				}
				revertMultiButton();
				break;
				
			case 1://In case 1, we are in the default state of the Multifunction button as well as the forum listing setting for forum field; if we click the button, go to forum posting page and change text to Exit
				jQuery.ajax({
   					type : "post",
  					url : myAjax.ajaxurl,
  					data : { action : "ForumForm" },
					success : function(response){
						if(response != ""){
							document.getElementById("ForumField").innerHTML = response;
						}
					}
  				});
				document.getElementById('Multifunction').value = "Submit";
				buttonfunction = 0;
				break;
			
			case 2://In case 2, we are reasding a forum post, if we click the button, we simply revert back to the original page.
				revertMultiButton();
				break;
		}
	})
	
	jQuery(document).on("keypress", 'form', function (e) {//When we press enter, the functionality differs based on what is hovered generally serving to submit a single text field, but definitely always differs from the functionality of default enter.
    	var code = e.keyCode || e.which;
		e.preventDefault();
    	if (code == 13) {
			if(document.getElementById("Message").is(':focus')){
				postToChat();
    		}
			else if (document.getElementById("forminput").is(':focus')) {
				changeName();
    		}
		}
	});

	jQuery("#formbutton").click(function (e) {//If we click the button next to the name field, it will always and only always call the change name function.
		changeName();
	});
	
	jQuery("#ForumField").on('click', '.forumpost', function(){//When we click on a forumpost, we open the forum as a full page.
		var postid = jQuery(this).attr('value');//The value attribute is synced with its ID in the database, allowing us to systematically query them properly and open them accordingly.
			jQuery.ajax({
            	type : "post",
            	url : myAjax.ajaxurl,
            	data : { action : "openForum", id : postid },
         		success : function(response){
            		if(response != ""){
                 		document.getElementById("ForumField").innerHTML = response;
				 		buttonfunction = 2;
				 		document.getElementById('Multifunction').value = "Exit";
             		}
         		}
           	});
		})
})//End of document ready functions

var messageParsing = window.setInterval(function loadMessages(){//We will be repeating this process as soon as possible and forever after. Load Messages into chat box from database once per second.
	jQuery.ajax({
   			type : "post",
  			url : myAjax.ajaxurl,
  			data : { action : "getChat" },
		success : function(response){
			if(response != ""){
				document.getElementById("MessageBox").innerHTML = response;
			}
		}
  		});
}, 1000);