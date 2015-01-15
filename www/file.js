/** JS SamsonCMS Select field interaction */
s('.__fieldUpload').pageInit( function( fields )
{
	/** Delete file handler */
	s('.__deletefield', fields ).click( function( btn )
	{
		// Flag for preventing bubbling delete event
		btn.deleting = false;

		// If we are not deleting right now - ask confirmation
		if( !btn.deleting && confirm('Удалить файл?'))
		{
			// Get input field block
			var parent = btn.parent('.__inputfield');

			// Flag for disabling delete event
			btn.deleting = true;

			// Create loader
			var loader = new Loader( parent.parent() );
			loader.show();

			// Perform ajax file delete
			s.ajax( btn.a('href'), function( responce )
			{
				// Upload field is became empty
				parent.addClass('empty');

				// Remove loader
				loader.remove();

				// Enable delete button for future
				btn.deleting = false;

				// Clear upload file value
				s('.__input', parent).val('');
				s('.__input', parent).show();
				s('.__delete', parent).hide();
                btn.hide();
                showImage();
			});
		}

	},true, true );

    // File selected event
    uploadFileHandler(s('input[type="file"]', fields ), {
        start : function() {
            fields.parent().css('padding', '0');
            s('.__progress_bar p',fields).css('width', "0%");
            s('.__input', fields).css('display', 'none');
            s('.__progress_text', fields).css('display', 'block');
        },
        response : function(response) {
            response = JSON.parse(response);
            if (response.status == 1) {
                s('.__progress_text', fields).css('display', 'none');
                fields.parent().css('padding', '5px 10px');
                s('.__deletefield', fields).show();
                s('.__file_name', fields).html(response.path);
                showImage(response.path);
            }
        },
        error: function(){
            fields.parent().css('padding', '5px 10px');
            s('.__progress_bar p',fields).css('display', "none");
            s('.__input', fields).css('display', 'block');
            s('.__progress_text', fields).css('display', 'none');
        }
    });

    showImage(s('.__file_name', fields).html());

    function showImage(newImage){
        var image = s('.__fileImage', fields.parent());
        if (newImage) {
            if (newImage.match(/\.(jpeg|jpg|gif|png)$/) != null) {
                image.a('src', newImage);
                image.parent().a('href', newImage);
                image.show();
            }
        } else {
            image.hide();
        }
    }
    
    s('.__fileImage', fields.parent()).lightbox();
});