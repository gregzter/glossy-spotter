<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        $france = Country::where('code', 'FR')->first();
        $usa = Country::where('code', 'US')->first();
        $uk = Country::where('code', 'GB')->first();

        $people = [
            [
                'firstname' => 'Jane',
                'lastname' => 'Doe',
                'profession' => 'actress',
                'country_id' => $usa->id,
                'biography' => [
                    'en' => 'American actress known for her versatile roles',
                    'fr' => 'Actrice américaine connue pour ses rôles variés'
                ]
            ],
            [
                'firstname' => 'Marie',
                'lastname' => 'Dupont',
                'profession' => 'presenter',
                'country_id' => $france->id,
                'biography' => [
                    'en' => 'French TV presenter and journalist',
                    'fr' => 'Présentatrice et journaliste française'
                ]
            ],
            [
                'firstname' => 'Laura',
                'lastname' => 'Smith',
                'nickname' => 'Lola',
                'profession' => 'singer',
                'country_id' => $uk->id,
                'biography' => [
                    'en' => 'British pop singer and songwriter',
                    'fr' => 'Chanteuse et compositrice britannique'
                ]
            ],
        ];

        foreach ($people as $person) {
            $biography = $person['biography'];
            unset($person['biography']);

            $model = Person::create($person);

            $model->setTranslations('biography', $biography);
        }
    }
}
