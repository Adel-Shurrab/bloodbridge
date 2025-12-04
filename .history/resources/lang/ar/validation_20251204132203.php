<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'يجب قبول :attribute.',
    'accepted_if'          => ':attribute يجب أن يتم قبوله عندما يكون :other مساوياً :value.',
    'active_url'           => ':attribute يجب أن يكون عنوان موقع ويب صحيح.',
    'after'                => ':attribute يجب أن يكون تاريخاً بعد :date.',
    'after_or_equal'       => ':attribute يجب أن يكون تاريخاً بعد أو مساوياً :date.',
    'alpha'                => ':attribute يجب أن يحتوي على أحرف فقط.',
    'alpha_dash'           => ':attribute يجب أن يحتوي على أحرف وأرقام وشرطات فقط.',
    'alpha_num'            => ':attribute يجب أن يحتوي على أحرف وأرقام فقط.',
    'array'                => ':attribute يجب أن يكون مصفوفة.',
    'before'               => ':attribute يجب أن يكون تاريخاً قبل :date.',
    'before_or_equal'      => ':attribute يجب أن يكون تاريخاً قبل أو مساوياً :date.',
    'between'              => [
        'numeric' => ':attribute يجب أن يكون بين :min و :max.',
        'file'    => ':attribute يجب أن يكون بين :min و :max كيلوبايت.',
        'string'  => ':attribute يجب أن يكون بين :min و :max أحرف.',
        'array'   => ':attribute يجب أن يحتوي على بين :min و :max عناصر.',
    ],
    'boolean'              => ':attribute يجب أن يكون صح أو خطأ.',
    'confirmed'            => 'تأكيد :attribute غير متطابق.',
    'current_password'     => 'كلمة السر غير صحيحة.',
    'date'                 => ':attribute ليس تاريخاً صحيحاً.',
    'date_equals'          => ':attribute يجب أن يكون تاريخاً مساوياً :date.',
    'date_format'          => ':attribute لا يطابق الصيغة :format.',
    'declined'             => ':attribute يجب أن يتم رفضه.',
    'declined_if'          => ':attribute يجب أن يتم رفضه عندما يكون :other مساوياً :value.',
    'different'            => ':attribute و :other يجب أن يكونا مختلفين.',
    'digits'               => ':attribute يجب أن يكون :digits أرقام.',
    'digits_between'       => ':attribute يجب أن يكون بين :min و :max أرقام.',
    'dimensions'           => ':attribute له أبعاد صورة غير صحيحة.',
    'distinct'             => ':attribute له قيمة مكررة.',
    'email'                => ':attribute يجب أن يكون بريداً إلكترونياً صحيحاً.',
    'ends_with'            => ':attribute يجب أن ينتهي بأحد الخيارات التالية: :values.',
    'exists'               => ':attribute المختار غير صحيح.',
    'file'                 => ':attribute يجب أن يكون ملفاً.',
    'filled'               => ':attribute يجب أن يحتوي على قيمة.',
    'gt'                   => [
        'numeric' => ':attribute يجب أن يكون أكبر من :value.',
        'file'    => ':attribute يجب أن يكون أكبر من :value كيلوبايت.',
        'string'  => ':attribute يجب أن يكون أكبر من :value أحرف.',
        'array'   => ':attribute يجب أن يحتوي على أكثر من :value عناصر.',
    ],
    'gte'                  => [
        'numeric' => ':attribute يجب أن يكون أكبر من أو مساوياً :value.',
        'file'    => ':attribute يجب أن يكون أكبر من أو مساوياً :value كيلوبايت.',
        'string'  => ':attribute يجب أن يكون أكبر من أو مساوياً :value أحرف.',
        'array'   => ':attribute يجب أن يحتوي على :value عنصر أو أكثر.',
    ],
    'image'                => ':attribute يجب أن تكون صورة.',
    'in'                   => ':attribute المختار غير صحيح.',
    'in_array'             => ':attribute لا يوجد في :other.',
    'integer'              => ':attribute يجب أن يكون عدداً صحيحاً.',
    'ip'                   => ':attribute يجب أن يكون عنوان IP صحيحاً.',
    'ipv4'                 => ':attribute يجب أن يكون عنوان IPv4 صحيحاً.',
    'ipv6'                 => ':attribute يجب أن يكون عنوان IPv6 صحيحاً.',
    'json'                 => ':attribute يجب أن يكون نصاً صحيحاً بصيغة JSON.',
    'lt'                   => [
        'numeric' => ':attribute يجب أن يكون أصغر من :value.',
        'file'    => ':attribute يجب أن يكون أصغر من :value كيلوبايت.',
        'string'  => ':attribute يجب أن يكون أصغر من :value أحرف.',
        'array'   => ':attribute يجب أن يحتوي على أقل من :value عنصر.',
    ],
    'lte'                  => [
        'numeric' => ':attribute يجب أن يكون أصغر من أو مساوياً :value.',
        'file'    => ':attribute يجب أن يكون أصغر من أو مساوياً :value كيلوبايت.',
        'string'  => ':attribute يجب أن يكون أصغر من أو مساوياً :value أحرف.',
        'array'   => ':attribute يجب أن يحتوي على :value عنصر أو أقل.',
    ],
    'max'                  => [
        'numeric' => ':attribute لا يجب أن يكون أكبر من :max.',
        'file'    => ':attribute لا يجب أن يكون أكبر من :max كيلوبايت.',
        'string'  => ':attribute لا يجب أن يكون أكبر من :max أحرف.',
        'array'   => ':attribute لا يجب أن يحتوي على أكثر من :max عناصر.',
    ],
    'mimes'                => ':attribute يجب أن يكون ملفاً من نوع: :values.',
    'mimetypes'            => ':attribute يجب أن يكون ملفاً من نوع: :values.',
    'min'                  => [
        'numeric' => ':attribute يجب أن يكون على الأقل :min.',
        'file'    => ':attribute يجب أن يكون على الأقل :min كيلوبايت.',
        'string'  => ':attribute يجب أن يكون على الأقل :min أحرف.',
        'array'   => ':attribute يجب أن يحتوي على الأقل :min عناصر.',
    ],
    'multiple_of'          => ':attribute يجب أن يكون مضاعفاً من :value.',
    'not_in'               => ':attribute المختار غير صحيح.',
    'not_regex'            => 'صيغة :attribute غير صحيحة.',
    'numeric'              => ':attribute يجب أن يكون رقماً.',
    'password'             => 'كلمة السر غير صحيحة.',
    'present'              => ':attribute يجب أن يكون موجوداً.',
    'regex'                => 'صيغة :attribute غير صحيحة.',
    'required'             => ':attribute مطلوب.',
    'required_if'          => ':attribute مطلوب عندما يكون :other مساوياً :value.',
    'required_unless'      => ':attribute مطلوب إلا إذا كان :other مساوياً :values.',
    'required_with'        => ':attribute مطلوب عندما يكون :values موجوداً.',
    'required_with_all'    => ':attribute مطلوب عندما يكون :values موجوداً.',
    'required_without'     => ':attribute مطلوب عندما لا يكون :values موجوداً.',
    'required_without_all' => ':attribute مطلوب عندما لا يكون :values موجوداً.',
    'same'                 => ':attribute و :other يجب أن يكونا متطابقين.',
    'size'                 => [
        'numeric' => ':attribute يجب أن يكون :size.',
        'file'    => ':attribute يجب أن يكون :size كيلوبايت.',
        'string'  => ':attribute يجب أن يكون :size أحرف.',
        'array'   => ':attribute يجب أن يحتوي على :size عنصر.',
    ],
    'starts_with'          => ':attribute يجب أن يبدأ بأحد الخيارات التالية: :values.',
    'string'               => ':attribute يجب أن يكون نصاً.',
    'timezone'             => ':attribute يجب أن يكون منطقة زمنية صحيحة.',
    'unique'               => ':attribute مستخدم بالفعل.',
    'uploaded'             => 'فشل تحميل :attribute.',
    'url'                  => 'صيغة :attribute غير صحيحة.',
    'uuid'                 => ':attribute يجب أن يكون UUID صحيحاً.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'email' => [
            'required' => 'البريد الإلكتروني مطلوب.',
            'email'    => 'البريد الإلكتروني غير صحيح.',
        ],
        'password' => [
            'required' => 'كلمة السر مطلوبة.',
            'min'      => 'كلمة السر قصيرة جداً.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [
        'email'    => 'البريد الإلكتروني',
        'password' => 'كلمة السر',
        'name'     => 'الاسم',
        'phone'    => 'رقم الهاتف',
        'address'  => 'العنوان',
    ],

];
