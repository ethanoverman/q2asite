$(document).ready(function () {
	$('.bulkDownload').on('click', function (e) {
	e.stopPropagation();
	e.preventDefault();

	$('#backUpStatus').html(""); //reset backup status message to empty upon new backup

	var category;
	var byCat;
	var catName;
	
	if($('#catCheck').attr('checked')!=null) //handle if backing up by category
	{
		var category =  $('#bCat').find('option:selected').attr('id');
		categoryid= category.substr(1);
		byCat = true;
		var catName = $('#bCat').val();
	}
	else
	{
		categoryid = -1;
		byCat=false;
		catName=null;
	}

	$.ajax('scripts/bulkDownload.php', //run bulk download php script
    	{
    		type: 'POST',
			cache: false,
			data: {cID: categoryid, byCat:byCat, catName:catName},
			//success: function (data) {console.log(data);},
			success: function (data) {$('#backUpStatus').html("Questions Successfully Backed Up");},
			error: function () {alert('FAILED TO BACKUP');}
     	});

	window.open('scripts/downloadfile.php'); //open new tab for generated file download

	});

});