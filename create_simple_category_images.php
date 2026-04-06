<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ক্যাটাগরি অনুযায়ী ইমেজ তৈরি করা হচ্ছে...\n";
echo "=====================================\n";

// ক্যাটাগরি অনুযায়ী ইমেজ তৈরি
$categoryImages = [
    'electronics' => 'Electronics Device',
    'mobile-phones' => 'Smartphone',
    'laptops' => 'Laptop Computer', 
    'tablets' => 'Tablet Device',
    'fashion' => 'Fashion Item',
    'home-living' => 'Home Product',
    'beauty-personal-care' => 'Beauty Product',
    'groceries' => 'Food Item',
    'sports-outdoors' => 'Sports Equipment',
    'books-media' => 'Book Media',
    'toys-games' => 'Toy Game',
    'computer' => 'Computer Hardware'
];

foreach ($categoryImages as $category => $text) {
    echo "Creating image for: {$category} - {$text}\n";
    
    // PowerShell script তৈরি
    $psContent = "Add-Type -AssemblyName System.Drawing\n";
    $psContent .= '$bmp = New-Object System.Drawing.Bitmap 400,400' . "\n";
    $psContent .= '$graphics = [System.Drawing.Graphics]::FromImage($bmp)' . "\n";
    
    // ব্যাকগ্রাউন্ড কালার
    $colors = [
        'electronics' => '52,152,219',
        'mobile-phones' => '155,89,182', 
        'laptops' => '41,128,185',
        'tablets' => '142,68,173',
        'fashion' => '231,76,60',
        'home-living' => '46,204,113',
        'beauty-personal-care' => '241,196,15',
        'groceries' => '230,126,34',
        'sports-outdoors' => '26,188,156',
        'books-media' => '149,165,166',
        'toys-games' => '192,57,43',
        'computer' => '44,62,80'
    ];
    
    $color = $colors[$category] ?? '240,240,240';
    $psContent .= '$graphics.Clear([System.Drawing.Color]::FromArgb(' . $color . '))' . "\n";
    
    // টেক্সট যোগ করুন
    $psContent .= '$font = New-Object System.Drawing.Font("Arial",16,[System.Drawing.FontStyle]::Bold)' . "\n";
    $psContent .= '$brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)' . "\n";
    $psContent .= '$text = "' . $text . '"' . "\n";
    $psContent .= '$textSize = $graphics.MeasureString($text, $font)' . "\n";
    $psContent .= '$x = (400 - $textSize.Width) / 2' . "\n";
    $psContent .= '$y = (400 - $textSize.Height) / 2' . "\n";
    $psContent .= '$graphics.DrawString($text, $font, $brush, $x, $y)' . "\n";
    
    // ক্যাটাগরি নাম
    $psContent .= '$smallFont = New-Object System.Drawing.Font("Arial",12)' . "\n";
    $psContent .= '$categoryText = "' . $category . '"' . "\n";
    $psContent .= '$categorySize = $graphics.MeasureString($categoryText, $smallFont)' . "\n";
    $psContent .= '$catX = (400 - $categorySize.Width) / 2' . "\n";
    $psContent .= '$catY = $y + 40' . "\n";
    $psContent .= '$graphics.DrawString($categoryText, $smallFont, $brush, $catX, $catY)' . "\n";
    
    // সেভ করুন
    $psContent .= '$bmp.Save("f:/xampp/htdocs/ecomerce_e/public/images/' . $category . '-placeholder.jpg", [System.Drawing.Imaging.ImageFormat]::Jpeg)' . "\n";
    $psContent .= '$graphics.Dispose()' . "\n";
    $psContent .= '$bmp.Dispose()' . "\n";
    $psContent .= 'Write-Host "Created: ' . $category . '-placeholder.jpg"' . "\n";
    
    // ফাইলে সেভ করুন এবং রান করুন
    file_put_contents("temp_{$category}.ps1", $psContent);
    shell_exec("powershell -ExecutionPolicy Bypass -File temp_{$category}.ps1 2>nul");
    unlink("temp_{$category}.ps1");
}

echo "\nইমেজ তৈরি সম্পন্ন!\n";

// প্রোডাক্টগুলোকে আপডেট করুন
echo "\nপ্রোডাক্টগুলোকে আপডেট করা হচ্ছে...\n";

$products = App\Models\Product::with(['primaryImage', 'category'])->get();

foreach ($products as $product) {
    if ($product->category && $product->primaryImage) {
        $categorySlug = $product->category->slug;
        $imagePath = "{$categorySlug}-placeholder.jpg";
        
        if (file_exists(public_path("images/{$imagePath}"))) {
            $product->primaryImage->update(['image_path' => "images/{$imagePath}"]);
            echo "✓ {$product->name} -> {$imagePath}\n";
        }
    }
}

echo "\n✅ সম্পন্ন! ওয়েবসাইট: http://localhost:8000/\n";
