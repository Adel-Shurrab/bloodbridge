<?php

return [
    'required' => 'The :attribute field is required.',
    'email' => 'The :attribute must be a valid email address.',
    'boolean' => 'The :attribute field must be true or false.',
    'numeric' => 'The :attribute must be a number.',
    'integer' => 'The :attribute must be an integer.',
    'date' => 'The :attribute is not a valid date.',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.',
    'in' => 'The selected :attribute is invalid.',
    'digits' => 'The :attribute must be :digits digits.',

    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'file' => 'The :attribute must be at least :min kilobytes.',
        'string' => 'The :attribute must be at least :min characters.',
        'array' => 'The :attribute must have at least :min items.',
    ],
    'max' => [
        'numeric' => 'The :attribute may not be greater than :max.',
        'file' => 'The :attribute may not be greater than :max kilobytes.',
        'string' => 'The :attribute may not be greater than :max characters.',
        'array' => 'The :attribute may not have more than :max items.',
    ],
    'confirmed' => 'The :attribute confirmation does not match.',
    'unique' => 'The :attribute has already been taken.',
    'exists' => 'The selected :attribute is invalid.',
    'current_password' => 'The current password is incorrect.',

    'password' => [
        'letters' => 'The :attribute must contain at least one letter.',
        'mixed' => 'The :attribute must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The :attribute must contain at least one number.',
        'symbols' => 'The :attribute must contain at least one symbol.',
        'uncompromised' => 'The given :attribute has appeared in a data leak. Please choose a different :attribute.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name' => 'Name',
        'email' => 'Email Address',
        'password' => 'Password',
        'phone' => 'Mobile Number',
        'national_id' => 'National ID',
        'birth_date' => 'Date of Birth',
        'gender' => 'Gender',
        'city' => 'City',
        'weight' => 'Weight',
        'height' => 'Height',
        'chronic_disease' => 'Chronic Diseases',
        'recent_donation' => 'Previous Donation',
        'infection' => 'Infection',
        'has_recent_surgery' => 'Recent Surgeries',
        'surgery_date' => 'Surgery Date',
        'last_donation_date' => 'Last Donation Date',

        // Organization Registration
        'organizationName' => 'Organization Name',
        'organizationDescription' => 'Organization Description',
        'opening_time' => 'Opening Time',
        'closing_time' => 'Closing Time',
        'working_days' => 'Working Days',
        'daily_capacity' => 'Daily Capacity',
        'contactEmail' => 'Contact Email',
        'contactPhone' => 'Contact Mobile Number',
        'streetAddress' => 'Street Address',
        'governorate_id' => 'Governorate',
        'licenseNumber' => 'License Number',
        'licenseUpload' => 'License File',
        'adminName' => 'Admin Contact Name',
        'responsible_person_position' => 'Job Title',
        'adminEmail' => 'Administrator Email',
        'adminPassword' => 'Password',
        'current_password' => 'Current Password',
        'new_password' => 'New Password',
        'new_password_confirmation' => 'New Password Confirmation',
        'is_24_hours' => 'Operates 24/7',
        'terms' => 'Terms and Conditions',
        'termsAgree' => 'Terms Agreement',
        'auto_location_address' => 'Automatic Location',
        'auto_lat' => 'Latitude',
        'auto_lng' => 'Longitude',
        'blood_type' => 'Blood Type',
    ],
];
