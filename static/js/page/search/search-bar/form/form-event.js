export default function formEvent(){
 

    //*** Ajout d'un input de recherche ***\\
    $('body').on('click', '.btn-form-add-value',function(){
        console.log('add', $('.inputs-form input[type=text]').length)
        let nInput = $('.inputs-form input[type=text]').length +1;
        $('.inputs-form').append(`
            <div>
                <input type="text" name="search[]" class="row-form-${nInput}"><input type="button" value="+" class="btn-form-add-value row-form-${nInput}"><input type="button" value="-" class="btn-form-del-value" data-index="${nInput}">
            </div>
        `)
    })
    //*** Suppression d'un input de recherche ***\\
    $('body').on('click', '.btn-form-del-value',function(){
        let id = $(this).data('index');
        $(".row-form-"+id).remove();
        $(this).remove();
    })

}