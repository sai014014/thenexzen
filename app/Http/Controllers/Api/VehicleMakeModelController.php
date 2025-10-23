<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VehicleMakeModelController extends Controller
{
    public function getMakes(Request $request)
    {
        $type = $request->get('type', 'car');
        
        $makes = $this->getVehicleMakesByType($type);
        
        return response()->json($makes);
    }
    
    public function getModels(Request $request)
    {
        $makeName = $request->get('make_name');
        
        if (!$makeName) {
            return response()->json([]);
        }
        
        $models = $this->getVehicleModelsByMake($makeName);
        
        return response()->json($models);
    }
    
    private function getVehicleMakesByType($type)
    {
        $vehicleData = $this->getVehicleData();
        
        if (!isset($vehicleData[$type])) {
            return [];
        }
        
        $makes = [];
        foreach ($vehicleData[$type] as $make => $models) {
            $makes[] = [
                'id' => strtolower(str_replace(' ', '_', $make)),
                'name' => $make
            ];
        }
        
        return $makes;
    }
    
    private function getVehicleModelsByMake($makeName)
    {
        $vehicleData = $this->getVehicleData();
        
        foreach ($vehicleData as $type => $makes) {
            foreach ($makes as $make => $models) {
                if ($make === $makeName) {
                    $modelList = [];
                    foreach ($models as $model) {
                        $modelList[] = [
                            'id' => strtolower(str_replace(' ', '_', $model)),
                            'name' => $model
                        ];
                    }
                    return $modelList;
                }
            }
        }
        
        return [];
    }
    
    private function getVehicleData()
    {
        return [
            'car' => [
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
                    'Toyota Camry', 'Toyota Corolla Altis', 'Toyota Etios', 'Toyota Liva', 'Toyota Prius',
                    'Toyota Land Cruiser', 'Toyota Prado', 'Toyota Qualis', 'Toyota Innova (older generation)'
                ],
                'Ford' => [
                    'Ford EcoSport', 'Ford Figo', 'Ford Aspire', 'Ford Freestyle', 'Ford Endeavour',
                    'Ford Fiesta', 'Ford Ikon', 'Ford Mondeo', 'Ford Fusion', 'Ford Focus'
                ],
                'Nissan' => [
                    'Nissan Micra', 'Nissan Sunny', 'Nissan Terrano', 'Nissan Kicks', 'Nissan Magnite',
                    'Nissan Teana', 'Nissan X-Trail', 'Nissan Livina'
                ],
                'Volkswagen' => [
                    'Volkswagen Polo', 'Volkswagen Vento', 'Volkswagen Ameo', 'Volkswagen Tiguan',
                    'Volkswagen Passat', 'Volkswagen Jetta', 'Volkswagen Beetle'
                ],
                'Skoda' => [
                    'Skoda Rapid', 'Skoda Octavia', 'Skoda Superb', 'Skoda Kodiaq', 'Skoda Kushaq',
                    'Skoda Laura', 'Skoda Fabia', 'Skoda Yeti'
                ],
                'Renault' => [
                    'Renault Kwid', 'Renault Duster', 'Renault Lodgy', 'Renault Triber', 'Renault Kiger',
                    'Renault Pulse', 'Renault Scala', 'Renault Fluence'
                ],
                'Fiat' => [
                    'Fiat Punto', 'Fiat Linea', 'Fiat Avventura', 'Fiat Urban Cross', 'Fiat Abarth Punto'
                ],
                'Chevrolet' => [
                    'Chevrolet Beat', 'Chevrolet Sail', 'Chevrolet Cruze', 'Chevrolet Tavera',
                    'Chevrolet Optra', 'Chevrolet Aveo', 'Chevrolet Spark'
                ],
                'BMW' => [
                    'BMW 3 Series', 'BMW 5 Series', 'BMW 7 Series', 'BMW X1', 'BMW X3', 'BMW X5',
                    'BMW X7', 'BMW Z4', 'BMW i8', 'BMW i3'
                ],
                'Mercedes-Benz' => [
                    'Mercedes A-Class', 'Mercedes C-Class', 'Mercedes E-Class', 'Mercedes S-Class',
                    'Mercedes GLA', 'Mercedes GLC', 'Mercedes GLE', 'Mercedes GLS', 'Mercedes CLA',
                    'Mercedes CLS', 'Mercedes G-Class'
                ],
                'Audi' => [
                    'Audi A3', 'Audi A4', 'Audi A6', 'Audi A8', 'Audi Q2', 'Audi Q3', 'Audi Q5',
                    'Audi Q7', 'Audi Q8', 'Audi TT', 'Audi R8'
                ],
                'Jaguar' => [
                    'Jaguar XE', 'Jaguar XF', 'Jaguar XJ', 'Jaguar F-PACE', 'Jaguar E-PACE', 'Jaguar I-PACE'
                ],
                'Land Rover' => [
                    'Land Rover Discovery Sport', 'Land Rover Discovery', 'Land Rover Range Rover Evoque',
                    'Land Rover Range Rover Velar', 'Land Rover Range Rover Sport', 'Land Rover Range Rover',
                    'Land Rover Defender'
                ],
                'Volvo' => [
                    'Volvo XC40', 'Volvo XC60', 'Volvo XC90', 'Volvo S60', 'Volvo S90', 'Volvo V40',
                    'Volvo V60', 'Volvo V90'
                ]
            ],
            'bike_scooter' => [
                'Honda' => [
                    'Honda Activa', 'Honda Dio', 'Honda Aviator', 'Honda CB Shine', 'Honda CB Unicorn',
                    'Honda CB Hornet', 'Honda CBR150R', 'Honda CBR250R', 'Honda CBR650R', 'Honda CB350',
                    'Honda CB200X', 'Honda CB300F', 'Honda CB300R', 'Honda CB650R', 'Honda CB1000R',
                    'Honda CBR1000RR', 'Honda Africa Twin', 'Honda CRF1000L', 'Honda Gold Wing'
                ],
                'Yamaha' => [
                    'Yamaha Fascino', 'Yamaha Ray ZR', 'Yamaha FZ', 'Yamaha FZ-S', 'Yamaha FZ25',
                    'Yamaha R15', 'Yamaha R3', 'Yamaha R1', 'Yamaha MT-15', 'Yamaha MT-07', 'Yamaha MT-09',
                    'Yamaha MT-10', 'Yamaha FZ-X', 'Yamaha Aerox', 'Yamaha NMax', 'Yamaha XSR155'
                ],
                'Bajaj' => [
                    'Bajaj Pulsar', 'Bajaj Discover', 'Bajaj Avenger', 'Bajaj Platina', 'Bajaj CT100',
                    'Bajaj Dominar', 'Bajaj V', 'Bajaj Chetak', 'Bajaj Qute', 'Bajaj Boxer',
                    'Bajaj Pulsar NS', 'Bajaj Pulsar RS', 'Bajaj Pulsar 220F', 'Bajaj Pulsar 150'
                ],
                'TVS' => [
                    'TVS Apache', 'TVS Jupiter', 'TVS Scooty', 'TVS Star City', 'TVS Sport',
                    'TVS Victor', 'TVS XL', 'TVS Wego', 'TVS NTorq', 'TVS Radeon', 'TVS Apache RTR',
                    'TVS Apache RR310', 'TVS Apache RTR 160', 'TVS Apache RTR 200', 'TVS Apache RTR 180'
                ],
                'Hero' => [
                    'Hero Splendor', 'Hero Passion', 'Hero Glamour', 'Hero HF Deluxe', 'Hero Super Splendor',
                    'Hero Achiever', 'Hero Hunk', 'Hero Karizma', 'Hero Xtreme', 'Hero Duet',
                    'Hero Maestro', 'Hero Pleasure', 'Hero Destini', 'Hero Xpulse', 'Hero Xtreme 200S'
                ],
                'Royal Enfield' => [
                    'Royal Enfield Classic', 'Royal Enfield Bullet', 'Royal Enfield Thunderbird',
                    'Royal Enfield Continental GT', 'Royal Enfield Himalayan', 'Royal Enfield Interceptor',
                    'Royal Enfield Meteor', 'Royal Enfield Hunter', 'Royal Enfield Scram'
                ],
                'KTM' => [
                    'KTM Duke', 'KTM RC', 'KTM Adventure', 'KTM 390 Duke', 'KTM 250 Duke',
                    'KTM 200 Duke', 'KTM 125 Duke', 'KTM RC 390', 'KTM RC 200', 'KTM RC 125',
                    'KTM 390 Adventure', 'KTM 250 Adventure'
                ],
                'Suzuki' => [
                    'Suzuki Gixxer', 'Suzuki Access', 'Suzuki Burgman', 'Suzuki Hayabusa',
                    'Suzuki GSX-R', 'Suzuki V-Strom', 'Suzuki Intruder', 'Suzuki Bandit'
                ],
                'Kawasaki' => [
                    'Kawasaki Ninja', 'Kawasaki Z', 'Kawasaki Versys', 'Kawasaki Vulcan',
                    'Kawasaki W800', 'Kawasaki Ninja 300', 'Kawasaki Ninja 650', 'Kawasaki Z650'
                ],
                'Ducati' => [
                    'Ducati Monster', 'Ducati Panigale', 'Ducati Multistrada', 'Ducati Diavel',
                    'Ducati Scrambler', 'Ducati Streetfighter', 'Ducati Hypermotard'
                ],
                'Aprilia' => [
                    'Aprilia RSV4', 'Aprilia Tuono V4', 'Aprilia RS 660', 'Aprilia Tuono 660',
                    'Aprilia SX 125', 'Aprilia SR 150', 'Aprilia SR 160', 'Aprilia SR 200',
                    'Aprilia SR 300', 'Aprilia Dorsoduro', 'Aprilia Shiver', 'Aprilia Caponord',
                    'Aprilia Mana', 'Aprilia Scarabeo', 'Aprilia Atlantic', 'Aprilia SR Max',
                    'Aprilia SportCity', 'Aprilia Mojito', 'Aprilia Habana', 'Aprilia Leonardo'
                ]
            ],
            'heavy_vehicle' => [
                'Tata' => [
                    'Tata Prima', 'Tata Signa', 'Tata LPT', 'Tata LPT 3118', 'Tata LPT 2518',
                    'Tata LPT 1618', 'Tata LPT 709', 'Tata LPT 1109', 'Tata LPT 1613',
                    'Tata LPT 2516', 'Tata LPT 3116', 'Tata LPT 3516', 'Tata LPT 4018'
                ],
                'Mahindra' => [
                    'Mahindra Blazo', 'Mahindra Blazo X', 'Mahindra Blazo X 31', 'Mahindra Blazo X 25',
                    'Mahindra Blazo X 37', 'Mahindra Blazo X 28', 'Mahindra Blazo X 35',
                    'Mahindra Blazo X 40', 'Mahindra Blazo X 31 Tipper', 'Mahindra Blazo X 25 Tipper'
                ],
                'Ashok Leyland' => [
                    'Ashok Leyland Partner', 'Ashok Leyland Dost', 'Ashok Leyland Boss',
                    'Ashok Leyland Captain', 'Ashok Leyland Comet', 'Ashok Leyland Lynx',
                    'Ashok Leyland 1616', 'Ashok Leyland 2516', 'Ashok Leyland 3116',
                    'Ashok Leyland 3516', 'Ashok Leyland 4018'
                ],
                'Eicher' => [
                    'Eicher Pro', 'Eicher Pro 1049', 'Eicher Pro 2049', 'Eicher Pro 3049',
                    'Eicher Pro 4049', 'Eicher Pro 5049', 'Eicher Pro 6049', 'Eicher Pro 7049',
                    'Eicher Pro 8051', 'Eicher Pro 9051'
                ],
                'BharatBenz' => [
                    'BharatBenz 1217', 'BharatBenz 1617', 'BharatBenz 2017', 'BharatBenz 2523',
                    'BharatBenz 3123', 'BharatBenz 4023', 'BharatBenz 4923', 'BharatBenz 6023'
                ],
                'Volvo' => [
                    'Volvo FM', 'Volvo FH', 'Volvo FMX', 'Volvo FM 400', 'Volvo FM 460',
                    'Volvo FH 500', 'Volvo FH 540', 'Volvo FMX 480', 'Volvo FMX 520'
                ],
                'Scania' => [
                    'Scania P-Series', 'Scania G-Series', 'Scania R-Series', 'Scania S-Series',
                    'Scania P 280', 'Scania P 320', 'Scania G 410', 'Scania R 450', 'Scania S 500'
                ],
                'MAN' => [
                    'MAN CLA', 'MAN TGS', 'MAN TGX', 'MAN CLA 18.250', 'MAN TGS 18.250',
                    'MAN TGS 26.250', 'MAN TGS 33.250', 'MAN TGX 26.250', 'MAN TGX 33.250'
                ]
            ]
        ];
        
        return $vehicleData;
    }
}