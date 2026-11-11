@props([
    'id' => 'file-modal',
    'title' => 'Upload File',
    'message' => 'Select a file to upload.',
    'formAction' => '#',
    'formMethod' => 'POST',
    'confirmText' => 'Upload',
    'cancelText' => 'Cancel',
	 'instructionImg' => '',
])

<!-- Modal -->
<div id="{{ $id }}" tabindex="-1" aria-hidden="true"
     class="hidden fixed top-0 left-0 right-0 z-50 w-full h-full justify-center items-center bg-[#00000086]">

	<div class="bg-white dark:bg-gray-700 rounded-lg shadow-lg w-full max-w-160 p-6 relative">

		<button type="button" data-modal-hide="{{ $id }}"
			class="absolute top-3 right-3 text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-600 
			p-2 rounded-full">
			✕
		</button>
		
		<form action="{{ $formAction }}" method="{{ $formMethod }}" enctype="multipart/form-data" id="{{ $id }}-form">
			@csrf
			<h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ $title }}</h2>

			<p class="text-gray-600 dark:text-gray-300 text-sm mb-4">{{ $message }}</p>

			<a href="{{ asset($instructionImg) }}" target="_blank">
				<img src="{{ asset($instructionImg) }}" alt="">
			</a>

			<!-- FILE INPUT -->
			<label class="text-sm font-medium text-gray-700 dark:text-gray-300">Choose file</label>
			<input type="file" name="file" required
				class="mt-1 block w-full p-2 border rounded bg-white dark:bg-gray-800 dark:text-gray-200">

			<div class="flex justify-end gap-3 mt-6">
					<button type="button" data-modal-hide="{{ $id }}"
							class="px-4 py-2 rounded bg-gray-200 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
						{{ $cancelText }}
					</button>

					<button type="submit"
							class="saveButton">
						{{ $confirmText }}
					</button>
			</div>
		</form>

	</div>
</div>

<!-- SCRIPT -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    let lastActionUrl = null;

    // Inject clicked button's URL into the modal form
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-modal-target="{{ $id }}"]');
        if (btn) {
            lastActionUrl = btn.getAttribute('data-action-url');
            if (lastActionUrl) {
                document.querySelector('#{{ $id }}-form').action = lastActionUrl;
            }
        }
    });
});
</script>
