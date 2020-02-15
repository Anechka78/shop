$( document ).ready(function(){

    $('.property').on('click', function (e){
        var id = e.target.id;
        var target_property = document.getElementById('td_'+id);
        if(target_property.style.display == "block"){
            target_property.style.display = "none"
        }else{
            target_property.style.display = "block"
        }
    });

    $('#property_name').on('change', function (event) {
        var id = $("#property_name option:selected").attr('id');
        $.ajax({ // используем массив полученных данных в ф-ии аякс
            type: 'POST',
            url: adminpath + "/property/get-values",
            data: {'id': id},
            dataType: 'json',
            success: function(data) {//в результате успешного выполнения отрабатывает ф-я data
                //console.log(data['values']);
                var target_p = document.getElementById('property_values');
                $(target_p).text('');
                if((data['values'])){
                    for(var key in data['values']){
                        $(target_p).append(data['values'][key]['name'], ', ');
                    }
                    $(target_p).text($(target_p).text().substring(0, $(target_p).text().length - 2));
                }
            },
            error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                console.log('Error...');
            }
        });
    });
    $('#btn_new_property').on('click', function (){
        var target = document.getElementById('new_property');
        var name = target.value;
        //console.log(target_val);
        $.ajax({ // используем массив полученных данных в ф-ии аякс
            type: 'POST',
            url: adminpath + "/property/add",
            data: {'name': name},
            dataType: 'json',
            success: function(data){//в результате успешного выполнения отрабатывает ф-я data
                //console.log(data);
                if(data['success'] == 1){
                    $('#property_name').append('<option id="'+ data['id']+'" class="property_name" value="'+ucFirst(name)+'">'+ucFirst(name)+'</option>');
                    $(target).val('');
                }
                    alert(data['message']);
            },
            error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                console.log('Error...');
            }
        });

    });

    $('#btn_new_property_value').on('click', function (){
        var property_id = $("#property_name option:selected").attr('id');
        var name = $('#new_property_name').val();
        var value = $('#new_property_value').val();
        if(!property_id){
            alert('Выберите характеристику');
            return;
        }else if(!name){
            alert('Введите название значения характеристики');
            return;
        }
        $.ajax({ // используем массив полученных данных в ф-ии аякс
            type: 'POST',
            url: adminpath + "/property/addpropval",
            data: {'name': name, 'value': value, 'property_id': property_id},
            dataType: 'json',
            success: function(data){//в результате успешного выполнения отрабатывает ф-я data
                //console.log(data);
                if(data['success'] == 1){
                    var target_p = document.getElementById('property_values');
                    $(target_p).append(', '+name);
                    $('#new_property_name').val('');
                    $('#new_property_value').val('');
                }
                alert(data['message']);
                $('#new_property_name').val('');
                $('#new_property_value').val('');
            },
            error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                console.log('Error...');
            }
        });

    });


/*
ДОБАВЛЕНИЕ НОВОГО ТОВАРА  /// ОБНОВЛЕНИЕ ИНФОРМАЦИИ
Добавление/обновление основной информации о товаре
 */
    $('#btn-add_main_info').on('click', function (e) {
        e.preventDefault();
        var data_update = $('#btn-add_main_info').attr('data-update');
        var secret_key = $('#secret_key').val();
        var name = $('#name').val();
        var title = $('#title').val();
        var meta_desc = $('#meta_desc').val();
        var price = $('#price').val();
        var old_price = $('#old_price').val();
        var count = $('#count').val();
        var weight = $('#weight').val();
        var vendor = $('#vendor').val();
        var category_id = $('#category_id').val();
        var description = $('#editor1').val();
        if ($('#status').is(':checked')){
            var status = 0;
        } else {
            var status = 0;
        }
        if ($('#hit').is(':checked')){
            var hit = 1;
        } else {
            var hit = 0;
        }
        if ($('#n_new').is(':checked')){
            var n_new = 1;
        } else {
            var n_new = 0;
        }
        if ($('#sale').is(':checked')){
            var sale = 1;
        } else {
            var sale = 0;
        }
//Если идет добавление основной информации о товаре
        if(data_update == 0){
            $.ajax({ // используем массив полученных данных в ф-ии аякс
                type: 'POST',
                url: adminpath + "/product/addMainInfo",
                data: {'name': name, 'title': title, 'meta_desc': meta_desc, 'secret_key': secret_key,
                    'price': price, 'old_price': old_price, 'count': count, 'weight': weight,
                    'vendor': vendor, 'category_id': category_id, 'description': description,
                    'status': status, 'hit': hit, 'new': n_new, 'sale': sale},
                dataType: 'json',
                success: function(data){//в результате успешного выполнения отрабатывает ф-я data
                    console.log(data);
                    if(data['success'] == 1){
                        console.log(data['message']);
                        var h = document.getElementsByTagName('h1')[0];
                        h.innerText = 'Добавление товара ' + data['product_name'];
                        $('#btn-add_main_info').attr('data-update', '1').text('Изменить данные').attr('data-product_id', data['product_id']);
                        $('#btn-add_related').attr('data-product_id', data['product_id']);
                        $('#add-product_id').val(data['product_id']);
                        //$('#btn-add_related').attr('data-secret_key', data['secret_key']);

                    }else{
                        alert(data['message']);
                    }
                },
                error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                    console.log('Error...');
                }
            });
        } else{
//Если идет обновление основной информации о товаре
            var product_id = $('#btn-add_main_info').attr('data-product_id');
            $.ajax({ // используем массив полученных данных в ф-ии аякс
                type: 'POST',
                url: adminpath + "/product/updateMainInfo",
                data: {'product_id': product_id, 'name': name, 'title': title, 'meta_desc': meta_desc, 'secret_key': secret_key,
                    'price': price, 'old_price': old_price, 'count': count, 'weight': weight,
                    'vendor': vendor, 'category_id': category_id, 'description': description,
                    'status': status, 'hit': hit, 'new': n_new, 'sale': sale},
                dataType: 'json',
                success: function(data){//в результате успешного выполнения отрабатывает ф-я data
                    console.log(data);
                    if(data['success'] == 1){
                        console.log(data['message']);
                        var h = document.getElementsByTagName('h1')[0];
                        h.innerText = 'Добавление товара ' + data['product_name'];
                    }else{
                        alert(data['message']);
                    }
                },
                error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                    console.log('Error...');
                }
            });
        }


    });

    $("#btn-add_related").on('click', function(e) {
        e.preventDefault();

        var data = {};
        var data_update = $('#btn-add_related').attr('data-update');
        $('#form-add_related').find ('input, textearea, select').each(function() {
            data[this.name] = $(this).val();
        });

        if(!$('#btn-add_related').attr('data-product_id')){
            alert('Заполните главную информацию о товаре');
        }
        if(data_update == 0){
            $.ajax({
                type: 'POST',
                url: adminpath + "/product/addRelated",
                data: data,
                dataType: 'json',
                success: function(data){
                    alert(data['message']);
                    $('#btn-add_related').attr('data-update', '1').text('Изменить данные');
                }
            });
        }

    })




});
function ucFirst(str) {
    if (!str) return str;

    return str[0].toUpperCase() + str.slice(1);
}