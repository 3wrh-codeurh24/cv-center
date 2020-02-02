$(function(){
    $('.btn-advanced-search').click(function(){
        $('#search-bar-advanced').css({
            opacity:1,
            display:'block'
        })
    })
    $('body').on('click', '.btn-form-add-value',function(){
        console.log('add')
        $('.inputs-form').append(`
            <div>
                <select name="advanced_search[select][]">
                    <option value="and">Et</option>
                    <option value="or">Ou</option>
                </select>
                <input type="text" name="advanced_search[text][]">
                <input type="button" value="+" class="btn-form-add-value">
            </div>
        `)
    })

})