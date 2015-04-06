<li class="col-md-4" style="list-style:none">
	<div class="cashu-wrap">
		<div class="container">
			<p class="payment_module cashu">				
				<a href="#" >
					<span class="img-wrap">
						<img src="{$base_dir_ssl|escape:'htmlall':'UTF-8'}modules/cashu/logo.png" alt="cashu" width="154" height="50" class="img-responsive" />
					</span>
					<span>Pay via Cash U</span>
					<span class="clearfix"></span>
				</a>
			</p>
		</div>
	</div>
	<form action="{$form_action}" method="post" id="cashu">
		<input type="hidden" name="merchant_id" value="{$merchant_id}">
		<input type="hidden" name="amount" value="{$amount}">
		<input type="hidden" name="currency" value="{$currency}">
		<input type="hidden" name="language" value="{$lang}">
		<input type="hidden" name="display_text" value="{$display_text}">	
		<input type="hidden" name="txt1" value="{$text1}">
		<input type="hidden" name="token" value="{$token}">
		<input type="hidden" name="service_name" value="{$service_name}">
		{*OPTIONAL PARAMETERS*}
		<input type="hidden" name="session_id" value="{$cart_id}">
		<input type="hidden" name="test_mode" value="{$test_mode}">
	</form>
</li>