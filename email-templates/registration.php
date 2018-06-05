<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"> 
<title><?php wp_title('|', true, 'right'); ?></title>
</head>
<style>
@import url('https://fonts.googleapis.com/css?family=Catamaran:400,700');

body,p {font-family: 'Catamaran', sans-serif; background-color:#fff; color:#28272e; font-size:14px; line-height:24px;}
h1,h2,h3,h4,h5,h6,.h1,.h2,.h3,.h4,.h5,.h6 {font-family: inherit; line-height:1.2em; color:#28272e; font-weight:normal; margin:5px 0;}
h1{font-size:24px;}
h2{font-size:18px;}
h3{font-size:16px;}
h4{font-size:14px;}
.text-center{text-align:center;}
.text-uppercase{text-transform: uppercase;}
p{line-height:18px; margin:0;}
a{color: #0099cc;}
.em-header{padding:10px; text-align: center;}
.em-container{width:600px; padding:10px; border:solid 1px #ddd;}
.em-content{margin-top:10px; padding:10px;}
.em-footer{padding:10px; margin-top:10px;}
.table td {padding-right:20px;}
</style>
<body>
<?php 
$login 	    = $params['login'] ? $params['login'] : '';
$password 	= $params['password'] ? $params['password'] : '';
?>
	<div class="em-container">
		<?php echo rfs_email_get_header(); ?>
        <div class="em-content text-center">
        	<h2>Witaj <?php echo $login; ?>.</h2>
        	<h3>Twoje konto w serwisie <strong class="text-uppercase"><?php bloginfo( 'name' ); ?></strong> zostało utworzone.</h3>
        	<h3>Pozniżej znajdują się szczegóły:</h3>
            <br/>
            <table class="table table-bordered" border="0" cellpadding="0" cellspacing="0">
                <tr>
                   <td><strong>Login: </strong></td>
                   <td> <?php echo $login; ?></td> 
                </tr>
                <tr>
                   <td><strong>Hasło: </strong></td>
                   <td> <?php echo $password; ?></td> 
                </tr>
            </table>
            <p class="text-center"><strong class="text-uppercase"><a href="<?php echo esc_url( rfs_redirect_to('login') ); ?>">Zaloguj się</a></strong></p>
        </div>
        <?php echo rfs_email_get_footer(); ?>
    </div>
</body>
</html>