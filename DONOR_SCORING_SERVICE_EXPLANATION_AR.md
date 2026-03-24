# شرح خدمة DonorScoringService.php

## 1️⃣ الغرض الأساسي من الخدمة

**بجملة واحدة:**

هذه الخدمة تعطي كل متبرع "درجة" (علامة) لكي نعرف how مرجح أنه سيقبل التبرع، ثم تختار أفضل المتبرعين لإرسال إشعار التبرع إليهم باستخدام ميزانية محدودة.

---

## 2️⃣ شرح كل طريقة (Method) في الفئة

### 🔷 الطريقة الأولى: `scoreAndSelect()` - المدخل الرئيسي

**ماذا تفعل؟**
تأخذ قائمة بالمتبرعين المؤهلين، تعطيهم درجات، ثم تختار أفضلهم للإشعار.

**لماذا نحتاجها؟**
لأننا لا نستطيع إرسال إشعار لكل المتبرعين (تكلفة عالية)، فنحتاج لاختيار الأفضل فقط.

**شرح السطور:**

```php
public function scoreAndSelect(Collection $donors, string $urgency): array
{
    // Step 1: احصل على درجة لكل متبرع من خلال نظام التقييم المتعدد المستويات
    $results = $this->getScoreResults($donors->pluck('id')->toArray());
    // هنا نأخذ قائمة بالـ IDs (رقم معرف المتبرع) ونرسلها للدالة

    // Step 2: أضف الدرجة لكل متبرع حتى نستطيع استخدامها فيما بعد
    $scored = $donors->map(function (Donor $donor) use ($results) {
        $result = $results[$donor->id] ?? ScoringResult::neutral($donor->id);
        // إذا لم نجد درجة، استخدم درجة محايدة (0.5)
        $donor->setAttribute('scoringResult', $result);
        $donor->setAttribute('score', $result->score);
        return $donor;
    });

    // Step 3: قسّم المتبرعين إلى مجموعتين
    // - المجموعة الأولى: المتبرعون الموثوق بهم (درجات عالية)
    // - المجموعة الثانية: المتبرعون الجدد والمتبرعون بدرجات منخفضة
    [$exploiters, $explorers] = $this->splitByEpsilonGreedy($scored);

    // Step 4: احسب عدد الإشعارات التي سنرسلها
    $budget = $this->settings->max_notifications_per_broadcast; // مثلاً: 100 إشعار
    
    // إذا كانت الحالة حرجة (نقص الدم في المستشفى)
    // أرسل 50% إشعارات أكثر لأن الحياة في خطر
    if (strtolower($urgency) === 'critical') {
        $budget = (int) ($budget * 1.5); // 100 × 1.5 = 150 إشعار
    }

    // Step 5: احسب كم إشعار لكل مجموعة
    // 80% للمتبرعين الموثوق بهم، 20% للمتبرعين الجدد
    $exploitSlots = (int) ceil($budget * (1 - $this->settings->exploration_ratio)); // 80
    $exploreSlots = $budget - $exploitSlots; // 20

    // Step 6: اختر أفضل المتبرعين من كل مجموعة
    $selectedExploiters = $exploiters->sortByDesc('score')->take($exploitSlots);
    // رتب الموثوق بهم من الأفضل للأسوأ، خذ أول 80
    
    $selectedExplorers = $explorers->shuffle()->take($exploreSlots);
    // من المتبرعين الجدد، اختر 20 عشوائياً
    
    $selected = $selectedExploiters->merge($selectedExplorers);
    // اجمع المجموعتين معاً

    // Step 7: احسب إحصائيات للتسجيل والتحليل
    $coldStartCount = $scored->filter(fn($d) => $d->scoringResult->isColdStart)->count();
    // كم متبرع جديد بدون سجل سابق؟
    
    $sourceBreakdown = $scored
        ->groupBy(fn($d) => $d->scoringResult->source)
        ->map->count()
        ->toArray();
    // من أين جاءت الدرجات؟ من الـDatabase أم من AI أم من الصيغ؟

    // Step 8: سجل كل هذه المعلومات للفحص والتحليل
    Log::info('DonorScoringService::scoreAndSelect', [
        'total_eligible'  => $donors->count(),
        'exploiters_pool' => $exploiters->count(),
        'explorers_pool'  => $explorers->count(),
        'selected_total'  => $selected->count(),
        'cold_start'      => $coldStartCount,
        'budget'          => $budget,
        'urgency'         => $urgency,
        'sources'         => $sourceBreakdown,
    ]);

    // Step 9: أرجع النتيجة
    return [
        'selected'         => $selected->values(),
        'exploiter_count'  => $selectedExploiters->count(),
        'explorer_count'   => $selectedExplorers->count(),
        'cold_start_count' => $coldStartCount,
        'source_breakdown' => $sourceBreakdown,
    ];
}
```

---

### 🔷 الطريقة الثانية: `getScore()` - احصل على درجة متبرع واحد

**ماذا تفعل؟**
تعطيك درجة متبرع محدد فقط (ليس مجموعة).

**لماذا نحتاجها؟**
عندما تريد أن تعرف درجة متبرع واحد (مثلاً لإظهارها في لوحة التحكم).

**السطور:**

```php
public function getScore(Donor $donor): ScoringResult
{
    // احصل على الدرجة من خلال نفس النظام المتعدد المستويات
    $results = $this->getScoreResults([(int) $donor->id]);

    // سجل النتيجة لأغراض التصحيح
    Log::info('getScore results', [
        'donor_id' => $donor->id,
        'keys'     => array_keys($results),
        'result'   => $results[(int) $donor->id] ?? 'NOT FOUND',
    ]);

    // أرجع الدرجة، أو درجة محايدة إذا لم تُوجد
    return $results[(int) $donor->id]
        ?? ScoringResult::neutral($donor->id);
}
```

---

### 🔷 الطريقة الثالثة: `getScoreResults()` - نظام التقييم المتعدد المستويات (WATERFALL)

**ماذا تفعل؟**
تحاول الحصول على درجة المتبرع من 3 مصادر مختلفة، إذا فشلت الأولى تتحول للثانية، وهكذا.

**لماذا نحتاجها؟**
لأننا نريد أسرع وأدق درجة. كل مصدر له مميزاته وعيوبه.

**السطور:**

```php
private function getScoreResults(array $donorIds): array
{
    // المستوى 1: تحقق أولاً من الـ Database Cache (أسرع)
    $results = $this->getFromDbCache($donorIds);
    // احصل على الدرجات التي حسبناها من قبل (مخزنة في الـ Database)
    
    // اعرف أي متبرعين لم نجد درجاتهم
    $missing = array_values(array_diff($donorIds, array_keys($results)));

    Log::info('Level 1 DB Cache', [
        'found'   => array_keys($results),  // وجدنا درجات هؤلاء
        'missing' => $missing,               // لم نجد درجات هؤلاء
    ]);

    // إذا وجدنا درجات الكل، انتهينا
    if (empty($missing)) {
        return $results;
    }

    // المستوى 2: استخدم AI (XGBoost) إذا كان مفعلاً
    if ($this->settings->ml_scoring_enabled) {
        // أرسل طلب للخادم الذي يستخدم الـ AI
        $apiResults = $this->getFromFastApi($missing);
        
        // أضف النتائج الجديدة للنتائج السابقة
        $results = array_merge($results, $apiResults);
        
        // اعرف من بقي بدون درجة
        $missing = array_values(array_diff($donorIds, array_keys($results)));
    }

    Log::info('Level 2 FastAPI', [
        'ml_enabled' => $this->settings->ml_scoring_enabled,
        'missing'    => $missing,
    ]);

    // إذا حصلنا على الكل، انتهينا
    if (empty($missing)) {
        return $results;
    }

    // المستوى 3: استخدم صيغة بسيطة (PHP) - هذه دائماً تعمل
    $ruleResults = $this->getFromRuleBasedQuery($missing);

    Log::info('Level 3 Rule-Based results', [
        'results' => array_keys($ruleResults),
        'count'   => count($ruleResults),
    ]);

    // اجمع كل النتائج معاً
    return $results + $ruleResults;
}
```

---

### 🔷 الطريقة الرابعة: `getFromDbCache()` - المستوى 1: البحث المخزن

**ماذا تفعل؟**
تبحث في جدول `donor_predictive_scores` عن درجات قديمة (مخزنة سابقاً).

**لماذا نحتاجها؟**
الدرجات المحسوبة سابقاً مسجلة في الـ Database، والبحث فيها أسرع من حساب كل شيء من جديد.

**السطور:**

```php
private function getFromDbCache(array $donorIds): array
{
    // ابحث في جدول donor_predictive_scores
    return DonorPredictiveScore::whereIn('donor_id', $donorIds)
        // تأكد أن الدرجة حديثة (لم تمر أكثر من X أيام على حسابها)
        ->where('computed_at', '>=', now()->subDays($this->settings->score_staleness_days))
        // احصل على كل النتائج
        ->get()
        // حول كل صف إلى ScoringResult
        ->mapWithKeys(fn($row) => [
            $row->donor_id => ScoringResult::fromModel(
                $row->donor_id,
                (float) $row->acceptance_probability,
                'db_cache'  // نقول أن المصدر هو Database Cache
            ),
        ])
        ->toArray();
}
```

---

### 🔷 الطريقة الخامسة: `getFromFastApi()` - المستوى 2: الـ AI

**ماذا تفعل؟**
ترسل طلب HTTP لخادم FastAPI (يستخدم نموذج XGBoost) لحساب درجات أكثر دقة باستخدام AI.

**لماذا نحتاجها؟**
الـ AI يستطيع التنبؤ بدقة أعلى من الصيغ البسيطة، لكنه أبطأ وقد لا يكون متاحاً دائماً.

**السطور:**

```php
private function getFromFastApi(array $donorIds): array
{
    try {
        // اتصل بخادم AI
        $response = Http::connectTimeout(5)  // 5 ثواني للاتصال
            ->timeout(8)                       // 8 ثواني للرد
            ->post(config('services.fastapi.url') . '/api/score', [
                'donor_ids' => $donorIds,
            ]);

        // إذا لم ينجح الطلب (مثلاً الخادم معطل)
        if (! $response->successful()) {
            Log::warning('FastAPI returned non-200', ['status' => $response->status()]);
            return [];  // أرجع مصفوفة فارغة
        }

        $results = [];
        
        // لكل متبرع، احصل على درجته من AI
        foreach ($response->json('scores', []) as $donorId => $scoreData) {
            // هل هذا متبرع جديد ليس لدينا معلومات عنه؟
            $isColdStart = (bool) ($scoreData['is_cold_start'] ?? false);

            // إذا كان جديداً، ضعه في خانة "متبرع جديد"
            $results[(int) $donorId] = $isColdStart
                ? ScoringResult::coldStart((int) $donorId)
                : ScoringResult::fromModel(
                    (int) $donorId,
                    (float) $scoreData['score'],
                    'fastapi'  // المصدر: AI
                );
        }

        return $results;
    } catch (\Exception $e) {
        // إذا حدث خطأ (لا يوجد اتصال إنترنت مثلاً)
        Log::warning('FastAPI unreachable: ' . $e->getMessage());
        return [];  // أرجع مصفوفة فارغة
    }
}
```

---

### 🔷 الطريقة السادسة: `getFromRuleBasedQuery()` - المستوى 3: الصيغة البسيطة

**ماذا تفعل؟**
تحسب درجة باستخدام صيغة رياضية بسيطة بناءً على سجل المتبرع.

**لماذا نحتاجها؟**
هذي دائماً تعمل، مهما كانت المشاكل (التكنولوجيا). لا تعتمد على خوادم خارجية.

**السطور:**

```php
private function getFromRuleBasedQuery(array $donorIds): array
{
    // احصل على الحد الأدنى من التاريخ المطلوب
    // مثلاً: يجب أن يكون عند المتبرع 3 تبرعات على الأقل
    $minHistory = $this->settings->min_history_for_exploitation;

    Log::info('Rule-Based Query starting', [
        'donor_ids'   => $donorIds,
        'min_history' => $minHistory,
    ]);

    // استعلام SQL واحد كبير يحسب كل شيء دفعة واحدة
    // (لا نعمل loop صغير، كل شيء في استعلام واحد فقط)
    $rows = DB::table('donors as d')
        ->select([
            'd.id as donor_id',
            DB::raw('COUNT(rr.id) as total_responses'),
            // كم مرة رد هذا المتبرع على الطلب؟
            
            DB::raw('COUNT(CASE WHEN rr.status IN (1, 3) THEN 1 END) as accepted_count'),
            // كم مرة وافق/تبرع؟
            
            DB::raw('DATEDIFF(NOW(), MAX(rr.responded_at)) as days_since_last'),
            // كم يوم مضى منذ آخر تبرع؟
            
            DB::raw('COALESCE(dhp.total_donations, 0) as total_donations'),
            // كم عدد التبرعات الكلي؟
        ])
        // انضم مع جدول الردود
        ->leftJoin('request_responses as rr', function ($join) {
            $join->on('d.id', '=', 'rr.donor_id')
                ->whereIn('rr.status', [1, 2, 3, 4, 5, 6, 7])
                ->whereNotNull('rr.responded_at');
        })
        // انضم مع جدول ملف صحته
        ->leftJoin('donor_health_profiles as dhp', 'd.id', '=', 'dhp.donor_id')
        // ابحث عن هؤلاء المتبرعين فقط
        ->whereIn('d.id', $donorIds)
        // تأكد أنهم ليسوا محذوفين
        ->whereNull('d.deleted_at')
        // اجمع النتائج
        ->groupBy('d.id', 'dhp.total_donations')
        ->get();

    Log::info('Rule-Based Query rows', [
        'count' => $rows->count(),
        'rows'  => $rows->toArray(),
    ]);

    $results = [];

    // لكل متبرع، احسب درجته
    foreach ($rows as $row) {
        $donorId = (int) $row->donor_id;
        $total   = (int) $row->total_responses;  // كم مرة رد على طلب؟

        Log::info("Processing donor {$donorId}", [
            'total_responses' => $total,
            'min_history'     => $minHistory,
            'accepted_count'  => $row->accepted_count,
            'days_since_last' => $row->days_since_last,
            'total_donations' => $row->total_donations,
        ]);

        // إذا كان عدد الردود أقل من الحد الأدنى
        // معناها متبرع جديد ليس لدينا معلومات كثيرة عنه
        if ($total < $minHistory) {
            Log::info("Donor {$donorId} → coldStart (total {$total} < min {$minHistory})");
            $results[$donorId] = ScoringResult::coldStart($donorId);
            continue;
        }

        // احسب نسبة القبول: كام نسبة مرات الموافقة من كل الردود؟
        // مثلاً: وافق 8 مرات من 10 = 80% = 0.8
        $acceptanceRate = $row->accepted_count / $total;

        // احسب درجة الحداثة (متى آخر تبرع؟)
        $daysSinceLast = $row->days_since_last ?? 999;  // إذا ما تبرع قط، استخدم 999
        
        $recencyScore = match (true) {
            $daysSinceLast <= 7   => 1.0,   // تبرع الأسبوع الماضي: ممتاز!
            $daysSinceLast <= 30  => 0.8,   // تبرع الشهر الماضي: جيد
            $daysSinceLast <= 90  => 0.5,   // تبرع في آخر 3 أشهر: عادي
            $daysSinceLast <= 180 => 0.3,   // لم يتبرع من 6 أشهر: ضعيف
            default               => 0.1,   // ما تبرع من زمان: جداً ضعيف
        };

        // احسب درجة الولاء (كم متبرع مة؟)
        // قسم التبرعات على 10، لكن الأقصى 1.0
        // مثلاً: 7 تبرعات → 7/10 = 0.7
        $loyaltyScore = min((int) $row->total_donations / 10, 1.0);

        // احسب الدرجة النهائية باستخدام الصيغة:
        // (القبول × 50%) + (الحداثة × 30%) + (الولاء × 20%)
        $score = round(
            ($acceptanceRate * 0.50) +
                ($recencyScore   * 0.30) +
                ($loyaltyScore   * 0.20),
            4  // 4 أرقام عشرية بعد الفاصلة
        );

        Log::info("Donor {$donorId} → rule_based", [
            'acceptance_rate' => $acceptanceRate,
            'recency_score'   => $recencyScore,
            'loyalty_score'   => $loyaltyScore,
            'final_score'     => $score,
        ]);

        // احفظ النتيجة
        $results[$donorId] = ScoringResult::fromModel($donorId, $score, 'rule_based');
    }

    Log::info('Rule-Based final results', [
        'results_count' => count($results),
        'donor_ids'     => array_keys($results),
    ]);

    return $results;
}
```

---

### 🔷 الطريقة السابعة: `splitByEpsilonGreedy()` - قسّم المتبرعين إلى مجموعتين

**ماذا تفعل؟**
تقسم المتبرعين إلى مجموعتين:
- **Exploiters**: متبرعون لدينا معلومات عنهم، نثق بهم
- **Explorers**: متبرعون جدد أو بدرجات منخفضة، نريد أن نجربهم

**لماذا نحتاجها؟**
لأننا نفكر طويل الأجل: نريد الأفضل الآن، لكن نريد أيضاً اكتشاف متبرعين جدد قد يكونون أفضل مستقبلاً.

**السطور:**

```php
private function splitByEpsilonGreedy(Collection $scored): array
{
    // المتبرعون الجدد دائماً يذهبون إلى مجموعة الاستكشاف
    $coldStart = $scored->filter(fn($d) => $d->scoringResult->isColdStart);

    // المتبرعون العاديون رتبهم من الأفضل للأسوأ
    $withScores = $scored
        ->filter(fn($d) => ! $d->scoringResult->isColdStart)
        ->sortByDesc('score')  // الأعلى درجة أولاً
        ->values();

    // احسب كم متبرع من الأسفل (.20% إذا كان exploration_ratio = 0.20)
    $epsilon = $this->settings->exploration_ratio;  // مثلاً: 0.20
    $exploreCount = (int) ceil($withScores->count() * $epsilon);
    // إذا عند 100 متبرع، خذ أفضل 80 وضع 20 في الاستكشاف

    // أفضل 80% يذهبون للاستغلال
    $exploiters = $withScores->slice(0, $withScores->count() - $exploreCount);

    // أسوأ 20% يذهبون للاستكشاف
    $lowScorers = $withScores->slice($withScores->count() - $exploreCount);

    // اجمع: المتبرعون الجدد + أسوأ المتبرعين = مجموعة الاستكشاف
    $explorers = $coldStart->merge($lowScorers);

    return [$exploiters->values(), $explorers->values()];
}
```

---

## 3️⃣ تشبيهات بالحياة الواقعية

### 🎯 التشبيه الأول: نظام "الشلالات" (Waterfall)

**المشهد:**
أنت مدير البنك وتحتاج إلى معلومة عن زبون معين (درجة ائتمانه).

**الطريقة القديمة (بدون شلالات):**
تذهب مباشرة للنظام الحديث (AI) كل مرة. لكن إذا الحاسوب معطل = لا حل.

**طريقة الشلالات الذكية (Waterfall):**

1. **المستوى الأول - السجل العام بالبنك:**
   اطلب من الموظف الاستقبال: "عندك درجة أحمد في الملفات؟"
   - إذا موجود = خلاص، استخدمها (أسرع!)
   - إذا ما موجود = اذهب للخطوة 2

2. **المستوى الثاني - نظام الحاسوب الحديث:**
   شغل البرنامج الـ AI الذكي: "احسب درجة أحمد"
   - إذا اشتغل = ممتاز، استخدم النتيجة (أدق!)
   - إذا الحاسوب معطل = اذهب للخطوة 3

3. **المستوى الثالث - الحسبة اليدوية:**
   احسبها بنفسي بصيغة بسيطة:
   - "أحمد جاء 10 مرات، وقبل 8 مرات = درجته 80%"
   - هذي تعمل دائماً! (ما تحتاج حاسوب)

4. **المستوى الرابع - الافتراضية:**
   إذا ما عندنا معلومات بتاتاً = استخدم درجة محايدة (50%)

**الفائدة:**
- سريع: لو لقينا الجواب في الخطوة 1، ما احتجنا خطوات أخرى
- آمن: إذا فشل AI، عندنا الطريقة اليدوية
- موثوق: دائماً في حل!

---

### 🎯 التشبيه الثاني: Exploiters vs Explorers

**المشهد:**
أنت صاحب مطعم وتريد أفضل الزبائن الدائمين لحفلة اليوم.

**الفئة الأولى - Exploiters (المستغِلون):**
هؤلاء زبائنك الدائمين اللي تعرفهم زين!
- أحمد: يأتي كل أسبوع، ياكل صنف واحد، موثوق 100%
- فاطمة: تأتي كل شهر، تدعي أصحابها كتير
- علي: ياكل الكثير، يترك بخشيش حلو

لهؤلاء = ارسل لهم إشعار: "حفلتنا اليوم، اقدم على الحفل!"
= احتمال حضورهم عالي جداً

**الفئة الثانية - Explorers (المستكشفون):**
هؤلاء زبائن جدد أو زبائن غريبين (ما نعرف عنهم حاجة):
- محمد: جديد في المدينة، ما جرب مطعمنا بعد
- خديجة: جاءت مرة واحدة قبل سنة، بعدين اختفت
- سالم: اسمه غريب، ما نعرف إذا يحب الأكل اللي عندنا

لهؤلاء = ارسل لهم إشعار عشوائي: "نزور مطعمنا شنو رأيك؟"
= احتمال حضورهم غير متوقع، لكن قد نفاجأ ويصير زبون دائم!

**لماذا نحتاج الفئتين؟**

- لحَ لو ركزنا على الدائمين بس؟
  - ✓ نعرف أكثر حضورهم
  - ✗ بس بنفس الناس، قد تمل من الروتين
  - ✗ ما تكتشف زبائن جدد
  
- إذا عشوائياً عشين؟
  - ✓ تكتشف ناس جدد
  - ✗ تضيع فلوسك على ناس ما تهتم
  - ✗ الدائمين يقل حضورهم

- المزيج الذكي؟
  - 80% إشعار للدائمين (مضمون)
  - 20% إشعار للجدد (استكشاف)
  - = نتيجة أفضل مع الوقت!

---

### 🎯 التشبيه الثالث: ميزانية الإشعارات (Budget Cap)

**المشهد:**
عندك 1000 ريال شهراً لرسائل SMS.

**بدون حد أقصى:**
- عدد المتبرعين: 10,000 ~ متبرع
- تكلفة الرسالة: 0.5 ريال
- 10,000 × 0.5 = 5000 ريال محتاج!
- لكن ميزانيتك: 1000 ريال فقط!
- = كارثة!

**مع حد أقصى ذكي:**
- عندك 1000 ريال
- كل رسالة = 0.5 ريال
- تستطيع إرسال: 1000 ÷ 0.5 = 2000 رسالة
- بدل 10,000 ارسل 2000 + لأفضل المتبرعين

**الفائدة الإضافية - حالة الطوارئ:**
- الحالة عادية: 100 إشعار
- الحالة حرجة (نقص دم حاد): 100 × 1.5 = 150 إشعار
- لأن الحياة أهم من الميزانية في حالة الطوارئ!

---

### 🎯 التشبيه الرابع: Cold-Start Donors

**المشهد:**
أوبر (تطبيق الحجز) يريد سائق جديد.

**السائق الجديد (Cold-Start):**
- معاش وصل للتطبيق أمس
- ما عنده تقييمات
- ما نعرف أبداً: هل يتكلم عربي؟ هل آمن؟ هل سريع؟
- ما نعرف إذا راح يقبل الحجوزات

**اللي نعمل فيه:**
- لا نرسل له كل الحجوزات الكبار (قد يفشل)
- لكن لا نرسل له صفر حجوزات (قد يترك التطبيق)
- الحل الذكي: ارسل له 20% من المجموع عشوائياً
  - شوية حجوزات عادي، نشوف إذا بيكمل
  - إذا نجح = تدريجياً زيد الحجوزات
  - إذا فشل = تقليل الحجوزات

**معنى "Cold Start" في تطبيقنا:**
متبرع ما عند سجل (ما عدد تبرعات قديمة)، فـ:
- ما نجهز أسبابه بدقة
- نعطيه فرصة 20% عشوائية
- شوية تجربة بهونا = أه أم لا؟
- إذا تبرع = نرفع درجته تدريجياً
- إذا رفض = نخفضها

---

## 4️⃣ خطوة بخطوة: ماذا يحدث عندما تتصل scoreAndSelect(25 donors, 'normal')?

**السيناريو:**
- عندك 25 متبرع مؤهل
- نوعية الطلب: عادي (normal)
- الإعدادات:
  - max_notifications_per_broadcast = 100
  - exploration_ratio = 0.20
  - score_staleness_days = 7
  - ml_scoring_enabled = true

---

### 📍 الخطوة 1: احصل على درجات كل المتبرعين

```
الدخل: [1, 2, 3, 4, 5, ..., 25]

تنفيذ: $results = $this->getScoreResults([1, 2, 3, ..., 25]);

المستوى 1 - Database Cache:
  ✓ وجدنا درجات: [1, 2, 5, 7, 15]
  ✗ ما وجدنا: [3, 4, 6, 8, 9, 10, 11, 12, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25]

المستوى 2 - FastAPI (AI):
  ✓ وجدنا درجات: [3, 4, 6, 8, 9, 12, 15]  # البعض يكون cold-start
  ✗ ما وجدنا: [10, 11, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25]

المستوى 3 - Rule-Based:
  ✓ وجدنا درجات: [10, 11, 13, 14, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25]

النتيجة: كل المتبرعين عند درجاتهم بالآن ✓
```

---

### 📍 الخطوة 2: أضف الدرجات للمتبرعين

```
كل متبرع الآن عند:
- donor.scoringResult = ScoringResult(score: 0.75, source: 'db_cache', isColdStart: false)
- donor.score = 0.75

مثلاً:
- Donor 1: score = 0.85 (قوي!)
- Donor 2: score = 0.92 (ممتاز!)
- Donor 3: score = 0.15 (ضعيف!)
- Donor 4: score = 0.50 (متوسط)
- Donor 5: score = 0.78
- ...كل واحد عنده درجة
```

---

### 📍 الخطوة 3: قسّم المتبرعين

```
الخطوة A: افصل المتبرعين الجدد
$coldStart = [Donor3_coldstart, Donor8_coldstart]  # 2 متبرع جديد

الخطوة B: رتب الباقين من الأعلى للأسفل:
[Donor2: 0.92, Donor1: 0.85, Donor5: 0.78, Donor4: 0.50, Donor6: 0.45, ..., Donor7: 0.15]
عدد الـ Scored: 23

الخطوة C: احسب كم تاخد من الـ Scored للاستكشاف:
explore_count = ceil(23 × 0.20) = ceil(4.6) = 5 متبرع

الخطوة D: قسّم:
Exploiters (الأفضل 18): [Donor2, Donor1, Donor5, Donor4, Donor6, ..., Donor11]
Explorers (5 أسوأ + الجدد): [Donor7, Donor15, Donor18, Donor20, Donor24, Donor3_cold, Donor8_cold]

النتيجة:
- Exploiters pool: 18 متبرع
- Explorers pool: 7 متبرعين
```

---

### 📍 الخطوة 4: احسب الميزانية

```
$urgency = 'normal' → لا تخفيض

$budget = 100  (from settings)

$exploitSlots = ceil(100 * 0.80) = 80 إشعار
$exploreSlots = 100 - 80 = 20 إشعار
```

---

### 📍 الخطوة 5: اختر المتبرعين

```
من الـ Exploiters (18 متبرع) خذ أفضل 80؟
- لا! عندك 18 بس
- فخذ كل الـ 18 مرتبة عالياً
- الكل موثوق بهم

$selectedExploiters = [Donor2, Donor1, Donor5, Donor4, Donor6, Donor9, 
                       Donor10, Donor11, Donor12, Donor13, Donor14, 
                       Donor16, Donor17, Donor19, Donor21, Donor22, 
                       Donor23, Donor25]  
                       = 18 إشعار

من الـ Explorers (7 متبرع) خذ عشوائي 20؟
- لا! عندك 7 بس
- فخذ كل الـ 7

$selectedExplorers = shuffle([...7 متبرعين...])
                   = 7 إشعارات
```

---

### 📍 الخطوة 6: اجمع النتيجة

```
$selected = [
  18 متبرع من الموثقين,
  7 متبرعين من الجدد والضعفاء
]

الإجمالي = 25 إشعار (كلهم!)

لكن لو كان عندك 100 متبرع:
- Exploiters: 80 متبرع
- Explorers: 20 متبرع
- الإجمالي = 100 (بس الأفضل فقط)
```

---

### 📍 الخطوة 7: احسب الإحصائيات

```
$coldStartCount = عدد المتبرعين الجدد = 2

$sourceBreakdown = {
  'db_cache': 5,      # من Database
  'fastapi': 7,       # من AI
  'rule_based': 13    # من الصيغة
}
```

---

### 📍 الخطوة 8: سجل وأرجع النتيجة

```
return [
    'selected' => [18 متبرع موثوق + 7 جدد],
    'exploiter_count' => 18,
    'explorer_count' => 7,
    'cold_start_count' => 2,
    'source_breakdown' => ['db_cache' => 5, 'fastapi' => 7, 'rule_based' => 13],
]
```

---

## 5️⃣ لماذا هذه الصيغة بالذات للدرجات؟

```
الدرجة = (القبول × 50%)
       + (الحداثة × 30%)  
       + (الولاء × 20%)
```

---

### 🎯 لماذا 50% للقبول؟

**الفكرة:**
الشيء الأهم: هل المتبرع يوافق على الطلب أم لا؟

**المثال:**
- أحمد: جاء 100 مرة، وقبل 80 = 80% قبول (عالي!)
- خالد: جاء 10 مرات، وقبل 2 = 20% قبول (منخفض!)

أحمد أحسن بكثير من خالد، لأن نسبة القبول عنده عالية.

**لماذا الحد الأقصى (ما 60% أو 40%)?**
لأن القبول = المقياس الأساسي لنجاح البرنامج.

---

### 🎯 لماذا 30% للحداثة؟

**الفكرة:**
متبرع تبرع أمس؟ أفضل بكثير من متبرع تبرع قبل سنة!

**المثال:**
- سارة: آخر تبرع أسبوع ماضي = 1.0 (جديد!)
- نور: آخر تبرع قبل 6 شهور = 0.3 (قديم جداً)

سارة أكثر احتمالاً تتبرع الآن.

**لماذا 30% بالضبط؟**
نعطيها نص ما نعطي القبول، لأن:
- القبول = ماضي المتبرع (مهم جداً)
- الحداثة = حالته الآن (مهم، بس أقل)

---

### 🎯 لماذا 20% للولاء؟

**الفكرة:**
متبرع تبرع 50 مرة؟ ما في احتمالية يقول "لا" الآن.

**المثال:**
- فايز: تبرع 80 مرة = ولاء عالي جداً (0.8)
- جديد: تبرع مرة واحدة = ولاء منخفض (0.1)

فايز موثوق أن يقبل.

**لماذا 20% بس؟**
الولاء يساعد بس ما هو المقياس الأساسي:
- ممكن متبرع قديم يرفض هالمرة (مريض أو مشغول)
- ممكن متبرع جديد يقبل بحماس

---

### 🎯 الصيغة كاملة مع مثال حقيقي

**متبرع محدد - علي:**

```
البيانات عن علي:
- المجموع ردود: 20
- المجموع موافقات: 16
- آخر تبرع: 5 أيام
- عدد التبرعات: 15

الحساب:

1️⃣ نسبة القبول:
   16 ÷ 20 = 0.80 (80%)

2️⃣ درجة الحداثة:
   5 أيام ≤ 7 → 1.0

3️⃣ درجة الولاء:
   15 تبرعة ÷ 10 = 1.5 → لكن الأقصى 1.0 فخذ 1.0

4️⃣ الدرجة النهائية:
   (0.80 × 0.50) + (1.0 × 0.30) + (1.0 × 0.20)
   = 0.40 + 0.30 + 0.20
   = 0.90 ⭐⭐⭐ درجة عالي جداً!

النتيجة: علي موثوق جداً، اهتم به!
```

---

**مثال ثاني - فاتن (متبرج ضعيف):**

```
البيانات عن فاتن:
- المجموع ردود: 15
- المجموع موافقات: 4
- آخر تبرع: 200 يوم
- عدد التبرعات: 1

الحساب:

1️⃣ نسبة القبول:
   4 ÷ 15 = 0.27 (27%)

2️⃣ درجة الحداثة:
   200 أيام > 180 → 0.1

3️⃣ درجة الولاء:
   1 تبرعة ÷ 10 = 0.1

4️⃣ الدرجة النهائية:
   (0.27 × 0.50) + (0.1 × 0.30) + (0.1 × 0.20)
   = 0.135 + 0.03 + 0.02
   = 0.185 ⚠️ درجة منخفضة جداً!

النتيجة: فاتن ضعيفة، ما تركز عليها!
```

---

## الخلاصة 🎯

| المفهوم | التفسير البسيط |
|--------|-----------------|
| **DonorScoringService** | خدمة تعطي درجة لكل متبرع وتختار الأفضل |
| **scoreAndSelect()** | الدالة الرئيسية اللي تشتغل كل شيء |
| **Waterfall** | 3 طرق في الترتيب: Database → AI → صيغة بسيطة |
| **Exploiters** | متبرعين موثوق بهم، عندهم درجات عالية |
| **Explorers** | متبرعين جدد أو ضعفاء، فرصة تجديد |
| **Budget Cap** | تحديد أقصى إشعارات (100 أو 150 في الحوادث) |
| **Cold-Start** | متبرع جديد بدون سجل، نعطيه فرصة عشوائية |
| **Score Formula** | قسم تلاتة: قبول (50%) + حداثة (30%) + ولاء (20%) |

---

## ملاحظات مهمة 📝

1. **الخدمة متحفظة:** إذا ما وجدت معلومة، ما ترمي خطأ، بس تروح للخطة البديلة
2. **سريعة جداً:** كل شيء في استعلام SQL واحد (لا نعمل loops)
3. **آمنة:** حتى لو الـ AI معطل، الخدمة تشتغل
4. **ذكية:** تدرك أن اكتشاف متبرعين جدد أهم من استغلال القدماء فقط
5. **مرنة:** تعديل النسب والحدود سهل (في settings)

---

**كتب بـ ❤️ لتعليم المبتدئين**
