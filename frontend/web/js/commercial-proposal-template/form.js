$(document).ready(function() {
    window.pdf_url = false;
    updateListeners();
    $(document).on("pjax:complete", function() {
        updateListeners();
        if (window.open_pdf) {
            window.open_pdf = false;
            window.open(window.pdf_url);
        }
    });
});

function updateListeners() {
    $('#commercialproposaltemplate-organization').change(function() {
        $('#commercialproposaltemplate-signer').html('');
        $('#commercialproposaltemplate-signer').prop('disabled', true);
        if ($(this).val()) {
            $.ajax({
                type: 'get',
                dataType: 'json',
                url: 'get-signers',
                data: {'organization_id': $(this).val()},
                success: function (data) {
                    data.forEach(function (item) {
                        $('#commercialproposaltemplate-signer').append('<option value="' + item.id + '">' + item.name + '</option>');
                        $('#commercialproposaltemplate-signer').prop('disabled', false);
                    });
                },
            });
        }
    });

    $("#get_pdf_button").click(function() {
        window.open_pdf = true;
        $("#commercial-proposal-template_submit").trigger("click");
    });
}



