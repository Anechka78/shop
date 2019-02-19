<header class="headerTop" xmlns="http://www.w3.org/1999/html">
	<div class="wrapper">
		<div class="headerTop__wrap">
			<div class="headerTop__contacts">
				<span class="headerTop__contacts-email">
					<i class="fa fa-at fa-lg"></i>
					<a href="mailto:7winx@bk.ru" title="Написать нам">7winx@bk.ru</a>
				</span>
				<span class="headerTop__contacts-phone">
					<i class="fa fa-phone fa-lg"></i>
					<span>089-202-93-99	</span><br>
					<span class="headerTop__contacts-phone-work">Ежедневно с 10 до 20 часов</span>
				</span>
				<span class="box">
					<select id="currency" tabindex="4" class="dropdown drop">
						<?php new \im\widgets\currency\Currency(); ?>
					</select>
				</span>
			</div><!--headerLogo__contacts-->
			<div class="headerTop__contacts-hidden">
				<span class="headerTop__contacts-email">
					<i class="fa fa-at fa-lg"></i>
					<a href="mailto:7winx@bk.ru" title="Написать нам">7winx@bk.ru</a>
				</span>
				<span class="headerTop__contacts-phone">
					<i class="fa fa-phone fa-lg"></i>
					<span>089-202-93-99	</span><br>
					<span class="headerTop__contacts-phone-work">Ежедневно с 10 до 20 часов</span>
				</span>	
			</div>
			<div class="headerTop__right">
				<span class="headerTop__enter" id="myBtn" title="Вход/Регистрация">
					<i class="fa fa-user fa-lg"></i> Личный кабинет
				</span>
					<div class="popup" id="mypopup">
					  <div class="popup-content">
					    <div class="popup-header">
					      <p class="popup-enter">Вход / регистрация пользователя</p>
					      <span class="close">&times;</span>
					    </div>
					    <div class="popup-body">		
					    		<div class="popup-login__form">
									<?php if(!empty($_SESSION['user'])):?>
										<?php //debug($_SESSION['user']);?>

										<!-- Если переменная существует - показываем пользователя, тк он уже залогинен-->
										<div id="userBox">
											<a href="/user/" id="userLink">Вы вошли как: <?= h($_SESSION['user']['login']); ?></a></br>
											<a href="/user/logout" onclick="" class="btn">Выход</a>
										</div>
									<?php else: ?>

									<!-- Если переменная не существует - показываем форму логина и регистрации-->

										<div id="userBox" class="hideme">
											<a href="#" id="userLink"> Вы вошли как:</a></br>
											<a href="/user/logout" onclick="" class="btn">Выход</a>
										</div>
										<!-- Следующая строчка нужна при оформлении заказа - сокрытие левого меню авторизации-->

										<div id="loginBox">
											<div class="popup__line">
								    			<h2>У меня уже есть регистрация!</h2>
								    		</div>
											<div class="popup__input">
												<span class="popup__input_text">Логин (e-mail): </span>
												<input type="text" id="loginEmail" name="loginEmail" value="" class="enter__imput"/>
											</div>
											<div class="popup__input">
												<span class="popup__input_text">Пароль: </span>
												<input type="password" id="loginPwd" name="loginPwd" value="" class="enter__imput"/>
											</div>
											<input type="button" class="btn" onclick="login();" value="Войти и заказать"/>
										</div>
										<div id="registerBox">
											<!--<div class="menuCaption showHidden" onclick="showRegisterBox();">Регистрация</div>-->
											<div class="popup__line">
								    			<h2>Я на сайте впервые / нет регистрации</h2>
								    		</div>

											<div id="registerBoxHidden">
												<div class="popup__input">
													<span class="popup__input_text">E-mail (login): </span>
													<input type="text" id="email" name="email" value="" class="enter__imput"/>
													<span class="error-info" style="display: none;" id="error-email"></span>
													<span class="success-info" style="display: none;" id="success-email"></span>
												</div>
												<div class="popup__input">
													<span class="popup__input_text" >Пароль: </span>
													<input type="password" id="pwd1" name="pwd1" value="" class="enter__imput" />
													<div class="success-info" id="pass-info">Длина пароля от 6-10 символов, <br>обязательна 1 заглавная буква A-Z</div>
													<span class="error-info" style="display: none;" id="error-pwd1"></span>
													<span class="success-info" style="display: none;" id="success-pwd1"></span>
												</div>
												<div class="popup__input">
													<span class="popup__input_text">Повторите пароль: </span>
													<input type="password" id="pwd2" name="pwd2" value="" class="enter__imput"/>
													<span class="error-info" style="display: none;" id="error-pwd2"></span>
													<span class="success-info" style="display: none;" id="success-pwd2"></span>
												</div>
												<input type="button" id="signup_btn" class="btn" onclick="registerNewUser();" value="Зарегистрироваться" disabled/></br>

											</div><!--registerBoxHidden-->
										</div><!--registerBox-->
									<?php endif; ?>

					    		</div>	<!--popup-login__form-->			    	
					    	<div style="clear: both;"></div>

					    </div><!--popup-body-->
					  </div> <!--popup-content-->
					</div><!-- popup-->
				<span class="headerTop__cart">

					<i class="fa fa-shopping-cart fa-lg"></i>&nbsp;<a href="/cart/" title="Перейти в корзину" id="cartCntSum">
						<?php if( (!empty($_SESSION['cart'])) && ($_SESSION['cart']['totalsum'] > 0) ): ?>
							<?= $_SESSION['cart.currency']['symbol_left'].' '.$_SESSION['cart']['totalsum']*$_SESSION['cart.currency']['value'].' '.$_SESSION['cart.currency']['symbol_right']; ?>

						<?php elseif( (!empty($_SESSION['user']['cart'])) && ($_SESSION['user']['cart']['totalsum']>0) ): ?>
							<?= $_SESSION['cart.currency']['symbol_left'].' '.$_SESSION['user']['cart']['totalsum']*$_SESSION['cart.currency']['value'].' '.$_SESSION['cart.currency']['symbol_right']; ?>

						<?php else:?>
							<?= 'КОРЗИНА'; ?>
						<?php endif; ?>
					</a>

					<span class="headerTop__cart_items" id="cartCntItems" title="Товаров в корзине">
						<?php if( !empty($_SESSION['cart'])):?>
							<?=($_SESSION['cart']['totalqty']);?>
						<?php elseif( !empty($_SESSION['user']['cart'])): ?>
							<?= $_SESSION['user']['cart']['totalqty']; ?>
						<?php else: ?>
							<?= '0'; ?>
						<?php endif; ?>
						<!--{if $cartCntItems > 0}{$cartCntItems}{else}0{/if}-->
					</span><!--headerTop__cart-->
				</span>
			</div>
		</div><!--headerTop__wrap-->

	</div><!--wrapper-->
</header>