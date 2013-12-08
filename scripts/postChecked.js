$(document).ready(function () {
	
	if($('#manyCategory').find('option:selected').val()=="Other")
	{
		$('#otherCatInput').prepend("<input type='text' id='newCatEntry' class='nci'>"); //create input if no category is created on load
	}
	
	$('#checkAll').on('click', function (e) { //handle check-all button
		$('.postMany').attr('checked','checked');
		$(this).attr('hidden',true);
		$('#uncheckAll').attr('hidden',false);
	});

	$('#uncheckAll').on('click', function (e) { //handle uncheck-all button
		$('.postMany').attr('checked',false);
		$(this).attr('hidden',true);
		$('#checkAll').attr('hidden',false);
	});

	$(document).on('change', 'select#manyCategory', function() { //create input box if 'other' is the selected category
    if($(this).val()=="Other")
    {
    	$('#otherCatInput').prepend("<input type='text' id='newCatEntry' class='nci'>");

    } 
    else //remove input box on selected option change
    {
    	if($('#newCatEntry')!=0)
    	{
    	$('#newCatEntry').remove();
    	}
    	$('#catFail').html("");
    }
	});

	$('.postChecked').on('click', function (e) { //handle post checked
	e.stopPropagation();
	e.preventDefault();

	if($('#manyCategory').val()=="Other") //handle post checked with 'other' category
	{
		category = $('#newCatEntry').val();
		$.ajax('scripts/newCat.php', //call newCat script to check if category is unique and create new category
    	{
    		type: 'POST',
			cache: false,
			data:{name:category},
			success: function (data) {$('#catFail').html(""); postMany(data);}, //post to generated category
			error: function () {$('#catFail').html("Category already created, please select from dropdown.");} //throw error for ingenuine category
     	});
	}
	else
	{
		category = $('#manyCategory').find('option:selected').attr('id').substr(1);
		postMany(category); //post to selected category
	}

	
	
    });

});

function postMany(category)
{
	$('.postMany:checked').each(function(){post($(this).val(),category);}) //post each check question to selected category
}


function post(pid,category)
{
	$.ajax('scripts/uploadquestion.php', //run uploadquestion script to post questions into q2a
 	   	{
 	   		type: 'POST',
			data: {pid: pid, categoryid:category},
			cache: false,
			success: function (data) {$('#cb'+pid).html(""); $('#p'+pid).html("<p class='postSuccess'>Question Successfully Posted</p>")},
			error: function () {alert('FAILED TO POST QUESTION');}
	     }); 
}