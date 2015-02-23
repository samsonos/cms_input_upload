/** JS SamsonCMS Select field interaction */
var SamsonCMS_InputUpload = function(field)
{
    /** Delete file handler */
    s('.__deletefield', field).click(function(btn) {
        // Flag for preventing bubbling delete event
        btn.deleting = false;

        // If we are not deleting right now - ask confirmation
        if (!btn.deleting && confirm('Удалить файл?')) {
            // Get input field block
            var parent = btn.parent('.__inputfield');

            // Flag for disabling delete event
            btn.deleting = true;

            // Create loader
            var loader = new Loader(parent.parent());
            loader.show();

            // Perform ajax file delete
            s.ajax(btn.a('href'), function(responce)
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

    },true, true);

    // File selected event
    uploadFileHandler(s('input[type="file"]', field), {
        start : function() {
            field.parent().css('padding', '0');
            s('.__progress_bar p',field).css('width', "0%");
            s('.__input', field).css('display', 'none');
            s('.__progress_text', field).css('display', 'block');
        },
        response : function(response) {
            response = JSON.parse(response);
            if (response.status == 1) {
                s('.__progress_text', field).css('display', 'none');
                field.parent().css('padding', '5px 10px');
                s('.__deletefield', field).show();
                s('.__file_name', field).html(response.path);
                showImage(response.path);
            }
        },
        error: function(){
            field.parent().css('padding', '5px 10px');
            s('.__progress_bar p',field).css('display', "none");
            s('.__input', field).css('display', 'block');
            s('.__progress_text', field).css('display', 'none');
        }
    });

    showImage(s('.__file_name', field).html());

    function showImage(newImage){
        var image = s('.__fileImage', field.parent());
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

    s('.__fileImage', field.parent()).lightbox();
};

// Bind input
SamsonCMS_Input.bind(SamsonCMS_InputUpload, '.__fieldUpload');