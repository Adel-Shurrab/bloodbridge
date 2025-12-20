<?php

/**
 * Arabic Translations for Application Constants
 */

return [
    // User Roles
    'user_roles' => [
        'donor' => 'متبرع',
        'organization' => 'منظمة',
        'admin' => 'مدير',
    ],

    // Blood Types
    'blood_types' => [
        'O+' => 'O+',
        'O-' => 'O-',
        'A+' => 'A+',
        'A-' => 'A-',
        'B+' => 'B+',
        'B-' => 'B-',
        'AB+' => 'AB+',
        'AB-' => 'AB-',
    ],

    // Gender
    'gender' => [
        'male' => 'ذكر',
        'female' => 'أنثى',
    ],

    // Organization Approval Status
    'organization_approval_status' => [
        'pending' => 'قيد الانتظار',
        'approved' => 'موافق عليه',
        'rejected' => 'مرفوض',
    ],

    // Blood Request Status
    'blood_request_status' => [
        'pending' => 'قيد الانتظار',
        'broadcasted' => 'تم البث',
        'matched' => 'تم التطابق',
        'fulfilled' => 'تم الوفاء',
        'cancelled' => 'ملغي',
        'expired' => 'منتهي الصلاحية',
    ],

    // Blood Request Urgency Levels
    'blood_request_urgency' => [
        'low' => 'منخفض',
        'medium' => 'متوسط',
        'high' => 'عالي',
        'critical' => 'حرج',
    ],

    // Appointment Status
    'appointment_status' => [
        'scheduled' => 'مجدول',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
    ],

    // Request Response Status
    'request_response_status' => [
        'pending' => 'قيد الانتظار',
        'accepted' => 'مقبول',
        'declined' => 'مرفوض',
        'completed' => 'مكتمل',
        'ignored' => 'تم تجاهله',
    ],

    // Eligibility Status
    'eligibility_status' => [
        'eligible' => 'مؤهل',
        'ineligible' => 'غير مؤهل',
    ],

    // Eligibility Check Types
    'eligibility_check_type' => [
        'registration' => 'التسجيل',
        'request_acceptance' => 'قبول الطلب',
        'profile_update' => 'تحديث الملف الشخصي',
    ],

    // Notification Types
    'notification_type' => [
        'email' => 'بريد إلكتروني',
        'sms' => 'رسالة نصية',
        'push' => 'إشعار فوري',
    ],

    // Achievement Criteria Types
    'achievement_criteria_type' => [
        'donations' => 'التبرعات',
        'points' => 'النقاط',
    ],
];
