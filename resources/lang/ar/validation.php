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

    'accepted' => 'يجب أن يتم قبول الـ:attribute المحدد',
    'accepted_if' => 'يجب قبول الـ:attribute عندما يكون الـ:other هو :value',
    'active_url' => 'ينبغي أن يكون الـ:attribute رابطًا صحيحًا (URL) لكي يتم قبوله ',
    'after' => 'يجب أن يكون الـ:attribute تاريخًا بعد :date.',
    'after_or_equal' => 'يجب أن يكون الـ:attribute تاريخًا بعد أو مساويًا لـ:date',
    'alpha' => 'يجب أن يحتوي الـ:attribute على أحرف فقط.',
    'alpha_dash' => 'يجب أن يحتوي الـ:attribute على أحرف، أرقام، و (-) و (_)',
    'alpha_num' => 'يجب أن يحتوي الـ:attribute على أحرف وأرقام فقط',
    'array' => 'يجب أن يكون الـ:attribute مصفوفة (Array).',
    'ascii' => 'يجب أن يحتوي الـ:attribute على أحرف أبجدية أحادية البايت وأرقام ورموز فقط',
    'before' => 'يجب أن يكون الـ:attribute تاريخًا قبل :date',
    'before_or_equal' => 'يجب أن يكون الـ:attribute تاريخًا قبل أو مساويًا لـ:date',
    'between' => [
        'array' => 'يجب أن يحتوي الـ:attribute على عدد من العناصر بين :min و :max.',
        'file' => 'يجب أن يكون حجم الـ:attribute بين :min و :max كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute بين :min و :max.',
        'string' => 'يجب أن يكون طول الـ:attribute بين :min و :max أحرف',
    ],
    'boolean' => 'يجب أن يكون حقل الـ:attribute إما صحيحًا (true) أو خاطئًا (false)',
    'confirmed' => 'تأكيد الـ:attribute غير متطابق',
    'current_password' => 'كلمة المرور غير صحيحة',
    'date' => 'الـ:attribute ليس تاريخًا صحيحًا',
    'date_equals' => 'يجب أن يكون الـ:attribute تاريخًا مساويًا لـ:date',
    'date_format' => 'الـ:attribute لا يتطابق مع الصيغة :format',
    'decimal' => 'يجب أن يحتوي الـ:attribute على :decimal أماكن عشرية',
    'declined' => 'يجب أن يكون الـ:attribute مرفوضًا',
    'declined_if' => 'يجب أن يكون الـ:attribute مرفوضًا عندما يكون :other هو :value',
    'different' => 'يجب أن يكون الـ:attribute و:other مختلفين',
    'digits' => 'يجب أن يحتوي الـ:attribute على :digits أرقام',
    'digits_between' => 'يجب أن يحتوي الـ:attribute على عدد من الأرقام بين :min و :max',
    'dimensions' => 'الـ:attribute يحتوي على أبعاد صورة غير صالحة',
    'distinct' => 'حقل الـ:attribute يحتوي على قيمة مكررة',
    'doesnt_end_with' => 'قد لا ينتهي الـ:attribute بأحد القيم التالية: :values',
    'doesnt_start_with' => 'قد لا يبدأ الـ:attribute بأحد القيم التالية: :values',
    'email' => 'يجب أن يكون الـ:attribute عنوان بريد إلكتروني صحيح',
    'ends_with' => 'يجب أن ينتهي الـ:attribute بأحد القيم التالية: :values',
    'enum' => 'الـ:attribute المحدد غير صالح',
    'exists' => 'الـ:attribute المحدد غير صالح',
    'file' => 'يجب أن يكون الـ:attribute ملفًا',
    'filled' => 'حقل الـ:attribute يجب أن يحتوي على قيمة',
    'gt' => [
        'array' => 'يجب أن يحتوي الـ:attribute على أكثر من :value عناصر',
        'file' => 'يجب أن يكون حجم الـ:attribute أكبر من :value كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute أكبر من :value',
        'string' => 'يجب أن يكون طول الـ:attribute أكبر من :value أحرف',
    ],
    'gte' => [
        'array' => 'يجب أن يحتوي الـ:attribute على عدد من العناصر أكثر من :value',
        'file' => 'يجب أن يكون حجم الـ:attribute أكبر من :value كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute أكبر من :value',
        'string' => 'يجب أن يكون طول الـ:attribute أكبر من :value أحرف.',
    ],
    'image' => 'يجب أن يكون الـ:attribute صورة',
    'in' => 'الـ:attribute المحدد غير صالح',
    'in_array' => 'حقل الـ:attribute غير موجود في :other',
    'integer' => 'يجب أن يكون الـ:attribute عددًا صحيحًا',
    'ip' => 'يجب أن يكون الـ:attribute عنوان IP صحيحًا',
    'ipv4' => 'يجب أن يكون الـ:attribute عنوان IPv4 صحيحًا',
    'ipv6' => 'يجب أن يكون الـ:attribute عنوان IPv6 صحيحًا',
    'json' => 'يجب أن يكون الـ:attribute سلسلة JSON صحيحة',
    'lowercase' => 'يجب أن يكون الـ:attribute حروفًا صغيرة',
    'lt' => [
        'array' => 'يجب أن يحتوي الـ:attribute على عدد من العناصر أقل من :value',
        'file' => 'يجب أن يكون حجم الـ:attribute أقل من :value كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute أقل من :value',
        'string' => 'يجب أن يكون طول الـ:attribute أقل من :value أحرف',
    ],
    'lte' => [
        'array' => 'يجب ألا يحتوي الـ:attribute على عدد من العناصر أكثر من :value',
        'file' => 'يجب أن يكون حجم الـ:attribute أقل من أو يساوي :value كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute أقل من أو يساوي :value.',
        'string' => 'يجب أن يكون طول الـ:attribute أقل من أو يساوي :value أحرف',
    ],
    'mac_address' => 'يجب أن يكون الـ:attribute عنوان MAC صحيح',
    'max' => [
        'array' => 'يجب ألا يحتوي الـ:attribute على عدد من العناصر أكثر من :max',
        'file' => 'يجب أن لا يتجاوز حجم الـ:attribute :max كيلوبايت',
        'numeric' => 'يجب أن لا يتجاوز الـ:attribute قيمة :max',
        'string' => 'يجب ألا يتجاوز طول الـ:attribute :max أحرف',
    ],
    'max_digits' => 'يجب ألا يحتوي الـ:attribute على عدد من الأرقام أكثر من :max',
    'mimes' => 'يجب أن يكون الـ:attribute ملفًا من النوع: :values',
    'mimetypes' => 'يجب أن يكون الـ:attribute ملفًا من النوع: :values',
    'min' => [
        'array' => 'يجب أن يحتوي الـ:attribute على عدد من العناصر لا يقل عن :min',
        'file' => 'يجب أن يكون حجم الـ:attribute على الأقل :min كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute على الأقل :min',
        'string' => 'يجب أن يكون طول الـ:attribute على الأقل :min أحرف',
    ],
    'min_digits' => 'يجب أن يحتوي الـ:attribute على عدد من الأرقام لا يقل عن :min',
    'missing' => 'يجب أن يكون حقل الـ:attribute مفقودًا',
    'missing_if' => 'يجب أن يكون حقل الـ:attribute مفقودًا عندما يكون :other هو :value',
    'missing_unless' => 'يجب أن يكون حقل الـ:attribute مفقودًا ما لم يكن :other هو :value',
    'missing_with' => 'يجب أن يكون حقل الـ:attribute مفقودًا عندما يكون :values موجودًا',
    'missing_with_all' => 'يجب أن يكون حقل الـ:attribute مفقودًا عندما تكون :values موجودة',
    'multiple_of' => 'يجب أن يكون الـ:attribute مضاعفًا للعدد :value',
    'not_in' => 'الـ:attribute المحدد غير صالح',
    'not_regex' => 'تنسيق الـ:attribute غير صالح',
    'numeric' => 'يجب أن يكون الـ:attribute رقمًا',
    'password' => [
        'letters' => 'يجب أن يحتوي الـ:attribute على حرف واحد على الأقل',
        'mixed' => 'يجب أن يحتوي الـ:attribute على حرف واحد على الأقل من الحروف الكبيرة والحروف الصغيرة',
        'numbers' => 'يجب أن يحتوي الـ:attribute لى رقم واحد على الأقل',
        'symbols' => 'يجب أن يحتوي الـ:attribute على رمز واحد على الأقل',
        'uncompromised' => ' تم ظهور الـ:attribute المحدد في تسريب بيانات. يرجى اختيار :مختلفة attribute',
    ],
    'present' => 'يجب توفر حقل الـ:attribute',
    'prohibited' => 'حقل الـ:attribute ممنوع',
    'prohibited_if' => 'حقل الـ:attribute ممنوع عندما يكون الـ:other هو :value',
    'prohibited_unless' => 'حقل الـ:attribute ممنوع ما لم يكن الـ:other موجود في :values',
    'prohibits' => 'حقل الـ:attribute يمنع تواجد الـ:other',
    'regex' => 'تنسيق الـ:attribute غير صالح',
    'required' => 'حقل الـ:attribute مطلوب',
    'required_array_keys' => 'يجب أن يحتوي حقل الـ:attribute على إدخالات لـ:values',
    'required_if' => 'حقل الـ:attribute مطلوب عندما يكون الـ:other هو :value',
    'required_if_accepted' => 'حقل الـ:attribute مطلوب عند قبول الـ:other',
    'required_unless' => 'حقل الـ:attribute مطلوب ما لم يكن الـ:other موجود في :values',
    'required_with' => 'حقل الـ:attribute مطلوب عند تواجد :values',
    'required_with_all' => 'حقل الـ:attribute مطلوب عند تواجد جميع :values',
    'required_without' => 'حقل الـ:attribute مطلوب عند عدم تواجد :values',
    'required_without_all' => 'حقل الـ:attribute مطلوب عند عدم تواجد أي من :values',
    'same' => 'يجب أن يتطابق الـ:attribute مع :other',
    'size' => [
        'array' => 'يجب أن يحتوي الـ:attribute على :size عناصر',
        'file' => 'يجب أن يكون حجم الـ:attribute :size كيلوبايت',
        'numeric' => 'يجب أن يكون الـ:attribute :size',
        'string' => 'يجب أن يكون طول الـ:attribute :size أحرف',
    ],
    'starts_with' => 'يجب أن يبدأ الـ:attribute بأحد القيم التالية: :values',
    'string' => 'يجب أن يكون الـ:attribute نص',
    'timezone' => 'يجب أن يكون الـ:attribute منطقة زمنية صالحة',
    'unique' => 'الـ:attribute تم اتخاذه بالفعل',
    'uploaded' => 'فشل رفع الـ:attribute',
    'uppercase' => 'يجب أن يكون الـ:attribute في حالة حروف كبيرة',
    'url' => 'يجب أن يكون الـ:attribute رابط صالح.',
    'ulid' => 'يجب أن يكون الـ:attribute ULID صالح',
    'uuid' => 'يجب أن يكون الـ:attribute UUID صالح',


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
        'attribute-name' => [
            'rule-name' => 'رسالة-مخصصة',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
