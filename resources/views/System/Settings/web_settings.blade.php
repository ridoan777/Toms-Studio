<x-app-layout>

	@php
		$breadCrumbs = [
			['label' => 'Settings', 'url' => 'backend_settings_index', 'icon' => 'gearLine'],
			['label' => 'Index'],
		];
	 @endphp

	<x-indicators.breadcrumb :crumbs="$breadCrumbs" />
	<!-- ---------------- APP AREA Starts ---------------- -->

	<!-- Start Main Widgets -->
	<x-indicators.page_header_widget pageName="Settings: Index" />
	<!-- End Main Widgets -->


	<!-- Alert Component -->
	<x-indicators.alert-component message="Form Submission" />
	<!-- Alert Component -->

	@php
		$CARDS = [
			'purging' => [
				'title' => "Purging",
				'subtitle' => "Purge stale or unused/unverified database records.",
				'icon' => 'trash',
				'link' => 'backend_settings_purging_index',
			],
			'static' => [
				'title' => "Static",
				'subtitle' => "Modify static pages, e.g., about us, privacy policy, etc.",
				'icon' => 'documentLined',
				'link' => 'backend_static_pages_index',
			],
			'seeder' => [
				'title' => "Database Seeder",
				'subtitle' => "Populate database tables instead of manually creating, e.g., vehicle metas, service categories, etc.",
				'icon' => 'uploadCloud',
				'link' => 'backend_settings_seeding',
			],
			'email' => [
				'title' => "Email Management",
				'subtitle' => "Manage your email notification templates.",
				'icon' => 'envelope',
				'link' => 'backend_settings_seeding',
				// 'link' => 'backend_settings_mail_index',
			],
			'site' => [
				'title' => "Site Management",
				'subtitle' => "Manage your site's static values like title, logo, etc.",
				'icon' => 'globe',
				'link' => 'backend_settings_site_setup_index',
			],
			'notification' => [
				'title' => "Notifications & Logs",
				'subtitle' => "Manage notification for end users.",
				'icon' => 'bell',
				'link' => 'backend_settings_notification_index',
			],
			'stripeKeys' => [
				'title' => "Payment Keys",
				'subtitle' => "Manage your payment API keys.",
				'icon' => 'key',
				'link' => 'backend_settings_pricing_key_index',
			],
			'AI Manager' => [
				'title' => "AI Keys",
				'subtitle' => "Manage your AI APIs & keys.",
				'icon' => 'brain',
				'link' => 'backend_settings_ai_index',
			],
			'system' => [
				'title' => "System Management",
				'subtitle' => "Manage your site's system variables.",
				'icon' => 'handsHolding',
				'link' => 'backend_settings_system_index',
			],
			'panel' => [
				'title' => "Control Panel (S-Panel)",
				'subtitle' => "Manage your site's sensitive area with highest authority.",
				'icon' => 'whmcs',
				'link' => 'backend_s_panel_index',
			],
		]
	@endphp

	<!-- ---------------- APP AREA Starts ---------------- -->
	<div class="max-w-5xl px-4 py-10 sm:px-6 lg:px-8 lg:py-14 mx-auto">
		<!-- Grid -->
		<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-6">

			@foreach ($CARDS as $key => $card)
				<!-- Card -->
				<a class="group flex flex-col bg-white border border-gray-200 shadow-2xs rounded-xl hover:shadow-md focus:outline-hidden focus:shadow-md transition dark:bg-neutral-900 dark:border-neutral-800"
					href="{{ route($card['link']) }}">
					<div class="p-4 md:p-5">
						<div class="flex gap-x-5">
							{!! \App\Helpers\IconPack::{$card['icon']}(['class' => 'iconPackItem mt-1 shrink-0 w-5 h-5 text-gray-500 dark:text-neutral-200']) !!}

							<div class="grow">
								<h3
									class="group-hover:text-blue-600 font-semibold text-gray-800 dark:group-hover:text-neutral-400 dark:text-neutral-200">
									{{ $card['title'] }}
								</h3>
								<p class="text-sm text-gray-500 dark:text-neutral-500">
									{{ $card['subtitle'] }}
								</p>
							</div>
						</div>
					</div>
				</a>
				<!-- End Card -->
			@endforeach

		</div>
		<!-- End Grid -->
	</div>
	<!-- ---------------- APP AREA Starts ---------------- -->

</x-app-layout>