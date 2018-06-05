<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Faktura VAT</title>
<style>
	body {
		font-family: 'DejaVu Sans';
		font-size: 14px;
		line-height: 20px;
	}
	p {
		margin: 5px 0;
	}
	.text-center {
		text-align: center;
	}
	.row:before,
	.row:after {
		display: table;
		content: " ";
	}
	.row:after{
		clear: both;
	}
	.col {
		float: left;
		box-sizing: border-box;
		min-height: 1px;
	}
	.col-50 {
		width: 50%;
	}
	.invoice-container h1 {
		font-family: 'DejaVu Sans';
		border-bottom: 1px solid #999;
		padding: 10px 0;
		margin: 20px 0;
	}
	.invoice-logo img {width: 150px; height: auto;}
	.invoice-table {width: 100%;}
	.invoice-col-1 {width: 50%;}
	.invoice-col-2,.invoice-col-3 {width: 25%;}
	.table.invoice-table > thead > tr > th,
	.table.invoice-table > tbody > tr > th,
	.table.invoice-table > tfoot > tr > th,
	.table.invoice-table > thead > tr > td,
	.table.invoice-table > tbody > tr > td,
	.table.invoice-table > tfoot > tr > td {padding: 8px;line-height: 1.42857143; vertical-align: middle; border-top: 1px solid #ddd;text-align: left;}
	.table.invoice-table > thead > tr > th {vertical-align: bottom;border-bottom: 2px solid #ddd;}
	.table.invoice-table > tbody > tr {background-color: #f9f9f9;}
	.invoice-total {font-weight: 900;text-decoration: underline;font-size: 15px;}
</style>
</head>

<body>
<?php  
require_once get_template_directory().'/functions/kwota.php';
$logo 									= get_field('_logo','option');
$order_id 							= isset($params['order_id']) ? sanitize_text_field( $params['order_id'] ) : '';
$payment_date 					= isset($params['payment_date']) ? sanitize_text_field( $params['payment_date'] ) : '';
$company_name 					= isset($params['company_name']) ? $params['company_name'] : '';
$company_address 				= isset($params['company_address']) ? $params['company_address'] : '';
$company_nip						= isset($params['company_nip']) ? $params['company_nip'] : '';
$place_company_name 		= isset($params['place_company_name']) ? $params['place_company_name'] : '';
$place_company_address 	= isset($params['place_company_address']) ? $params['place_company_address'] : '';
$place_company_nip			= isset($params['place_company_nip']) ? $params['place_company_nip'] : '';
$products								= isset($params['products']) ? $params['products'] : array();
$taxes      						= array();
$prices     						= array();
?>
	<div class="invoice-container">

		<div class="row text-center invoice-logo">
			<?php if($logo) : ?>
			<img src="<?php echo esc_url($logo['sizes']['medium']); ?>" alt="<?php echo esc_attr($logo['alt']); ?>">
			<?php endif; ?>
		</div>

		<h1 class="text-center text-uppercase">Faktura VAT nr <?php echo $order_id; ?></h1>

		<div class="row">
			<div class="col-50 col">
				<h4>Sprzedawca:</h4>
				<?php if( $company_name ) : ?><p><?php echo $company_name; ?></p><?php endif; ?>
				<?php if( $company_address ) : ?><p><?php echo $company_address; ?></p><?php endif; ?>
				<?php if( $company_nip ) : ?><p><?php echo $company_nip; ?></p><?php endif; ?>
			</div>
			<div class="col-50 col">
				<h4>Nabywca:</h4>
				<?php if( $place_company_name ) : ?><p><?php echo $place_company_name; ?></p><?php endif; ?>
				<?php if( $place_company_address ) : ?><p><?php echo $place_company_address; ?></p><?php endif; ?>
				<?php if( $place_company_nip ) : ?><p><?php echo $place_company_nip; ?></p><?php endif; ?>
			</div>
		</div>
		<br><br><br>
		<div class="row">
			<p>Data wystawienia: <?php echo $payment_date; ?></p>

			<?php if($products) : ?>
			<table class="table table-striped invoice-table" border="0" cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<th class="invoice-col-1">Nazwa</th>
						<th class="invoice-col-2">Okres ważności</th>
						<th class="invoice-col-3">Cena netto</th>
					</tr>
				</thead>
				<tbody>
				<?php foreach($products as $product) : ?>
				<?php  
				$taxes[]        = $product['price'] * $product['tax'];
				$prices[]       = $product['total'];
				$type           = $product['type'];
				$term           = $product['term'];
				$activationDate = $product['activation_date'] ? $product['activation_date'] : '';
				$expiryDate     = '';
				switch($type) {
					case 'subscription':
						$expiryDate = subscription_term($term, $activationDate, true);
						break;
					case 'promo':
						$expiryDate = promo_term($term, $activationDate, true);
						break;
				}
				?>
					<tr>
						<td><?php echo esc_html( $product['name'] ) ?></td>
						<td><?php echo date( 'Y-m-d', strtotime($expiryDate) ); ?></td>
						<td><?php echo esc_html( $product['price'] ) ?> PLN</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td></td>
						<td><strong>VAT</strong></td>
						<td class="invoice-vat"><?php echo format_price( array_sum($taxes) ); ?> PLN</td>
					</tr>
					<tr>
						<td></td>
						<td><strong>Razem</strong></td>
						<td class="invoice-total"><?php echo format_price( array_sum($prices) ); ?> PLN</td>
					</tr>
				</tfoot>
			</table>
			<br>
			<p>Razem do zapłaty: <strong><?php echo format_price( array_sum($prices) ); ?> PLN</strong></p>
			<p>Słownie: <strong><?php echo Kwota::getInstance()->slownie( array_sum($prices), null, false, false ); ?></strong></p>
			<?php endif; ?>
		</div>
		<br>
		<br>
		<br>
		<div class="row text-center">
			<div class="col-50 col">
				<p>.........................................</p>
				<p>Osoba upoważniona do dobioru</p>
			</div>
			<div class="col-50 col">
				<p>.........................................</p>
				<p>Osoba upoważniona do wystawienia</p>
			</div>
		</div>
		
	</div>

</body>
</html>