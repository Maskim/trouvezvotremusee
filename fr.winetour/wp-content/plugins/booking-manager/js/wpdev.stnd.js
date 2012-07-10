//<![CDATA[
function importBookings(){
          var ajax_crm_message = 'Working...';
          document.getElementById('ajax_working').innerHTML =
            '<div class="info_message ajax_message" id="ajax_message">\n\
                <div style="float:left;">'+ajax_crm_message+'</div> \n\
                <div  style="float:left;width:80px;margin-top:-3px;">\n\
                       <img src="'+wpdev_crm_plugin_url+'/img/ajax-loader.gif">\n\
                </div>\n\
            </div>';

            // Ajax POST here
            jWPDev.ajax({                                           // Start Ajax Sending
                url: wpdev_crm_plugin_url+ '/' + wpdev_crm_plugin_filename,
                type:'POST',
                success: function (data, textStatus){  if( textStatus == 'success')   jWPDev('#ajax_respond').html( data );},
                error:function (XMLHttpRequest, textStatus, errorThrown){ window.status = 'Ajax sending Error status:'+ textStatus; alert(XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText); if (XMLHttpRequest.status == 500) { alert('Please check at this page according this error:' + ' http://onlinebookingcalendar.com/faq/#faq-13'); } },
                // beforeSend: someFunction,
                data:{
                    ajax_action_crm : 'IMPORT_BOOKINGS'
                }
            });
    }
//]]>

function showAllRecordsAtOnePage(){
    //var myloc = window.location.href ;
    //myloc = myloc.replace(/(&show_all_records=1)|(&show_all_records=0)/g, "");
    //window.location.href = myloc + '&show_all_records=1';
    window.location.href = setURLParameter('show_all_records',1,window.location.href);
}

function showNormalRecordsAtOnePage(){
    //var myloc = window.location.href ;
    //myloc = myloc.replace(/(&show_all_records=1)|(&show_all_records=0)/g, "");
    //window.location.href = myloc;
    window.location.href = setURLParameter('show_all_records',0,window.location.href);
}



function openModalWindowCRM(content_ID){
    //alert('!!!' + content);
    jWPDev('.modal_content_text').attr('style','display:none;')
    document.getElementById( content_ID ).style.display = 'block';
    var buttons = {};//{ "Ok": wpdev_crm_dialog_close };
    
    jWPDev("#wpdev-crm-dialog").dialog( {
            autoOpen: false,
            width: 700,
            height: 200,
            buttons:buttons,
            draggable:false,
            hide: 'slide',
            resizable: false,
            modal: true,
            title: '<img src="'+wpdev_crm_plugin_url+ '/img/crm-16x16.png" align="middle" style="margin-top:1px;"> Orders'
    });
    jWPDev("#wpdev-crm-dialog").dialog("open");
}

function wpdev_crm_dialog_close(){
    jWPDev("#wpdev-crm-dialog").dialog("close");
}



function wpdev_togle_box(boxid){
    if ( jWPDev( '#' + boxid ).hasClass('closed') ) jWPDev('#' + boxid).removeClass('closed');
    else                                            jWPDev('#' + boxid).addClass('closed');
}



// Support function.

// Set parameters to url and return url string
function setURLParameter(myParam, myValue, myLink) {
   var myloc = myLink ;

   var single_par = '';
   var params = myloc.split('?');
   var return_link = params[0];
   params = params[1];
   params = params.split('&');

   var isSet = false;
   for(var i=0; i< params.length; i++){
       single_par = params[i].split('=');

       if ( i !== 0) return_link += '&';
       else          return_link += '?';

       if (single_par[0]==myParam) {
           isSet = true;
           return_link +=  single_par[0] + '=' + myValue;
       } else {
           return_link +=  single_par[0] + '=' + single_par[1];
       }

   }

   if (! isSet) {
       return_link += '&' + myParam + '=' + myValue;
   }

   return return_link;
}

function reloadpage_with_param(myParam, myValue){
  window.location.href = setURLParameter( myParam, myValue ,window.location.href);
}