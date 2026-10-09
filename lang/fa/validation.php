<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines (Persian)
    |--------------------------------------------------------------------------
    |
    | Persian versions of Laravel's default validation messages. Rules that are
    | missing here fall back to the English defaults of the framework.
    |
    */

    'accepted' => ':attribute باید پذیرفته شود.',
    'accepted_if' => 'وقتی :other برابر :value است، :attribute باید پذیرفته شود.',
    'active_url' => ':attribute باید یک URL معتبر باشد.',
    'after' => ':attribute باید تاریخی بعد از :date باشد.',
    'after_or_equal' => ':attribute باید تاریخی برابر یا بعد از :date باشد.',
    'alpha' => ':attribute فقط می‌تواند شامل حروف باشد.',
    'alpha_dash' => ':attribute فقط می‌تواند شامل حروف، اعداد، خط تیره و زیرخط باشد.',
    'alpha_num' => ':attribute فقط می‌تواند شامل حروف و اعداد باشد.',
    'any_of' => ':attribute نامعتبر است.',
    'array' => ':attribute باید یک آرایه باشد.',
    'ascii' => ':attribute فقط می‌تواند شامل حروف، اعداد و نمادهای تک‌بایتی باشد.',
    'before' => ':attribute باید تاریخی قبل از :date باشد.',
    'before_or_equal' => ':attribute باید تاریخی برابر یا قبل از :date باشد.',
    'between' => [
        'array' => ':attribute باید بین :min تا :max مورد داشته باشد.',
        'file' => 'حجم :attribute باید بین :min تا :max کیلوبایت باشد.',
        'numeric' => ':attribute باید بین :min تا :max باشد.',
        'string' => ':attribute باید بین :min تا :max نویسه باشد.',
    ],
    'boolean' => ':attribute باید درست یا نادرست باشد.',
    'can' => ':attribute شامل مقداری غیرمجاز است.',
    'confirmed' => 'تکرار :attribute با آن یکسان نیست.',
    'contains' => ':attribute یکی از مقدارهای لازم را ندارد.',
    'current_password' => 'رمز عبور نادرست است.',
    'date' => ':attribute باید یک تاریخ معتبر باشد.',
    'date_equals' => ':attribute باید تاریخی برابر با :date باشد.',
    'date_format' => ':attribute باید با قالب :format مطابقت داشته باشد.',
    'decimal' => ':attribute باید :decimal رقم اعشار داشته باشد.',
    'declined' => ':attribute باید رد شود.',
    'declined_if' => 'وقتی :other برابر :value است، :attribute باید رد شود.',
    'different' => ':attribute و :other باید متفاوت باشند.',
    'digits' => ':attribute باید :digits رقم باشد.',
    'digits_between' => ':attribute باید بین :min تا :max رقم باشد.',
    'dimensions' => 'ابعاد تصویر :attribute نامعتبر است.',
    'distinct' => ':attribute مقدار تکراری دارد.',
    'doesnt_contain' => ':attribute نباید هیچ‌یک از این مقدارها را داشته باشد: :values.',
    'doesnt_end_with' => ':attribute نباید با یکی از این مقدارها تمام شود: :values.',
    'doesnt_start_with' => ':attribute نباید با یکی از این مقدارها شروع شود: :values.',
    'email' => ':attribute باید یک نشانی ایمیل معتبر باشد.',
    'encoding' => ':attribute باید با رمزگذاری :encoding باشد.',
    'ends_with' => ':attribute باید با یکی از این مقدارها تمام شود: :values.',
    'enum' => ':attribute انتخاب‌شده نامعتبر است.',
    'exists' => ':attribute انتخاب‌شده نامعتبر است.',
    'extensions' => ':attribute باید یکی از این پسوندها را داشته باشد: :values.',
    'file' => ':attribute باید یک فایل باشد.',
    'filled' => ':attribute باید مقدار داشته باشد.',
    'gt' => [
        'array' => ':attribute باید بیش از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید بیشتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگ‌تر از :value باشد.',
        'string' => ':attribute باید بیشتر از :value نویسه باشد.',
    ],
    'gte' => [
        'array' => ':attribute باید :value مورد یا بیشتر داشته باشد.',
        'file' => 'حجم :attribute باید بزرگ‌تر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید بزرگ‌تر یا برابر :value باشد.',
        'string' => ':attribute باید :value نویسه یا بیشتر باشد.',
    ],
    'hex_color' => ':attribute باید یک رنگ هگزادسیمال معتبر باشد.',
    'image' => ':attribute باید یک تصویر باشد.',
    'in' => ':attribute انتخاب‌شده نامعتبر است.',
    'in_array' => ':attribute باید در :other وجود داشته باشد.',
    'in_array_keys' => ':attribute باید دست‌کم یکی از این کلیدها را داشته باشد: :values.',
    'integer' => ':attribute باید یک عدد صحیح باشد.',
    'ip' => ':attribute باید یک نشانی IP معتبر باشد.',
    'ipv4' => ':attribute باید یک نشانی IPv4 معتبر باشد.',
    'ipv6' => ':attribute باید یک نشانی IPv6 معتبر باشد.',
    'json' => ':attribute باید یک رشتهٔ JSON معتبر باشد.',
    'list' => ':attribute باید یک فهرست باشد.',
    'lowercase' => ':attribute باید با حروف کوچک باشد.',
    'lt' => [
        'array' => ':attribute باید کمتر از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید کمتر از :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کوچک‌تر از :value باشد.',
        'string' => ':attribute باید کمتر از :value نویسه باشد.',
    ],
    'lte' => [
        'array' => ':attribute نباید بیش از :value مورد داشته باشد.',
        'file' => 'حجم :attribute باید کوچک‌تر یا برابر :value کیلوبایت باشد.',
        'numeric' => ':attribute باید کوچک‌تر یا برابر :value باشد.',
        'string' => ':attribute باید :value نویسه یا کمتر باشد.',
    ],
    'mac_address' => ':attribute باید یک نشانی MAC معتبر باشد.',
    'max' => [
        'array' => ':attribute نباید بیش از :max مورد داشته باشد.',
        'file' => 'حجم :attribute نباید بیشتر از :max کیلوبایت باشد.',
        'numeric' => ':attribute نباید بزرگ‌تر از :max باشد.',
        'string' => ':attribute نباید بیشتر از :max نویسه باشد.',
    ],
    'max_digits' => ':attribute نباید بیش از :max رقم داشته باشد.',
    'mimes' => ':attribute باید فایلی از این نوع‌ها باشد: :values.',
    'mimetypes' => ':attribute باید فایلی از این نوع‌ها باشد: :values.',
    'min' => [
        'array' => ':attribute باید دست‌کم :min مورد داشته باشد.',
        'file' => 'حجم :attribute باید دست‌کم :min کیلوبایت باشد.',
        'numeric' => ':attribute باید دست‌کم :min باشد.',
        'string' => ':attribute باید دست‌کم :min نویسه باشد.',
    ],
    'min_digits' => ':attribute باید دست‌کم :min رقم داشته باشد.',
    'missing' => ':attribute نباید وجود داشته باشد.',
    'missing_if' => 'وقتی :other برابر :value است، :attribute نباید وجود داشته باشد.',
    'missing_unless' => ':attribute نباید وجود داشته باشد، مگر اینکه :other برابر :value باشد.',
    'missing_with' => 'وقتی :values وجود دارد، :attribute نباید وجود داشته باشد.',
    'missing_with_all' => 'وقتی :values وجود دارند، :attribute نباید وجود داشته باشد.',
    'multiple_of' => ':attribute باید مضربی از :value باشد.',
    'not_in' => ':attribute انتخاب‌شده نامعتبر است.',
    'not_regex' => 'قالب :attribute نامعتبر است.',
    'numeric' => ':attribute باید یک عدد باشد.',
    'password' => [
        'letters' => ':attribute باید دست‌کم یک حرف داشته باشد.',
        'mixed' => ':attribute باید دست‌کم یک حرف بزرگ و یک حرف کوچک داشته باشد.',
        'numbers' => ':attribute باید دست‌کم یک عدد داشته باشد.',
        'symbols' => ':attribute باید دست‌کم یک نماد داشته باشد.',
        'uncompromised' => ':attribute واردشده در یک نشت اطلاعاتی دیده شده است. لطفاً :attribute دیگری انتخاب کنید.',
    ],
    'present' => ':attribute باید وجود داشته باشد.',
    'present_if' => 'وقتی :other برابر :value است، :attribute باید وجود داشته باشد.',
    'present_unless' => ':attribute باید وجود داشته باشد، مگر اینکه :other برابر :value باشد.',
    'present_with' => 'وقتی :values وجود دارد، :attribute هم باید وجود داشته باشد.',
    'present_with_all' => 'وقتی :values وجود دارند، :attribute هم باید وجود داشته باشد.',
    'prohibited' => ':attribute مجاز نیست.',
    'prohibited_if' => 'وقتی :other برابر :value است، :attribute مجاز نیست.',
    'prohibited_if_accepted' => 'وقتی :other پذیرفته شده است، :attribute مجاز نیست.',
    'prohibited_if_declined' => 'وقتی :other رد شده است، :attribute مجاز نیست.',
    'prohibited_unless' => ':attribute مجاز نیست، مگر اینکه :other یکی از :values باشد.',
    'prohibits' => 'وقتی :attribute وجود دارد، :other نباید وجود داشته باشد.',
    'regex' => 'قالب :attribute نامعتبر است.',
    'required' => ':attribute الزامی است.',
    'required_array_keys' => ':attribute باید این موارد را داشته باشد: :values.',
    'required_if' => 'وقتی :other برابر :value است، :attribute الزامی است.',
    'required_if_accepted' => 'وقتی :other پذیرفته شده است، :attribute الزامی است.',
    'required_if_declined' => 'وقتی :other رد شده است، :attribute الزامی است.',
    'required_unless' => ':attribute الزامی است، مگر اینکه :other یکی از :values باشد.',
    'required_with' => 'وقتی :values وجود دارد، :attribute الزامی است.',
    'required_with_all' => 'وقتی :values وجود دارند، :attribute الزامی است.',
    'required_without' => 'وقتی :values وجود ندارد، :attribute الزامی است.',
    'required_without_all' => 'وقتی هیچ‌یک از :values وجود ندارند، :attribute الزامی است.',
    'same' => ':attribute باید با :other یکسان باشد.',
    'size' => [
        'array' => ':attribute باید :size مورد داشته باشد.',
        'file' => 'حجم :attribute باید :size کیلوبایت باشد.',
        'numeric' => ':attribute باید :size باشد.',
        'string' => ':attribute باید :size نویسه باشد.',
    ],
    'starts_with' => ':attribute باید با یکی از این مقدارها شروع شود: :values.',
    'string' => ':attribute باید یک رشتهٔ متنی باشد.',
    'timezone' => ':attribute باید یک منطقهٔ زمانی معتبر باشد.',
    'unique' => ':attribute قبلاً استفاده شده است.',
    'uploaded' => 'بارگذاری :attribute ناموفق بود.',
    'uppercase' => ':attribute باید با حروف بزرگ باشد.',
    'url' => ':attribute باید یک URL معتبر باشد.',
    'ulid' => ':attribute باید یک ULID معتبر باشد.',
    'uuid' => ':attribute باید یک UUID معتبر باشد.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | Persian names for the request fields and Livewire properties the console
    | validates, so a message reads «نام کاربری الزامی است.» instead of using the
    | raw field name. Used only when the code passes no attribute name itself.
    |
    */

    'attributes' => [
        // Sign-in, invitations, password reset, account
        'username' => 'نام کاربری',
        'password' => 'رمز عبور',
        'password_confirmation' => 'تکرار رمز عبور',
        'email' => 'ایمیل',
        'name' => 'نام',
        'login' => 'نام کاربری یا ایمیل',
        'code' => 'کد',
        'theme' => 'پوسته',
        'currentPassword' => 'رمز عبور فعلی',
        'passwordConfirmation' => 'تکرار رمز عبور',
        'confirmCode' => 'کد تأیید',
        'disablePassword' => 'رمز عبور',

        // Users, roles, groups, API tokens
        'note' => 'یادداشت',
        'notes' => 'یادداشت‌ها',
        'description' => 'توضیحات',
        'is_admin' => 'مدیر کل',
        'is_active' => 'فعال',
        'role_id' => 'نقش',
        'require_two_factor' => 'الزام ورود دومرحله‌ای',
        'permissions' => 'مجوزها',
        'permissions.*' => 'مجوز',
        'user_group_ids' => 'گروه‌های کاربری',
        'user_group_ids.*' => 'گروه کاربری',
        'device_group_ids' => 'گروه‌های دستگاه',
        'device_group_ids.*' => 'گروه دستگاه',
        'accessor_group_ids' => 'گروه‌های کاربری دارای دسترسی',
        'accessor_group_ids.*' => 'گروه کاربری',
        'expiresDays' => 'مدت اعتبار (روز)',

        // Devices
        'device' => 'دستگاه',
        'formRustdeskId' => 'شناسهٔ دستگاه',
        'formAlias' => 'نام مستعار',
        'formNote' => 'یادداشت',
        'formGroupId' => 'گروه دستگاه',
        'formUserId' => 'مالک',
        'formStrategyId' => 'سیاست',
        'formCanInitiate' => 'مجاز به شروع نشست',
        'moveGroupId' => 'گروه دستگاه',
        'abBookId' => 'دفترچهٔ نشانی',

        // Address books
        'bookName' => 'نام دفترچه',
        'bookNote' => 'یادداشت دفترچه',
        'tagName' => 'نام برچسب',
        'tagColor' => 'رنگ برچسب',
        'entryRustdeskId' => 'شناسهٔ RustDesk',
        'entryAlias' => 'نام مستعار',
        'ruleSubjectType' => 'نوع دریافت‌کننده',
        'ruleSubjectId' => 'دریافت‌کننده',
        'rulePermission' => 'مجوز',

        // Strategies
        'formName' => 'نام',
        'formConfirmationTimeout' => 'مهلت تأیید (دقیقه)',
        'revisionNote' => 'یادداشت نسخه',
        'assignDeviceIds.*' => 'دستگاه',
        'assignUserIds.*' => 'کاربر',
        'assignGroupIds.*' => 'گروه دستگاه',

        // Client downloads
        'file' => 'فایل',
        'label' => 'برچسب',
        'platform' => 'سیستم‌عامل',
        'arch' => 'معماری',
        'version' => 'نسخه',

        // Settings
        'idServer' => 'سرور شناسه',
        'relayServer' => 'سرور رله',
        'publicKey' => 'کلید عمومی',
        'onlineWindow' => 'بازهٔ آنلاین بودن',
        'rdgenUrl' => 'نشانی rdgen',
        'logRetentionDays' => 'مدت نگهداری گزارش‌ها (روز)',
        'serverAccessMode' => 'حالت دسترسی سرور',
        'relayServers' => 'سرورهای رله',
        'relayServers.*.address' => 'نشانی رله',
        'relayServers.*.geo' => 'موقعیت رله',
        'oidcDiscoveryUrl' => 'URL سرویس هویت',
        'oidcPublicBaseUrl' => 'نشانی عمومی پنل',
        'oidcClientId' => 'شناسهٔ کلاینت',
        'oidcClientSecret' => 'رمز کلاینت',
        'oidcScopes' => 'محدوده‌ها (scopes)',
        'oidcButtonLabel' => 'متن دکمهٔ ورود',
        'oidcNewUserPolicy' => 'سیاست کاربران جدید',
        'oidcDefaultGroupId' => 'گروه پیش‌فرض',
        'oidcAllowedDomains' => 'دامنه‌های مجاز',
        'smtpHost' => 'میزبان SMTP',
        'smtpPort' => 'پورت SMTP',
        'smtpEncryption' => 'رمزنگاری SMTP',
        'smtpUsername' => 'نام کاربری SMTP',
        'smtpPassword' => 'رمز عبور SMTP',
        'smtpFromAddress' => 'نشانی فرستنده',
        'smtpFromName' => 'نام فرستنده',
        'smtpTestTo' => 'نشانی گیرندهٔ آزمایشی',
        'emailTrustedDeviceDays' => 'مدت اعتماد به دستگاه (روز)',
        'appriseEndpoint' => 'نشانی Apprise',
        'appriseMode' => 'حالت Apprise',
        'appriseConfigKey' => 'کلید پیکربندی Apprise',
        'appriseUrls' => 'URLهای Apprise',
        'appriseCooldownMinutes' => 'فاصلهٔ تکرار اعلان (دقیقه)',
        'appriseOfflineGraceMinutes' => 'مهلت آفلاین شدن (دقیقه)',
        'presenceSnoozeTargetType' => 'نوع هدف',
        'presenceSnoozeTargetId' => 'هدف',
        'presenceSnoozeMinutes' => 'مدت توقف هشدار (دقیقه)',
    ],

];
