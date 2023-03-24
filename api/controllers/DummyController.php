<?php


namespace api\controllers;


use api\components\Controller;
use api\components\Response;

class DummyController extends Controller
{
    public function behaviors()
    {
        $behaviors                              = parent::behaviors();
//        $behaviors['systemAppFilter']['except'] = ['index'];

        return $behaviors;
    }

    public function actionIndex()
    {
        $json = [
            [
                "id"           => 1,
                "cModel"       => "Elise",
                "cManufacture" => "Lotus",
                "cModelYear"   => 2004,
                "cMileage"     => 116879,
                "cDescription" => "The Lotus Elise first appeared in 1996 and revolutionised small sports car design with its lightweight extruded aluminium chassis and composite body. There have been many variations, but the basic principle remain the same.",
                "cColor"       => "Red",
                "cPrice"       => 18347,
                "cCondition"   => 1,
                "createdDate"  => "09/30/2017",
                "cStatus"      => 0,
                "cVINCode"     => "1FTWX3D52AE575540"
            ],
            [
                "id"           => 2,
                "cModel"       => "Sunbird",
                "cManufacture" => "Pontiac",
                "cModelYear"   => 1984,
                "cMileage"     => 99515,
                "cDescription" => "The Pontiac Sunbird is an automobile that was produced by Pontiac, initially as a subcompact for the 1976 to 1980 cModel years,and later as a compact for the 1982 to 1994 cModel years. The Sunbird badge ran for 18 years (with a hiatus during the 1981 and 1982 cModel years, as the 1982 cModel was called J2000) and was then replaced in 1995 by the Pontiac Sunfire. Through the years the Sunbird was available in notchback coupé, sedan, hatchback, station wagon, and convertible body styles.",
                "cColor"       => "Khaki",
                "cPrice"       => 165956,
                "cCondition"   => 0,
                "createdDate"  => "03/22/2018",
                "cStatus"      => 1,
                "cVINCode"     => "JM1NC2EF8A0293556"
            ],
            [
                "id"           => 4,
                "cModel"       => "LS",
                "cManufacture" => "Lexus",
                "cModelYear"   => 2004,
                "cMileage"     => 183068,
                "cDescription" => "The Lexus LS (Japanese: レクサス・LS, Rekusasu LS) is a full-size luxury car (F-segment in Europe) serving as the flagship cModel of Lexus, the luxury division of Toyota. For the first four generations, all LS cModels featured V8 engines and were predominantly rear-wheel-drive, with Lexus also offering all-wheel-drive, hybrid, and long-wheelbase variants. The fifth generation changed to using a V6 engine with no V8 option, and the long wheelbase variant was removed entirely.",
                "cColor"       => "Mauv",
                "cPrice"       => 95410,
                "cCondition"   => 1,
                "createdDate"  => "02/03/2018",
                "cStatus"      => 1,
                "cVINCode"     => "2T1BU4EE6DC859114"
            ],
            [
                "id"           => 5,
                "cModel"       => "Paseo",
                "cManufacture" => "Toyota",
                "cModelYear"   => 1997,
                "cMileage"     => 74884,
                "cDescription" => "The Toyota Paseo (known as the Cynos in Japan and other regions) is a sports styled compact car sold from 1991–1999 and was loosely based on the Tercel. It was available as a coupe and in later cModels as a convertible. Toyota stopped selling the car in the United States in 1997, however the car continued to be sold in Canada, Europe and Japan until 1999, but had no direct replacement. The Paseo, like the Tercel, shares a platform with the Starlet. Several parts are interchangeable between the three",
                "cColor"       => "Pink",
                "cPrice"       => 24796,
                "cCondition"   => 1,
                "createdDate"  => "08/13/2017",
                "cStatus"      => 0,
                "cVINCode"     => "1D7RB1GP0AS597432"
            ],
            [
                "id"           => 6,
                "cModel"       => "M",
                "cManufacture" => "Infiniti",
                "cModelYear"   => 2009,
                "cMileage"     => 194846,
                "cDescription" => "The Infiniti M is a line of mid-size luxury (executive) cars from the Infiniti luxury division of Nissan.
The first iteration was the M30 Coupe/Convertible, which were rebadged JDM Nissan Leopard.
After a long hiatus, the M nameplate was used for Infiniti's mid-luxury sedans (executive cars). First was the short-lived M45 sedan, a rebadged version of the Japanese-spec Nissan Gloria. The next generations, the M35/45 and M37/56/35h/30d, became the flagship of the Infiniti brand and are based on the JDM Nissan Fuga.",
                "cColor"       => "Puce",
                "cPrice"       => 30521,
                "cCondition"   => 1,
                "createdDate"  => "01/27/2018",
                "cStatus"      => 0,
                "cVINCode"     => "YV1940AS1D1542424"
            ],
            [
                "id"           => 7,
                "cModel"       => "Phantom",
                "cManufacture" => "Rolls-Royce",
                "cModelYear"   => 2008,
                "cMileage"     => 164124,
                "cDescription" => "The Rolls-Royce Phantom VIII is a luxury saloon car cManufactured by Rolls-Royce Motor Cars. It is the eighth and current generation of Rolls-Royce Phantom, and the second launched by Rolls-Royce under BMW ownership. It is offered in two wheelbase lengths",
                "cColor"       => "Purple",
                "cPrice"       => 196247,
                "cCondition"   => 1,
                "createdDate"  => "09/28/2017",
                "cStatus"      => 1,
                "cVINCode"     => "3VWML7AJ1DM234625"
            ],
            [
                "id"           => 8,
                "cModel"       => "QX",
                "cManufacture" => "Infiniti",
                "cModelYear"   => 2002,
                "cMileage"     => 57410,
                "cDescription" => "The Infiniti QX80 (called the Infiniti QX56 until 2013) is a full-size luxury SUV built by Nissan Motor Company's Infiniti division. The naming convention originally adhered to the current trend of using a numeric designation derived from the engine's displacement, thus QX56 since the car has a 5.6-liter engine. From the 2014 cModel year, the car was renamed the QX80, as part of Infiniti's cModel name rebranding. The new name carries no meaning beyond suggesting that the vehicle is larger than smaller cModels such as the QX60",
                "cColor"       => "Green",
                "cPrice"       => 185775,
                "cCondition"   => 1,
                "createdDate"  => "11/15/2017",
                "cStatus"      => 0,
                "cVINCode"     => "WDDHF2EB9CA161524"
            ],
            [
                "id"           => 9,
                "cModel"       => "Daytona",
                "cManufacture" => "Dodge",
                "cModelYear"   => 1993,
                "cMileage"     => 4444,
                "cDescription" => "The Dodge Daytona was an automobile which was produced by the Chrysler Corporation under their Dodge division from 1984 to 1993. It was a front-wheel drive hatchback based on the Chrysler G platform, which was derived from the Chrysler K platform. The Chrysler Laser was an upscale rebadged version of the Daytona. The Daytona was restyled for 1987, and again for 1992. It replaced the Mitsubishi Galant-based Challenger, and slotted between the Charger and the Conquest. The Daytona was replaced by the 1995 Dodge Avenger, which was built by Mitsubishi Motors. The Daytona derives its name mainly from the Dodge Charger Daytona, which itself was named after the Daytona 500 race in Daytona Beach, Florida.",
                "cColor"       => "Maroon",
                "cPrice"       => 171898,
                "cCondition"   => 0,
                "createdDate"  => "12/24/2017",
                "cStatus"      => 1,
                "cVINCode"     => "WBAET37422N752051"
            ],
            [
                "id"           => 10,
                "cModel"       => "1500 Silverado",
                "cManufacture" => "Chevrolet",
                "cModelYear"   => 1999,
                "cMileage"     => 195310,
                "cDescription" => "The Chevrolet Silverado, and its mechanically identical cousin, the GMC Sierra, are a series of full-size and heavy-duty pickup trucks cManufactured by General Motors and introduced in 1998 as the successor to the long-running Chevrolet C/K line. The Silverado name was taken from a trim level previously used on its predecessor, the Chevrolet C/K pickup truck from 1975 through 1998. General Motors continues to offer a GMC-badged variant of the Chevrolet full-size pickup under the GMC Sierra name, first used in 1987 for its variant of the GMT400 platform trucks.",
                "cColor"       => "Blue",
                "cPrice"       => 25764,
                "cCondition"   => 0,
                "createdDate"  => "08/30/2017",
                "cStatus"      => 1,
                "cVINCode"     => "1N6AF0LX6EN590806"
            ],
        ];

        $response          = new Response;
        $response->name    = \Yii::$app->name;
        $response->message = 'API is running';
        $response->code    = 0;
        $response->status  = 200;
        $response->data    = $json;
        $response->meta    = [
            'record' => [
                'current' => 1,
                'total'   => 1200
            ],
            'page'   => [
                'current' => 1,
                'total'   => 120
            ]
        ];
        return $response;
    }
}