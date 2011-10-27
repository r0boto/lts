//Define const
settingsDir = 'settings/';
settingsFile = "settings.xml";


function translateApp()
{
	// TRANSLATE APP
			
                var translateTo = "cs";
                
                jQuery.getScript("translations/" + translateTo + ".js", function(){
                
                    $("h1#loginHeader").text(text['hlavni_menu']);
                    
                    //alert(text['hello']);
                    
          		 });
}






