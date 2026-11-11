<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'AI Keys'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Index" />
	<!-- End Main Widgets -->

	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$ACCORDION_1_VISIBILITY = ''; // hidden or ''
	@endphp

	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
	<div class="hs-accordion-group">

		<x-ui_items.accordion.accordion-destroy />


		<div id="hs-destroy-heading-one"
			class="hs-accordion-to-destroy hs-accordion hs-accordion-active:border-gray-200 PrelineAccordionItemParent">

			<x-ui_items.accordion.accordion_toggle_button toggleLabel="OpenAI/ChatGPT" />

			<div id="hs-destroy-collapse-one"
				class="hs-accordion-content {{ $ACCORDION_1_VISIBILITY }} w-full overflow-hidden transition-[height] duration-300"
				role="region" aria-labelledby="hs-destroy-heading-one">
				<div class="p-3">
					<!-- --------------------------------- -->
					<section
						class="displayArea mx-auto my-5 rounded-lg border-none bg-white p-4 shadow-md shadow-gray-400 dark:bg-neutral-700 dark:shadow-none">
						<div class="my-5">
							<form method="POST" action="{{ route('backend_settings_ai_store') }}"
								class="rounded-lg p-2 dark:bg-neutral-700" enctype="multipart/form-data">
								@csrf

								<div>

									<div class="mb-5 mt-2">
										@php
											$ENV_CONTENT = [
												"label" => "API Key",
												"name" => "OPENAI_API_KEY",
												"note" => "Place your API key here as it is. No blank is accepted. ⚠️ For safety, Keys will be encrypted when you save. ",
												'old_value' => null
											];
											$CONTENTS = [
												[
													"label" => "Model",
													"name" => "ai_model",
													"note" => "Higher models are more efficient and charge more. Standard is gpt-4.1-mini. Model 5.x will produce result patterns completely different than model 4.x. ⚠️ OpenAI uses a trust + usage system. Pro models or 4.1x versions do not unlock just like that. You have to use mini/nano models → consistant usage → build spend → bill more → history updates → higher models unlock. It's auto & till this day cannot be altered!",
													"options" => [
														"gpt-5.2 (unlocks by Trust)" => 'gpt-5.2',
														"gpt-5.1 (unlocks by Trust)" => 'gpt-5.1',
														"gpt-5-mini (unlocks by Trust)" => 'gpt-5-mini',
														"gpt-5-nano (unlocks by Trust)" => 'gpt-5-nano',
														"gpt-4.1 (unlocks by Trust)" => 'gpt-4.1',
														"gpt-4.1-mini (standard)" => 'gpt-4.1-mini',
														"gpt-4.1-nano" => 'gpt-4.1-nano',
														"gpt-4o" => 'gpt-4o',
														"gpt-4o-mini" => 'gpt-4o-mini',
													],
												],
												[
													"label" => "Max Token Outputs",
													"name" => "max_output_tokens",
													"note" => "The model will not exceed this limit. A 3-day meal plan usually costs around 1500 tokens. Standard is ≈ 9999 for 30-day",
													"options" => [
														"100K (GPT 4.1-full & higher, stressed🍀)" => 100000,
														"63,999 (GPT 4.1-full, safe🍀)" => 63999,
														"49,000 (GPT 4.x-mini & higher)" => 49000,
														"30,000 (GPT 4.x-mini & higher)" => 30000,
														"14999" => 14999,
														"9999 (standard)" => 9999,
														"5000" => 5000,
														"3000" => 3000,
														"1500" => 1500,
													],
												],
												[
													"label" => "Timeout seconds",
													"name" => "timeout_seconds",
													"note" => "Time this server will wait to for the HTTP request to connect, send & receive responses from/to the OpenAI.",
													"options" => [
														"60" => 60,
														"120 (standard)" => 120,
														"180" => 180,
														"240 (safe for 30-days)" => 240,
														"300" => 300,
													],
												],
												[
													"label" => "OpenAI API endpoint",
													"name" => "api_endpoint",
													"note" => "This is the API endpoint which will communicate with the OpenAI servers.",
													'old_value' => "https://api.openai.com/v1/responses"
												],
												[
													"label" => "Role:System global behavior and authority",
													"name" => "role_system_content",
													"note" => "Defines persona, tone, boundaries & can override Role:User intent. Bad value will greatly affect meal generation process.",
													'old_value' => "You are a gym & dietry trainer, skilled in training in complex situations. You consult with clients on their exercises and diet plan to help them get fit."
												],
											];
										@endphp
										<!------>
										<label for="{{ $ENV_CONTENT['label'] }}" class="mt-5 inputLabelMedium">
											{{ $ENV_CONTENT['label'] }}
											<span class="text-sm text-gray-400">({{ $ENV_CONTENT['note'] }})</span>
										</label>
										<input type="text" name="{{ $ENV_CONTENT['name'] }}" class="normalInput"
											placeholder="{{ isset($aiKey['OPENAI_API_KEY']) ? 'sk-******************** (saved) | type the word `cancel` and save to clear the key' : 'Enter API key' }}">

										<!------>
										@foreach ($CONTENTS as $item)
											<label for="{{ $item['name'] }}" class="mt-5 inputLabelMedium">
												{{ $item['label'] }}
												<span class="text-sm text-gray-400">({{ $item['note'] }})</span>
											</label>

											@if(isset($item['options']))
												<select name="{{ $item['name'] }}" class="dropdownSelectInputs">
													@foreach ($item['options'] as $text => $val)
														<option value="{{ $val }}" {{ (old($item['name'], $aiSetup[$item['name']] ?? null) == $val) ? 'selected' : '' }}>
															{{ $text }}
														</option>
													@endforeach
												</select>
											@else
												<input type="text" name="{{ $item['name'] }}" class="normalInput"
													value="{{ $aiSetup[$item['name']] ?? $item['old_value'] }}">
											@endif

											<x-ui_items.forms.input-error class="mt-2" :messages="$errors->get($item['name'])" />
										@endforeach
									</div>

								</div>

								<button type="submit" class="saveButton my-4">Save</button>
							</form>
						</div>
					</section>
				</div>
			</div>
		</div>

	</div>
	<!-- -------------------------------- APP AREA Starts -------------------------------- -->
</x-app-layout>