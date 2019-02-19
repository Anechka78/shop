$( document ).ready(function(){
    //***************pop-up окно регистрации-авторизации пользователя
    var popup = document.getElementById('mypopup'),
        popupToggle = document.getElementById('myBtn'),
        popupClose = document.querySelector('.close');

    popupToggle.onclick = function(){
        popup.style.display = "block";
    };

    popupClose.onclick = function(){
        popup.style.display = "none";
    };

    window.onclick = function(e){
        if(e.target == popup){
            popup.style.display = "none";
        }
    };


    //*****************************изменение валюты****************
    $('#currency').change(function(){
        window.location = '/currency/change?curr=' + $(this).val();
    });


    //*****************************РЕГИСТРАЦИЯ**********************
    var mail = $('#email');
    var pwd1 = $('#pwd1'),
        //pattern_pwd = /^.*[A-Z]+.*$/;
        pattern_pwd = /.*[A-Z]+.*/;
    var pwd2 = $('#pwd2');

    pwd2.on('input',function(){
        if(pwd1.val() !== pwd2.val()){
            $('#success-pwd2').css({'display':'none'});
            $('#pwd2').css({'border':'1px solid red'});
            $('#error-pwd2').html('Пароли должны совпадать');
            $('#error-pwd2').css({'display':'block'});
            $('#signup_btn').attr('disabled', true);
        } else{
            $('#error-pwd2').css({'display':'none'});
            $('#pwd2').css({'border':'1px solid green'});
            $('#success-pwd2').html('Пароли совпадают');
            $('#success-pwd2').css({'display':'block'});
            $('#signup_btn').attr('disabled', false);
            console.log('ok');
        }
    });


    pwd1.blur(function(){
        if (pwd1.val().length < 6 || pwd1.val().length > 10) {
            $('#success-pwd1').css({'display':'none'});
            $('#pwd1').css({'border':'1px solid red'});
            $('#error-pwd1').html('Длина пароля должна быть от 6 до 10 символов');
            $('#error-pwd1').css({'display':'block'});
            $('#signup_btn').attr('disabled', true);
            return;
        }else if (pwd1.val().search(pattern_pwd) !== 0){
            $('#success-pwd1').css({'display':'none'});
            $('#pwd1').css({'border':'1px solid red'});
            $('#error-pwd1').html('Пароль должен содержать хотя бы 1 заглавную букву A - Z');
            $('#error-pwd1').css({'display':'block'});
            $('#signup_btn').attr('disabled', true);
            return;
        }else{
            $('#error-pwd1').css({'display':'none'});
            $('#pwd1').css({'border':'1px solid green'});
            $('#success-pwd1').html('Пароль введен корректно!');
            $('#success-pwd1').css({'display':'block'});
            $('#signup_btn').attr('disabled', false);
        }
    });

    pwd1.focus(function(){
        $('#pass-info').css({'display':'none'});
        $('#signup-error-info').remove();
        $('#success-pwd1').css({'display':'none'});
        $('#pwd1').css({'border':'1px solid #CCC'});
        $('#error-pwd1').html('');
        $('#error-pwd1').css({'display':'none'});
        $('#signup_btn').attr('disabled', false);
    });


    var pattern = /^([.a-z0-9_-]+)@([.a-z0-9-_]+)\.([a-z0-9_-]{1,6})$/i;

    mail.blur(function(){
        if(mail.val() != ''){
            if(mail.val().search(pattern) == 0){
                $('#error-email').css({'display':'none'});
                $('#email').css({'border':'1px solid green'});
                $('#success-email').html('Ok!');
                $('#success-email').css({'display':'block'});
                $('#signup_btn').attr('disabled', false);
                console.log('ok');
            }else{
                $('#success-email').css({'display':'none'});
                $('#email').css({'border':'1px solid red'});
                $('#error-email').html('E-mail введен некорректно');
                $('#error-email').css({'display':'block'});
                $('#signup_btn').attr('disabled', true);
                return;
            }
        }else{
            $('#success-email').css({'display':'none'});
            $('#email').css({'border':'1px solid red'});
            $('#error-email').html('E-mail не должен быть пустым');
            $('#error-email').css({'display':'block'});
            $('#signup_btn').attr('disabled', true);
            return;
        }

    });

    mail.focus(function(){
        $('#signup-error-info').remove();
        $('#success-email').css({'display':'none'});
        $('#email').css({'border':'1px solid #CCC'});
        $('#error-email').html('');
        $('#error-email').css({'display':'none'});
        $('#signup_btn').attr('disabled', false);
    });

    //*****************************РЕГИСТРАЦИЯ END**********************


});

function showMiniCart(cart){
    //console.log(cart);
    var sum = cart['totalsum']*course;
    if(sum > 0){
        document.getElementById("cartCntItems").textContent=cart['totalqty'];
        document.getElementById("cartCntSum").textContent= symLeft+sum.toFixed(2)+symRight;
    }else{
        document.getElementById("cartCntItems").textContent=cart['totalqty'];
        document.getElementById("cartCntSum").innerHTML = 'КОРЗИНА';
    }
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
function registerNewUser(){
    var postData = getData('#registerBox'); //получаем данные (registerBox), обрабатываем их getData и заносим в переменную postData
console.log(postData); die();
    $.ajax({
        type: 'POST',
        async: false,
        url: "/user/signup",
        data: postData,
        dataType: 'json',
        success: function(data){
            //то, что мы вернули из json_encode файла usercontroller функции action signup
            console.log(data);
            if (data['success']){ // если ключ success, который там есть истинен, то пишем, что регистрация прошла успешно
                alert(data['message']);
                var div = document.createElement('div');
                $(div).addClass('success-info');
                div.innerHTML = 'Вы успешно зарегистрированы, теперь можете авторизоваться!';
                $(div).insertBefore('#registerBoxHidden');

            } else{ // если произошла ошибка
                alert(data['message']);
                var div = document.createElement('div');
                $(div).addClass('error-info');
                $(div).attr('id', 'signup-error-info');
                $(div).css('display', 'block');
                div.innerHTML = data['message'];
                $(div).insertBefore('#registerBoxHidden');
            }
        }
    });
}

/**
 * Авторизация пользователя
 */
function login(){
    var email = $('#loginEmail').val();
    var pwd = $('#loginPwd').val();
    $.ajax({ // выполнение ajax запроса
        type: 'POST',
        //async: false,
        async: true,
        url: "/user/login",
        data: {email:email, pwd: pwd},
        dataType: 'json',
        success: function(data){
            //console.log(data);
            if(data['success']){
                $('.error-info').remove();
                alert(data['message']);
                $('#registerBox').hide(); // прячем формы после успешной авторизации -это и слева и на странице оформления заказа, тк идентификаторы названы одинаково
                $('#loginBox').hide();

                $('#userLink').attr('href', '/user/'); // ссылка на личную страничку пользователя
                $('#userLink').html("Вы вошли как: " + data['user']['login']); // имя или мыло польз-ля
                $('#userBox').show(); // показываем опцию выхода

                //удаляем поля на странице заказа /cart/index - авторизация, регистрация, покупка в один клик
                $('#user_login').remove();
                $('#user_signup').remove();
                $('.order_without_login').remove();
                //заполняем поля с информацией о клиенте на странице заказа /cart/index
                $('#user_order_email').val(data['user']['email']);
                $('#user_order_name').val(data['user']['name']);
                $('#user_order_phone').val(data['user']['phone']);
                $('#user_order_adress').val(data['user']['address']);

                //$('#btnSaveOrder').show(); //ищем объект с идентификатором btnSaveOrder и выполняем метод show, те показываем его
            }else{ // если массив пуст = выдаем сообщение об ошибке
                alert(data['message']);
                var div = document.createElement('div');
                $(div).addClass('error-info');
                $(div).attr('id', 'signup-error-info');
                $(div).css('display', 'block');
                div.innerHTML = data['message'];
                $(div).insertBefore('#loginBox');
            }
        }
    });
}

/**
 * Показывать или прятать форму регистрации
 */
function showRegisterBox(){
    if( $("#registerBoxHidden").css('display') != 'block'){ //проверяем стиль у css блока display в leftcolumn.tpl. Если он виден, то значение его = block
        $("#registerBoxHidden").show(); // вызываем метод, т.е. показываем, стилю присваиваем значение блок, значит - показываем
    } else{
        $("#registerBoxHidden").hide(); // прячем дисплей св-во
    }
}