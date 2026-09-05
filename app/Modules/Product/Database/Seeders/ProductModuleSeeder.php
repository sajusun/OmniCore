<?php

namespace App\Modules\Product\Database\Seeders;

use App\Models\User;
use App\Modules\Product\Models\Product;
use App\Modules\Product\Models\ProductAttribute;
use App\Modules\Product\Models\ProductBrand;
use App\Modules\Product\Models\ProductCategory;
use App\Modules\Product\Models\ProductReview;
use App\Modules\Product\Models\ProductTag;
use App\Modules\Product\Services\VariantMatrixService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductModuleSeeder extends Seeder
{
    public function run(): void
    {
        echo "--> Seeding Categories...\n";
        $categories = $this->seedCategories();

        echo "--> Seeding Brands...\n";
        $brands = $this->seedBrands();

        echo "--> Seeding Attributes...\n";
        $attributes = $this->seedAttributes();

        echo "--> Seeding Tags...\n";
        $tags = $this->seedTags();

        echo "--> Seeding 35+ Rich Products & Variants...\n";
        $products = $this->seedProducts($categories, $brands, $attributes, $tags);

        echo "--> Seeding Reviews & Wishlists...\n";
        $this->seedReviewsAndWishlists($products);

        echo "✓ Product Module Seeding Complete!\n";
    }

    protected function seedCategories(): array
    {
        $data = [
            'Electronics' => [
                'icon' => 'fa-tv',
                'children' => ['Smartphones & Accessories', 'Laptops & Computers', 'Audio & Headphones', 'Gaming & Consoles'],
            ],
            'Fashion & Apparel' => [
                'icon' => 'fa-tshirt',
                'children' => ["Men's Clothing", "Women's Clothing", 'Footwear & Sneakers', 'Watches & Accessories'],
            ],
            'Home & Lifestyle' => [
                'icon' => 'fa-couch',
                'children' => ['Smart Home Devices', 'Kitchen Appliances', 'Workspace & Office'],
            ],
        ];

        $categoriesMap = [];
        $order = 1;

        foreach ($data as $parentName => $info) {
            $parent = ProductCategory::firstOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'icon' => $info['icon'],
                    'order' => $order++,
                    'is_active' => true,
                    'is_featured' => true,
                    'description' => "Explore premium collection of {$parentName}",
                ]
            );

            $categoriesMap[$parentName] = $parent;
            $childOrder = 1;

            foreach ($info['children'] as $childName) {
                $child = ProductCategory::firstOrCreate(
                    ['slug' => Str::slug($childName)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $childName,
                        'order' => $childOrder++,
                        'is_active' => true,
                        'is_featured' => true,
                        'description' => "Best selection of {$childName}",
                    ]
                );
                $categoriesMap[$childName] = $child;
            }
        }

        return $categoriesMap;
    }

    protected function seedBrands(): array
    {
        $brandsList = [
            ['name' => 'Apple', 'website' => 'https://apple.com', 'is_featured' => true],
            ['name' => 'Samsung', 'website' => 'https://samsung.com', 'is_featured' => true],
            ['name' => 'Sony', 'website' => 'https://sony.com', 'is_featured' => true],
            ['name' => 'Nike', 'website' => 'https://nike.com', 'is_featured' => true],
            ['name' => 'Adidas', 'website' => 'https://adidas.com', 'is_featured' => true],
            ['name' => 'Dell', 'website' => 'https://dell.com', 'is_featured' => false],
            ['name' => 'Logitech', 'website' => 'https://logitech.com', 'is_featured' => true],
            ['name' => 'Bose', 'website' => 'https://bose.com', 'is_featured' => false],
            ['name' => 'Zara', 'website' => 'https://zara.com', 'is_featured' => false],
            ['name' => 'Puma', 'website' => 'https://puma.com', 'is_featured' => false],
        ];

        $brandsMap = [];
        foreach ($brandsList as $b) {
            $brand = ProductBrand::firstOrCreate(
                ['slug' => Str::slug($b['name'])],
                [
                    'name' => $b['name'],
                    'website' => $b['website'],
                    'is_active' => true,
                    'is_featured' => $b['is_featured'],
                    'description' => "Official products from {$b['name']}",
                ]
            );
            $brandsMap[$b['name']] = $brand;
        }

        return $brandsMap;
    }

    protected function seedAttributes(): array
    {
        // 1. Color
        $attrColor = ProductAttribute::firstOrCreate(['slug' => 'color'], ['name' => 'Color', 'type' => 'color']);
        $colorVals = [
            'Black' => '#000000',
            'White' => '#FFFFFF',
            'Space Gray' => '#4B4846',
            'Navy Blue' => '#000080',
            'Crimson Red' => '#DC143C',
            'Olive Green' => '#556B2F',
        ];
        $colorMap = [];
        $order = 1;
        foreach ($colorVals as $val => $code) {
            $colorMap[$val] = $attrColor->values()->firstOrCreate(
                ['value' => $val],
                ['code' => $code, 'order' => $order++]
            );
        }

        // 2. Clothing Size
        $attrSize = ProductAttribute::firstOrCreate(['slug' => 'size'], ['name' => 'Size', 'type' => 'button']);
        $sizeVals = ['S', 'M', 'L', 'XL', 'XXL'];
        $sizeMap = [];
        $order = 1;
        foreach ($sizeVals as $s) {
            $sizeMap[$s] = $attrSize->values()->firstOrCreate(['value' => $s], ['order' => $order++]);
        }

        // 3. Storage
        $attrStorage = ProductAttribute::firstOrCreate(['slug' => 'storage'], ['name' => 'Storage', 'type' => 'select']);
        $storageVals = ['128GB', '256GB', '512GB', '1TB'];
        $storageMap = [];
        $order = 1;
        foreach ($storageVals as $st) {
            $storageMap[$st] = $attrStorage->values()->firstOrCreate(['value' => $st], ['order' => $order++]);
        }

        // 4. Shoe Size
        $attrShoe = ProductAttribute::firstOrCreate(['slug' => 'shoe-size'], ['name' => 'Shoe Size', 'type' => 'button']);
        $shoeVals = ['US 8', 'US 9', 'US 10', 'US 11'];
        $shoeMap = [];
        $order = 1;
        foreach ($shoeVals as $sh) {
            $shoeMap[$sh] = $attrShoe->values()->firstOrCreate(['value' => $sh], ['order' => $order++]);
        }

        return [
            'color' => $colorMap,
            'size' => $sizeMap,
            'storage' => $storageMap,
            'shoe' => $shoeMap,
        ];
    }

    protected function seedTags(): array
    {
        $tagsList = ['Best Seller', 'Trending', 'New Arrival', 'On Sale', 'Eco Friendly', 'Premium', 'Wireless', 'Limited Edition'];
        $tagsMap = [];
        foreach ($tagsList as $t) {
            $tagsMap[$t] = ProductTag::firstOrCreate(['slug' => Str::slug($t)], ['name' => $t]);
        }

        return $tagsMap;
    }

    protected function seedProducts(array $categories, array $brands, array $attributes, array $tags): array
    {
        $matrixService = app(VariantMatrixService::class);

        $catalog = [
            // Smartphones
            [
                'name' => 'Apple iPhone 16 Pro Max',
                'category' => 'Smartphones & Accessories',
                'brand' => 'Apple',
                'price' => 1199.00,
                'compare_at_price' => 1299.00,
                'type' => 'variable',
                'stock' => 45,
                'featured' => true,
                'desc' => 'Featuring grade 5 titanium design with a new 48MP Fusion camera and the groundbreaking A18 Pro chip.',
                'tags' => ['Best Seller', 'Trending', 'Premium'],
                'variant_type' => 'phone',
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'category' => 'Smartphones & Accessories',
                'brand' => 'Samsung',
                'price' => 1099.00,
                'compare_at_price' => 1199.00,
                'type' => 'variable',
                'stock' => 38,
                'featured' => true,
                'desc' => 'Galaxy AI is here. Search like never before, get real-time interpretation on a call, and format notes with S Pen.',
                'tags' => ['Trending', 'Premium'],
                'variant_type' => 'phone',
            ],
            [
                'name' => 'Apple iPhone 15',
                'category' => 'Smartphones & Accessories',
                'brand' => 'Apple',
                'price' => 799.00,
                'compare_at_price' => 899.00,
                'type' => 'variable',
                'stock' => 60,
                'featured' => false,
                'desc' => 'Dynamic Island, 48MP Main camera, and USB-C — all in a durable color-infused glass and aluminum design.',
                'tags' => ['On Sale'],
                'variant_type' => 'phone',
            ],
            [
                'name' => 'Samsung Galaxy Z Fold6',
                'category' => 'Smartphones & Accessories',
                'brand' => 'Samsung',
                'price' => 1899.00,
                'compare_at_price' => 1999.00,
                'type' => 'simple',
                'stock' => 15,
                'featured' => true,
                'desc' => 'Next-gen foldable with AI-powered multitasking, slim lightweight titanium hinge, and vivid dual displays.',
                'tags' => ['Premium', 'New Arrival'],
            ],
            [
                'name' => 'Anker MagSafe 10000mAh Power Bank',
                'category' => 'Smartphones & Accessories',
                'brand' => 'Apple',
                'price' => 59.99,
                'compare_at_price' => 79.99,
                'type' => 'simple',
                'stock' => 120,
                'featured' => false,
                'desc' => 'Snap and go wireless magnetic power bank with foldable stand and fast 20W USB-C output.',
                'tags' => ['Best Seller'],
            ],

            // Laptops & Computers
            [
                'name' => 'Apple MacBook Pro 16" M3 Max',
                'category' => 'Laptops & Computers',
                'brand' => 'Apple',
                'price' => 2499.00,
                'compare_at_price' => 2699.00,
                'type' => 'variable',
                'stock' => 20,
                'featured' => true,
                'desc' => 'Scary fast performance with Liquid Retina XDR display, up to 22 hours battery life, and pro connectivity ports.',
                'tags' => ['Best Seller', 'Premium'],
                'variant_type' => 'laptop',
            ],
            [
                'name' => 'Apple MacBook Air 15" M3',
                'category' => 'Laptops & Computers',
                'brand' => 'Apple',
                'price' => 1299.00,
                'compare_at_price' => 1399.00,
                'type' => 'simple',
                'stock' => 50,
                'featured' => false,
                'desc' => 'Impossibly thin design with vibrant 15.3-inch Liquid Retina display and all-day battery efficiency.',
                'tags' => ['Trending'],
            ],
            [
                'name' => 'Dell XPS 15 OLED InfinityEdge',
                'category' => 'Laptops & Computers',
                'brand' => 'Dell',
                'price' => 1799.00,
                'compare_at_price' => 1999.00,
                'type' => 'simple',
                'stock' => 18,
                'featured' => true,
                'desc' => 'Precision crafted CNC aluminum chassis with 3.5K OLED touchscreen and NVIDIA RTX 4060 graphics.',
                'tags' => ['Premium'],
            ],
            [
                'name' => 'Dell UltraSharp 32" 4K Thunderbolt Monitor',
                'category' => 'Laptops & Computers',
                'brand' => 'Dell',
                'price' => 799.99,
                'compare_at_price' => 899.99,
                'type' => 'simple',
                'stock' => 30,
                'featured' => false,
                'desc' => 'IPS Black technology with 2000:1 contrast ratio, 90W power delivery, and built-in RJ45 Ethernet.',
                'tags' => ['New Arrival'],
            ],
            [
                'name' => 'Logitech MX Master 3S Wireless Mouse',
                'category' => 'Laptops & Computers',
                'brand' => 'Logitech',
                'price' => 99.99,
                'compare_at_price' => 119.99,
                'type' => 'simple',
                'stock' => 85,
                'featured' => true,
                'desc' => '8K DPI any-surface tracking, quiet click switches, and MagSpeed electromagnetic scrolling.',
                'tags' => ['Best Seller'],
            ],
            [
                'name' => 'Logitech MX Keys S Wireless Keyboard',
                'category' => 'Laptops & Computers',
                'brand' => 'Logitech',
                'price' => 109.99,
                'compare_at_price' => 129.99,
                'type' => 'simple',
                'stock' => 70,
                'featured' => false,
                'desc' => 'Low-profile mechanical comfort with smart illumination and multi-device Easy-Switch.',
                'tags' => ['Trending'],
            ],

            // Audio & Headphones
            [
                'name' => 'Sony WH-1000XM5 Wireless Noise Canceling',
                'category' => 'Audio & Headphones',
                'brand' => 'Sony',
                'price' => 349.99,
                'compare_at_price' => 399.99,
                'type' => 'variable',
                'stock' => 40,
                'featured' => true,
                'desc' => 'Industry-leading noise canceling with Auto NC Optimizer, crystal clear hands-free calling, and 30-hr battery.',
                'tags' => ['Best Seller', 'Wireless'],
                'variant_type' => 'audio',
            ],
            [
                'name' => 'Apple AirPods Pro (2nd Generation, USB-C)',
                'category' => 'Audio & Headphones',
                'brand' => 'Apple',
                'price' => 229.00,
                'compare_at_price' => 249.00,
                'type' => 'simple',
                'stock' => 95,
                'featured' => true,
                'desc' => 'Up to 2x more Active Noise Cancellation, Adaptive Audio, Transparency mode, and Personalized Spatial Audio.',
                'tags' => ['Best Seller', 'Wireless'],
            ],
            [
                'name' => 'Bose QuietComfort Ultra Headphones',
                'category' => 'Audio & Headphones',
                'brand' => 'Bose',
                'price' => 379.00,
                'compare_at_price' => 429.00,
                'type' => 'simple',
                'stock' => 25,
                'featured' => false,
                'desc' => 'Breakthrough spatialized audio for immersive listening, world-class active noise cancellation, and CustomTune tech.',
                'tags' => ['Premium'],
            ],
            [
                'name' => 'Sony WF-1000XM5 Earbuds',
                'category' => 'Audio & Headphones',
                'brand' => 'Sony',
                'price' => 269.99,
                'compare_at_price' => 299.99,
                'type' => 'simple',
                'stock' => 55,
                'featured' => false,
                'desc' => 'Dual processor noise canceling engine with dynamic driver X for rich audio reproduction.',
                'tags' => ['Trending', 'Wireless'],
            ],

            // Gaming
            [
                'name' => 'Sony PlayStation 5 Slim Digital Edition',
                'category' => 'Gaming & Consoles',
                'brand' => 'Sony',
                'price' => 449.99,
                'compare_at_price' => 499.99,
                'type' => 'simple',
                'stock' => 30,
                'featured' => true,
                'desc' => 'Lightning fast loading with ultra-high speed SSD, deeper immersion with haptic feedback and 3D Audio.',
                'tags' => ['Best Seller'],
            ],
            [
                'name' => 'PlayStation DualSense Wireless Controller',
                'category' => 'Gaming & Consoles',
                'brand' => 'Sony',
                'price' => 69.99,
                'compare_at_price' => 74.99,
                'type' => 'variable',
                'stock' => 75,
                'featured' => false,
                'desc' => 'Dynamic adaptive triggers, built-in microphone, and signature comfort in iconic colorways.',
                'tags' => ['Wireless'],
                'variant_type' => 'controller',
            ],
            [
                'name' => 'Logitech G Pro X Superlight 2 Wireless',
                'category' => 'Gaming & Consoles',
                'brand' => 'Logitech',
                'price' => 149.99,
                'compare_at_price' => 159.99,
                'type' => 'simple',
                'stock' => 40,
                'featured' => false,
                'desc' => '60g ultra-lightweight esports mouse with HERO 2 sensor and LIGHTFORCE hybrid optical switches.',
                'tags' => ['Trending'],
            ],

            // Men's Clothing
            [
                'name' => 'Nike Dri-FIT UV Running Shirt',
                'category' => "Men's Clothing",
                'brand' => 'Nike',
                'price' => 42.00,
                'compare_at_price' => 55.00,
                'type' => 'variable',
                'stock' => 100,
                'featured' => true,
                'desc' => 'Breathable, sweat-wicking knit fabric with UVA/UVB sun protection for morning runs.',
                'tags' => ['Best Seller', 'Eco Friendly'],
                'variant_type' => 'clothing',
            ],
            [
                'name' => 'Adidas Essentials 3-Stripes Track Jacket',
                'category' => "Men's Clothing",
                'brand' => 'Adidas',
                'price' => 55.00,
                'compare_at_price' => 70.00,
                'type' => 'variable',
                'stock' => 80,
                'featured' => false,
                'desc' => 'Classic retro tracksuit jacket crafted from 100% recycled tricot with ribbed collar and cuffs.',
                'tags' => ['Trending'],
                'variant_type' => 'clothing',
            ],
            [
                'name' => 'Nike Club Fleece Pullover Hoodie',
                'category' => "Men's Clothing",
                'brand' => 'Nike',
                'price' => 65.00,
                'compare_at_price' => 75.00,
                'type' => 'variable',
                'stock' => 90,
                'featured' => true,
                'desc' => 'Brushed fleece for everyday warmth and soft comfort with embroidered Futura logo.',
                'tags' => ['Best Seller'],
                'variant_type' => 'clothing',
            ],
            [
                'name' => 'Zara Slim Fit Stretch Oxford Shirt',
                'category' => "Men's Clothing",
                'brand' => 'Zara',
                'price' => 49.90,
                'compare_at_price' => 59.90,
                'type' => 'variable',
                'stock' => 65,
                'featured' => false,
                'desc' => 'Tailored slim fit in premium stretch cotton with button-down collar.',
                'tags' => ['New Arrival'],
                'variant_type' => 'clothing',
            ],
            [
                'name' => 'Puma Modern Basics Cargo Pants',
                'category' => "Men's Clothing",
                'brand' => 'Puma',
                'price' => 50.00,
                'compare_at_price' => 65.00,
                'type' => 'variable',
                'stock' => 55,
                'featured' => false,
                'desc' => 'Utility style meets street comfort with multiple cargo pockets and elasticated cuffs.',
                'tags' => ['Trending'],
                'variant_type' => 'clothing',
            ],

            // Women's Clothing
            [
                'name' => 'Nike Zenvy Gentle-Support High-Waisted Leggings',
                'category' => "Women's Clothing",
                'brand' => 'Nike',
                'price' => 95.00,
                'compare_at_price' => 110.00,
                'type' => 'variable',
                'stock' => 70,
                'featured' => true,
                'desc' => 'InfinaSoft fabric feels lightweight yet passes squat test effortlessly with drop-in back pocket.',
                'tags' => ['Best Seller'],
                'variant_type' => 'clothing',
            ],
            [
                'name' => 'Adidas Tiro 24 Training Pants',
                'category' => "Women's Clothing",
                'brand' => 'Adidas',
                'price' => 45.00,
                'compare_at_price' => 55.00,
                'type' => 'variable',
                'stock' => 85,
                'featured' => false,
                'desc' => 'AEROREADY moisture management with ankle zips for effortless transitions over shoes.',
                'tags' => ['On Sale'],
                'variant_type' => 'clothing',
            ],
            [
                'name' => 'Zara Oversized Double-Breasted Blazer',
                'category' => "Women's Clothing",
                'brand' => 'Zara',
                'price' => 119.00,
                'compare_at_price' => 139.00,
                'type' => 'simple',
                'stock' => 40,
                'featured' => true,
                'desc' => 'Structured shoulder blazer with notched lapels and front welt flap pockets.',
                'tags' => ['Trending', 'Premium'],
            ],

            // Footwear & Sneakers
            [
                'name' => 'Nike Air Force 1 07 Classic',
                'category' => 'Footwear & Sneakers',
                'brand' => 'Nike',
                'price' => 115.00,
                'compare_at_price' => 130.00,
                'type' => 'variable',
                'stock' => 110,
                'featured' => true,
                'desc' => 'The radiance lives on with crisp leather edges, bold accents, and air-cushioned comfort.',
                'tags' => ['Best Seller', 'Trending'],
                'variant_type' => 'shoes',
            ],
            [
                'name' => 'Adidas Samba OG Shoes',
                'category' => 'Footwear & Sneakers',
                'brand' => 'Adidas',
                'price' => 100.00,
                'compare_at_price' => 120.00,
                'type' => 'variable',
                'stock' => 95,
                'featured' => true,
                'desc' => 'Streetwear staple with full grain leather upper, suede T-toe, and signature gum rubber sole.',
                'tags' => ['Best Seller', 'Trending'],
                'variant_type' => 'shoes',
            ],
            [
                'name' => 'Nike Pegasus 41 Road Running Shoes',
                'category' => 'Footwear & Sneakers',
                'brand' => 'Nike',
                'price' => 140.00,
                'compare_at_price' => 160.00,
                'type' => 'variable',
                'stock' => 60,
                'featured' => false,
                'desc' => 'Responsive cushioning with dual Air Zoom units and all-new ReactX foam midsole.',
                'tags' => ['New Arrival'],
                'variant_type' => 'shoes',
            ],
            [
                'name' => 'Puma Suede Classic XXI Sneakers',
                'category' => 'Footwear & Sneakers',
                'brand' => 'Puma',
                'price' => 75.00,
                'compare_at_price' => 85.00,
                'type' => 'variable',
                'stock' => 50,
                'featured' => false,
                'desc' => 'Iconic silhouette with premium suede upper and modern comfort sockliner.',
                'tags' => ['On Sale'],
                'variant_type' => 'shoes',
            ],

            // Smart Home & Lifestyle
            [
                'name' => 'Philips Hue Smart Bridge & Bulb Starter Kit',
                'category' => 'Smart Home Devices',
                'brand' => 'Apple',
                'price' => 159.99,
                'compare_at_price' => 189.99,
                'type' => 'simple',
                'stock' => 35,
                'featured' => true,
                'desc' => '16 million colors with Matter and Apple HomeKit support for automated ambient lighting.',
                'tags' => ['Wireless'],
            ],
            [
                'name' => 'Sonos Era 100 Smart Speaker',
                'category' => 'Smart Home Devices',
                'brand' => 'Bose',
                'price' => 249.00,
                'compare_at_price' => 279.00,
                'type' => 'simple',
                'stock' => 28,
                'featured' => false,
                'desc' => 'Next-gen acoustic architecture delivers finely tuned stereo separation with voice control.',
                'tags' => ['Premium', 'Wireless'],
            ],
            [
                'name' => 'Breville Barista Touch Espresso Machine',
                'category' => 'Kitchen Appliances',
                'brand' => 'Dell',
                'price' => 999.95,
                'compare_at_price' => 1099.95,
                'type' => 'simple',
                'stock' => 12,
                'featured' => true,
                'desc' => 'Automated touchscreen coffee preparation with integrated conical burr grinder and thermo-jet heating.',
                'tags' => ['Premium'],
            ],
            [
                'name' => 'Ninja Air Fryer Pro 4-in-1',
                'category' => 'Kitchen Appliances',
                'brand' => 'Samsung',
                'price' => 119.99,
                'compare_at_price' => 139.99,
                'type' => 'simple',
                'stock' => 45,
                'featured' => false,
                'desc' => 'Crisp with up to 75% less fat than traditional frying methods using Air Crisp technology.',
                'tags' => ['Best Seller'],
            ],
            [
                'name' => 'Ergonomic Mesh Office Chair with Lumbar Support',
                'category' => 'Workspace & Office',
                'brand' => 'Logitech',
                'price' => 289.00,
                'compare_at_price' => 349.00,
                'type' => 'simple',
                'stock' => 22,
                'featured' => true,
                'desc' => 'Breathable dual-mesh backrest with 3D adjustable armrests and synchronous tilt mechanism.',
                'tags' => ['Trending'],
            ],
        ];

        $createdProducts = [];

        foreach ($catalog as $item) {
            $category = $categories[$item['category']] ?? null;
            $brand = $brands[$item['brand']] ?? null;

            $product = Product::create([
                'category_id' => $category?->id,
                'brand_id' => $brand?->id,
                'name' => $item['name'],
                'slug' => Str::slug($item['name']) . '-' . rand(100, 999),
                'sku' => Str::upper(Str::slug(Str::substr($item['name'], 0, 8))) . '-' . rand(1000, 9999),
                'type' => $item['type'],
                'short_description' => Str::substr($item['desc'], 0, 120) . '...',
                'description' => $item['desc'],
                'price' => $item['price'],
                'compare_at_price' => $item['compare_at_price'] ?? null,
                'cost_price' => round($item['price'] * 0.55, 2),
                'stock_quantity' => $item['stock'],
                'low_stock_threshold' => 5,
                'manage_stock' => true,
                'is_in_stock' => $item['stock'] > 0,
                'status' => 'published',
                'is_featured' => $item['featured'],
                'views_count' => rand(120, 3500),
                'sales_count' => rand(15, 450),
            ]);

            // Attach Tags
            if (! empty($item['tags'])) {
                $tagIds = [];
                foreach ($item['tags'] as $tagName) {
                    if (isset($tags[$tagName])) {
                        $tagIds[] = $tags[$tagName]->id;
                    }
                }
                $product->tags()->sync($tagIds);
            }

            // Generate Variants if Variable
            if ($product->type === 'variable' && ! empty($item['variant_type'])) {
                $this->generateVariantsForProduct($matrixService, $product, $item['variant_type'], $attributes);
            }

            $createdProducts[] = $product;
        }

        return $createdProducts;
    }

    protected function generateVariantsForProduct(VariantMatrixService $matrixService, Product $product, string $variantType, array $attributes): void
    {
        $attributeValueIds = [];

        if ($variantType === 'phone') {
            // Colors (Space Gray, Black) x Storage (128GB, 256GB, 512GB)
            $colors = [$attributes['color']['Space Gray']->id, $attributes['color']['Black']->id];
            $storage = [$attributes['storage']['128GB']->id, $attributes['storage']['256GB']->id, $attributes['storage']['512GB']->id];
            $attributeValueIds = [$colors, $storage];
        } elseif ($variantType === 'laptop') {
            // Space Gray, Black x 512GB, 1TB
            $colors = [$attributes['color']['Space Gray']->id, $attributes['color']['Black']->id];
            $storage = [$attributes['storage']['512GB']->id, $attributes['storage']['1TB']->id];
            $attributeValueIds = [$colors, $storage];
        } elseif ($variantType === 'audio' || $variantType === 'controller') {
            // Black, White, Navy Blue
            $colors = [$attributes['color']['Black']->id, $attributes['color']['White']->id, $attributes['color']['Navy Blue']->id];
            $attributeValueIds = [$colors];
        } elseif ($variantType === 'clothing') {
            // Black, Navy Blue, Crimson Red x S, M, L, XL
            $colors = [$attributes['color']['Black']->id, $attributes['color']['Navy Blue']->id, $attributes['color']['Crimson Red']->id];
            $sizes = [$attributes['size']['S']->id, $attributes['size']['M']->id, $attributes['size']['L']->id, $attributes['size']['XL']->id];
            $attributeValueIds = [$colors, $sizes];
        } elseif ($variantType === 'shoes') {
            // White, Black x US 9, US 10, US 11
            $colors = [$attributes['color']['White']->id, $attributes['color']['Black']->id];
            $shoes = [$attributes['shoe']['US 9']->id, $attributes['shoe']['US 10']->id, $attributes['shoe']['US 11']->id];
            $attributeValueIds = [$colors, $shoes];
        }

        if (! empty($attributeValueIds)) {
            $matrixService->generateMatrix($product, $attributeValueIds, [
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'stock_quantity' => 15,
                'manage_stock' => true,
            ]);
        }
    }

    protected function seedReviewsAndWishlists(array $products): void
    {
        $users = User::take(5)->get();
        if ($users->isEmpty()) {
            return;
        }

        $comments = [
            5 => [
                'Absolutely phenomenal! Exceeded all my expectations in terms of build quality and performance.',
                'Best purchase of the year! Highly recommend to anyone looking for premium quality.',
                'Super fast delivery and top-notch packaging. Works like a charm!',
            ],
            4 => [
                'Very good product overall. Solid battery life and smooth experience, minor nitpicks.',
                'Great value for money. Looks sleek and premium in person.',
            ],
            3 => [
                'Decent product for the price. Does the job as described.',
            ],
        ];

        foreach ($products as $product) {
            // Add 1-3 reviews per product
            $reviewCount = rand(1, 3);
            for ($i = 0; $i < $reviewCount; $i++) {
                $user = $users->random();
                $rating = rand(4, 5); // mostly positive ratings
                $commentList = $comments[$rating] ?? $comments[5];
                $comment = $commentList[array_rand($commentList)];

                ProductReview::create([
                    'product_id' => $product->id,
                    'user_id' => $user->id,
                    'rating' => $rating,
                    'title' => 'Verified Review',
                    'comment' => $comment,
                    'is_verified_purchase' => true,
                    'status' => 'approved',
                ]);
            }

            // Recalculate stats
            $product->updateRatingStats();

            // Randomly add to wishlist of user 1
            if (rand(0, 1) === 1 && isset($users[0])) {
                $product->toggleBookmark($users[0], 'wishlist');
            }
        }
    }
}
