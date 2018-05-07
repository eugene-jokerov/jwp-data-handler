jQuery(function($){
	
	$('.jwp-dh-start').on('click', function(){
		$(this).hide();
		$('.jwp-dh-process').show();

		var ai_data = {
			'action': 'jwp_dh_callback',
			'offset': $('.jwp-dh-offset').text(),
			'total': $('.jwp-dh-total').text(),
			'slug': $('#jwp-dh-handler-slug').val(),
			'_wpnonce': $('#jwp-dh-nonce').val(),
		};
		send_post_data(ai_data);
		
	});
	
	function send_post_data(data){
		$.post('/wp-admin/admin-ajax.php', data, function(response) {
			console.log(response);
			if ( response.output instanceof Array ) {
				$.each( response.output, function( index, value ) {
					$('.jwp-dh-output').prepend( '<p>' + value + '</p>' );
				}); 
			} else {
				$('.jwp-dh-output').prepend( '<p>' + response.output +'</p>' );
			}
			$('.jwp-dh-total').text(response.total);
			$('.jwp-dh-offset').text(response.offset);
			data.total = response.total;
			if(response.offset < response.total){
				data.offset = response.offset;
				send_post_data(data);
			} else {
				alert("Обработка завершена");
			}
		});
	}
	
	
});
