/** JS SamsonCMS Select field interaction */
var SamsonCMS_InputUpload = function(field)
{
    /** Delete file handler */
    s('.__deletefield', field).click(function(btn) {
        // Flag for preventing bubbling delete event
        btn.deleting = false;

        // TODO Rewrite this block to template view
        var div = '<div class="confirm"><div class="confirm-wrapper"><div class="inner-confirm">Delete?<div class="confirm-button icon-delete">Yes</div><div class="close icon2 icon_16x16 fa-times"></div></div></div></div>';
        s('html').append(div);

        // Show info
        var tb = tinybox(s('.confirm'), true);
        tb.show();

        // Close info-popup
        s('.confirm .close').click(function() {
            tb.hide();
        });

        // If we are not deleting right now - ask confirmation
        s('.confirm-button').click(function() {
            // Get input field block
            var parent = btn.parent('.__inputfield');

            // Flag for disabling delete event
            btn.deleting = true;

            // Create loader
            var loader = new Loader(parent.parent(), {type: 'absolute', top: 1, left: 1});
            loader.show();

            // Close div - confirm
            tb.hide();

            // Perform ajax file delete
            s.ajax(btn.a('href'), function()
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

                s('.__file_name', parent).hide();
                s('.__delete', parent).hide();
                btn.hide();
                showImage();
            });
        });

    },true, true);

    // File selected event
    uploadFileHandler(s('input[type="file"]', field), {
        start : function() {
            field.parent().css('padding', '0');
            s('.__input', field).hide();
            s('.__progress_bar',field).show();
            s('.__progress_bar p',field).css('width', "0%");
            s('.__progress_text', field).css('display', 'block');
        },
        response : function(response) {
            response = JSON.parse(response);
            if (response.status == 1) {
                s('.__progress_text', field).css('display', 'none');
                s('.__progress_bar',field).hide();
                field.parent().css('padding', '5px 10px');
                s('.__deletefield', field).show();
                s('.__file_name', field).show();
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
        var imageContainer = s('.__field_upload_image', field.parent());
        var image = s('.__fileImage', field.parent());
        if (newImage) {
            if (newImage.match(/\.(jpeg|jpg|gif|png)$/) != null) {
                image.a('src', newImage);
                image.parent().a('href', newImage);
                image.show();
                image.load(function(){
                    var height = parseInt(image.css('max-height'));
                    height = image.height() > height ? height : image.height();
                    imageContainer.height(height);
                    var width = parseInt(image.css('max-width'));
                    width = image.width() > width ? width : image.width();
                    imageContainer.width(width);
                });
            }
        } else {
            image.hide();
            imageContainer.height(0);
        }
    }

    s('.__fileImage', field.parent()).lightbox();
};

// Bind input
SamsonCMS_Input.bind(SamsonCMS_InputUpload, '.__fieldUpload');