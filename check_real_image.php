<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "আসল ইমেজ ফাইল চেক:\n";
echo "====================\n";

$imagePath = 'images/placeholder-product.jpg';
$fullPath = public_path($imagePath);

echo "ফাইল পাথ: {$fullPath}\n";
echo "ফাইল আছে: " . (file_exists($fullPath) ? 'হ্যাঁ' : 'না') . "\n";
echo "ফাইল সাইজ: " . (file_exists($fullPath) ? filesize($fullPath) . ' bytes' : 'N/A') . "\n";

// ফাইলের কন্টেন্ট দেখান
if (file_exists($fullPath)) {
    echo "ফাইল টাইপ: " . mime_content_type($fullPath) . "\n";
    
    // ফাইলের প্রথম কয়েকটি বাইট দেখান
    $handle = fopen($fullPath, 'rb');
    $header = fread($handle, 20);
    fclose($handle);
    
    echo "ফাইল হেডার (hex): " . bin2hex($header) . "\n";
    
    // JPEG ফাইল কিনা চেক করুন
    if (substr($header, 0, 2) === "\xFF\xD8") {
        echo "ফাইল টাইপ: ✅ JPEG ইমেজ\n";
    } else {
        echo "ফাইল টাইপ: ❌ JPEG নয়\n";
    }
}

echo "\nওয়েবে টেস্ট:\n";
$imageUrl = 'http://localhost:8000/' . $imagePath;
echo "URL: {$imageUrl}\n";

// কার্ল দিয়ে টেস্ট করুন
$ch = curl_init($imageUrl);
curl_setopt($ch, CURLOPT_NOBODY, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);

$headers = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP কোড: {$httpCode}\n";
if ($httpCode === 200) {
    echo "স্ট্যাটাস: ✅ ওয়েবে ইমেজ একসেসিবল\n";
} else {
    echo "স্ট্যাটাস: ❌ ওয়েবে ইমেজ একসেসিবল নয়\n";
}
