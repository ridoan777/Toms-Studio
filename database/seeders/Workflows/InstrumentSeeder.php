<?php

namespace Database\Seeders\Workflows;

use Illuminate\Database\Seeder;
use App\Models\Workflows\Instruments\Instrument;

class InstrumentSeeder extends Seeder
{
	public function run(): void
	{
		$names = ['Piano','Guitar','Drums','Violin','Vocals','Keyboard','Flute','Saxophone','Ukulele','Bass Guitar','Cello','Trumpet','Clarinet','Harmonica','DJ'];

		$notes = ['Beginner friendly','Intermediate to advanced','Performance focused','Classical & modern techniques','Stage preparation training','Creative expression encouraged','Hands-on practical sessions','Theory + practical balance','Exam preparation available','Flexible learning pace','Group & solo sessions','Professional training','Kids & adults welcome','Studio experience included','Skill-based progression'];

		foreach ($names as $name) {
			Instrument::create([
				'name' => $name,
				'note' => $notes[array_rand($notes)],
				'status' => 1,
			]);
		}
	}
}