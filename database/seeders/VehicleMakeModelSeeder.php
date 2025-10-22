<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleMakeModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Car Makes and Models - EXACTLY as per scope document
        $carMakes = [
            'Tata' => [
                'Tata Nano', 'Tata Indica', 'Tata Indigo', 'Tata Vista', 'Tata Zest', 'Tata Bolt',
                'Tata Hexa', 'Tata Nexon', 'Tata Harrier', 'Tata Safari', 'Tata Altroz', 'Tata Tiago',
                'Tata Tigor', 'Tata Punch', 'Tata EV models (Nexon EV, Tigor EV)', 'Tata Sumo',
                'Tata Safari (older generation)', 'Tata Aria', 'Tata Indigo eCS'
            ],
            'Mahindra' => [
                'Mahindra Thar', 'Mahindra Scorpio (Classic and N)', 'Mahindra XUV500', 'Mahindra XUV700',
                'Mahindra Bolero', 'Mahindra Marazzo', 'Mahindra TUV300', 'Mahindra KUV100', 'Mahindra eKUV100',
                'Mahindra Alturas G4', 'Mahindra Xylo', 'Mahindra Quanto', 'Mahindra Verito',
                'Mahindra TUV300 (old generation)', 'Mahindra Bolero (older models)'
            ],
            'Maruti Suzuki' => [
                'Maruti Alto (800, K10)', 'Maruti Swift (all generations)', 'Maruti Dzire (all generations)',
                'Maruti WagonR (all versions)', 'Maruti Baleno', 'Maruti Vitara Brezza', 'Maruti Ciaz',
                'Maruti Ertiga', 'Maruti S-Presso', 'Maruti Ignis', 'Maruti Celerio (new generation)',
                'Maruti XL6', 'Maruti 800', 'Maruti Zen', 'Maruti Estilo', 'Maruti Kizashi', 'Maruti SX4',
                'Maruti Gypsy', 'Maruti Omni'
            ],
            'Hyundai' => [
                'Hyundai Santro', 'Hyundai i10', 'Hyundai Grand i10', 'Hyundai i20 (all variants)',
                'Hyundai Verna', 'Hyundai Creta', 'Hyundai Venue', 'Hyundai Tucson', 'Hyundai Alcazar',
                'Hyundai Aura', 'Hyundai Elantra', 'Hyundai i20 (older generations)', 'Hyundai Accent',
                'Hyundai Elantra (older generations)', 'Hyundai Veloster'
            ],
            'Honda' => [
                'Honda City (4th and 5th Gen)', 'Honda Amaze', 'Honda WR-V', 'Honda Jazz', 'Honda CR-V',
                'Honda HR-V', 'Honda Civic', 'Honda Accord', 'Honda BR-V', 'Honda Mobilio'
            ],
            'Toyota' => [
                'Toyota Fortuner', 'Toyota Innova Crysta', 'Toyota Urban Cruiser', 'Toyota Glanza',
                'Toyota Yaris (discontinued)', 'Toyota Camry', 'Toyota Hilux', 'Toyota Corolla Altis',
                'Toyota Yaris', 'Toyota Etios', 'Toyota Innova (old version)'
            ],
            'Kia' => [
                'Kia Seltos', 'Kia Sonet', 'Kia Carnival', 'Kia Carens', 'Kia EV6'
            ],
            'MG Motors' => [
                'MG Hector', 'MG Hector Plus', 'MG ZS EV', 'MG Gloster', 'MG Astor'
            ],
            'Nissan' => [
                'Nissan Kicks', 'Nissan Magnite', 'Nissan Micra', 'Nissan Sunny', 'Nissan Terrano'
            ],
            'Renault' => [
                'Renault Kwid', 'Renault Triber', 'Renault Duster', 'Renault Kiger', 'Renault Pulse', 'Renault Lodgy'
            ],
            'Volkswagen' => [
                'Volkswagen T-Roc', 'Volkswagen Tiguan', 'Volkswagen Virtus', 'Volkswagen Polo',
                'Volkswagen Vento', 'Volkswagen Beetle'
            ],
            'Skoda' => [
                'Skoda Kushaq', 'Skoda Octavia', 'Skoda Superb', 'Skoda Rapid', 'Skoda Yeti', 'Skoda Fabia'
            ],
            'Ford' => [
                'Ford EcoSport', 'Ford Endeavour', 'Ford Freestyle', 'Ford Fiesta', 'Ford Figo (older versions)',
                'Ford Aspire', 'Ford Endeavour (older versions)'
            ],
            'Fiat' => [
                'None', 'Fiat Punto', 'Fiat Linea', 'Fiat Abarth Punto'
            ],
            'Jeep' => [
                'Jeep Compass', 'Jeep Meridian', 'Jeep Wrangler (older models)'
            ],
            'Datsun' => [
                'Datsun Go', 'Datsun Go+', 'Datsun Redi-Go'
            ],
            'BMW' => [
                'BMW 3 Series', 'BMW 5 Series', 'BMW 7 Series', 'BMW X1', 'BMW X3', 'BMW X5', 'BMW Z4',
                'BMW X6 (older generations)', 'BMW 1 Series (older versions)'
            ],
            'Mercedes-Benz' => [
                'Mercedes-Benz A-Class', 'Mercedes-Benz C-Class', 'Mercedes-Benz E-Class', 'Mercedes-Benz S-Class',
                'Mercedes-Benz GLA', 'Mercedes-Benz GLC', 'Mercedes-Benz GLE', 'Mercedes-Benz GLS',
                'Mercedes-Benz B-Class', 'Mercedes-Benz CLA (older versions)'
            ],
            'Audi' => [
                'Audi A3', 'Audi A4', 'Audi A6', 'Audi A8', 'Audi Q2', 'Audi Q3', 'Audi Q5', 'Audi Q7',
                'Audi Q8', 'Audi Q5', 'Audi A1'
            ],
            'Volvo' => [
                'Volvo XC40', 'Volvo XC60', 'Volvo XC90', 'Volvo S60 (older generations)'
            ],
            'Porsche' => [
                'Porsche Macan', 'Porsche Cayenne', 'Porsche 911', 'Porsche 918 Spyder'
            ]
        ];

        // Bike Makes and Models - EXACTLY as per scope document
        $bikeMakes = [
            'Hero MotoCorp' => [
                'Hero Splendor Plus', 'Hero HF Deluxe', 'Hero Passion Pro', 'Hero Glamour', 'Hero Xpulse 200',
                'Hero Xtreme 160R', 'Hero Super Splendor', 'Hero Maestro Edge (Scooter)', 'Hero Destini 125 (Scooter)',
                'Hero Pleasure+ (Scooter)', 'Hero Karizma ZMR', 'Hero Impulse', 'Hero Hunk', 'Hero Xtreme Sports',
                'Hero Splendor iSmart 110'
            ],
            'Bajaj Auto' => [
                'Bajaj Pulsar 125', 'Bajaj Pulsar NS160', 'Bajaj Pulsar RS200', 'Bajaj Pulsar 220F',
                'Bajaj Avenger Street 160', 'Bajaj Avenger Cruise 220', 'Bajaj Dominar 400', 'Bajaj Platina 100',
                'Bajaj CT 100', 'Bajaj Chetak (Electric Scooter)', 'Bajaj Pulsar 135LS', 'Bajaj Discover 150',
                'Bajaj V15', 'Bajaj Kristal (Scooter)'
            ],
            'TVS Motor Company' => [
                'TVS Apache RTR 160', 'TVS Apache RTR 180', 'TVS Apache RTR 200 4V', 'TVS Star City Plus',
                'TVS Sport', 'TVS Radeon', 'TVS XL 100 (Moped)', 'TVS Jupiter (Scooter)', 'TVS Ntorq 125 (Scooter)',
                'TVS Scooty Pep Plus (Scooter)', 'TVS Zest 110 (Scooter)', 'TVS Victor', 'TVS Wego (Scooter)',
                'TVS Phoenix', 'TVS Flame'
            ],
            'Royal Enfield' => [
                'Royal Enfield Classic 350', 'Royal Enfield Bullet 350', 'Royal Enfield Meteor 350',
                'Royal Enfield Himalayan', 'Royal Enfield Interceptor 650', 'Royal Enfield Continental GT 650',
                'Royal Enfield Thunderbird 350', 'Royal Enfield Thunderbird 500', 'Royal Enfield Bullet Electra'
            ],
            'Honda Motorcycles' => [
                'Honda Activa 6G (Scooter)', 'Honda Activa 125 (Scooter)', 'Honda Dio (Scooter)', 'Honda Grazia (Scooter)',
                'Honda Shine', 'Honda Unicorn', 'Honda Hornet 2.0', 'Honda XBlade', 'Honda CB350 H\'ness', 'Honda CBR650R',
                'Honda CB Trigger', 'Honda CBR150R', 'Honda CBR250R', 'Honda Navi (Scooter)', 'Honda Aviator (Scooter)'
            ],
            'Yamaha' => [
                'Yamaha FZ S FI', 'Yamaha FZS V3', 'Yamaha MT-15', 'Yamaha R15 V3', 'Yamaha Fascino 125 (Scooter)',
                'Yamaha Ray ZR 125 (Scooter)', 'Yamaha Fazer 25', 'Yamaha SZ-RR V2.0', 'Yamaha Saluto',
                'Yamaha Alpha (Scooter)', 'Yamaha Crux'
            ],
            'Suzuki' => [
                'Suzuki Access 125 (Scooter)', 'Suzuki Burgman Street (Scooter)', 'Suzuki Gixxer 150',
                'Suzuki Gixxer SF 250', 'Suzuki Intruder 150', 'Suzuki Hayate', 'Suzuki Slingshot Plus',
                'Suzuki Let\'s (Scooter)'
            ],
            'KTM' => [
                'KTM 125 Duke', 'KTM 200 Duke', 'KTM 250 Duke', 'KTM 390 Duke', 'KTM RC 125',
                'KTM RC 200', 'KTM RC 390', 'KTM 390 Adventure'
            ],
            'Ducati' => [
                'Ducati Monster', 'Ducati Panigale V2', 'Ducati Panigale V4', 'Ducati Scrambler Icon',
                'Ducati Multistrada 950 S', 'Ducati Diavel 1260', 'Ducati Hypermotard 950',
                'Ducati Scrambler 1100 (older versions)', 'Ducati SuperSport (older versions)'
            ],
            'BMW Motorrad' => [
                'BMW G 310 R', 'BMW G 310 GS', 'BMW S 1000 RR', 'BMW R 1250 GS', 'BMW F 900 R',
                'BMW F 900 XR', 'BMW K 1600 B', 'BMW S 1000 XR (older version)'
            ],
            'Harley-Davidson' => [
                'Harley-Davidson Iron 883', 'Harley-Davidson Forty-Eight', 'Harley-Davidson Street Bob',
                'Harley-Davidson Fat Boy', 'Harley-Davidson Road King', 'Harley-Davidson Street 750', 'Harley-Davidson Street Rod'
            ],
            'Benelli' => [
                'Benelli Imperiale 400', 'Benelli Leoncino 500', 'Benelli TRK 502', 'Benelli 302R',
                'Benelli TNT 600i', 'Benelli TNT 300', 'Benelli TNT 25'
            ],
            'Triumph' => [
                'Triumph Street Triple R', 'Triumph Bonneville T100', 'Triumph Bonneville T120',
                'Triumph Tiger 900 GT', 'Triumph Rocket 3', 'Triumph Speed Twin', 'Triumph Street Twin',
                'Triumph Thunderbird Storm', 'Triumph Daytona 675'
            ],
            'Aprilia' => [
                'Aprilia SR 160 (Scooter)', 'Aprilia SR 125 (Scooter)', 'Aprilia SXR 160 (Scooter)',
                'Aprilia RSV4 1100 Factory', 'Aprilia Tuono V4', 'Aprilia Mana 850', 'Aprilia Dorsoduro 900'
            ],
            'Vespa' => [
                'Vespa SXL 125', 'Vespa SXL 150', 'Vespa VXL 125', 'Vespa VXL 150', 'Vespa Elegante 150',
                'Vespa ZX 125', 'Vespa Notte 125', 'Vespa Urban Club 125', 'Vespa LX 125', 'Vespa Esclusivo', 'Vespa Red'
            ]
        ];

        // Heavy Vehicle Makes and Models - EXACTLY as per scope document
        $heavyVehicleMakes = [
            'Tata Motors' => [
                'Tata Ace Gold', 'Tata Ace HT', 'Tata Ace EX', 'Tata Ace Mega', 'Tata Ace Zip', 'Tata Ace EV (Electric)',
                'Tata Super Ace', 'Tata Intra V10', 'Tata Intra V20', 'Tata Intra V30', 'Tata Yodha 1700',
                'Tata Yodha 1500', 'Tata Yodha 4x4', 'Tata LPT 407', 'Tata LPT 909', 'Tata LPT 1613',
                'Tata LPT 2518', 'Tata LPT 3118', 'Tata LPT 3718', 'Tata Ultra 1014', 'Tata Ultra 1518',
                'Tata Ultra 1918.T', 'Tata Prima 4925', 'Tata Prima 5530.S', 'Tata Signa 4018.S',
                'Tata Signa 5525.S', 'Tata Xenon'
            ],
            'Ashok Leyland' => [
                'Dost Strong', 'Dost Lite', 'Dost+', 'Bada Dost i1', 'Bada Dost i2', 'Bada Dost i3',
                'Partner 4-Tyre', 'Partner 6-Tyre', 'Ecomet 1215', 'Ecomet 1615', 'Captain 3118',
                'Captain 2518', 'Captain 4923', 'Boss 913', 'Boss 1115', 'Boss 1415', 'U 4936', 'U 3723'
            ],
            'Mahindra & Mahindra' => [
                'Bolero Pik-Up ExtraStrong', 'Bolero Pik-Up ExtraLong', 'Bolero Pik-Up ExtraLong 4x4',
                'Bolero Camper', 'Bolero Maxitruck Plus', 'Jeeto S6-16', 'Jeeto S6-11', 'Jeeto S8-16',
                'Jeeto X7-16', 'Jeeto Plus', 'Jeeto Load', 'Jeeto CNG', 'Supro Minitruck', 'Supro Maxitruck',
                'Supro Ambulance', 'Treo Zor (Electric Cargo)'
            ],
            'Eicher' => [
                'Eicher Pro 1049', 'Eicher Pro 3015', 'Eicher Pro 3016', 'Eicher Pro 3018', 'Eicher Pro 3025',
                'Eicher Pro 3035', 'Eicher Pro 3042', 'Eicher Pro 3055', 'Eicher Pro 3065', 'Eicher Pro 3075'
            ],
            'BharatBenz' => [
                'BharatBenz 1214R', 'BharatBenz 1217R', 'BharatBenz 1617R', 'BharatBenz 1618R',
                'BharatBenz 2523R', 'BharatBenz 3123R', 'BharatBenz 4023R', 'BharatBenz 4923R'
            ],
            'Force Motors' => [
                'Force Traveller', 'Force Trax', 'Force Gurkha', 'Force One', 'Force Urbania'
            ],
            'SML Isuzu' => [
                'SML Isuzu S7-1200', 'SML Isuzu S7-1500', 'SML Isuzu S7-1800', 'SML Isuzu S7-2100'
            ],
            'Volvo' => [
                'Volvo FM', 'Volvo FMX', 'Volvo FH', 'Volvo FH16', 'Volvo VNL', 'Volvo VNR'
            ],
            'Scania' => [
                'Scania P Series', 'Scania G Series', 'Scania R Series', 'Scania S Series'
            ],
            'MAN Trucks' => [
                'MAN TGS', 'MAN TGX', 'MAN CLA', 'MAN TGL', 'MAN TGM'
            ]
        ];

        // Create tables for vehicle makes and models
        DB::statement('CREATE TABLE IF NOT EXISTS vehicle_makes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type ENUM("car", "bike_scooter", "heavy_vehicle") NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )');

        DB::statement('CREATE TABLE IF NOT EXISTS vehicle_models (
            id INT AUTO_INCREMENT PRIMARY KEY,
            make_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (make_id) REFERENCES vehicle_makes(id) ON DELETE CASCADE
        )');

        // Insert car makes and models
        foreach ($carMakes as $make => $models) {
            $makeId = DB::table('vehicle_makes')->insertGetId([
                'name' => $make,
                'type' => 'car',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($models as $model) {
                DB::table('vehicle_models')->insert([
                    'make_id' => $makeId,
                    'name' => $model,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Insert bike makes and models
        foreach ($bikeMakes as $make => $models) {
            $makeId = DB::table('vehicle_makes')->insertGetId([
                'name' => $make,
                'type' => 'bike_scooter',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($models as $model) {
                DB::table('vehicle_models')->insert([
                    'make_id' => $makeId,
                    'name' => $model,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        // Insert heavy vehicle makes and models - EXACTLY as per scope document
        foreach ($heavyVehicleMakes as $make => $models) {
            $makeId = DB::table('vehicle_makes')->insertGetId([
                'name' => $make,
                'type' => 'heavy_vehicle',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            foreach ($models as $model) {
                DB::table('vehicle_models')->insert([
                    'make_id' => $makeId,
                    'name' => $model,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }

        $this->command->info('Vehicle makes and models seeded successfully!');
    }
}