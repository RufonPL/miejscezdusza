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
h4{font-size:15px;}
.text-center{text-align:center;}
.text-uppercase{text-transform: uppercase;}
p{line-height:18px; margin:0;}
a{color: #0099cc;}
.em-header{padding:10px; text-align: center;}
.em-container{width:600px; padding:10px; border:solid 1px #ddd;}
.em-content{margin-top:10px; padding:10px;}
.em-footer{padding:10px; margin-top:10px;}
.summary-table {max-width: 600px; width: 100%; margin: 20px auto 30px;}
.summary-col-1 {width: 50%;}
.summary-col-2,.summary-col-3 {width: 25%;}
.table.summary-table > thead > tr > th,
.table.summary-table > tbody > tr > th,
.table.summary-table > tfoot > tr > th,
.table.summary-table > thead > tr > td,
.table.summary-table > tbody > tr > td,
.table.summary-table > tfoot > tr > td {padding: 8px;line-height: 1.42857143; vertical-align: middle; border-top: 1px solid #ddd;text-align: left;}
.table.summary-table > thead > tr > th {vertical-align: bottom;border-bottom: 2px solid #ddd;}
.table.summary-table > tbody > tr {background-color: #f9f9f9;}
.summary-total {font-weight: 900;text-decoration: underline;font-size: 16px;color: #0099cc;}
</style>
<body>
<?php 
$name 	    = $params['name'] ? $params['name'] : '';
$orderId 	  = $params['order_id'] ? $params['order_id'] : '';
$trId 	    = $params['tr_id'] ? $params['tr_id'] : '';
$products 	= $params['products'] ? $params['products'] : array();
$payUrl 	  = $params['pay_url'] ? $params['pay_url'] : '';
$method 	  = $params['method'] ? $params['method'] : '';
$taxes      = array();
$prices     = array();
?>
	<div class="em-container">
		<?php echo rfs_email_get_header(); ?>
        <div class="em-content text-center">
        	<h2>Witaj <?php echo $name; ?>.</h2>
        	<h3>Twoje zamówienie o nr <strong><?php echo $orderId; ?></strong> zostało zrealizowane.</h3>
        	<h3>Szczegóły zamówienia:</h3>
          <br/>
          <h4>Nr transakcji w serwisie <span class="text-uppercase"><?php echo $method; ?>:</span> <strong><?php echo $trId; ?></strong></h4>
          <br/>
          <?php if($products) : ?>
          <h2>Produkty:</h2>
          <table class="table table-striped summary-table" border="0" cellpadding="0" cellspacing="0">
						<thead>
							<tr>
								<th class="summary-col-1">Nazwa</th>
								<th class="summary-col-2">Okres ważności</th>
								<th class="summary-col-3">Cena netto</th>
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
								<td class="summary-vat"><?php echo format_price( array_sum($taxes) ); ?> PLN</td>
							</tr>
							<tr>
								<td></td>
								<td><strong>Zapłacono</strong></td>
								<td class="summary-total"><?php echo format_price( array_sum($prices) ); ?> PLN</td>
							</tr>
						</tfoot>
					</table>
          <?php endif; ?>
        </div>
        <?php echo rfs_email_get_footer(); ?>
    </div>
</body>
</html>