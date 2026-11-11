<?php

namespace Database\Seeders\System;

use App\Models\System\Settings\OptionStaticPage;
use Illuminate\Database\Seeder;

class SaticSiteSeeder extends Seeder
{
	public function run(): void
	{
		$statics = [
			[
				'type' => 'about_us',
				'title' => 'About Us',
				'body' => '
					<p>Master Admin is a centralized administrative platform designed to manage internal configuration, operational settings, and system-level preferences in a structured and controlled manner ✔️.</p>

					<p>The platform focuses on clarity, consistency, and long-term maintainability. Administrative tools and configuration options are organized to reduce complexity and improve visibility across internal operations ℹ️.</p>

					<p>Master Admin is not intended for public or commercial use. It functions exclusively as an internal control interface, ensuring a clear separation between administrative responsibilities and application usage ❌.</p>

					<p>Consistency in layout, labeling, and interaction patterns helps support efficient administration and predictable system behavior ⚙️.</p>

					<p>Security and stability are core considerations. Access is restricted to authorized roles, and safeguards are applied to reduce accidental or unauthorized changes 🔒.</p>
				',
				'feature_image' => null,
				'status' => 1,
			],

			[
				'type' => 'term_n_conditions',
				'title' => 'Terms & Conditions',
				'body' => '
					<p>These Terms &amp; Conditions govern the use of the Master Admin platform. Accessing or using the system indicates acceptance of these terms and applicable internal policies ✔️.</p>

					<p>Master Admin is intended solely for authorized administrative use. Unauthorized access, misuse, or attempts to bypass system controls are strictly prohibited ❌.</p>

					<p>All configurations, uploads, and settings managed through the platform are considered internal data. Users are responsible for ensuring compliance with organizational standards, including file size limits and supported formats ℹ️.</p>

					<p>The platform is provided on an “as-is” basis for internal operations. Temporary unavailability may occur due to maintenance or system updates ⚙️.</p>

					<p>Administrative actions may be logged for auditing and troubleshooting purposes to ensure transparency, accountability, and system integrity 🔒.</p>
				',
				'feature_image' => null,
				'status' => 1,
			],

			[
				'type' => 'privacy_policy',
				'title' => 'Privacy Policy',
				'body' => '
					<p>This Privacy Policy describes how information is handled within the Master Admin platform. The system is designed for internal administrative use and does not operate as a commercial data service ✔️.</p>

					<p>Data processed within the platform typically includes configuration values, uploaded assets, and system metadata required for administrative functionality ℹ️.</p>

					<p>Access to sensitive information is limited through role-based controls. Only authorized users may view or modify protected data 🔒.</p>

					<p>The platform does not intentionally collect personal information beyond what is required for authentication and authorization purposes ❌.</p>

					<p>Audit logs may record administrative actions to support operational stability, compliance, and security monitoring ⚙️.</p>
				',
				'feature_image' => null,
				'status' => 1,
			],

			[
				'type' => 'help_n_support',
				'title' => 'Help & Support',
				'body' => '
					<p>The Help &amp; Support section provides guidance for effective and responsible use of the Master Admin platform ✔️.</p>

					<p>Users are encouraged to review labels, descriptions, and contextual information provided throughout the interface to understand configuration options ℹ️.</p>

					<p>Common issues often relate to permission restrictions, unsupported file formats, or validation rules. Reviewing system messages can help resolve these quickly ❌.</p>

					<p>For unresolved issues, internal support channels should be used in accordance with established procedures ⚙️.</p>

					<p>Security-related concerns should be escalated promptly to ensure system stability and data protection 🔒.</p>
				',
				'feature_image' => null,
				'status' => 1,
			],
		];

		foreach ($statics as $page) {
			OptionStaticPage::updateOrCreate(
				['type' => $page['type']],
				$page
			);
		}
	}
}
