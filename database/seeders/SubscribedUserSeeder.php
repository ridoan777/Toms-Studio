<?php

namespace Database\Seeders;

use App\Helpers\UidGenerator;
use Illuminate\Database\Seeder;
use App\Models\Billings\Subscriptions\SubscribedUserApp;

class SubscribedUserSeeder extends Seeder
{
	public function run(): void
	{
		$data = [

			// ----------------- USER 5 -----------------
			['subscription_uid' => UidGenerator::uniqueULID("APP-SUB-CHARLIE", 15, 26, 8),'user_id'=>5,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'APP_STORE','payment_status'=>'paid','sub_status'=>'running','country'=>'US','currency'=>'USD','amount'=>100,'duration'=>'365','note'=>'Subscription RENEWED','status'=>1,'rc_product_id'=>'com.subscription.yearly','rc_subscription_id'=>'SUB_50001','rc_purchase_token'=>'TXN_50001','rc_event_id'=>'EVT_50001','purchased_at'=>now()->subYear(),'renewal_at'=>now()->addYear()],
			
			['subscription_uid' => UidGenerator::uniqueULID("APP-SUB-CHARLIE", 15, 26, 8), 'user_id'=>5, 'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'PLAY_STORE','payment_status'=>'paid','sub_status'=>'cancelled','country'=>'US','currency'=>'USD','amount'=>10,'duration'=>'30','note'=>'Subscription CANCELLED','status'=>0,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_50002','rc_purchase_token'=>'TXN_50002','rc_event_id'=>'EVT_50002','purchased_at'=>now()->subMonths(2),'renewal_at'=>now()->subMonth(),'cancelled_at'=>now()->subMonth()],

			// ----------------- USER 6 -----------------
			['subscription_uid' => UidGenerator::uniqueULID("APP-SUB-DELTA", 15, 26, 8), 'user_id'=>6,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'PLAY_STORE','payment_status'=>'paid','sub_status'=>'running','country'=>'BD','currency'=>'BDT','amount'=>1400,'duration'=>'30','note'=>'Subscription ACTIVE','status'=>1,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_60001','rc_purchase_token'=>'TXN_60001','rc_event_id'=>'EVT_60001','purchased_at'=>now()->subDays(10),'renewal_at'=>now()->addDays(20)],

			['subscription_uid' => UidGenerator::uniqueULID("APP-SUB-DELTA", 15, 26, 8), 'user_id'=>6,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'PLAY_STORE','payment_status'=>'pending','sub_status'=>'expired','country'=>'US','currency'=>'USD','amount'=>0,'duration'=>'0','note'=>'Subscription EXPIRED','status'=>0,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_60002','rc_purchase_token'=>'TXN_60002','rc_event_id'=>'EVT_60002','purchased_at'=>now()->subMonths(2),'renewal_at'=>now()->subMonth()],

			// // ----------------- USER 7 -----------------
			// ['user_id'=>7,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'APP_STORE','payment_status'=>'paid','sub_status'=>'running','country'=>'US','currency'=>'USD','amount'=>15,'duration'=>'30','note'=>'Subscription RENEWED','status'=>1,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_70001','rc_purchase_token'=>'TXN_70001','rc_event_id'=>'EVT_70001','purchased_at'=>now()->subDays(5),'renewal_at'=>now()->addDays(25)],

			// // repeat history for same user
			// ['user_id'=>7,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'APP_STORE','payment_status'=>'paid','sub_status'=>'cancelled','country'=>'US','currency'=>'USD','amount'=>15,'duration'=>'30','note'=>'Subscription CANCELLED','status'=>0,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_70002','rc_purchase_token'=>'TXN_70002','rc_event_id'=>'EVT_70002','purchased_at'=>now()->subMonths(1),'renewal_at'=>now()->subDays(5),'cancelled_at'=>now()->subDays(5)],

			// // ----------------- USER 11 -----------------
			// ['user_id'=>8,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'PLAY_STORE','payment_status'=>'paid','sub_status'=>'running','country'=>'BD','currency'=>'BDT','amount'=>1400,'duration'=>'30','note'=>'Subscription ACTIVE','status'=>1,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_110001','rc_purchase_token'=>'TXN_110001','rc_event_id'=>'EVT_110001','purchased_at'=>now()->subDays(2),'renewal_at'=>now()->addDays(28)],

			// ['user_id'=>8,'tier_id'=>null,'entitlement_id'=>'premium','provider'=>'revenuecat','store'=>'PLAY_STORE','payment_status'=>'paid','sub_status'=>'expired','country'=>'BD','currency'=>'BDT','amount'=>1400,'duration'=>'30','note'=>'Subscription EXPIRED','status'=>0,'rc_product_id'=>'bf_premium_month','rc_subscription_id'=>'SUB_110002','rc_purchase_token'=>'TXN_110002','rc_event_id'=>'EVT_110002','purchased_at'=>now()->subMonths(2),'renewal_at'=>now()->subMonth()],
		];

		foreach ($data as $row) {
			SubscribedUserApp::create($row);
		}
	}
}