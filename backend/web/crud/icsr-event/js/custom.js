$('#icsrevent-meddra_pt_text').on('input',function ()
{
    var url = window.location.href;
    url = url.substring(0,url.lastIndexOf("/"));

    var searchTerm = $(this).val();
    $.ajax(
        {
            'url' : url +'/search-pt?term='+searchTerm,
            'method' : 'GET',
            'success' : function (response)
            {
                $('#icsrevent-meddra_pt_text').autocomplete("option", "source", response.ptTerms);

            }
        }
    );

});


$('#icsrevent-meddra_llt_text').on('input',function () {

    var pt = $('#icsrevent-meddra_pt_text').val();

    var url = window.location.href;
    url = url.substring(0,url.lastIndexOf("/"));

    var searchTerm = $(this).val();
    $.ajax(
        {
            'url': url + '/search-llt?ptTerm='+pt.trim()+'&searchTerm='+searchTerm,
            'method' : 'GET',
            'success' : function (response) {
                $('#icsrevent-meddra_llt_text').autocomplete("option", "source", response.lltTerms);

            }
        }
    );


});


$('#icsrevent-meddra_llt_text').on('focusout',function () {

    var url = window.location.href;
    url = url.substring(0,url.lastIndexOf("/"));
$.ajax({
    'url' : url + '/get-pt-from-lt?lltTerm='+ $(this).val(),
    'method' : 'GET',
    'success' : function (response){
        if (response.ptTerm )
        {
            $('#icsrevent-meddra_pt_text').val(response.ptTerm);
        }
    }
});
});

$('#icsrevent-meddra_pt_text').on('focusout',function () {
    var url = window.location.href;
    url = url.substring(0,url.lastIndexOf("/"));


    $.ajax({
        'url' : url + '/get-first-lt-from-pt?ptTerm='+ $(this).val(),
        'method' : 'GET',
        'success' : function (response){
            console.log(response.ltTerm);
            if (response.ltTerm )
            {
                $('#icsrevent-meddra_llt_text').val(response.ltTerm);
            }
        }
    });


});