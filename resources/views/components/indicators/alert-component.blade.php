@if (session('success') || session('error') || $errors->any() || session('info') || session('dpanel'))
	<div class="mb-4 mx-auto w-3/4" id="messageArea">
		@if(session('dpanel'))
			<div class="successAlertBlock" role="alert">
				<span class="block text-left whitespace-pre-line">{{ session('dpanel') }}</span>
			</div>
		@endif
		<!------------------------------>
		@if(session('success'))
			<div class="successAlertBlock" role="alert">
				<strong class="font-bold text-center">Success!</strong>
				<span class="block sm:inline text-right">{{ session('success') }}</span>
			</div>
		@endif
		<!------------------------------>
		@if(session('error'))
			<div class="errorAlertBlock" role="alert">
				<strong class="font-bold text-center">Error!</strong>
				<span class="block sm:inline text-right">{{ session('error') }}</span>
			</div>
		@endif
		<!------------------------------>
		@if($errors->any())
			<ul class="alertBlock ">
				<li class="text-blue-500"><b>{{ $message }} Error/s:</b></li>
				@foreach ($errors->all() as $error)
					<li class="text-red-500">{{ $error }}</li>
				@endforeach
			</ul>
		@endif
		<!------------------------------>
		@if(session('info'))
			<div class="infoAlertBlock" role="info">
				<strong class="font-bold text-center">Note: </strong>
				<span class="block sm:inline text-right">{{ session('info') }}‼️</span>
			</div>
		@endif
		<!------------------------------>
	</div>
@endif

<script>
	setTimeout(function () {
		const msg = document.getElementById('messageArea');
		if (msg) {
			msg.classList.add('transition-opacity', 'duration-1000', 'opacity-0');
			setTimeout(() => {
				msg.style.display = 'none';
			}, 1000); // Wait for the fade-out to finish
		}
	}, 10000);
</script>