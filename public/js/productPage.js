$(document).ready(function() {
	//слайдер товаров
	$('.slider-for').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: true,
		asNavFor: '.slider-nav'
	});
	$('.slider-nav').slick({
		prevArrow: '<button type="button" class="slick-prev" style="background-color: #2D2D2D;">Previous</button>',
		nextArrow: '<button type="button" class="slick-next" style="background-color: #2D2D2D;">Next</button>',
		slidesToShow: 3,
		slidesToScroll: 3,
		asNavFor: '.slider-for',
		dots: true,
		centerMode: true,
		focusOnSelect: true
	});



	var BASE_PRICE = $('.product-price span#base-price').text();
	var oldPrice = $('.product-price span#old-for-price').text();
	//Функция для показа дочерних характеристик в зависимости от родительской
	$('.sizes span').on('click', function(){
		//сбрасываем активный класс у родительских и дочерних характеристик
		$('.sizes span').removeClass('active');
		$('.colors span').removeClass('active');
		$('.btn__cart').removeClass('hidden');

		//ставим активный класс родительской характеристике, по которой кликнули
		$(this).addClass('active');

		// показ цветов для выбранного размера

		//всем дочерним характеристикам добавляем невидимость
		$('.colors span').addClass('hidden');
		//парсим атрибут data-color родительской характеристики, чтобы получить массив доступных дочерних вариантов
		// например, для размера (род х-ка) выбираем все доступные цвета (дочерние хар-ки)
		var colors = JSON.parse($(this).attr('data-color'));
		//console.log(colors);
		for(var key in colors){
			//console.log(colors[key]['child_val']);
			//для доступных в родителе детей снимаем невидимость
			$('.colors span[title="'+colors[key]['child_val']+'"]').removeClass('hidden');
			if(colors[key]['count'] == 0){
				var color = $('.colors span[title="'+colors[key]['child_val']+'"]');
				//console.log(color);
				//color.removeClass('active');
				color.addClass('nonactive');

				var span = document.createElement('span');
				$(span).addClass('not_in_stock');

				$(span).insertBefore('.nonactive');
				//$('.btn__cart').addClass('hidden');
				$('.colors span.not_in_stock').on('click', function(){
					$('.btn__cart').addClass('hidden');
				});
			}
		}

		if($('.colors span.active').hasClass('hidden')){
			$('.product-size-colors').setAttribute('data-childVal', '');
			$('.colors span').removeClass('active');
		}
		//Создаем атбрибут data-pdobj, в который будем записывать выбранные родительские и дочерние характеристики
		$('.btn__cart').attr('data-pdobj', '');
	});



	$('.colors span').on('click', function(){
		//если был выбран ранее отсутствующий цвет
		$('.btn__cart').removeClass('hidden');
		//выбранный цвет
		var curcolor = $(this).attr('data-color');
		//console.log(curcolor);
		var sizes = JSON.parse($(this).attr('data-size'));

		//Если при клике на доч х-ку есть активная родительская х-ка
		if($('.sizes span').hasClass('active')){
			//скидываем у всех детей возможный класс актив
			$('.colors span').removeClass('active');
			//добавляем класс актив тому ребенку, по которому кликнули
			$(this).addClass('active');
			//находим родителя и берем его данные из атрибута data-color
			var parent_span = $('.sizes span.active').attr('data-color');
			var parent_span = JSON.parse(parent_span);
			//console.log(parent_span);

			//Выбираем все характеристики, которые относятся к паре родитель-ребенок
			for(var key in parent_span){
				if(parent_span[key]['child_val'] == curcolor){
					//Находим id товара из таблицы products
					var product_id = parent_span[key]['product_id'];
					//Находим id товара из таблицы product_dependences
					var pd_id = key;
					//Находим название родительской и дочерней характеристик
					var parent_name = parent_span[key]['parent_name'];
					var child_name = parent_span[key]['child_name'];
					var count = parent_span[key]['count'];
					var weight = parent_span[key]['weight'];

					if(parent_span[key]['price'] != 0){
						var price = parent_span[key]['price'];
					}
					if(parent_span[key]['old_price'] != 0){
						var oldprice = parent_span[key]['old_price'];
					}
					//Находим базовые цены basePrice и oldPrice из таблицы products, которые стоят на товар по дефолту
					//var BASE_PRICE = $('.product-price span#base-price').text();
					//var oldPrice = $('.product-price span#old-for-price').text();

					//Прописываем логику работы с ценой
					if(!oldPrice && oldprice){
						$('.product-price span#old-price').removeClass('hidden');
						var span = document.createElement("span");
						span.id = "old-price";
						span.innerHTML = "<del>"+"<span id='old-for-price'>"+"</span></del>";
						var div = document.getElementsByClassName('product-price')[0];
						//вставляем спан со старой ценой перед спаном с текущей ценой
						div.insertBefore(span, div.children[0]);
						//Подставляем во вновь созданный элемент цену дочерней х-ки, умноженную на курс
						$('.product-price span#old-for-price').text(oldprice*course);
					} else if(oldPrice && oldprice){
						$('.product-price span#old-price').removeClass('hidden');
						$('.product-price span#old-for-price').text(oldprice*course);
					} else if(oldPrice && !oldprice){
						$('.product-price span#old-for-price').text();
						$('.product-price span#old-price').addClass('hidden');
					}

					if(price){
						$('.product-price span#base-price').text(price*course);
					} else{
						$('.product-price span#base-price').text(BASE_PRICE);
					}

				}
			}

			var cursize =  $('.sizes span').attr('data-size');
			var pd_obj = {};
			pd_obj.pdId = pd_id;

			//pd_obj.childVal = curcolor;
			//pd_obj.childName = child_name;
			//pd_obj.parentName = parent_name;
			//pd_obj.parentVal = cursize;
			//pd_obj.pdCount = count;
			//pd_obj.pdWeight = weight;
			////console.log(BASE_PRICE);
			//if(price){
			//	pd_obj.pdPrice = price*course;
			//}else{
			//	pd_obj.pdPrice = BASE_PRICE;
			//	$('.product-price span#old-price').removeClass('hidden');
			//	$('.product-price span#old-for-price').text(oldPrice);
			//}
			////pd_obj.pdPrice = price*course;
			//pd_obj.pdLeftCur = symLeft;
			//pd_obj.pdRightCur = symRight;

			$('.btn__cart').attr('data-pdobj', JSON.stringify(pd_obj));

		} else{
			$(this).removeClass('active');
			alert('Выберите размер, чтобы посмотреть доступные цвета!');
		}

		var finalPrice = $('.product-price span#base-price').text();
		//console.log(finalPrice);
	});

	

	//Табы на странице товара
	$('.tabs a').click(function () {
		$(this).parents('.tab-wrap').find('.tab-cont').addClass('hide');
		$(this).parent().siblings().removeClass('active');
		var id = $(this).attr('href');
		$(id).removeClass('hide');
		$(this).parent().addClass('active');
		return false
	});

	function getProperties() {
		//Получаем дивы с характеристиками
		var mods = document.getElementsByClassName('product_properties');
		//console.log(mods);
		//Объявляем объект, куда будем записывать свойства полученных/выбранных характеристик
		var chars = {};
		//Считаем длину массива
		q = mods.length;

		for (var i = 0; i < q; i++) {
			//console.log('Название: '+JSON.parse($(mods[i]).find('label').eq(0).attr('data-name')));
			//находим спаны с характеристиками, которые нам нужно распрарсить
			var valSpan = $(mods[i]).find('span.property_values')[0];
			//console.log(valSpan);
			//если в этом спане есть список
			if ($(valSpan).find('select').length > 0) {
				//console.log($(valSpan).find('select option:selected').val());
				//console.log($(valSpan).find('select option:selected').text());

				//находим id выбранной характеристики из таблицы product_properties_values
				var id = $(valSpan).find('select option:selected').attr('data-id');
				//console.log(id);
				//находим родительское название выбранной характеристики из таблицы product_properties_values
				var p_name = JSON.parse($(mods[i]).find('label').eq(0).attr('data-name'));
				//console.log(p_name);
				//находим всю информацию по характеристике с указанным выше id из таблицы product_properties_values
				var info = $(valSpan).find('select option:selected').attr('data-info');
				//console.log(info);
				var info_j = JSON.parse(info);
				for(var key in info_j){

					if(key == 'price' && info_j[key] != 0){
						//записываем цену товара из характеристики
						var price = info_j[key];
						//console.log(price);
						$('.product-price span#base-price').text(price*course);

						for(var key in info_j) {
							//если есть новая цена товара и старая цена товара в характеристиках
							// пример - черный
							if (key == 'old_price' && info_j[key] != 0) {
								var oldprice = info_j[key];
								//console.log(oldprice);
								//если нет дефолтной старой цены товара из таблицы products
								if (!oldPrice && oldprice) {
									$('.product-price span#old-price').remove();
									var span = document.createElement("span");
									span.id = "old-price";
									span.innerHTML = "<del>" + symLeft + "<span id='old-for-price'>" + "</span>" + symRight + "</del>";
									var div = document.getElementsByClassName('product-price')[0];
									//вставляем спан со старой ценой перед спаном с текущей ценой
									div.insertBefore(span, div.children[0]);
									//Подставляем во вновь созданный элемент цену дочерней х-ки, умноженную на курс
									$('.product-price span#old-for-price').text(oldprice * course);
								}
								// если есть дефолтная старая цена товара из таблицы products
								else if (oldPrice && oldprice) {
									$('.product-price span#old-price').removeClass('hidden');
									$('.product-price span#old-for-price').text(oldprice * course);
								}
							}
							//если есть новая цена товара в характеристиках и нет старой цены в характеристиках
							// пример - красный
							else if (key == 'old_price' && info_j[key] == 0){
								//console.log('Нет старой цены в характеристиках');
								$('.product-price span#old-price').addClass('hidden');
								$('.product-price span#old-for-price').text('');
							}
						}
					}
					//если новая цена товара в характеристиках равна нулю - берем базовую цену из таблицы products
					else if(key == 'price' && info_j[key] == 0){
						$('.product-price span#base-price').text(BASE_PRICE);

						for(var key in info_j) {
							//если нет новой цены в характеристиках и вдруг почему-то есть старая цена в характеристиках
							// это баг и мы выводим дефолтную старую цену из таблицы products, если она есть
							if (key == 'old_price' && info_j[key] != 0) {
								if(oldPrice){
									//console.log('это баг!!!');
									$('.product-price span#old-for-price').text(oldPrice);
								}
								//если старой дефолтной цены нет - прячем блок со старой ценой вообще
								else{
									$('.product-price span#old-price').addClass('hidden');
									$('.product-price span#old-for-price').text('');
								}
							}
							//если нет новой цены в характеристиках и нет старой цены в характеристиках
							else{
								if(oldPrice){
									$('.product-price span#old-price').removeClass('hidden');
									$('.product-price span#old-for-price').text(oldPrice);
								}
								//если старой дефолтной цены нет - прячем блок со старой ценой вообще
								else{
									$('.product-price span#old-price').addClass('hidden');
									$('.product-price span#old-for-price').text('');
								}
							}
						}
					}

				}

				chars[p_name] = {
				////chars[] = {
				//	'name': $(valSpan).find('select option:selected').text(),
					'id': id,
				//	'info': JSON.parse(info)
				//	'price':$('.product-price span#base-price').text(),
				//	'symLeft': symLeft,
				//	'symRight': symRight
				};
			} //если в спане не список, а другие спаны с обычными х-ками без возможности выбора
			else if ($(valSpan).find('span').length > 0) {
				//console.log($(valSpan).find('span').eq(0).text());
				var id = $(valSpan).find('span').attr('data-id');
				//console.log(id);
				//var info = $(valSpan).find('span').attr('data-info');
				//console.log(info);
				//var info_j = JSON.parse(info);

				chars[JSON.parse($(mods[i]).find('label').eq(0).attr('data-name'))] = {
					'id': id,
					//'info': JSON.parse(info)
					//'name': $(valSpan).find('span').eq(0).text()
				};
			} else {
				console.log('Не обрабатываемый тип значения. Ошибка.');
			}
			//chars['price'] = $('.product-price span#base-price').text();
			//chars['symLeft'] = symLeft;
			//chars['symRight'] = symRight;
		}

		//console.log(chars);
		$('.btn__cart').attr('data-ppobj', JSON.stringify(chars));
		//var selected = $('.property_values option:selected').val();
		//var span = document.getElementById('properties').getElementsByClassName('property_name');
	}
	if(document.getElementById('properties')) {
		getProperties();
	}

	$('.property_values select').on('change', function(){
		//alert('etuytgjkgdfk');
		$('.btn__cart').attr('data-ppobj', '');
		getProperties();
	});

	//console.log(span);

	/*CART*/
	$('.btn__cart').on('click', function(e){
		e.preventDefault();
		var id = $(this).data('id');
		//console.log(id);
		var el_pd = document.getElementsByClassName('product-size-colors');
		if(el_pd.length >0 ){
			var tmp_pd = $(this).attr('data-pdobj');
			var pd_id = [];
			//console.log(tmp_pd); die();
			if(!tmp_pd){
				alert('Выберите характеристики товара, чтобы положить его в корзину!');
				return;
			}else{
				var pd = JSON.parse( $(this).attr('data-pdobj'));
				pd_id.push(pd['pdId']) ;
			}
			/*var pd = JSON.parse( $(this).attr('data-pdobj'));
			//console.log(pd);
			var pd_id = [];
			if(!pd){
				alert('Выберите характеристики товара, чтобы положить его в корзину!');
				return;
			}else{
				pd_id.push(pd['pdId']) ;
			}*/
			//console.log(pd_id);
		}

		var el_pp = document.getElementsByClassName('properties');

		if(el_pp.length >0 ){
			var pp = JSON.parse($(this).attr('data-ppobj'));
			if(!pp){
				alert('Выберите характеристики товара, чтобы положить его в корзину!');
				return;
			}else{
				var pp_id = [];
				for(var key in pp){
					if(typeof pp[key] == 'object'){
						var obj = pp[key];
						//console.log(obj);
						for(var pp_key in obj){
							if(pp_key == 'id'){
								pp_id.push(obj[pp_key]);
							}
						}
					}

				}
			}
			//console.log(pp_id);
		}
		if(pd_id === undefined){
			$.ajax({
				url: '/cart/add',
				data: {id: id, pp_id: pp_id},
				type: 'POST',
				dataType: 'json',
				success: function(data){//в результате успешного выполнения отрабатывает ф-я data
					alert(data['message']); // выводим сообщение об успехе
					//console.log(data);
					if(data['success']){//при условии, что success=1
						showMiniCart(data['cart']);// выводим сообщение
					}
				},
				error: function(res){
					console.log(res);
					if(res['message']){
						alert(res['message']);
					}else{
						alert('Ошибка! Попробуйте позже.');
					}
				}
			});
		} else if(pp_id === undefined){
			$.ajax({
				url: '/cart/add',
				data: {id: id, pd_id: pd_id},
				type: 'POST',
				dataType: 'json',
				success: function(data){//в результате успешного выполнения отрабатывает ф-я data
					alert(data['message']); // выводим сообщение об успехе
					//console.log(data);
					if(data['success']){//при условии, что success=1
						showMiniCart(data['cart']);// выводим сообщение
					}
				},
				error: function(res){
					console.log(res);
					if(res['message']){
						alert(res['message']);
					}else{
						alert('Ошибка! Попробуйте позже.');
					}
				}
			});
		} else if(pp_id && pd_id){
			$.ajax({
				url: '/cart/add',
				data: {id: id, pd_id: pd_id, pp_id: pp_id},
				type: 'POST',
				dataType: 'json',
				success: function(data){//в результате успешного выполнения отрабатывает ф-я data
					alert(data['message']); // выводим сообщение об успехе
					console.log(data);
					if(data['success']){//при условии, что success=1
						showMiniCart(data['cart']);// выводим сообщение
					}
				},
				error: function(res){
					//console.log(res);
					if(res['message']){
						alert(res['message']);
					}else{
						alert('Ошибка! Попробуйте позже.');
					}
				}
			});
		} else{
			$.ajax({
				url: '/cart/add',
				data: {id: id},
				type: 'POST',
				dataType: 'json',
				success: function(res){
					showMiniCart(res['cart']);
				},
				error: function(){
					alert('Ошибка! Попробуйте позже.');
				}
			});
		}

	});
});


function showCart(cart){
	//console.log(cart); die();
	if($.trim(cart) == '<h3>Корзина пуста</h3>'){
		$('#cartForm').css('display', 'none');
	}else{
		$('#cartForm').css('display', 'inline-block');
	}
	$('#wrapper_cart').html(cart);

	if($('.summ-count').text()){
		$('#cartCntSum').html($('summ-count').text());
	}else{
		$('#cartCntSum').text('Empty Cart');
	}
}

/**
 * Функция добавления товара в корзину
 * ID продукта
 * в случае успеха обновятся данные корзины на странице
 */
function addToCart(itemId){
	var curentProd = {
		'size' : 	$('.sizes .active').attr('data-size'),
		'color': 	$('.colors .active').attr('data-color')
	};
        
    if((curentProd.size == '') || (curentProd.color == '')){
    	alert('Выберите размер и цвет товара');
    } else {
    	//var postData = {curentProd: curentProd};
    	var postData = curentProd;
    	//console.log(postData);
	    $.ajax({                                      // ф-я из библиотеки, предназначена для создания ajax запросов, ниже - параметры запроса
	        type: 'POST',                             // тип запроса - метод
	        async: true,                             // асинхронность - ставим false, запрос не асинхронен
	        data: {'postData': JSON.stringify(postData)},
	        url: "/cart/addtocart/" + itemId + '/',   // адрес, куда мы будем обращаться - к картконтроллеру, ф-я addtocart и передавать ему будем Id товара
	        dataType: 'json',                          //тип данных, если не json - не сможем обрабатывать
	        success: function(data){                  //попадаем в ф-ю, когда получили данные выше. data - то, что к нам пришло из CartController - json_encode($resData)
	            if(data['success']){                  //если значение ключа success истинно - выполняем код ниже
	                $('#cartCntItems').html(data['cntItems']); //меняет кол-во элементов в корзине

	                /*$('#addCart_'+ itemId).hide(); // подменяются ссылки Добавить в корзину и Удалить из корзины
	                $('#removeCart_'+ itemId).show();*/
	            }
	        }
	    });
	}
}

/**
 * Удаление продукта из корзины
 *
 * @param integer itemId ID продукта
 * возвращает функция в случае успеха обновленные данные на странице
 */
function removeFromCart(itemId){
   // console.log("js - removeFromCart("+itemId+")"); // отладочная функция
    $.ajax({
        type: 'POST',
        async: false,
        url: "/cart/removefromcart/" + itemId + '/',
        dataType: 'json',
        success: function(data) {                  //попадаем в ф-ю, когда получили данные выше. data - то, что к нам пришло из CartController - json_encode($resData)
            if (data['success']) {                  //если значение ключа success истинно - выполняем код ниже
                $('#cartCntItems').html(data['cntItems']); //меняет кол-во элементов в корзине

                $('#addCart_' + itemId).show(); // подменяются ссылки Добавить в корзину и Удалить из корзины
                $('#removeCart_' + itemId).hide();
            }
        }
    });
}
