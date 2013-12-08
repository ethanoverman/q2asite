$(document).ready(function () {

    $('form:regex(id,^postid)').on('submit', function (e) { //handle postQuestion button on individual question level
	e.stopPropagation();
	e.preventDefault();
	var pid = $(this).find('.pid').val(); //grab post id
	var category = $(this).find('.category').find('option:selected').attr('id'); //grab category id
	categoryid= category.substr(1);
    post(pid,categoryid); //post question through ajax call
    });

    $('.deleteButton').on('click', function (e) { //handle delete button
	e.stopPropagation();
	e.preventDefault();
	
	postid = $(this).parent().attr('id');
	postid = postid.substr(1);	
    $.ajax('scripts/uploadquestion.php', // run upload question script with delete post data
    	{
    		type: 'POST',
			data: {delete: postid},
			cache: false,
			//success: function (data) {console.log(data);},
			success: function (data) {$('#p'+postid).html("<p class='postDelete'>Question Successfully Deleted</p>")},
			error: function () {alert('FAILED TO delete QUESTION');}
     	}); 
    });
});