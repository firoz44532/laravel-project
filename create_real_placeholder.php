<?php

// একটি আসল JPEG placeholder image তৈরি করুন
$imageWidth = 400;
$imageHeight = 400;

// ইমেজ তৈরি করুন
$image = imagecreatetruecolor($imageWidth, $imageHeight);

// ব্যাকগ্রাউন্ড কালার সেট করুন (light gray)
$bgColor = imagecolorallocate($image, 240, 240, 240);
imagefill($image, 0, 0, $bgColor);

// টেক্সট কালার (dark gray)
$textColor = imagecolorallocate($image, 100, 100, 100);

// টেক্সট যোগ করুন
$text = "Product Image";
$fontSize = 20;
$angle = 0;

// টেক্সটের সাইজ ক্যালকুলেট করুন
$textBox = imagettfbbox($fontSize, $angle, 'arial.ttf', $text);
$textWidth = $textBox[2] - $textBox[0];
$textHeight = $textBox[1] - $textBox[7];

// টেক্সট সেন্টার করুন
$x = ($imageWidth - $textWidth) / 2;
$y = ($imageHeight + $textHeight) / 2;

// টেক্সট যোগ করুন (যদি arial.ttf না থাকে)
if (file_exists('C:\Windows\Fonts\arial.ttf')) {
    imagettftext($image, $fontSize, $angle, $x, $y, $textColor, 'C:\Windows\Fonts\arial.ttf', $text);
} else {
    // বিল্ট-ইন ফন্ট ব্যবহার করুন
    $text = "Product Image";
    $textWidth = imagefontwidth(5) * strlen($text);
    $textHeight = imagefontheight(5);
    $x = ($imageWidth - $textWidth) / 2;
    $y = ($imageHeight - $textHeight) / 2;
    imagestring($image, 5, $x, $y, $text, $textColor);
}

// ইমেজ সেভ করুন
$savePath = 'public/images/placeholder-product.jpg';
imagejpeg($image, $savePath, 90);

// মেমরি ফ্রি করুন
imagedestroy($image);

echo "✅ আসল JPEG placeholder image তৈরি হয়েছে!\n";
echo "পাথ: {$savePath}\n";
echo "ফাইল সাইজ: " . filesize($savePath) . " bytes\n";
echo "ফাইল টাইপ: " . mime_content_type($savePath) . "\n";

// টেস্ট করুন
echo "\nটেস্ট:\n";
$imageUrl = 'http://localhost:8000/images/placeholder-product.jpg';
echo "URL: {$imageUrl}\n";
echo "এখন ওয়েবসাইটে ইমেজ দেখা যাবে!\n";
