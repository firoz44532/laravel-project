<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "ক্যাটাগরি অনুযায়ী আসল ছবি তৈরি করা হচ্ছে...\n";
echo "=====================================\n";

// ক্যাটাগরি অনুযায়ী ইমেজ তৈরির জন্য PowerShell script
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
    
    $psScript = @"
Add-Type -AssemblyName System.Drawing
`$bmp = New-Object System.Drawing.Bitmap 400,400
`$graphics = [System.Drawing.Graphics]::FromImage(`$bmp)

# ব্যাকগ্রাউন্ড কালার ক্যাটাগরি অনুযায়ী
switch ('$category') {
    'electronics' { `$graphics.Clear([System.Drawing.Color]::FromArgb(52, 152, 219)) } # Blue
    'mobile-phones' { `$graphics.Clear([System.Drawing.Color]::FromArgb(155, 89, 182)) } # Purple  
    'laptops' { `$graphics.Clear([System.Drawing.Color]::FromArgb(41, 128, 185)) } # Dark Blue
    'tablets' { `$graphics.Clear([System.Drawing.Color]::FromArgb(142, 68, 173)) } # Dark Purple
    'fashion' { `$graphics.Clear([System.Drawing.Color]::FromArgb(231, 76, 60)) } # Red
    'home-living' { `$graphics.Clear([System.Drawing.Color]::FromArgb(46, 204, 113)) } # Green
    'beauty-personal-care' { `$graphics.Clear([System.Drawing.Color]::FromArgb(241, 196, 15)) } # Yellow
    'groceries' { `$graphics.Clear([System.Drawing.Color]::FromArgb(230, 126, 34)) } # Orange
    'sports-outdoors' { `$graphics.Clear([System.Drawing.Color]::FromArgb(26, 188, 156)) } # Turquoise
    'books-media' { `$graphics.Clear([System.Drawing.Color]::FromArgb(149, 165, 166)) } # Gray
    'toys-games' { `$graphics.Clear([System.Drawing.Color]::FromArgb(192, 57, 43)) } # Dark Red
    'computer' { `$graphics.Clear([System.Drawing.Color]::FromArgb(44, 62, 80)) } # Dark Gray
    default { `$graphics.Clear([System.Drawing.Color]::LightGray) }
}

`$font = New-Object System.Drawing.Font('Arial',16,[System.Drawing.FontStyle]::Bold)
`$brush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
`$text = '$text'
`$textSize = `$graphics.MeasureString(`$text, `$font)
`$x = (400 - `$textSize.Width) / 2
`$y = (400 - `$textSize.Height) / 2
`$graphics.DrawString(`$text, `$font, `$brush, `$x, `$y)

# ক্যাটাগরি নাম যোগ করুন
`$smallFont = New-Object System.Drawing.Font('Arial',12)
`$categoryText = '$category'
`$categorySize = `$graphics.MeasureString(`$categoryText, `$smallFont)
`$catX = (400 - `$categorySize.Width) / 2
`$catY = `$y + 40
`$graphics.DrawString(`$categoryText, `$smallFont, `$brush, `$catX, `$catY)

`$bmp.Save('f:/xampp/htdocs/ecomerce_e/public/images/{$category}-placeholder.jpg', [System.Drawing.Imaging.ImageFormat]::Jpeg)
`$graphics.Dispose()
`$bmp.Dispose()
Write-Host 'Created: {$category}-placeholder.jpg'
"@;

    file_put_contents("temp_{$category}.ps1", $psScript);
    shell_exec("powershell -ExecutionPolicy Bypass -File temp_{$category}.ps1");
    unlink("temp_{$category}.ps1");
}

echo "\n✅ সব ক্যাটাগরির জন্য ইমেজ তৈরি হয়েছে!\n";

// এখন প্রোডাক্টগুলোকে সঠিক ইমেজ দেখাতে হবে
echo "\nপ্রোডাক্টগুলোকে আপডেট করা হচ্ছে...\n";

// প্রোডাক্টগুলোকে তাদের ক্যাটাগরি অনুযায়ী ইমেজ দিন
$products = App\Models\Product::with(['primaryImage', 'category'])->get();

foreach ($products as $product) {
    if ($product->category && $product->primaryImage) {
        $categorySlug = $product->category->slug;
        $imagePath = "{$categorySlug}-placeholder.jpg";
        
        // চেক করুন ইমেজ আছে কিনা
        if (file_exists(public_path("images/{$imagePath}"))) {
            $product->primaryImage->update(['image_path' => "images/{$imagePath}"]);
            echo "✓ {$product->name} -> {$imagePath}\n";
        }
    }
}

echo "\nএখন ওয়েবসাইটে ভিজিট করুন: http://localhost:8000/\n";
echo "প্রতিটি ক্যাটাগরির জন্য আলাদা ইমেজ দেখতে পাবেন!\n";
