
jQuery(document).ready(function ($) 
{
    function taxonomy_media_upload(button_class) 
    {
        $('body').on('click', button_class, function (_e) 
        {
            const button_id = '#' + $(this).attr('id');
            wp.media.editor.send.attachment = function (_props, attachment) 
            {
                $('#image_id').val(attachment.id);
                $('#image_wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
                $('#image_wrapper .custom_media_image').attr('src', attachment.url).css('display', 'block');
            }
            wp.media.editor.open($(button_id));
            return false;
        });
    }

    taxonomy_media_upload('.taxonomy_media_button.button');
    $('body').on('click', '.taxonomy_media_remove', function () 
    {
        $('#image_id').val('');
        $('#image_wrapper').html('<img class="custom_media_image" src="" style="margin:0;padding:0;max-height:100px;float:none;" />');
    });

    $(document).ajaxComplete(function (_event, xhr, settings) 
    {
        if ($.inArray('action=add-tag', settings.data.split('&')) !== -1) 
        {
            if ($(xhr.responseXML).find('term_id').text() != "")
                $('#image_wrapper').html('');
        }
    });
});