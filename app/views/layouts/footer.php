	<section class="section__footer">
		<div class="wrapper">
			<span class="headerTop__contacts-email copy">
					<i class="fa fa-copyright fa-lg"></i>
					<span> 2018 Made by Anna</span>
			</span>
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
			</div><!--headerLogo__contacts-->			
		</div>
		<?php if(isset($_SESSION['error'])): ?>
			<div>
				<?= $_SESSION['error']; unset($_SESSION['error']); ?>
			</div>
		<?php endif; ?>

		<?php if(isset($_SESSION['success'])): ?>
			<div>
				<?= $_SESSION['success']; unset($_SESSION['success']); ?>
			</div>
		<?php endif; ?>
	</section>
</div><!--page-->
</body>
</html> 
