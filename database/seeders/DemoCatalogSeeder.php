<?php

namespace Database\Seeders;

use App\Enums\BannerPlacement;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

/**
 * Local demo data for building the storefront. Run explicitly:
 * php artisan db:seed --class=DemoCatalogSeeder
 */
class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            'Access Point', 'Adaptadores', 'USB Inalámbrico', 'Herramientas',
            'Suministros', 'Papel', 'Sensores', 'Alcohol Isopropílico', 'Parlantes',
        ])->mapWithKeys(fn (string $name, int $index) => [
            $name => Category::firstOrCreate(['name' => $name], ['sort_order' => $index]),
        ]);

        $brands = collect(['Ubiquiti', 'TP-Link', 'D-Link', 'Epson', 'Brother', 'MikroTik', 'Genérico'])
            ->mapWithKeys(fn (string $name) => [$name => Brand::firstOrCreate(['name' => $name])]);

        $products = [
            ['Ubiquiti PowerBeam M5 PBE-M5-400 25dBi', 'Access Point', 'Ubiquiti', 380, 456, 12],
            ['Ubiquiti LiteBeam 5AC Gen2 23dBi', 'Access Point', 'Ubiquiti', 295, null, 8],
            ['MikroTik SXTsq 5 ac', 'Access Point', 'MikroTik', 230, null, 15],
            ['Adaptador USB 3.0 a VGA Full HD 1080p', 'Adaptadores', 'Genérico', 45, null, 22],
            ['Adaptador Conversor HDMI a VGA + Audio', 'Adaptadores', 'Genérico', 25, 35, 30],
            ['Adaptador USB a Ethernet Gigabit RJ45', 'Adaptadores', 'TP-Link', 48, null, 18],
            ['Adaptador USB Wi-Fi D-Link DWA-131 Nano', 'USB Inalámbrico', 'D-Link', 38, null, 9],
            ['Adaptador USB Wi-Fi TP-Link Archer T2U', 'USB Inalámbrico', 'TP-Link', 69, null, 4],
            ['Kit Crimpador RJ45 Pro + Pelador', 'Herramientas', 'Genérico', 55, 65, 20],
            ['Probador de Cable de Red RJ45 / RJ11', 'Herramientas', 'Genérico', 35, null, 14],
            ['Caja de Mantenimiento Epson T6711 Original', 'Suministros', 'Epson', 110, 130, 16],
            ['Caja de Mantenimiento Epson T04D1 EcoTank', 'Suministros', 'Epson', 95, null, 11],
            ['Botella de Tinta Brother BTD60BK Negro', 'Suministros', 'Brother', 42, null, 25],
            ['Papel Bond A4 75g x 500 hojas', 'Papel', 'Genérico', 22, null, 60],
            ['Sensor de Movimiento PIR Wi-Fi', 'Sensores', 'Genérico', 48, null, 7],
            ['Alcohol Isopropílico 99.8% 1 Litro', 'Alcohol Isopropílico', 'Genérico', 22, 28, 40],
            ['Parlante Bluetooth Portátil 10W', 'Parlantes', 'Genérico', 79, null, 3],
            ['Cable Patch Cord Cat6 UTP 3 Metros', 'Adaptadores', 'Genérico', 12, null, 50],
        ];

        foreach ($products as $index => [$name, $category, $brand, $price, $regularPrice, $stock]) {
            Product::firstOrCreate(['name' => $name], [
                'category_id' => $categories[$category]->id,
                'brand_id' => $brands[$brand]->id,
                'summary' => 'Producto de demostración para el desarrollo de la tienda.',
                'description' => '<p>Descripción de demostración. El contenido real se carga desde el panel.</p>',
                'information' => '<ul><li>Información técnica de demostración.</li></ul>',
                'price' => $price,
                'regular_price' => $regularPrice,
                'stock' => $stock,
                'unit' => 'unidad',
                'is_featured' => $index < 8,
            ]);
        }

        Banner::firstOrCreate(['title' => 'Banner principal de demostración'], ['placement' => BannerPlacement::Hero]);
        Banner::firstOrCreate(['title' => 'Banner lateral de demostración'], ['placement' => BannerPlacement::Side]);

        Promotion::firstOrCreate(['title' => 'Promoción de demostración'], [
            'product_id' => Product::where('name', 'like', 'Ubiquiti PowerBeam%')->value('id'),
        ]);
    }
}
