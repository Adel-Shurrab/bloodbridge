<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => ':attribute يجب أن يكون عنوان بريد إلكتروني صحيحًا.',
    'boolean' => 'حقل :attribute يجب أن يكون صحيحاً (True) أو خطأ (False).',
    'numeric' => 'يجب أن يكون حقل :attribute رقماً.',
    'integer' => 'يجب أن يكون حقل :attribute عدداً صحيحاً.',
    'date' => 'حقل :attribute ليس تاريخًا صالحًا.',
    'before_or_equal' => 'حقل :attribute يجب أن يكون تاريخًا قبل أو يساوي :date.',
    'in' => 'القيمة المحددة لحقل :attribute غير صالحة.',
    'digits' => 'حقل :attribute يجب أن يتكون من :digits أرقام.',

    'min' => [
        'numeric' => ':attribute يجب أن يكون :min على الأقل.',
        'file' => ':attribute يجب أن يكون :min كيلوبايت على الأقل.',
        'string' => ':attribute يجب أن يكون :min أحرف على الأقل.',
        'array' => ':attribute يجب أن يحتوي على :min عنصر على الأقل.',
    ],
    'max' => [
        'numeric' => ':attribute قد لا يكون أكثر من :max.',
        'file' => ':attribute قد لا يكون أكثر من :max كيلوبايت.',
        'string' => ':attribute قد لا يكون أكثر من :max أحرف.',
        'array' => ':attribute قد لا يحتوي على أكثر من :max عنصر.',
    ],
    'confirmed' => 'تأكيد :attribute غير متطابق.',
    'unique' => ':attribute مسجل لدينا بالفعل.',
    'exists' => ':attribute المحدد غير صالح.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | هنا نترجم أسماء الحقول البرمجية إلى أسماء عربية مقروءة
    */
    'attributes' => [
        'name' => 'الاسم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'phone' => 'رقم الجوال',
        'national_id' => 'رقم الهوية',
        'birth_date' => 'تاريخ الميلاد',
        'gender' => 'الجنس',
        'city' => 'المدينة',
        'weight' => 'الوزن',
        'height' => 'الطول',
        'chronic_disease' => 'الأمراض المزمنة',
        'recent_donation' => 'التبرع السابق',
        'infection' => 'العدوى',
        'has_recent_surgery' => 'العمليات الجراحية',
        'surgery_date' => 'تاريخ العملية',
        'last_donation_date' => 'تاريخ آخر تبرع',

        // Organization Registration
        'organizationName' => 'اسم المنظمة',
        'organizationDescription' => 'وصف المنظمة',
        'opening_time' => 'وقت الافتتاح',
        'closing_time' => 'وقت الإغلاق',
        'working_days' => 'أيام العمل',
        'daily_capacity' => 'القدرة الاستيعابية اليومية',
        'contactEmail' => 'البريد الإلكتروني للتواصل',
        'contactPhone' => 'رقم الجوال للتواصل',
        'streetAddress' => 'اسم الشارع',
        'governorate_id' => 'المحافظة',
        'licenseNumber' => 'رقم الترخيص',
        'licenseUpload' => 'ملف الترخيص',
        'adminName' => 'اسم المسؤول الإداري',
        'responsible_person_position' => 'المسمى الوظيفي للمسؤول',
        'adminEmail' => 'البريد الإلكتروني المسؤول',
        'adminPassword' => 'كلمة السر',
    ],
];
