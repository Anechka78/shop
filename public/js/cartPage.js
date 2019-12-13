$(document).ready(function() {
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

/*
 Обработка клика по кнопкам удалить, минус или плюс на странице корзины
 */

$('.cart-product__item').on('click', function (event) {
    var id = $(this).attr('data-id');
    var target = event.target;//тот элемент, по которому кликнули
    if($(target).hasClass('plus')){
        var proc = $(target).attr('data-proc');
        changeItemsInCart(id, proc);
    } else if ($(target).hasClass('minus')){
        var proc = $(target).attr('data-proc');
        changeItemsInCart(id, proc);
    } else if($(target).hasClass('itemToDel')){
        var proc = $(target).attr('data-proc');
        var qty = $('#itemInfo_'+id+' div.cart-product__info div.cart-product__count input.itemCnt').val();
        var summ = $('#itemInfo_'+id+' div.cart-product__info div.cart-product__count span.itemPrice')[0].attributes[2].value*qty;
        var weight = $('#itemInfo_'+id+' div.cart-product__info div.cart-product__count input.itemCnt')[0].attributes[6].value*qty;
        //console.log(weight); die();
        deleteItemsFromCart(id, qty, summ, weight);
    }
});

/*
 Изменение кол-ва товаров в корзине (клик по кнопкам + или -)
 */
    function changeItemsInCart(id, proc){
        var input = $('#itemCnt_'+id);

        if(proc == 'minus'){
            var count = parseInt(input.val()) - 1;
        } else if(proc == 'plus'){
            var count = parseInt(input.val()) + 1;
        }
        var realCount = $('#itemCnt_'+id).attr('data-count');
        //console.log(realCount);
        if(count > realCount){
            count=realCount;
            alert('В наличии '+realCount+' шт. этого артикула');
        }
        count = count < 1 ? 0 : count;
        input.val(count);

        if(count == 0){
            $('#itemInfo_'+id).remove(); //удаляем див с данным товаром
            getTotalCartSum(); //Меняем итоговую сумму в корзине
        }else{
            //Меняем стоимость товара в зависимости от кол-ва
            var price = $('#itemPrice_'+id).attr('value');//22.5 f.e.
            var sumSpan = $('#itemRealPrice_'+id);
            //console.log(id);
            var itemsum = (price*count*course).toFixed(2);
            $(sumSpan).find('span').eq(0).html(itemsum);

            //Меняем итоговую сумму в корзине
            getTotalCartSum();
        }

        //Передаем данные в ajax, чтобы изменить корзину в сессии
        $.ajax({ // используем массив полученных данных в ф-ии аякс
            type: 'POST',
            url: "/cart/change",
            data: {'id': id, 'qty': count},
            dataType: 'json',
            success: function(data){//в результате успешного выполнения отрабатывает ф-я data
                if(data['cart']){
                    showMiniCart(data['cart']);
                } else{
                    console.log(data);
                    alert(data['message']);
                    $('#cartForm').remove();
                    var div = document.createElement('div');
                    div.innerHTML = "В корзине пусто.";
                    var wrap = $('.wrapper_cart')[0];
                    wrap.appendChild(div);
                    document.getElementById("cartCntItems").textContent='0';
                    document.getElementById("cartCntSum").innerHTML= 'КОРЗИНА';
                }
                //console.log('Success...');
                //console.log(data);

            },
            error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                console.log('Error...');
            }
        });
    }
/*
 Функция подсчета итоговой суммы по заказу в корзине
 */
    function getTotalCartSum() {
        var items = $('.product-summ span');
        //console.log(items); die();
        var totalSumm = 0;
        var q = items.length;
        for(var i=0; i<q; i++ ){
            totalSumm += parseFloat(items[i].innerHTML, 2);
        }
        //console.log(totalSumm); die();
        $('.summ-count').html(symLeft+totalSumm+symRight);
    }

//****** УДАЛЕНИЕ ТОВАРА************
    function deleteItemsFromCart(id, qty, summ, weight){
        var itemToDel = $('#itemToDel_'+id);
        // console.log(itemToDel); die();
        $('#itemInfo_'+id).remove();
        getTotalCartSum();

        var postData = {id: id, qty: qty, summ: summ, weight: weight};

        $.ajax({
            type: 'POST',
            async: true,
            url: "/cart/removefromcart/",
            data: postData,
            dataType: 'json',
            success: function(data) {
                var items = $('.product-summ span');
                //console.log(items); die();
                var totalSumm = 0;
                var q = items.length;
                for(var i=0; i<q; i++ ){
                    totalSumm += parseFloat(items[i].innerHTML, 2);
                }
                var itemsqty = $('.itemCnt');
                //console.log(itemsqty); die();
                var totalQty = 0;
                var r = itemsqty.length;
                for(var t=0; t<r; t++ ){
                    totalQty += +itemsqty[t].value;
                }
                var sum = totalSumm*course;
                if(sum > 0){
                    document.getElementById("cartCntItems").textContent=totalQty;
                    document.getElementById("cartCntSum").textContent= symLeft+sum.toFixed(2)+symRight;
                }else{
                    document.getElementById("cartCntItems").textContent=totalQty;
                    document.getElementById("cartCntSum").innerHTML = 'КОРЗИНА';
                    $('#cartForm').remove();
                    var div = document.createElement('div');
                    div.innerHTML = "В корзине пусто.";
                    var wrap = $('.wrapper_cart')[0];
                    wrap.appendChild(div);
                }
            }
        });
    }




    /*
    Запись данных в сессию и передача их в cart/orderAction
    */
    $('.cart-checkout__order').on('click', function (event) {
        $('.cart-checkout__order').css('display', 'none');
        $('.user-order').css('display', 'block');

        /*var ItemsInOrder = [];
        $('.cart-product__item').each(function(index, element) {
            var newArr = {};
            newArr['productId']     = $('.cart-product__name').attr('data-id');
            newArr['itemCount']     = $(element).find('.itemCnt').eq(0).val();
            newArr['itemRealPrice'] = $(element).find('.product-summ').eq(0).html();
            newArr['itemSize']      = $(element).find('.cart-product__size').eq(0).html();
            newArr['itemColor']     = $(element).find('.cart-product__color').eq(0).html();

            ItemsInOrder.push(newArr); //получаем объекты с данными товаров в массиве заказа
        });
        var ItemsCount = $('.summ-count').html();
        //console.log(ItemsCount);
        var postData = {ItemsInOrder: ItemsInOrder, ItemsCount: ItemsCount};
        //console.log(postData);

        openUrl('http://site1.loc/cart/order/', {'dataStr': JSON.stringify(postData)});*/

        /*$.ajax({ // используем массив полученных данных в ф-ии аякс
            type: 'POST',
            async: true,
            url: "/cart/order/",
            data: {'dataStr': JSON.stringify(postData)},
            dataType: 'json',
            success: function(data){//в результате успешного выполнения отрабатывает ф-я data
                alert(data['message']); // выводим сообщение об успехе
                if(data['success']){//при условии, что success=1
                    console.log(data); // выводим сообщение
                }
            },
            error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
                //alert('ERROR'); // выводим сообщение
                console.log(data); // выводим сообщение
            }
        });*/
    });


    $('#user_login').on('click', function () {
        document.getElementById('mypopup').style.display = "block";
        //$('.order_without_login').css('display', 'none');
    });
    $('#user_signup').on('click', function () {
        document.getElementById('mypopup').style.display = "block";
        //$('.order_without_login').css('display', 'none');
    });





});

/**
 * оформление заказа
 */

function saveOrder(){
    var userInfo = getData('.user-order');


    //if(userInfo['name'] == '' || userInfo['email'] == '' || userInfo['adress'] == ''|| userInfo['phone'] == ''){
    //    alert("Все обязательные поля должны быть заполнены!");
    //    return false;
    //}

    var ItemsInOrder = [];
     $('.cart-product__item').each(function(index, element) {
         //console.log(element);
     var newArr = {};
         newArr['product_id']     = $(element).attr('data-productid');
         newArr['multiple_id']    = $(element).attr('data-id');
         newArr['qty']            = $(element).find('.itemCnt').eq(0).val();
         newArr['price']          = $(element).find('.product-summ span').eq(0).html();
         newArr['title']          = $(element).find('.cart-product__name a').eq(0).html();
         newArr['alias']          = $(element).find('.cart-product__name a').eq(0).attr('href');

     ItemsInOrder.push(newArr); //получаем объекты с данными товаров в массиве заказа
     });
     var ItemsSum = parseFloat($('.summ-count').html());
     //console.log(ItemsCount);
     var postData = {userInfo: userInfo, ItemsInOrder: ItemsInOrder, ItemsSum: ItemsSum};
     console.log(postData);

    $.ajax({ // с помощью ajax функции обращаемся к контроллеру, передавая ему данные
        type: 'POST', //тип запроса аякс
        async: false,
        url: "/cart/order",
        data: postData, // передаем туда наши данные в формате ниже json
        dataType: 'json',
        success: function(data){ // если экшн вернул данные, проверяем
            if(data['success']){ // если все записалось в БД хорошо (ключи success и message создаются в контроллере карт и экшене ордер)
                alert(data['message']); // выводим сообщение, что все ок
                document.location = '/';// затем редиректим на главную страницу
            } else{ // если произошла ошибка
                alert(data['message']);
            }
        }
    });
}


/**
 * Очистка корзины
 */
function clearCart(){
    $.ajax({ // используем массив полученных данных в ф-ии аякс
        type: 'POST',
        url: "/cart/clear",
        dataType: 'json',
        success: function(data){//в результате успешного выполнения отрабатывает ф-я data
            document.getElementById("cartCntItems").textContent='0';
            document.getElementById("cartCntSum").innerHTML= 'КОРЗИНА';
            $('#cartForm').remove();
            var div = document.createElement('div');
            div.innerHTML = "В корзине пусто.";
            var wrap = $('.wrapper_cart')[0];
            wrap.appendChild(div);
        },
        error: function(data) {//в результате ошибки выполнения отрабатывает ф-я data
            alert('Ошибка при удалении товаров! Попробуйте позже.');
        }
    });
}

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

/**
 * Регистрация нового пользователя
 */
/*
function registerNewUser(){
    var postData = getData('#registerBox'); //из leftcolumn.tpl получаем данные (registerBox), обрабатываем их getData и заносим в переменную postData
    $.ajax({
        type: 'POST', //тип запроса аякс
        async: false,
        url: "/user/register/", //url, по которому будем обращаться - из usercontroller вызывается useraction
        data: postData, // передаем туда наши данные, которые собрали в getData в формате ниже json
        dataType: 'json',
        success: function(data){ // какое событие будет происходить, если мы туда успешно передали данные и ф-я что-то вернула. В переменную data попадает
            //то, что мы вернули из json_encode файла usercontroller функции registerAction

            if (data['success']){ // если ключ success, который там есть истинен, то пишем, что регистрация прошла успешно
                alert('Регистрация прошла успешно');

                //> блок в левом столбце
                $('#registerBox').hide(); // скрываем блок регистрации после успешного завершения процесса

                 $('#userLink').attr('href', '/user/'); //обращаемся к id=userLink leftcolumn.tpl таким образом. Изменяем атрибут # теперь равен /user/
                 $('#userLink').html(data['userName']);//у этого же объекта мы меняем html код - удаляем текст, который между тегами <a></a> leftcolumn.tpl . заменится на то, что приходит в data['userName'] из контроллера - resData формируется и мы берем значение userName.
                 $('#userBox').show();
                //<

                //> страница заказа
                 $('#loginBox').hide(); //прячем блок авторизации
                 $('#btnSaveOrder').show();//показываем кнопку сохранения заказа
                //<
            } else{ // если произошла ошибка
                alert(data['message']);
            }

        }
    });
}*/

/**
 * Авторизация пользователя
 */
/*
function login(){
    var email = $('#loginEmail').val(); // инициализируем переменную email и передаем в нее значение идентификатора из инпута в форме leftcolumn.tpl
    var pwd = $('#loginPwd').val(); // то же самое

    var postData = "email="+ email +"&pwd=" +pwd;// формируем строку запроса, фактически - гет запрос, см отличие в форме регистрации

    $.ajax({ // выполнение ajax запроса
        type: 'POST', //тип запроса аякс
        async: false,
        url: "/user/login/", //url, по которому будем обращаться
        data: postData, // передаем туда наши данные в формате ниже json
        dataType: 'json',
        success: function(data){ // если массив не пустой (data содержит json, который мы передали из loginAction), выполняем код ниже
            if(data['success']){
                $('#registerBox').hide(); // прячем формы после успешной авторизации -это и слева и на странице оформления заказа, тк идентификаторы названы одинаково
                $('#loginBox').hide();

                $('#userLink').attr('href', '/user/'); // ссылка на личную страничку пользователя
                $('#userLink').html(data['displayName']); // имя или мыло польз-ля
                $('#userBox').show(); // показываем опцию выхода

                //заполняем поля на странице заказа /cart/order/ - засовываем их в пустые поля регистрации, которая скрыта на странице карт/ордер
                $('#name').val(data['name']);
                $('#phone').val(data['phone']);
                $('#adress').val(data['adress']);

                $('#btnSaveOrder').show(); //ищем объект с идентификатором btnSaveOrder и выполняем метод show, те показываем его
            }else{ // если массив пуст = выдаем сообщение об ошибке
                alert(data['message']);
            }
        }
    });
}*/

/**
 * Показывать или прятать форму регистрации
 */
/*
function showRegisterBox(){
    if( $("#registerBoxHidden").css('display') != 'block'){ //проверяем стиль у css блока display в leftcolumn.tpl. Если он виден, то значение его = block
        $("#registerBoxHidden").show(); // вызываем метод, т.е. показываем, стилю присваиваем значение блок, значит - показываем
    } else{
        $("#registerBoxHidden").hide(); // прячем дисплей св-во
    }
}*/

/**
 * Обновление данных пользователя
 */
function updateUserData(){
   // console.log("js - updateUserData()");
    //можно было через функцию getData получить все данные, только обернуть в див табличку
    var phone = $('#newPhone').val(); //собираем данные из формы
    var adress = $('#newAdress').val();
    var pwd1 = $('#newPwd1').val();
    var pwd2 = $('#newPwd2').val();
    var curPwd = $('#curPwd').val();
    var name = $('#newName').val();

    var postData = {// формируем массив, чтобы отправить его в updateAction контроллера
        phone:phone,
        adress:adress,
        pwd1:pwd1,
        pwd2:pwd2,
        curPwd:curPwd,
        name:name};

    $.ajax({
        type: 'POST', //тип запроса аякс
        async: false,
        url: "/user/update/", //url, по которому будем обращаться: controller - user, action - update
        data: postData, // передаем туда наши данные в формате ниже json
        dataType: 'json',
        success: function(data){ //если данные мы получили
            if(data['success']){
                $('#userLink').html(data['userName']); //объекту userLink мы присваиваем его имя. Объект - приветствие пользователя
                alert(data['message']); //выводим сообщение об успешной коррекции данных
            } else{
                alert(data['message']); // выводим сообщение об ошибке
            }
        }
    });
}

/**
 * Сохранение заказа
 */
/*function saveOrder(){
    var postData = getData('form');//объявляем переменную postData и собираем данные из формы с помощью ф-ии getData, параметром передаем тег form(он один на странице, иначе было бы недоразумение), могли передать и id и любой идентификатор divа
    $.ajax({ // с помощью ajax функции обращаемся к контроллеру, передавая ему данные
        type: 'POST', //тип запроса аякс
        async: false,
        url: "/cart/saveorder/", //url, по которому будем обращаться: controller - cart, action - saveorder
        data: postData, // передаем туда наши данные в формате ниже json
        dataType: 'json',
        success: function(data){ // если экшн вернул данные, проверяем
            if(data['success']){ // если все записалось в БД хорошо (ключи success и message создаются в контроллере карт и экшене ордер)
                alert(data['message']); // выводим сообщение, что все ок
                document.location = '/';// затем редиректим на главную страницу
            } else{ // если произошла ошибка
                alert(data['message']);
            }
        }
    });
}*/

/**
 * Показывать или прятать данные о заказе
 * @param $id продукта
 */
function showProducts(id){
    var objName = "#purchasesForOrderId_" + id;
    if( $(objName).css('display') != 'table-row' ) {//смотрим, если у объекта css стиль дисплей не равен table-row, те скрыт - его надо показать
        $(objName).show(); //данный стиль css table-row - специальный стиль для столбца таблиц
    } else{
        $(objName).hide();// если нет - скрыть
    }
}