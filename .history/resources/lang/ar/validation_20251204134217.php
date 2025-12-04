<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'email' => ':attribute يجب أن يكون عنوان بريد إلكتروني صحيحًا.',
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
    'unique' => ':attribute تم اختياره بالفعل.',
    'exists' => ':attribute المحدد غير صالح.',
];
