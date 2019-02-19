/**
 * Получение данных из форм
 */
function getData(obj_form){ //переметром передается объект формы (для регистрации)
    var hData = {}; // инициализируем переменную hData, присваиваем пустой массив
    $('input, textarea, select', obj_form).each(function(){ // из модуля jquery пробегаем по всем инпутам, селектам и тд объекта и собираем их значения
        if(this.name && this.name!=''){
            hData[this.name] = this.value;
            //console.log('hData[' + this.name + '] =' + hData[this.name]); // в консоли выводим элемент для наглядности
        }
    });
    return hData; // возвращаем массив (пустой или с данными пользователя)
}


//CKEDITOR.replace('editor1');
$('#editor1').ckeditor();

$('.delete').click(function(){
    var res = confirm('Подтвердите действие');
    if(!res) return false;
});

$('.sidebar-menu a').each(function(){
    var location = window.location.protocol + '//' + window.location.host + window.location.pathname;
    var link = this.href;
    if(link == location){
        $(this).parent().addClass('active');
        $(this).closest('.treeview').addClass('active');
    }
});

$('.sort').on('click', function (event) {
    var target = event.target;//тот элемент, по которому кликнули
    var val = $(target).attr('data-val');
    var sort = $(target).attr('data-dir');
    //console.log(val+' '+sort); die();
    var url = window.location.href;
    openUrl(url, {'val': val, 'sort': sort}, '_self');

});

//*************************************
//      Отправка POST запроса.
//  url    - относительный или абсолютный адрес страницы
//  post   - объект с парами ключ <=> значение или ''. пример: {sourceName: 'directory_go', sourceComand: 'exportWord', orgId: tmpOrgId}
//  target - куда открывать: blank или blank = blank, все остальное = self.  по умолчанию - self
//
//  openUrl('/../views/forms/calendar_table.php', {
//   sourceName  : 'calendar',
//   sourceComand : 'exportWord',
//   eventTitle  : JSON.stringify('eventTitle')
//  }, '_self');
//*************************************
function openUrl(url, post, target)
{
    if ((target !== undefined) && ((target == '_blank') || (target == 'blank'))){
        target = '_blank';
    }else{
        target = '_self';
    }
    //alert(target);
    if (post) {
        //alert('post');
        var form = $('<form/>', {
            action: url,
            method: 'POST',
            target: target,
            style: {
                display: 'none'
            }
        });

        for(var key in post) {
            form.append($('<input/>',{
                type: 'hidden',
                name: key,
                value: post[key]
            }));
        }

        form.appendTo(document.body); // Необходимо для некоторых браузеров
        form.submit();

    } else {
        //alert('open');
        window.open(url, target);
    }
}
//****** КОНЕЦ Отправка POST запроса

$('#reset-filter').click(function(){
    $('#filter input[type=radio]').prop('checked', false);
    return false;
});

$(".select2").select2({
    placeholder: "Начните вводить наименование товара",
    //minimumInputLength: 2,
    cache: true,
    ajax: {
        url: adminpath + "/product/related-product",
        delay: 250,
        dataType: 'json',
        data: function (params) {
            return {
                q: params.term,
                page: params.page
            };
        },
        processResults: function (data, params) {
            return {
                results: data.items
            };
        }
    }
});

var buttonMulti = $("#multi"),
    file;

new AjaxUpload(buttonMulti, {
    action: adminpath + buttonMulti.data('url') + "?upload=1",
    data: {name: buttonMulti.data('name')},
    name: buttonMulti.data('name'),
    onSubmit: function(file, ext){
        if (! (ext && /^(jpg|png|jpeg|gif)$/i.test(ext))){
            alert('Ошибка! Разрешены только картинки');
            return false;
        }
        buttonMulti.closest('.file-upload').find('.overlay').css({'display':'block'});

    },
    onComplete: function(file, response){
        setTimeout(function(){
            buttonMulti.closest('.file-upload').find('.overlay').css({'display':'none'});

            response = JSON.parse(response);
            $('.' + buttonMulti.data('name')).append('<img src="/images/products/' + response.file + '" style="max-height: 150px;">');
        }, 500);
    }
});

/**
 * Изменение выбранной родительской характеристики
 */
$('#parent_name').on('change', function (event) {
    var id = $(this).val();
    $("#child_name").find('option').removeAttr('disabled');

    if(id == '0'){
        alert('Вы не выбрали значение!');
    }
    $('#parent_names').find('option').remove();
    $("#parent_names").prepend( $('<option value="0">Выберите значение</option>') );

    $('#child_name').find('option[value='+id+']').attr("disabled", "disabled");
    $('#child_name').find('option[value='+id+']').css('color', '#D3D3D3');

    getModValues(id, 'parent_names');
});

/**
 * Изменение выбранной дочерней характеристики
 */
$('#child_name').on('change', function (event) {
    var id = $(this).val();
    //$("#prop_name").find('option').removeAttr('disabled');
    if(id == '0'){
        alert('Вы не выбрали значение!');
    }
    $('#child_names').find('option').remove();
    $("#child_names").prepend( $('<option value="0">Выберите значение</option>') );
    //$('#prop_name').find('option[value='+id+']').attr("disabled", "disabled");

    getModValues(id, 'child_names');
});

/**
 * Изменение выбранной дочерней характеристики
 */
$('#prop_name').on('change', function (event) {
    var id = $(this).val();
    //$("#prop_name").find('option').removeAttr('disabled');
    if(id == '0'){
        alert('Вы не выбрали значение!');
    }
    $('#prop_value').find('option').remove();
    $("#prop_value").prepend( $('<option value="0">Выберите значение</option>') );
    //$('#prop_name').find('option[value='+id+']').attr("disabled", "disabled");

    getModValues(id, 'prop_value');
});

/**
 * Получаем из БД значения характеристик
 * @param id характеристики
 * @param getVal
 */
function getModValues(id, getVal){
    $.ajax({ // используем массив полученных данных в ф-ии аякс
        type: 'POST',
        url: adminpath + "/product/get-mods",
        data: {'id': id},
        dataType: 'json',
        success: function(data){//в результате успешного выполнения отрабатывает ф-я data
            var s =  document.getElementById(getVal).options;
            var val = data['mod_values'];
            for(key in val){
                //console.log(val[key]);
                s[s.length]= new Option(val[key]['name'], val[key]['id']);
            }
            //console.log(data);

        },
        error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
            console.log('Error...');
        }
    });
}
function addPropertyVal(){
    var data = getData('.properties_values');
    if(data['prop_name'] == '0' || data['prop_value'] == '0'){
        alert('Выберите характеристику и ее значение');
        return;
    }
    var property_name_name = $('#prop_name option:selected').text();
    var property_name_value = $('#prop_value option:selected').text();
    //console.log(data); console.log(property_name_name+property_name_value); die();
    $.ajax({ // используем массив полученных данных в ф-ии аякс
        type: 'POST',
        url: adminpath + "/product/set-propertyValue",
        data: {'data': data,
            'property_name_name' : property_name_name,
            'property_name_value' : property_name_value},
        dataType: 'json',
        success: function(data){//в результате успешного выполнения отрабатывает ф-я data
            if(data['success']){
                alert(data['message']);
                console.log(data);

                $('#prop_name option:eq(0)').prop('selected',true);
                //$("#parent_name").prepend( $('<option value="0">Выберите главную характеристику</option>') );
                $('#prop_value').find('option').remove();
                $('#prop_value').prepend( $('<option value="0">Выберите значение</option>') );

                if(document.getElementById('pv_count')){
                    document.getElementById('pv_count').value = "";
                }
                if(document.getElementById('pv_price')){
                    document.getElementById('pv_price').value = "";
                }
                if(document.getElementById('pv_oldprice')) {
                    document.getElementById('pv_oldprice').value = "";
                }
                if(document.getElementById('pv_weight')) {
                    document.getElementById('pv_weight').value = "";
                }

                var div = $('#properties_values_values')[0];
                var p = document.createElement('p');
                p.setAttribute( "id", 'pv-'+data['key'] );
                p.innerHTML = "Добавлена характеристика: "+data['pv']['property_name'] +': '
                    +data['pv']['property_value'] +
                    ', Кол-во: '+data['pv']['count'] + ', Цена: ' +data['pv']['price'] + ', Старая цена: '+data['pv']['old_price'] +
                    ', Вес: ' + data['pv']['weight'];
                div.appendChild(p);
                var foo = document.getElementById('pv-'+data['key']);
                var span = document.createElement('span');
                span.setAttribute( "id", data['key'] );
                span.innerHTML = '<i id="'+data['key']+'" class="fa fa-fw fa-times" title ="Удалить характеристику" style="margin-left: 5px; color: red; cursor: pointer;"></i>';
                foo.appendChild(span);

            }else{
                console.log(data);
                alert(data['message']);
            }
        }

    });
}

/**
 * Добавление взаимозависимой характеристики в сессию
 */
function addPropertyDep(){
    var data = getData('.properties_dependences');
    var parent_name_value = $('#parent_name option:selected').text();
    var parent_names_value = $('#parent_names option:selected').text();
    var child_name_value = $('#child_name option:selected').text();
    var child_names_value = $('#child_names option:selected').text();
    //console.log(data);
    $.ajax({ // используем массив полученных данных в ф-ии аякс
        type: 'POST',
        url: adminpath + "/product/set-propertyDependence",
        data: {'data': data,
                'parent_name_value' : parent_name_value,
                'parent_names_value' : parent_names_value,
                'child_name_value' : child_name_value,
                'child_names_value' :child_names_value},
        dataType: 'json',
        success: function(data){//в результате успешного выполнения отрабатывает ф-я data
             if(data['success']){
                 alert(data['message']);
                 console.log(data);

                 $('#parent_name option:eq(0)').prop('selected',true);
                 //$("#parent_name").prepend( $('<option value="0">Выберите главную характеристику</option>') );
                 $('#parent_names').find('option').remove();
                 $("#parent_names").prepend( $('<option value="0">Выберите значение</option>') );
                 $("#child_name").find('option').removeAttr('disabled');
                 $('#child_name option:eq(0)').prop('selected',true);
                 //$("#child_name").prepend( $('<option value="0">Выберите зависимую характеристику</option>') );
                 $('#child_names').find('option').remove();
                 $("#child_names").prepend( $('<option value="0">Выберите значение</option>') );
                 document.getElementById('pd_count').value = "";
                 document.getElementById('pd_price').value = "";
                 document.getElementById('pd_oldprice').value = "";
                 document.getElementById('pd_weight').value = "";

                 var div = $('#properties_dependences_values')[0];
                 var p = document.createElement('p');
                 p.setAttribute( "id", 'pd-'+data['key'] );
                 p.innerHTML = "Добавлена характеристика: "+data['pd_val']['parent_name_value'] +': '
                                +data['pd_val']['parent_names_value'] + ', '+data['pd_val']['child_name_value']+ ': ' + data['pd_val']['child_names_value'] +
                                ', Кол-во: '+data['pd']['count'] + ', Цена: ' +data['pd']['price'] + ', Старая цена: '+data['pd']['old_price'] +
                                ', Вес: ' + data['pd']['weight'];
                 div.appendChild(p);
                 var foo = document.getElementById('pd-'+data['key']);
                 var span = document.createElement('span');
                 span.setAttribute( "id", data['key'] );
                 span.innerHTML = '<i id="'+data['key']+'" class="fa fa-fw fa-times" title ="Удалить характеристику" style="margin-left: 5px; color: red; cursor: pointer;"></i>';
                 foo.appendChild(span);

                 $("#prop_name option").each(function(){
                     var value = $(this).val();
                     if(value == data['pd']['parent_property_name'] || value == data['pd']['child_property_name']){
                         $(this).prop('disabled', true);
                         $(this).css('color', '#D3D3D3');
                     }
                 });

                 if(data['count'] == 1){
                     $('.pv_count').remove();
                     $('.pv_price').remove();
                     $('.pv_oldprice').remove();
                     $('.pv_weight').remove();
                 }

             }else{
                 console.log(data);
                 alert(data['message']);
             }
        }
    });
}
/**
 * Удаление взаимозависимой характеристики
 */
$('#properties_dependences_values').on('click', function(event){
    var id = event.target.id;//тот элемент, по которому кликнули
    $.ajax({ // используем массив полученных данных в ф-ии аякс
        type: 'POST',
        url: adminpath + "/product/delete-mod",
        data: {'id': id, 'name': 'pd'},
        dataType: 'json',
        success: function(data){//в результате успешного выполнения отрабатывает ф-я data
            if(data['success']){
                $('#'+data['name']+data['id']).remove();
            }
            console.log(data);

        }
    });

});

/**
 * Удаление взаимозависимой характеристики
 */
$('#properties_values_values').on('click', function(event){
    var id = event.target.id;//тот элемент, по которому кликнули
    $.ajax({ // используем массив полученных данных в ф-ии аякс
        type: 'POST',
        url: adminpath + "/product/delete-mod",
        data: {'id': id, 'name': 'pv'},
        dataType: 'json',
        success: function(data){//в результате успешного выполнения отрабатывает ф-я data
            if(data['success']){
                $('#'+data['name']+data['id']).remove();
            }
            console.log(data);

        }
    });

});

