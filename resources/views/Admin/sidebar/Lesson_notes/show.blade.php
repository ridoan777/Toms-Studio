<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Lesson List', 'url' => 'backend_lesson_index', 'icon' => 'music'],
			['label' => 'Grouped Lesson List', 'url' => 'backend_group_lesson_index', 'icon' => 'music'],
			['label' => 'Note List', 'url' => 'backend_lesson_note_index', 'icon' => 'documentLined'],
			['label' => 'Modify Note'],
		];

		$galleries = $lessonNote->noteRelationWith_Gallery ?? collect();

		$videoGalleries = $galleries->filter(function ($file) {
			$mime = strtolower($file->metadata['mime'] ?? $file->metadata['mime_type'] ?? '');
			$ext = strtolower(pathinfo($file->filename ?? $file->filepath ?? '', PATHINFO_EXTENSION));

			return str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'mkv', 'qt']);
		})->values();

		$audioGalleries = $galleries->filter(function ($file) {
			$mime = strtolower($file->metadata['mime'] ?? $file->metadata['mime_type'] ?? '');
			$ext = strtolower(pathinfo($file->filename ?? $file->filepath ?? '', PATHINFO_EXTENSION));

			return str_starts_with($mime, 'audio/') || in_array($ext, ['mp3', 'wav', 'aac', 'm4a', 'ogg']);
		})->values();

		$docGalleries = $galleries->filter(function ($file) {
			$mime = strtolower($file->metadata['mime'] ?? $file->metadata['mime_type'] ?? '');
			$ext = strtolower(pathinfo($file->filename ?? $file->filepath ?? '', PATHINFO_EXTENSION));

			return !str_starts_with($mime, 'video/') && !str_starts_with($mime, 'audio/') && in_array($ext, ['pdf', 'doc', 'docx', 'txt', 'xls', 'xlsx']);
		})->values();
	@endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Modify Lesson Note" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	<!-- ---------------- APP AREA Starts ---------------- -->
	<section class="mb-12 p-6">
		<div class="flex justify-end my-2">
			<button type="button" data-modal-target="hs-delete-modal" data-modal-toggle="hs-delete-modal"
				data-delete-url="{{ route('backend_lesson_note_delete', $lessonNote?->id) }}" class="deleteButton">
				{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-3 h-3 text-gray-100']) !!}
			</button>
		</div>

		<x-ui_items.modals.delete-modal id="hs-delete-modal" title="Confirm Deletion" confirmText="Yes, delete it"
			cancelText="Cancel" message="Are you sure you want to delete this note? This action cannot be undone" />

		<form method="POST" action="{{ route('backend_lesson_note_store') }}" enctype="multipart/form-data"
			class="rounded-lg p-6 bg-gray-200 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
			@csrf

			<input type="hidden" name="id" value="{{ $lessonNote->id }}">

			<div>

				<div class="mb-12 p-4 grid grid-cols-12 gap-6 border border-gray-400 rounded-lg">

					<div class="col-span-6 my-2 flex_between_center">
						<label class="inputLabelMedium min-w-24 m-0">DB ID:</label>
						<input type="text" class="normalInput" value="{{ $lessonNote->id }}" readonly disabled>
					</div>

					<div class="col-span-6 my-2 flex_between_center">
						<label class="inputLabelMedium min-w-28 m-0">Note UID:</label>
						<input type="text" class="normalInput w-full" value="{{ $lessonNote->note_uid }}" readonly disabled>
					</div>

					<div class="col-span-5 grid grid-cols-4 my-2">
						@php
							$creator = $lessonNote?->noteRelatingBackTo_Creator;
						@endphp
						<label class="col-span-4 inputLabelMedium min-w-24 m-0">Creator:</label>
						<a href="{{ route('backend_single_user', $creator?->id) }}" class="col-span-1">
							<img
								src="{{ $creator?->avatar ? Storage::url($creator->avatar) : asset('site_assets/dummies/dummy_man.webp') }}"
								alt="creator_pic" class="w-24 max-h-40 rounded-lg hover:scale-110">
						</a>
						<div class="col-span-3">
							<input class="smallInput w-full my-1 border"
								value="Name: {{ $creator ? $creator?->name . " " . " | DB:[" . $creator?->id . "]" : 'N/A' }}"
								readonly disabled>
							<input class="smallInput w-full my-1"
								value="Email: {{ $creator?->user_role ? ucfirst($creator->user_role) : 'N/A' }}" readonly
								disabled>

							<input class="smallInput w-full my-1" value="Email: {{ $creator?->email ?? 'N/A' }}" readonly
								disabled>

							<input class="smallInput w-full my-1" value="Created at: {{ $lessonNote->created_at }}" readonly
								disabled>
						</div>
					</div>

					<div class="col-span-2"></div>

					<div class="col-span-5 grid grid-cols-4 my-2">
						@php
							$lastModifier = $lessonNote?->noteRelatingBackTo_LastModifier;
						@endphp
						<label class="col-span-4 inputLabelMedium min-w-24 m-0">Last Modifier:</label>
						<a href="{{ $lastModifier ? route('backend_single_user', $lastModifier->id) : "" }}"
							class="col-span-1">
							<img
								src="{{ $lastModifier?->avatar ? Storage::url($lastModifier->avatar) : asset('site_assets/dummies/dummy_man.webp') }}"
								alt="creator_pic" class="w-24 max-h-40 rounded-lg hover:scale-110">
						</a>
						<div class="col-span-3">
							<input class="smallInput w-full my-1 border"
								value="Name: {{ $lastModifier ? $lastModifier?->name . " " . " | DB:[" . $lastModifier?->id . "]" : 'N/A' }}"
								readonly disabled>
							<input class="smallInput w-full my-1"
								value="Email: {{ $lastModifier?->user_role ? ucfirst($lastModifier->user_role) : 'N/A' }}"
								readonly disabled>

							<input class="smallInput w-full my-1" value="Email: {{ $lastModifier?->email ?? 'N/A' }}" readonly
								disabled>

							<input class="smallInput w-full my-1" value="Created at: {{ $lessonNote->updated_at }}" readonly
								disabled>
						</div>
					</div>

				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Lesson</label>
						<select name="lesson_id" id="lesson_id" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute">
							<option value="">--N/A--</option>
							@foreach ($lessons as $lesson)
								<option value="{{ $lesson->id }}" {{ $lessonNote->lesson_id == $lesson->id ? 'selected' : '' }}>
									[{{ $lesson->id }}] {{ $lesson->title }} |
									{{ $lesson?->lessonRelatingBackTo_Intrument?->name ?? "No Instrument" }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Group Lesson</label>
						<select name="group_id" id="group_id" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute">
							<option value="">--N/A--</option>
							@foreach ($lessonGroups as $group)
								<option value="{{ $group->id }}" {{ $lessonNote->group_id == $group->id ? 'selected' : '' }}>
									[{{ $group->id }}] {{ $group->title }} |
									{{ $group?->groupRelatingBackTo_Intrument?->name ?? "No Instrument" }}
								</option>
							@endforeach
						</select>
					</div>

				</div>

				<div class="grid grid-cols-2 gap-4">

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Student <span class="redLabel">*</span></label>
						<select name="student_id[]" class="singleSelectWithSearch dropdownSelectInputs"
							data-dropdown-position="absolute">
							<option value="">--N/A--</option>
							@foreach ($students as $student)
								<option value="{{ $student->id }}" {{ $lessonNote->student_id == $student->id ? 'selected' : '' }}>
									[{{ $student->id }}] | {{ $student->name }} | {{ $student?->address }}
								</option>
							@endforeach
						</select>
					</div>

					<div class="mb-5 mt-2">
						<label class="inputLabelMedium">Status</label>
						<select name="status" class="dropdownSelectInputs">
							<option value="1" {{ $lessonNote->status == 1 ? 'selected' : '' }}>Active</option>
							<option value="0" {{ $lessonNote->status == 0 ? 'selected' : '' }}>Inactive</option>
						</select>
					</div>

				</div>

				<div class="mb-5 mt-2">
					<label class="inputLabelMedium">Title <span class="redLabel">*</span></label>
					<input type="text" name="title" class="normalInput" placeholder="Note title"
						value="{{ $lessonNote->title }}">
				</div>

				<div class="mb-5 mt-2">
					<label for="note" class="inputLabelMedium">Note
						<span class="text-sm text-gray-400 font-light"> (OPTIONAL! Max 2000 characters.)</span>
					</label>
					<textarea class="summernote" name="note" id="note">{!! $lessonNote->note !!}</textarea>
				</div>

				<div class="mt-12 p-4 border border-gray-400 rounded-lg">
					<h2 class="mb-4">Video Gallery</h2>

					<div id="videoBlocks" class="mb-5 mt-2">
						@foreach($videoGalleries as $index => $file)
							<div
								class="gallery-block existing-file grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
								<input type="hidden" class="gallery-id" name="video[{{ $index }}][id]" value="{{ $file->id }}">
								<input type="hidden" class="gallery-remove" name="video[{{ $index }}][remove_file]" value="0">

								<div class="col-span-11">
									<label class="inputLabelMedium">Video File</label>
									<div class="flex items-center gap-4">
										<a href="{{ $file->filepath_url }}" target="_blank"
											class="text-violet-600 text-sm underline">{{ $file->filename ?? 'View file' }}</a>
										<input type="file" class="normalInput gallery-file" name="video[{{ $index }}][file]"
											accept=".mp4,.mov,.mkv,.ogg,.qt">
									</div>
								</div>

								<div class="col-span-1 flex items-end">
									<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
										{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
									</button>
								</div>
							</div>
						@endforeach
					</div>

					<template id="videoTemplate">
						<div class="gallery-block grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							<input type="hidden" class="gallery-id" value="">
							<input type="hidden" class="gallery-remove" value="0">

							<div class="col-span-11">
								<label class="inputLabelMedium">Video File</label>
								<input type="file" class="normalInput gallery-file" accept=".mp4,.mov,.mkv,.ogg,.qt">
							</div>

							<div class="col-span-1 flex items-end">
								<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>
					</template>

					<div class="flex justify-end">
						<button type="button" id="addVideoBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+
							Add Video</button>
					</div>
				</div>

				<div class="mt-12 p-4 border border-gray-400 rounded-lg">
					<h2 class="mb-4">Audio Gallery</h2>

					<div id="audioBlocks" class="mb-5 mt-2">
						@foreach($audioGalleries as $index => $file)
							<div
								class="gallery-block existing-file grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
								<input type="hidden" class="gallery-id" name="audio[{{ $index }}][id]" value="{{ $file->id }}">
								<input type="hidden" class="gallery-remove" name="audio[{{ $index }}][remove_file]" value="0">

								<div class="col-span-11">
									<label class="inputLabelMedium">Audio File</label>
									<div class="flex items-center gap-4">
										<a href="{{ $file->filepath_url }}" target="_blank"
											class="text-violet-600 text-sm underline">{{ $file->filename ?? 'View file' }}</a>
										<input type="file" class="normalInput gallery-file" name="audio[{{ $index }}][file]"
											accept=".mp3,.wav,.aac,.m4a,.ogg">
									</div>
								</div>

								<div class="col-span-1 flex items-end">
									<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
										{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
									</button>
								</div>
							</div>
						@endforeach
					</div>

					<template id="audioTemplate">
						<div class="gallery-block grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							<input type="hidden" class="gallery-id" value="">
							<input type="hidden" class="gallery-remove" value="0">

							<div class="col-span-11">
								<label class="inputLabelMedium">Audio File</label>
								<input type="file" class="normalInput gallery-file" accept=".mp3,.wav,.aac,.m4a,.ogg">
							</div>

							<div class="col-span-1 flex items-end">
								<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>
					</template>

					<div class="flex justify-end">
						<button type="button" id="addAudioBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+
							Add Audio</button>
					</div>
				</div>

				<div class="mt-12 p-4 border border-gray-400 rounded-lg">
					<h2 class="mb-4">Document Gallery</h2>

					<div id="docBlocks" class="mb-5 mt-2">
						@foreach($docGalleries as $index => $file)
							<div
								class="gallery-block existing-file grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
								<input type="hidden" class="gallery-id" name="doc[{{ $index }}][id]" value="{{ $file->id }}">
								<input type="hidden" class="gallery-remove" name="doc[{{ $index }}][remove_file]" value="0">

								<div class="col-span-11">
									<label class="inputLabelMedium">Document File</label>
									<div class="flex items-center gap-4">
										<a href="{{ $file->filepath_url }}" target="_blank"
											class="text-violet-600 text-sm underline">{{ $file->filename ?? 'View file' }}</a>
										<input type="file" class="normalInput gallery-file" name="doc[{{ $index }}][file]"
											accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
									</div>
								</div>

								<div class="col-span-1 flex items-end">
									<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
										{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
									</button>
								</div>
							</div>
						@endforeach
					</div>

					<template id="docTemplate">
						<div class="gallery-block grid grid-cols-12 gap-4 p-4 border border-gray-400 rounded-lg mb-4">
							<input type="hidden" class="gallery-id" value="">
							<input type="hidden" class="gallery-remove" value="0">

							<div class="col-span-11">
								<label class="inputLabelMedium">Document File</label>
								<input type="file" class="normalInput gallery-file" accept=".pdf,.doc,.docx,.txt,.xls,.xlsx">
							</div>

							<div class="col-span-1 flex items-end">
								<button type="button" class="removeGalleryBtn p-3 hover:bg-red-200 rounded-lg">
									{!! \App\Helpers\IconPack::trash(['class' => 'iconPackItem w-5 h-5 text-red-500']) !!}
								</button>
							</div>
						</div>
					</template>

					<div class="flex justify-end">
						<button type="button" id="addDocBtn" class="px-4 py-2 text-xs bg-violet-600 text-white rounded">+ Add
							Document</button>
					</div>
				</div>

			</div>

			<button type="submit" class="my-12 showButton">Update Note</button>
		</form>
	</section>
	<!-- ---------------- APP AREA Ends ---------------- -->

</x-app-layout>

<!---------- PREVENT LESSON + GROUP BOTH SELECTED ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		const lessonInput = document.getElementById('lesson_id');
		const groupInput = document.getElementById('group_id');

		lessonInput.addEventListener('change', function () {
			if (lessonInput.value) {
				groupInput.value = '';
				groupInput.dispatchEvent(new Event('change'));
			}
		});

		groupInput.addEventListener('change', function () {
			if (groupInput.value) {
				lessonInput.value = '';
				lessonInput.dispatchEvent(new Event('change'));
			}
		});
	});
</script>
<!---------- PREVENT LESSON + GROUP BOTH SELECTED ------------>

<!---------- GALLERY BLOCKS ------------>
<script>
	document.addEventListener('DOMContentLoaded', function () {
		function galleryHandler(type, containerId, buttonId, templateId) {
			const container = document.getElementById(containerId);
			const button = document.getElementById(buttonId);
			const template = document.getElementById(templateId);

			function reindex() {
				const blocks = container.querySelectorAll('.gallery-block');

				blocks.forEach((block, index) => {
					const idInput = block.querySelector('.gallery-id');
					const fileInput = block.querySelector('.gallery-file');
					const removeInput = block.querySelector('.gallery-remove');

					if (idInput) idInput.name = `${type}[${index}][id]`;
					if (fileInput) fileInput.name = `${type}[${index}][file]`;
					if (removeInput) removeInput.name = `${type}[${index}][remove_file]`;
				});
			}

			function bindRemove(button) {
				button.addEventListener('click', function () {
					const block = button.closest('.gallery-block');

					if (block.classList.contains('existing-file')) {
						block.style.display = 'none';
						block.querySelector('.gallery-remove').value = '1';
					} else {
						block.remove();
					}

					reindex();
				});
			}

			container.querySelectorAll('.removeGalleryBtn').forEach(bindRemove);

			button.addEventListener('click', function () {
				const clone = template.content.cloneNode(true);
				const block = clone.querySelector('.gallery-block');

				bindRemove(block.querySelector('.removeGalleryBtn'));

				container.appendChild(block);

				reindex();
			});

			reindex();
		}

		galleryHandler('video', 'videoBlocks', 'addVideoBtn', 'videoTemplate');
		galleryHandler('audio', 'audioBlocks', 'addAudioBtn', 'audioTemplate');
		galleryHandler('doc', 'docBlocks', 'addDocBtn', 'docTemplate');
	});
</script>
<!---------- GALLERY BLOCKS ------------>