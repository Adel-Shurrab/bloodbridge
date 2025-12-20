<?php

/**
 * Application Constants
 * 
 * This file contains all application-wide constants used throughout the system.
 * Centralizing constants here ensures consistency and makes maintenance easier.
 */

return [
    // ============================================
    // USER & AUTHENTICATION CONSTANTS
    // ============================================
    
    'user' => [
        'roles' => [
            'DONOR' => 'donor',
            'ORGANIZATION' => 'organization',
            'ADMIN' => 'admin',
        ],
        'default_role' => 'donor',
        'default_is_active' => true,
    ],

    // ============================================
    // BLOOD TYPE CONSTANTS
    // ============================================
    
    'blood_types' => [
        'O_POSITIVE' => 'O+',
        'O_NEGATIVE' => 'O-',
        'A_POSITIVE' => 'A+',
        'A_NEGATIVE' => 'A-',
        'B_POSITIVE' => 'B+',
        'B_NEGATIVE' => 'B-',
        'AB_POSITIVE' => 'AB+',
        'AB_NEGATIVE' => 'AB-',
    ],

    'blood_types_list' => [
        'O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'
    ],

    // ============================================
    // GENDER CONSTANTS
    // ============================================
    
    'gender' => [
        'MALE' => 'male',
        'FEMALE' => 'female',
    ],

    'gender_list' => [
        'male', 'female'
    ],

    // ============================================
    // ORGANIZATION CONSTANTS
    // ============================================
    
    'organization' => [
        'approval_status' => [
            'PENDING' => 'pending',
            'APPROVED' => 'approved',
            'REJECTED' => 'rejected',
        ],
        'default_approval_status' => 'pending',
        'default_daily_capacity' => 0,
        'default_total_request_created' => 0,
        'default_total_donation_verified' => 0,
    ],

    'organization_approval_status_list' => [
        'pending', 'approved', 'rejected'
    ],

    // ============================================
    // BLOOD REQUEST CONSTANTS
    // ============================================
    
    'blood_request' => [
        'status' => [
            'PENDING' => 'pending',
            'BROADCASTED' => 'broadcasted',
            'MATCHED' => 'matched',
            'FULFILLED' => 'fulfilled',
            'CANCELLED' => 'cancelled',
            'EXPIRED' => 'expired',
        ],
        'default_status' => 'pending',
        'urgency_level' => [
            'LOW' => 'low',
            'MEDIUM' => 'medium',
            'HIGH' => 'high',
            'CRITICAL' => 'critical',
        ],
        'default_urgency_level' => 'low',
        'default_search_radius_km' => 10,
        'default_donors_accepted' => 0,
        'default_donors_completed' => 0,
    ],

    'blood_request_status_list' => [
        'pending', 'broadcasted', 'matched', 'fulfilled', 'cancelled', 'expired'
    ],

    'blood_request_urgency_level_list' => [
        'low', 'medium', 'high', 'critical'
    ],

    // ============================================
    // APPOINTMENT CONSTANTS
    // ============================================
    
    'appointment' => [
        'status' => [
            'SCHEDULED' => 'scheduled',
            'COMPLETED' => 'completed',
            'CANCELLED' => 'cancelled',
        ],
        'default_status' => 'scheduled',
    ],

    'appointment_status_list' => [
        'scheduled', 'completed', 'cancelled'
    ],

    // ============================================
    // REQUEST RESPONSE CONSTANTS
    // ============================================
    
    'request_response' => [
        'status' => [
            'PENDING' => 'pending',
            'ACCEPTED' => 'accepted',
            'DECLINED' => 'declined',
            'COMPLETED' => 'completed',
            'IGNORED' => 'ignored',
        ],
        'default_status' => 'pending',
        'default_verification_qr_code' => false,
    ],

    'request_response_status_list' => [
        'pending', 'accepted', 'declined', 'completed', 'ignored'
    ],

    // ============================================
    // DONOR HEALTH PROFILE CONSTANTS
    // ============================================
    
    'donor_health_profile' => [
        'default_chronic_disease' => false,
        'default_recent_donation' => false,
        'default_infection' => false,
        'default_is_eligible' => true,
        'default_has_recent_surgery' => false,
        'default_total_donations' => 0,
    ],

    // ============================================
    // ELIGIBILITY CONSTANTS
    // ============================================
    
    'eligibility' => [
        'check_type' => [
            'REGISTRATION' => 'registration',
            'REQUEST_ACCEPTANCE' => 'request_acceptance',
            'PROFILE_UPDATE' => 'profile_update',
        ],
        'status' => [
            'ELIGIBLE' => 'eligible',
            'INELIGIBLE' => 'ineligible',
        ],
    ],

    'eligibility_check_type_list' => [
        'registration', 'request_acceptance', 'profile_update'
    ],

    'eligibility_status_list' => [
        'eligible', 'ineligible'
    ],

    // ============================================
    // NOTIFICATION CONSTANTS
    // ============================================
    
    'notification' => [
        'type' => [
            'EMAIL' => 'email',
            'SMS' => 'sms',
            'PUSH' => 'push',
        ],
        'default_type' => 'email',
    ],

    'notification_type_list' => [
        'email', 'sms', 'push'
    ],

    // ============================================
    // ACHIEVEMENT CONSTANTS
    // ============================================
    
    'achievement' => [
        'criteria_type' => [
            'DONATIONS' => 'donations',
            'POINTS' => 'points',
        ],
        'default_points_rewards' => 0,
        'default_criteria_value' => 0,
        'default_display_order' => 0,
    ],

    'achievement_criteria_type_list' => [
        'donations', 'points'
    ],

    // ============================================
    // DONOR CONSTANTS
    // ============================================
    
    'donor' => [
        'default_points' => 0,
        'default_level' => 1,
        'national_id_length' => 9,
    ],

    // ============================================
    // ADMIN PANEL CONSTANTS
    // ============================================
    
    'admin' => [
        'panel_id' => 'admin',
        'panel_path' => 'admin',
    ],

    // ============================================
    // LOCALE & LANGUAGE CONSTANTS
    // ============================================
    
    'locale' => [
        'default' => 'ar',
        'fallback' => 'ar',
        'direction' => 'rtl', // Right-to-left for Arabic
        'supported' => ['ar', 'en'],
    ],
];

