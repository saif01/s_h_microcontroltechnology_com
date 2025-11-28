<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class OnlineUpsProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create the UPS Systems category
        $upsCategory = Category::firstOrCreate(
            ['slug' => 'ups-systems', 'type' => 'product'],
            [
                'name' => 'UPS Systems',
                'slug' => 'ups-systems',
                'type' => 'product',
                'published' => true,
                'order' => 1
            ]
        );

        // Define all On-Line UPS models from the PDF
        $models = [
            ['model' => 'OL-1K', 'capacity' => '1KVA'],
            ['model' => 'OL-2K', 'capacity' => '2KVA'],
            ['model' => 'OL-3K', 'capacity' => '3KVA'],
            ['model' => 'OL-4K', 'capacity' => '4KVA'],
            ['model' => 'OL-5K', 'capacity' => '5KVA'],
            ['model' => 'OL-6K', 'capacity' => '6KVA'],
            ['model' => 'OL-7.5K', 'capacity' => '7.5KVA'],
            ['model' => 'OL-10K', 'capacity' => '10KVA'],
            ['model' => 'OL-15K', 'capacity' => '15KVA'],
            ['model' => 'OL-20K', 'capacity' => '20KVA'],
        ];

        // Common specifications from the PDF
        $commonSpecs = [
            'Technology' => 'Double conversion SPWM technology',
            'AC Input Voltage' => '220V AC (Single phase) or 380V AC (Three phase), ±25% ±20%',
            'AC Input Frequency' => '50/60 Hz ±3 Hz',
            'Battery Type' => 'Sealed lead acid battery, Maintenance free battery',
            'Battery Charging Time' => 'About 10 hours 90% after fully discharged',
            'Protection' => 'Lightning, Transient/Spike, Surge, Sag, Black Out, Brown out, Flicker, Over voltage, Under voltage, Over Load, Short circuit, Battery over charge, Battery low(deep discharge) & Noise (EMI/RFI) as per International safety standard',
            'AC Output Frequency' => '50/60 Hz',
            'AC Output Voltage' => '110 V or 220 V (±5% adjustable), Single Phase',
            'Voltage Regulation' => '±1% or ±2%',
            'Frequency Stability' => '±0.5%',
            'Power Factor' => '0.7 ~ 1 (Lagging)',
            'Transient Response' => '+4% for 100% instantaneous load variation, recovery to 1% of rated load within 2 ms',
            'Harmonic Distortion' => '<3% Of T.H.D at linear load',
            'Wave Form' => 'Pure sine wave',
            'Overload Detection' => '125% for 10 secs. and 150% for 10 cycles. Crest factor 3:1',
            'Synchronize Slew Rate' => '<1 Hz/sec',
            'Efficiency' => '>85%',
            'Transfer Time' => '0 (zero) ms',
            'Overload Recovery' => 'Auto transfer to UPS',
            'UPS to Bypass Transfer' => 'Uninterrupted transfer of 100% load from UPS to bypass and vice versa',
            'Noise Level' => '<55 dBA at 1m distance',
            'LED Indicators' => 'Line input (green), Inverter output (green), Bypass (amber), Fault (red)',
            'Battery Capacity Indicators' => '5 green LEDs, 2 red LEDs and red LEDs indicate battery low condition',
            'Load Level Indicators' => '5 green LEDs, 2 red LEDs and red LEDs indicate overload condition',
            'Battery Discharge Alarm' => 'The first warning will beep every 4 seconds to indicate battery in use. The second warning will beep every 1 second to signal battery low condition',
            'UPS Fault Alarm' => 'Continuous beeping sound',
            'Operating Temperature' => '10°C - 60°C',
            'Operating Humidity' => '20% - 90% (Non condensing)',
            'Computer Interface' => 'Support RS 232 for UPS monitoring utility software. Provide power management & diagnostic functions including power status (Input/output voltage, frequency), Battery Low, Schedule UPS ON/OFF, Battery/load level display and more. Compatible with MS Dos, Windows 3.XX/95/98/XP/7/NT & Novell, etc.',
            'Display' => 'LCD Display',
            'Back-up Time Series' => 'Available in S, X, M, L, LL series with different back-up times',
            'Back-up Time at Full Load' => 'S: 15 min, X: 30 min, M: 60 min, L: 120 min, LL: 240 min',
            'Back-up Time at Half Load' => 'S: 30 min, X: 60 min, M: 120 min, L: 240 min, LL: 480 min',
        ];

        foreach ($models as $index => $modelData) {
            $model = $modelData['model'];
            $capacity = $modelData['capacity'];
            $slug = Str::slug(strtolower($model) . '-online-ups');

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'title' => "On-Line UPS {$model} ({$capacity})",
                    'slug' => $slug,
                    'sku' => $model,
                    'short_description' => "Professional On-Line UPS system with {$capacity} capacity. Features double conversion SPWM technology, LCD display, and zero transfer time for critical applications.",
                    'description' => $this->generateDescription($model, $capacity, $commonSpecs),
                    'price' => null,
                    'price_range' => 'Contact for pricing',
                    'show_price' => false,
                    'specifications' => $this->generateSpecifications($model, $capacity, $commonSpecs),
                    'downloads' => [
                        [
                            'name' => 'On-Line Catalog 1',
                            'url' => '/Project Report/On-Line Catalog1.pdf',
                            'type' => 'pdf'
                        ],
                        [
                            'name' => 'On-Line Catalog 2',
                            'url' => '/Project Report/On-Line Catalog2.pdf',
                            'type' => 'pdf'
                        ]
                    ],
                    'meta_title' => "On-Line UPS {$model} ({$capacity}) - Micro Control Technology",
                    'meta_description' => "Professional On-Line UPS system {$model} with {$capacity} capacity. Double conversion SPWM technology, LCD display, zero transfer time. Ideal for servers, data centers, and critical applications.",
                    'meta_keywords' => "online ups, {$model}, {$capacity}, double conversion, SPWM, LCD display, zero transfer time, UPS system, Micro Control Technology",
                    'published' => true,
                    'featured' => $index < 3, // First 3 models featured
                    'order' => $index + 1,
                ]
            );

            // Attach to UPS Systems category
            $product->categories()->syncWithoutDetaching([$upsCategory->id]);
        }
    }

    /**
     * Generate detailed description for the product
     */
    private function generateDescription($model, $capacity, $specs): string
    {
        return "<h2>On-Line UPS {$model} - {$capacity}</h2>
        <p>Professional-grade On-Line UPS system with True Double Conversion technology, designed for critical applications requiring zero transfer time and continuous power protection.</p>
        
        <h3>Key Features:</h3>
        <ul>
            <li><strong>Technology:</strong> {$specs['Technology']}</li>
            <li><strong>Zero Transfer Time:</strong> {$specs['Transfer Time']} - Uninterrupted power supply</li>
            <li><strong>LCD Display:</strong> {$specs['Display']} for real-time monitoring</li>
            <li><strong>Pure Sine Wave Output:</strong> {$specs['Wave Form']} for sensitive equipment</li>
            <li><strong>High Efficiency:</strong> {$specs['Efficiency']}</li>
            <li><strong>Comprehensive Protection:</strong> {$specs['Protection']}</li>
            <li><strong>RS-232 Interface:</strong> {$specs['Computer Interface']}</li>
        </ul>

        <h3>Applications:</h3>
        <ul>
            <li>Server rooms and data centers</li>
            <li>Medical equipment and healthcare facilities</li>
            <li>Industrial automation systems</li>
            <li>Telecommunications equipment</li>
            <li>Critical business operations</li>
            <li>Financial institutions</li>
            <li>Security and surveillance systems</li>
        </ul>

        <h3>Technical Specifications:</h3>
        <ul>
            <li><strong>AC Input:</strong> {$specs['AC Input Voltage']}, {$specs['AC Input Frequency']}</li>
            <li><strong>AC Output:</strong> {$specs['AC Output Voltage']}, {$specs['AC Output Frequency']}</li>
            <li><strong>Voltage Regulation:</strong> {$specs['Voltage Regulation']}</li>
            <li><strong>Frequency Stability:</strong> {$specs['Frequency Stability']}</li>
            <li><strong>Power Factor:</strong> {$specs['Power Factor']}</li>
            <li><strong>Harmonic Distortion:</strong> {$specs['Harmonic Distortion']}</li>
            <li><strong>Battery:</strong> {$specs['Battery Type']}</li>
            <li><strong>Charging Time:</strong> {$specs['Battery Charging Time']}</li>
            <li><strong>Operating Environment:</strong> {$specs['Operating Temperature']}, {$specs['Operating Humidity']}</li>
            <li><strong>Noise Level:</strong> {$specs['Noise Level']}</li>
        </ul>

        <h3>Back-up Time Options:</h3>
        <p>Available in multiple series (S, X, M, L, LL) with different back-up times:</p>
        <ul>
            <li><strong>Full Load:</strong> {$specs['Back-up Time at Full Load']}</li>
            <li><strong>Half Load:</strong> {$specs['Back-up Time at Half Load']}</li>
        </ul>

        <p><strong>Note:</strong> Actual series will be indicated with each model depending on back-up time. Capacity and back-up time mentioned are for computer load. For any other application, please contact Micro Control Technology.</p>";
    }

    /**
     * Generate specifications array for the product
     */
    private function generateSpecifications($model, $capacity, $specs): array
    {
        return [
            'Model' => $model,
            'Capacity' => $capacity,
            'Technology' => $specs['Technology'],
            'AC Input' => [
                'Voltage' => $specs['AC Input Voltage'],
                'Frequency' => $specs['AC Input Frequency']
            ],
            'AC Output' => [
                'Voltage' => $specs['AC Output Voltage'],
                'Frequency' => $specs['AC Output Frequency'],
                'Voltage Regulation' => $specs['Voltage Regulation'],
                'Frequency Stability' => $specs['Frequency Stability'],
                'Power Factor' => $specs['Power Factor'],
                'Wave Form' => $specs['Wave Form'],
                'Harmonic Distortion' => $specs['Harmonic Distortion'],
                'Transient Response' => $specs['Transient Response']
            ],
            'Battery' => [
                'Type' => $specs['Battery Type'],
                'Charging Time' => $specs['Battery Charging Time']
            ],
            'Protection' => $specs['Protection'],
            'Efficiency' => $specs['Efficiency'],
            'Transfer Time' => $specs['Transfer Time'],
            'Overload Detection' => $specs['Overload Detection'],
            'Display' => $specs['Display'],
            'Indicators' => [
                'Status' => $specs['LED Indicators'],
                'Battery Capacity' => $specs['Battery Capacity Indicators'],
                'Load Level' => $specs['Load Level Indicators']
            ],
            'Alarms' => [
                'Battery Discharge' => $specs['Battery Discharge Alarm'],
                'UPS Fault' => $specs['UPS Fault Alarm']
            ],
            'Operating Environment' => [
                'Temperature' => $specs['Operating Temperature'],
                'Humidity' => $specs['Operating Humidity']
            ],
            'Noise Level' => $specs['Noise Level'],
            'Interface' => $specs['Computer Interface'],
            'Back-up Time Options' => [
                'Full Load' => $specs['Back-up Time at Full Load'],
                'Half Load' => $specs['Back-up Time at Half Load']
            ]
        ];
    }
}

