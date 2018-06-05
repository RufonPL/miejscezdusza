<article data-ng-controller="LoginCtrl">
	<div id="post-<?php the_ID(); ?>" <?php post_class('row'); ?>>
		<div class="row">
			<header>
				<h1 class="text-uppercase text-center"><?php the_title(); ?></h1>
			</header>

			<div class="alert alert-danger text-center alert-sm" data-ng-if="iserror && !success && (forms.loginForm.$submitted || forms.registerForm.$submitted || forms.remindForm.$submitted)">
				<p class="no-margin" data-ng-if="error">Wystąpił błąd. Odśwież stronę i spróbuj ponownie</p>
				<p class="no-margin" data-ng-if="(forms.loginForm.$invalid && forms.loginForm.$submitted) || (forms.registerForm.$invalid && forms.registerForm.$submitted && !forms.registerForm.$error.email) || (forms.remindForm.$invalid && forms.remindForm.$submitted)">Wypełnij wszystkie pola</p>
				<p class="no-margin" data-ng-if="wrong">Nieprawidłowy login lub hasło</p>
				<p class="no-margin" data-ng-if="emailexists">Podany adres email jest już zarejestrowany</p>
				<p class="no-margin" data-ng-if="userexists">Podana nazwa użytkownika jest zajęta</p>
				<p class="no-margin" data-ng-if="emailError || (forms.registerForm.$submitted && forms.registerForm.$error.email) || (forms.remindForm.$submitted && forms.remindForm.$error.email)">Nieprawidłowy adres email</p>
				<p class="no-margin" data-ng-if="notexists">Użytkownik o podanym adresie email nie istnieje</p>
				<p class="no-margin" data-ng-if="notactive">Funkcja chwilowo wyłączona</p>
			</div>
			<div class="alert alert-success text-center alert-sm" data-ng-if="success">
				<p class="no-margin" data-ng-if="registered">Rejestracja zakończona sukcesem. Hasło zostało wysłane mailem</p>
				<p class="no-margin" data-ng-if="reset">Twoje nowe hasło zostało wysłane mailem</p>
			</div>

			<div class="login-view" data-ng-show="view == 'login'">
				<div class="view-inner">
					<p class="f24 color2 font2"><strong>Zaloguj się</strong></p>

					<form method="post" name="forms.loginForm" data-ng-submit="processLogin('login')" novalidate  data-ng-init="referrer='<?php echo esc_url( $_SERVER['HTTP_REFERER'] ); ?>'">
						<div class="form-group">
							<label for="login-name">Nazwa użytkownika<span class="asterisk">*</span></label>
							<input type="text" class="form-control" name="login-name" data-ng-model="formData.loginName" data-ng-required="true" placeholder="Login">
						</div>
						<div class="form-group">
							<label for="login-pass">Hasło<span class="asterisk">*</span></label>
							<input type="password" class="form-control" name="login-pass" data-ng-model="formData.loginPass" data-ng-required="true" placeholder="Hasło">
						</div>
						<div class="form-group">
							<button class="btn btn-primary text-uppercase" data-ng-disabled="loading" type="submit">Zaloguj</button>
							<div class="inline-block" data-ng-if="loading"><?php show_preloader(); ?></div>
						</div>
					</form>

					<div class="login-links">
						<p class="no-margin color2">Nie masz konta? <a data-ng-click="changeView('register')">Zarejestruj się</a></p>
						<p class="no-margin color2">Zapomniałeś hasła? <a data-ng-click="changeView('remind')">Przypomnij</a></p>
					</div>

				</div>
			</div>

			<div class="register-view" data-ng-show="view == 'register'">
				<div class="view-inner">
					<p class="f24 color2 font2"><strong>Załóż konto</strong></p>

					<form method="post" name="forms.registerForm" data-ng-submit="processLogin('register')" novalidate>
						<div class="form-group">
							<label for="register-name">Nazwa użytkownika<span class="asterisk">*</span></label>
							<input type="text" class="form-control" name="register-name" data-ng-model="formData.registerName" data-ng-required="true" placeholder="Login">
						</div>
						<div class="form-group">
							<label for="register-email">Adres email<span class="asterisk">*</span></label>
							<input type="email" class="form-control" name="register-email" data-ng-model="formData.registerEmail" data-ng-required="true" placeholder="Email">
						</div>
						<div class="form-group">
							<button class="btn btn-primary text-uppercase" type="submit">Zarejestruj</button>
							<div class="inline-block" data-ng-if="loading"><?php show_preloader(); ?></div>
						</div>
					</form>

					<div class="login-links">
						<p class="no-margin color2">Posiadasz konto? <a data-ng-click="changeView('login')">Zaloguj się</a></p>
					</div>

				</div>
			</div>

			<div class="register-view" data-ng-show="view == 'remind'">
				<div class="view-inner">
					<p class="f24 color2 font2"><strong>Przypomnij hasło</strong></p>

					<form method="post" name="forms.remindForm" data-ng-submit="processLogin('remind')" novalidate>
						<div class="form-group">
							<label for="remind-email">Adres email<span class="asterisk">*</span></label>
							<input type="email" class="form-control" name="remind-email" data-ng-model="formData.remindEmail" data-ng-required="true" placeholder="Email">
						</div>
						<div class="form-group">
							<button class="btn btn-primary text-uppercase" type="submit">Wyślij</button>
							<div class="inline-block" data-ng-if="loading"><?php show_preloader(); ?></div>
						</div>
					</form>

					<div class="login-links">
						<p class="no-margin color2"><a data-ng-click="changeView('login')">Zaloguj się</a></p>
					</div>

				</div>
			</div>

		</div>
	</div>
</article>