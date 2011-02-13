$(document).ready(function() {
	
	// Expand Panel
	$("#open, .openPanel").click(function(){
		$("div#panel").fadeIn("slow");
	});
	
	// Collapse Panel
	$("#close").click(function(){
		$("div#panel").slideUp("slow");	
	});		
	
	// Switch buttons from "Log In | Register" to "Close Panel" on click
	$("#toggle a").click(function () {
		$("#toggle a").toggle();
	});		
		
});


$(document).ready(function(){
    $('#speciesPickerSelect').change(function(){
    	window.location = baseUrl + 'evolve/' + $(this).val();
	});
    
	//$("#createSpecies").validate();
    $("#createSpecies").validate({
        rules: {
            name: {
                required: true,
                minlength: 4
            },
            tempo: {
                required: true,
                min: 20,
                max: 250
            },
            reference_note: {
                required: true,
                min: 20,
                max: 120
            }
       },
       messages: {
         name: { //jQuery.format("At least {0} characters required!")
           required: function() { forminfo('Required', '#Nameofspecies-ariaLabel', '#txt_Nameofspecies') },
           minlength: function() { forminfo('At least 4 characters required!', '#Nameofspecies-ariaLabel', '#txt_Nameofspecies') }
         },
         tempo: {
           required: function() { forminfo('Required', '#TempoBPM-ariaLabel', '#sldr_TempoBPM') },
           min: function() { forminfo('Tempo must be between 20 and 250', '#TempoBPM-ariaLabel', '#sldr_TempoBPM') }
         },
         reference_note: {
            required: function() { forminfo('Required', '#Nameofspecies-ariaLabel', '#txt_Nameofspecies') },
            min: function() { forminfo('This is the reference note of your scale - 60 is the Middle C, 62 Middle D and so on... Therefore selecting 60 will mean that your species will mutate with a high probability of being in C scale (if you selected Major, C Major)', '#Referencenote-ariaLabel', '#Referencenote') },
            max: function() { forminfo('This is the reference note of your scale - 60 is the Middle C, 62 Middle D and so on... Therefore selecting 60 will mean that your species will mutate with a high probability of being in C scale (if you selected Major, C Major)', '#Referencenote-ariaLabel', '#Referencenote') }
         }
       }
    })
    
    // if user clicked on button, the overlay layer or the dialogbox, close the dialog 
    $('a.btn-ok, #dialog-overlay, #dialog-box').click(function () {    
        $('#dialog-overlay, #dialog-box').hide();      
        return false;
    });
});
//$('#dialog-overlay, #dialog-box').hide(); 

function forminfo(message, id, mytarget)
{
    // Destroy currrent tooltip if present
    if($(id).data("qtip")) $(id).qtip("destroy");
    if(message.length > 1)
    {
        $(id).qtip(
        {
           content: message,
           position: {
               corner: {
                   target: 'leftMiddle',
                   tooltip: 'rightMiddle'
               }
           },
           adjust: {
              screen: true // Keep the tooltip on-screen at all times
           },
           show: {
               when: {
                   target: $(mytarget),
                   event: 'focus'
               }
           },
           hide: {
               when: {
                   target: $(mytarget),
                   event: 'blur'
               }
           },
           style: {
              border: {
                 width: 3,
                 radius: 7
              },
              padding: 3, 
              textAlign: 'center',
              tip: true, // Give it a speech bubble tip with automatic corner detection
              name: 'dark' // Style it according to the preset 'cream' style
           }
           
        });
    }
}

function popupOK(message)
{
    popup(message+'<a href="#" class="button">Close</a>');
}

//Popup dialog
function popup(message) {
         
    // get the screen height and width  
    var maskHeight = $(document).height();  
    var maskWidth = $(window).width();
     
    // calculate the values for center alignment
    var dialogTop =  (maskHeight/3) - ($('#dialog-box').height());  
    var dialogLeft = (maskWidth/2) - ($('#dialog-box').width()/2); 
     
    // assign values to the overlay and dialog box
    $('#dialog-overlay').css({height:maskHeight, width:maskWidth}).show();
    $('#dialog-box').css({top:dialogTop, left:dialogLeft}).show();
     
    // display the message
    $('#dialog-message').html(message);
             
}
/*
*/
$(document).ready(function() {

    //When page loads...
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	//On Click Event
	$("ul.tabs li").click(function() {

		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
    
    
    //When page loads...
    $(".tab2_content").hide(); //Hide all content
    $("ul.tabs2 li:first").addClass("active").show(); //Activate first tab
    $(".tab2_content:first").show(); //Show first tab content
    
    //On Click Event
    $("ul.tabs2 li").click(function() {
    
    	$(".tabs2 li").removeClass("active"); //Remove any "active" class
    	$(this).addClass("active"); //Add "active" class to selected tab
    	$(".tab2_content").hide(); //Hide all tab content
    
    	var activeTab2 = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
    	$(activeTab2).fadeIn(); //Fade in the active ID content
    	return false;
    });
    
    FB.init({
     appId : '180743755293160',
     status : true, // check login status
     cookie : true, // enable cookies to allow the server to access the session
     xfbml : true // parse XFBML
    });
    FB.Canvas.setSize();
    
    function disableTooltip(id){
        $(id).qtip(
        {
           content: 'Disabled for now, sorry!',
           position: {
               corner: {
                   target: 'leftMiddle',
                   tooltip: 'rightMiddle'
               }
           },
           adjust: {
              screen: true // Keep the tooltip on-screen at all times
           },
           show: {
               when: {
                   event: 'mouseenter'
               }
           },
           hide: {
               when: {
                   event: 'mouseleave'
               }
           },
           style: {
              border: {
                 width: 3,
                 radius: 7
              },
              padding: 3, 
              textAlign: 'center',
              tip: true, // Give it a speech bubble tip with automatic corner detection
              name: 'dark' // Style it according to the preset 'cream' style
           }
           
        });
    }
    disableTooltip('#scalelabel');
    disableTooltip('#tempolabel');
    disableTooltip('#instrumentlabel');
    disableTooltip('#referencelabel');
    
});

window.fbAsyncInit = function() {
  FB.Canvas.setAutoResize();
}
