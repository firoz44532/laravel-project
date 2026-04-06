<?php

namespace App\Services;

class WhatsAppService
{
    /**
     * Get WhatsApp business number
     */
    public static function getPhoneNumber()
    {
        // You can get this from database, .env file, or settings
        return env('WHATSAPP_PHONE_NUMBER', '+1234567890');
    }
    
    /**
     * Generate WhatsApp URL for chat
     */
    public static function generateChatUrl($message = '')
    {
        $phoneNumber = self::getPhoneNumber();
        $cleanNumber = preg_replace('/[^\d]/', '', $phoneNumber);
        
        if ($message) {
            $encodedMessage = urlencode($message);
            return "https://wa.me/{$cleanNumber}?text={$encodedMessage}";
        }
        
        return "https://wa.me/{$cleanNumber}";
    }
    
    /**
     * Generate WhatsApp URL with pre-filled message for product inquiry
     */
    public static function generateProductInquiryUrl($productName, $productUrl = '')
    {
        $message = "Hi! I'm interested in this product: {$productName}";
        if ($productUrl) {
            $message .= "\nProduct link: {$productUrl}";
        }
        $message .= "\n\nCan you provide more information?";
        
        return self::generateChatUrl($message);
    }
    
    /**
     * Generate WhatsApp URL for order support
     */
    public static function generateOrderSupportUrl($orderNumber)
    {
        $message = "Hi! I need help with my order #{$orderNumber}. Can you please assist me?";
        return self::generateChatUrl($message);
    }
    
    /**
     * Check if WhatsApp is enabled
     */
    public static function isEnabled()
    {
        return env('WHATSAPP_ENABLED', true);
    }
    
    /**
     * Get WhatsApp business hours
     */
    public static function getBusinessHours()
    {
        return [
            'sunday' => ['9:00 AM - 6:00 PM'],
            'monday' => ['9:00 AM - 6:00 PM'],
            'tuesday' => ['9:00 AM - 6:00 PM'],
            'wednesday' => ['9:00 AM - 6:00 PM'],
            'thursday' => ['9:00 AM - 6:00 PM'],
            'friday' => ['9:00 AM - 6:00 PM'],
            'saturday' => ['10:00 AM - 4:00 PM'],
        ];
    }
    
    /**
     * Check if currently within business hours
     */
    public static function isWithinBusinessHours()
    {
        $currentDay = strtolower(date('l'));
        $currentTime = date('H:i');
        
        $businessHours = self::getBusinessHours();
        
        if (!isset($businessHours[$currentDay])) {
            return false;
        }
        
        $hours = $businessHours[$currentDay][0];
        $timeRange = explode(' - ', $hours);
        
        if (count($timeRange) !== 2) {
            return false;
        }
        
        $startTime = strtotime($timeRange[0]);
        $endTime = strtotime($timeRange[1]);
        $currentTimestamp = strtotime($currentTime);
        
        return $currentTimestamp >= $startTime && $currentTimestamp <= $endTime;
    }
}
