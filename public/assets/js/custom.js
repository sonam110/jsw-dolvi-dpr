 $(document).ready(function () {
     
    $("#from_date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
        
    });
     $("#to_date").datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true
        
    });
    $("#date_date").datepicker({
          dateFormat: 'yy-mm-dd',
          changeMonth: true,
          changeYear: true
          
    });
 });
$(document).on('click', '.extraDd', function(){
      var date_date = $('#date_date').val();
      var vendor_id = $('#vendor_id').val();
      var project_id = $('#project_id').val();
      //alert(1);
      var item_desc = $('#item_desc').val();
      if(date_date==''){
        alert('Please select data date');
      }
      $('#download-div').hide();
      if(date_date!=''){
        $.ajax({
            url: appurl+"get-dpr-report",
            type: 'POST',
            data: "date="+date_date+"&vendor_id="+vendor_id+"&project_id="+project_id+"&item_desc="+item_desc,
            success:function(info){
              if(info.trim() !== "") {
        
                $('#download-div').show();
                $('#dpr-report').html(info);
              } else {
                  $('#dpr-report tbody').html('<tr><td colspan="2">No record found in table</td></tr>');

              }

            }
        });
      }
    });

   $(document).on('click', '.w-100', function(){
      var type = $(this).attr('data-type');
      var date_date = $('#date_date').val();
      var vendor_id = $('#vendor_id').val();
      var project_id = $('#project_id').val();
      var item_desc = $('#item_desc').val();

      $.ajax({
          url: appurl+"download-dpr-report",
          type: 'POST',
           data: "date="+date_date+"&vendor_id="+vendor_id+"&project_id="+project_id+"&item_desc="+item_desc+"&type="+type,
          success:function(info){
            var fileUrl = info.data;

            // Create a hidden anchor element
            if(type ='html'){
                var anchor = $('<a>', {
                  href: fileUrl,
                  download: 'downloaded_file.' + type, // Set the desired file extension based on type
                  style: 'display: none;',
                  target: '_blank' // Open in a new tab
                });
            } else{
                var anchor = $('<a>', {
                  href: fileUrl,
                  download: 'downloaded_file.' + type, // Set the desired file extension based on type
                  style: 'display: none;'
                   
                  });
            }


            // Append the anchor to the body
            $('body').append(anchor);

            // Simulate a click on the anchor to initiate the download
            anchor[0].click();

            // Remove the anchor from the DOM
            anchor.remove();

          }
      });
  });

$(document).on('click', '.extrabtn', function(){
    var from_date = $('#from_date').val();
    
    var to_date = $('#to_date').val();
    if(from_date==''){
      alert('Please select from date');
    }
    if(to_date==''){
      alert('Please select to date');
    }
    var type = $(this).attr('data-type');
    var item_desc = $('#item_desc').val();
    var vendor_id = $('#vendor_id').val();
    if(vendor_id==''){
      alert('Please select vendor');
    }
    if(item_desc==''){
      alert('Please select item description');
    }
    $('#download-div').hide();
    if(from_date!='' && to_date !=''  && vendor_id !='' && item_desc !=''){
      $.ajax({
          url: appurl+"get-summery-report",
          type: 'POST',
          data: "from_date="+from_date+"&to_date="+to_date+"&type="+type+"&vendor_id="+vendor_id+"&item_desc="+item_desc,
          success:function(info){
            if(info.trim() !== "") {
      
              $('#download-div').show();
              $('#summery-report').html(info);
            } else {
                $('#summery-report tbody').html('<tr><td colspan="2">No record found in table</td></tr>');

            }

          }
      });
    }
});