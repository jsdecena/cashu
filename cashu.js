window.cashu = {

	bindEvent : function() {
		$('.cashu a').on('click', function(){    

			$('#cashu').submit();

			return false;
		});
	}
}

window.cashu.bindEvent();

