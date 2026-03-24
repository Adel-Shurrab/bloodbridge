# خطة BloodBridge AI — من الصفر للإنتاج
**الإصدار: النهائي الشامل**
**آخر تحديث: مارس 2026**
**اللغة: عربي مع المصطلحات التقنية كما هي**

---

## قبل ما تبدأ — اقرأ هاي النقطة

هاي الخطة مقسومة لـ **مرحلتين كبيرتين**:

**المرحلة A — التعلم** (قبل ما تكتب أي كود)
**المرحلة B — التنفيذ** (خطوة خطوة من الصفر)

كل خطوة تعلم مرتبطة بفيديو YouTube محدد ومصدر مكتوب. ما في شي مبهم.

**المبدأ الأساسي:**
> لا تكتب كود ما فهمته. كل ساعة تعلم توفر عليك 10 ساعات debugging.

---

## الصورة الكاملة — شو اللي بنبنيه؟

```
متبرع جديد          متبرع قديم نشيط       متبرع قديم خامل
     │                     │                      │
     ▼                     ▼                      ▼
  Score: 0.5          Score: 0.85            Score: 0.30
  (Cold Start)        (ML Model)             (ML Model)
     │                     │                      │
     └──────────┬──────────┘                      │
                ▼                                  │
    Epsilon-Greedy Selection                       │
    ┌─────────────────────┐                        │
    │ 80% Exploitation    │◄── Top scorers          │
    │ 20% Exploration     │◄── Cold start + خامل ──┘
    └─────────────────────┘
                ▼
    Max 20 donors notified
    (بدل 200 كانوا قبل)
                ▼
    Acceptance Rate: 35% → 55%
```

---

# المرحلة A — التعلم

## الفصل 1: Python من الصفر
**المدة: 5 أيام**
**الهدف: تعرف تكتب Python وتفهمها**

### ليش Python؟
كل الـ ML code في مشروعك مكتوب بـ Python.
الـ FastAPI microservice بـ Python.
الـ XGBoost model بـ Python.
بدونها ما تقدر تفهم أي شي من الكود.

### اليوم 1-2: أساسيات Python

**📺 الفيديو الرئيسي:**
[Python Full Course for Beginners — Programming with Mosh](https://www.youtube.com/watch?v=_uQrJ0TkZlc)
⏱ 6 ساعات | المستوى: مبتدئ كامل

> **ليش هاد الفيديو تحديداً؟**
> Mosh Hamedani بيشرح بطريقة منهجية جداً. ما بيفترض إنك عارف شي.
> من أول `print("Hello")` لـ Object-Oriented Programming.

**شو تتعلم:**
- Variables, Data Types, Strings
- Lists, Dictionaries, Tuples
- If/Else, Loops (for, while)
- Functions
- Classes (Object-Oriented Programming)

**تمارين مهمة بعد كل جزء:**
```python
# جرب تكتب هاد بنفسك
donors = [
    {"name": "أحمد", "blood_type": "O+", "acceptance_rate": 0.85},
    {"name": "محمد", "blood_type": "A+", "acceptance_rate": 0.60},
    {"name": "سارة",  "blood_type": "B+", "acceptance_rate": 0.92},
]

# رتّب المتبرعين من الأعلى score للأدنى
sorted_donors = sorted(donors, key=lambda d: d["acceptance_rate"], reverse=True)

for donor in sorted_donors:
    print(f"{donor['name']}: {donor['acceptance_rate']}")
```

### اليوم 3: Virtual Environments و Packages

**📺 الفيديو:**
[Python Virtual Environments — Corey Schafer](https://www.youtube.com/watch?v=Kg1Yvry_Ydk)
⏱ 15 دقيقة

**ليش مهم؟**
مشروعك بيحتاج packages محددة بإصدارات محددة.
الـ virtual environment يعزل هاد عن باقي مشاريعك.

```bash
# هاد اللي رح تعمله لمشروع BloodBridge
python -m venv bloodbridge-ai
source bloodbridge-ai/bin/activate  # Linux/Mac
bloodbridge-ai\Scripts\activate     # Windows

pip install fastapi xgboost pandas numpy scikit-learn
```

### اليوم 4-5: Python للـ Data Science

**📺 الفيديو:**
[Data Analysis with Python — freeCodeCamp (NumPy + Pandas)](https://www.youtube.com/watch?v=r-uOLxNrNk8)
⏱ 4 ساعات

**📖 مصدر مكتوب:**
[NumPy and Pandas Tutorial — DataQuest](https://www.dataquest.io/tutorial/numpy-and-pandas-for-data-analysis/)

**ليش NumPy و Pandas؟**
- **NumPy**: يتعامل مع الأرقام بشكل سريع جداً (بدونه الـ ML بطيء)
- **Pandas**: يتعامل مع البيانات زي جداول Excel بس بـ Python

**شو تتعلم:**

```python
import pandas as pd
import numpy as np

# هيك رح تجيب بيانات المتبرعين من قاعدة البيانات
df = pd.read_sql("SELECT * FROM donor_behavioral_metrics", engine)

# هيك رح تحسب الـ acceptance_rate
df['acceptance_rate'] = df['accepted_count'] / df['total_responses'].clip(lower=1)

# هيك رح تحسب الـ recency_score
df['recency_score'] = np.exp(-df['days_since_last'] / 60)

# هيك رح تعرض أول 5 صفوف
print(df.head())
```

**نقطة تحقق — تأكد إنك تفهم:**
- [ ] عارف تعمل DataFrame من dict
- [ ] عارف تفلتر صفوف بشرط
- [ ] عارف تحسب average, sum, count
- [ ] عارف تعمل vectorized operations (بدون loops)

---

## الفصل 2: SQL المتقدم — Window Functions
**المدة: 2 أيام**
**الهدف: تفهم الـ query اللي بتحل مشكلة الـ feature leakage**

### ليش مهم جداً؟

في V4، الـ training query بتستخدم window functions.
هاي كانت المشكلة الأكبر في V3 — كانت بتشغل 4000 query بدل query وحدة.

**بدون هاد الفهم، ما تقدر تصلح الـ query إذا في مشكلة.**

### اليوم 1: مفهوم الـ Window Functions

**📺 الفيديو:**
[SQL Window Functions — Alex The Analyst](https://www.youtube.com/watch?v=Ww71knvhQ-s)
⏱ 30 دقيقة

**📖 مصدر مكتوب:**
[SQL Window Functions — Mode Analytics](https://mode.com/sql-tutorial/sql-window-functions/)

**الفكرة الأساسية:**

```sql
-- بدون Window Functions (بطيء - لكل صف query مستقلة)
SELECT
    rr.donor_id,
    (SELECT COUNT(*) FROM request_responses r2
     WHERE r2.donor_id = rr.donor_id
     AND r2.created_at < rr.created_at) AS total_before
FROM request_responses rr;

-- مع Window Functions (سريع - pass واحدة على البيانات)
SELECT
    donor_id,
    COUNT(*) OVER (
        PARTITION BY donor_id      -- لكل متبرع بشكل منفصل
        ORDER BY created_at         -- مرتب بالوقت
        ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING  -- كل الصفوف قبل الحالي
    ) AS total_before
FROM request_responses;
```

**المصطلحات المهمة:**

| المصطلح | الشرح |
|---|---|
| `OVER()` | يحول الـ aggregate function لـ window function |
| `PARTITION BY donor_id` | احسب بشكل منفصل لكل متبرع |
| `ORDER BY created_at` | رتّب الصفوف بالوقت قبل الحساب |
| `ROWS UNBOUNDED PRECEDING` | خذ كل الصفوف من البداية للصف الحالي |
| `LAG()` | خذ قيمة الصف اللي قبل الصف الحالي |

### اليوم 2: تطبيق على بياناتك

**📖 مصدر:**
[MySQL 8.0 Window Functions — Official Docs](https://dev.mysql.com/doc/refman/8.0/en/window-functions-usage.html)

```sql
-- هاد بالضبط شو بيحدث في data_pipeline.py تبعك
-- مثال مبسط لتفهم الفكرة

WITH donor_history AS (
    SELECT
        donor_id,
        created_at,
        status,
        -- الـ acceptance rate لهاد المتبرع قبل هاد الصف
        SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END)
            OVER (
                PARTITION BY donor_id
                ORDER BY created_at
                ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING
            ) AS accepted_before,

        COUNT(*)
            OVER (
                PARTITION BY donor_id
                ORDER BY created_at
                ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING
            ) AS total_before,

        -- كم يوم من آخر استجابة
        DATEDIFF(
            created_at,
            LAG(responded_at) OVER (PARTITION BY donor_id ORDER BY created_at)
        ) AS days_since_prev
    FROM request_responses
)
SELECT
    donor_id,
    COALESCE(accepted_before / NULLIF(total_before, 0), 0.5) AS acceptance_rate_at_time,
    COALESCE(days_since_prev, 999) AS days_since_last
FROM donor_history;
```

**نقطة تحقق:**
- [ ] تفهم الفرق بين `GROUP BY` و `PARTITION BY`
- [ ] تفهم `ROWS BETWEEN UNBOUNDED PRECEDING AND 1 PRECEDING`
- [ ] تفهم `LAG()`
- [ ] تعرف ليش هاد أسرع من الـ correlated subqueries

---

## الفصل 3: Machine Learning و XGBoost
**المدة: 5 أيام**
**الهدف: تفهم كيف الـ model بيتعلم ويتنبأ**

### قاعدة مهمة قبل ما تبدأ:

> اتبع **StatQuest with Josh Starmer** بالترتيب.
> ما تقفز على فيديو قبل اللي قبله.
> كل فيديو بيبني على اللي قبله.

**📺 القناة:** [StatQuest with Josh Starmer](https://www.youtube.com/@statquest)

### اليوم 1: Decision Trees — الأساس

**📺 الفيديو:**
[Decision and Classification Trees — StatQuest](https://www.youtube.com/watch?v=_L39rN6gz7Y)
⏱ 18 دقيقة

**ليش البداية من هون؟**
XGBoost مبني على Decision Trees.
ما تفهم XGBoost بدون ما تفهم Decision Tree.

**الفكرة:**
```
هل المتبرع استجاب آخر 30 يوم؟
        ├── نعم → هل acceptance rate > 70%؟
        │           ├── نعم → Score: 0.85 (رح يقبل)
        │           └── لا  → Score: 0.45
        └── لا  → Score: 0.20 (على الأرجح ما يقبل)
```

### اليوم 2: Gradient Boosting — كيف نحسّن الـ Decision Tree

**📺 الفيديو 1:**
[Gradient Boost Part 1 — StatQuest](https://www.youtube.com/watch?v=3CC4N4z3GJc)
⏱ 15 دقيقة

**📺 الفيديو 2:**
[Gradient Boost Part 2 — StatQuest](https://www.youtube.com/watch?v=2xudPOBz-vs)
⏱ 12 دقيقة

**الفكرة:**
```
Tree 1: Score خاطئ بـ 0.15
Tree 2: بيصلح الخطأ → خطأ بقى 0.08
Tree 3: بيصلح الخطأ → خطأ بقى 0.04
...
100 شجرة كل وحدة بتصلح أخطاء اللي قبلها
النتيجة: model دقيق جداً
```

### اليوم 3: XGBoost — الـ Model الرئيسي في مشروعك

**📺 الفيديو 1 (Regression):**
[XGBoost Part 1 — StatQuest](https://www.youtube.com/watch?v=OtD8wVaFm6E)
⏱ 26 دقيقة

**📺 الفيديو 2 (Classification — هاد اللي بستخدمه):**
[XGBoost Part 2 — StatQuest](https://www.youtube.com/watch?v=8b1JEDvenQU)
⏱ 25 دقيقة

**شو بيعمل XGBoost في مشروعك:**
```
Input (features):
  - acceptance_rate: 0.75
  - days_since_last: 5
  - recency_score: 0.92
  - response_time_hours: 0.5
  - blood_type_acceptance_rate: 0.68
  - ...

↓ XGBoost يحلل الـ features

Output:
  - probability: 0.83 (83% احتمال يقبل)
  - is_cold_start: false
```

### اليوم 4: XGBoost بالكود Python

**📺 الفيديو:**
[XGBoost in Python from Start to Finish — StatQuest](https://www.youtube.com/watch?v=GrJP9FLV3FE)
⏱ 50 دقيقة

```python
import xgboost as xgb
from sklearn.model_selection import train_test_split
from sklearn.metrics import roc_auc_score

# بياناتك
X = features_df.drop('donor_id', axis=1)  # الـ features
y = labels_df['acceptance']               # 1 قبل، 0 رفض

# قسّم لتدريب واختبار
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# درّب الـ model
model = xgb.XGBClassifier(
    max_depth=4,
    learning_rate=0.1,
    n_estimators=100,
)
model.fit(X_train, y_train)

# قيّم الدقة
y_pred_proba = model.predict_proba(X_test)[:, 1]
auc = roc_auc_score(y_test, y_pred_proba)
print(f"AUC-ROC: {auc:.3f}")  # نبغى > 0.72

# تنبأ لمتبرع جديد
new_donor = [[0.75, 5, 0.92, 0.5, ...]]  # features
probability = model.predict_proba(new_donor)[0][1]
print(f"Acceptance Probability: {probability:.2%}")
```

### اليوم 5: AUC-ROC و Model Evaluation

**📺 الفيديو:**
[ROC and AUC — StatQuest](https://www.youtube.com/watch?v=4jRBRDbJemM)
⏱ 15 دقيقة

**ليش مهم؟**
الـ AUC-ROC هو الرقم اللي بيقيس دقة الـ model.
في مشروعك، الهدف > 0.72.
لازم تفهمه عشان تعرف إذا الـ model شغال صح.

**الجدول:**
| AUC-ROC | المعنى |
|---|---|
| 1.0 | مثالي (مستحيل في الواقع) |
| 0.85+ | ممتاز |
| 0.72-0.84 | جيد جداً ✅ هدفك |
| 0.65-0.71 | مقبول |
| 0.5 | عشوائي (زي رمي عملة) |
| < 0.5 | أسوأ من العشوائي ❌ |

**نقطة تحقق بعد الفصل 3:**
- [ ] تفهم الفرق بين Decision Tree و XGBoost
- [ ] تعرف تشرح ليش Gradient Boosting أحسن من tree وحدة
- [ ] تعرف تدرّب XGBoost model بالكود
- [ ] تعرف تحسب AUC-ROC وتفسر النتيجة

---

## الفصل 4: FastAPI — بناء الـ Microservice
**المدة: 3 أيام**
**الهدف: تعرف تبني API بـ Python يقدر Laravel يتصل فيه**

### ليش FastAPI؟

مشروعك بـ PHP/Laravel والـ ML بـ Python.
اللغتين ما تحكوا مع بعض مباشرة.
الـ FastAPI هو الجسر — يحوّل الـ Python ML model لـ REST API.

```
Laravel (PHP)
     │
     │ POST /api/score
     │ {"donor_ids": [1, 2, 3]}
     │
     ▼
FastAPI (Python)
     │
     │ يشغّل الـ XGBoost model
     │
     ▼
Response: {"scores": {1: 0.85, 2: 0.60, 3: 0.92}}
     │
     ▼
Laravel يستخدم النتيجة
```

### اليوم 1: FastAPI Basics

**📺 الفيديو الرئيسي:**
[FastAPI Tutorial — Building RESTful APIs — Amigoscode](https://www.youtube.com/watch?v=GN6ICac3OXY)
⏱ 1 ساعة

**📖 المصدر الرسمي:**
[FastAPI Official Documentation](https://fastapi.tiangolo.com/tutorial/)
(أحسن documentation لـ framework بالعالم — اقرأها)

**أول API في حياتك:**
```python
from fastapi import FastAPI
from pydantic import BaseModel

app = FastAPI()

class ScoreRequest(BaseModel):
    donor_ids: list[int]

@app.post("/api/score")
async def score_donors(request: ScoreRequest):
    # هون بتحسب الـ scores
    scores = {}
    for donor_id in request.donor_ids:
        scores[donor_id] = 0.75  # مؤقتاً ثابت

    return {"scores": scores}

# شغّله بـ:
# uvicorn app:app --reload --port 8000
```

### اليوم 2: ربط FastAPI مع Database

**📺 الفيديو:**
[Build an AI App with FastAPI and Docker — Patrick Loeber](https://www.youtube.com/watch?v=iqrS7Q174Ac)
⏱ 30 دقيقة

**📖 مصدر:**
[SQLAlchemy Tutorial — DataCamp](https://www.datacamp.com/tutorial/sqlalchemy-tutorial-examples)

```python
from sqlalchemy import create_engine, text
import pandas as pd

# اتصال بنفس قاعدة بيانات Laravel
engine = create_engine(
    "mysql+pymysql://user:password@localhost/bloodbridge"
)

# جلب بيانات المتبرع
def get_donor_features(donor_id: int) -> dict:
    query = text("""
        SELECT
            d.id as donor_id,
            COUNT(rr.id) as total_responses,
            COUNT(CASE WHEN rr.status = 1 THEN 1 END) as accepted_count
        FROM donors d
        LEFT JOIN request_responses rr ON d.id = rr.donor_id
        WHERE d.id = :donor_id
        GROUP BY d.id
    """)

    with engine.connect() as conn:
        result = conn.execute(query, {"donor_id": donor_id})
        row = result.fetchone()
        return dict(row) if row else {}
```

### اليوم 3: Health Checks و Rate Limiting

**📺 الفيديو:**
[FastAPI Advanced Features — Sebastián Ramírez](https://www.youtube.com/watch?v=PnpTY1f4k2U)
⏱ 45 دقيقة

**المفاهيم:**
- **Health Check**: endpoint يتحقق إن الـ service شغال
- **Rate Limiting**: يمنع إنك ترسل أكثر من 100 request بالدقيقة
- **Pydantic Validation**: يتحقق إن الـ input صحيح

```python
# Health check — Laravel بيستخدمه قبل كل broadcast
@app.get("/api/health")
async def health():
    return {
        "status": "healthy",
        "model_loaded": model is not None,
        "db_connected": check_db_connection()
    }

# Validation — بيرفض أي request فيها أكثر من 500 donor
class ScoreRequest(BaseModel):
    donor_ids: list[int]

    @field_validator('donor_ids')
    def validate_size(cls, v):
        if len(v) > 500:
            raise ValueError('Maximum 500 donors per request')
        return v
```

**نقطة تحقق:**
- [ ] تعرف تبني API endpoint بـ FastAPI
- [ ] تعرف تتصل بـ MySQL من Python
- [ ] تعرف تشغّل الـ server بـ uvicorn
- [ ] تعرف تختبر الـ API من المتصفح (Swagger UI)

---

## الفصل 5: Epsilon-Greedy و Reinforcement Learning Basics
**المدة: 1.5 يوم**
**الهدف: تفهم كيف نختار مين نبعتله الإشعار**

### اليوم 1: فكرة الـ Multi-Armed Bandit

**📺 الفيديو 1:**
[Multi-Armed Bandits Explained — Arxiv Insights](https://www.youtube.com/watch?v=e3L4VocZnnQ)
⏱ 12 دقيقة

**📺 الفيديو 2:**
[Epsilon-Greedy Algorithm — The Coding Train](https://www.youtube.com/watch?v=JlJYcKiKhys)
⏱ 20 دقيقة

**الفكرة بمثال واقعي:**

```
عندك 200 متبرع مؤهل
Budget: 20 إشعار فقط
        │
        ▼
Epsilon = 0.20 (20% exploration)

حساب الـ slots:
  - Exploitation slots = 20 × 0.80 = 16 (الأعلى score)
  - Exploration slots  = 20 × 0.20 = 4  (عشوائيين)

النتيجة:
  16 متبرع بأعلى scores + 4 متبرعين عشوائيين = 20 إشعار
```

**ليش الـ Exploration مهم؟**

```
بدون Exploration:
  - نفس المتبرعين بيجوا إشعار دايماً
  - متبرعين جدد ما يلاقوا فرصة
  - الـ model ما يتعلم أنماط جديدة

مع Exploration:
  - 20% فرصة لمتبرعين جدد
  - الـ model يكتشف متبرعين ذهبيين مخفيين
  - البيانات تصير أغنى مع الوقت
```

### اليوم 1.5: Epsilon Decay

**الفكرة:**
```
اليوم 1-13  (أسبوعين):  ε = 0.20 (نستكشف كثير — model جديد)
اليوم 14-29 (أسبوعين):  ε = 0.15
اليوم 30-59 (شهر):      ε = 0.10
اليوم 60+   (ما بعد):   ε = 0.05 (نستكشف قليل — model ناضج)
```

**ليش بالوقت وليس بعدد التدريبات؟**
عدد التدريبات غير متوقع — ممكن الـ training يفشل، ممكن تشتغل يدوياً.
الوقت ثابت ومتوقع — دايماً بتعرف كم يوم مضى.

---

## الفصل 6: Circuit Breaker Pattern
**المدة: نصف يوم**
**الهدف: تفهم كيف نحمي النظام لما FastAPI ينقطع**

**📺 الفيديو:**
[Circuit Breaker Pattern Explained — ByteByteGo](https://www.youtube.com/watch?v=ADHjvEG-FQY)
⏱ 8 دقائق

**الفكرة:**

```
Normal (Closed):
Laravel → FastAPI ✅ → Score

FastAPI انقطع 3 مرات متتالية:

Open Circuit (دقيقتين):
Laravel → FastAPI ✗ → Rule-Based Fallback
         (ما يحاول حتى)

Half-Open (بعد دقيقتين):
Laravel → FastAPI (تجربة وحدة) → إذا نجح: Closed
                                 → إذا فشل: Open مجدداً
```

**ليش هاد مهم؟**
بدون Circuit Breaker:
- FastAPI واقع
- كل broadcast ينتظر 8 ثواني timeout
- 50 broadcast متزامن = 400 ثانية ضياع
- Queue workers محجوزين
- الإشعارات تتأخر على المتبرعين

مع Circuit Breaker:
- FastAPI واقع
- أول 3 فشلات: يحاول
- بعدها: يروح مباشرة للـ Rule-Based
- صفر وقت ضياع

---

# المرحلة B — التنفيذ

## الجدول الزمني الكامل

| الأسبوع | الخطوات | المخرج |
|---|---|---|
| أسبوع 1 | Migrations + Settings + Enum | قاعدة البيانات جاهزة |
| أسبوع 2 | PHP Services (CircuitBreaker, ScoringResult, DonorScoringService) | الـ Laravel layer جاهز |
| أسبوع 3 | Python FastAPI (config + features + training) | الـ ML service جاهز |
| أسبوع 4 | Integration + Wire + Commands + Widget | كل شي متصل |
| أسبوع 5 | Tests + A/B Enable + Monitor | Production |

---

## الخطوة 1: Migrations
**الترتيب مهم — تنفيذها بالتسلسل**

```bash
# 1. Settings migration
php artisan make:settings-migration CreateScoringSettings

# 2. Index migration على request_responses
php artisan make:migration add_scoring_indexes_to_request_responses

# 3. A/B tracking table
php artisan make:migration create_broadcast_experiment_results_table

# 4. شغّل كلها
php artisan migrate

# تحقق
php artisan tinker
> Schema::hasTable('broadcast_experiment_results'); // true
> Schema::hasIndex('request_responses', 'idx_rr_donor_time_status'); // true
```

**ليش الـ Indexes قبل أي شي؟**
لو شغّلت الـ feature engineering query بدون indexes:
- 200 donor × 2M rows = بطيء جداً
- ممكن تأخذ دقائق بدل ثواني

---

## الخطوة 2: ResponseStatus Enum

```php
// app/Enums/ResponseStatus.php
enum ResponseStatus: int
{
    case PENDING   = 0;
    case ACCEPTED  = 1;
    case DECLINED  = 2;
    case COMPLETED = 3;
    case IGNORED   = 4; // ← الجديد — ما رد على الإشعار
    case NO_SHOW   = 5;
}
```

---

## الخطوة 3: ScoringSettings

```php
// app/Settings/ScoringSettings.php
// (الكود الكامل موجود في V4)
```

**بعد ما تكتبه، اختبره:**
```php
php artisan tinker
> app(App\Settings\ScoringSettings::class)->ml_scoring_enabled
// false
> app(App\Settings\ScoringSettings::class)->max_notifications_per_broadcast
// 20
```

---

## الخطوة 4: ScoringResult Value Object

**هاد أول شي تكتبه قبل أي Service.**
كل الـ services تعتمد عليه.

```php
// app/DataTransferObjects/ScoringResult.php
// (الكود الكامل في V4)
```

**اختبار سريع:**
```php
php artisan tinker
> $r = App\DataTransferObjects\ScoringResult::coldStart(123)
> $r->isColdStart // true
> $r->score       // 0.5
> $r->source      // 'cold_start'

> $r2 = App\DataTransferObjects\ScoringResult::fromModel(456, 1.5, 'fastapi')
> $r2->score  // 1.0 (clamped — الـ clamp يمنع قيم خارج 0-1)
```

---

## الخطوة 5: FastApiCircuitBreaker

```php
// app/Services/FastApiCircuitBreaker.php
// (الكود الكامل في V4)
```

**اختبار الـ circuit breaker:**
```php
php artisan tinker
> $cb = app(App\Services\FastApiCircuitBreaker::class)
> $cb->isAvailable()
// true (لما FastAPI شغال)
// false (لما FastAPI واقع)
```

---

## الخطوة 6: AppServiceProvider Bindings

```php
// app/Providers/AppServiceProvider.php
public function register(): void
{
    $this->app->singleton(FastApiCircuitBreaker::class, function ($app) {
        return new FastApiCircuitBreaker($app->make(ScoringSettings::class));
    });

    $this->app->singleton(DonorScoringService::class, function ($app) {
        return new DonorScoringService(
            $app->make(ScoringSettings::class),
            $app->make(FastApiCircuitBreaker::class),
        );
    });
}
```

---

## الخطوة 7: Python FastAPI Setup

```bash
# إنشاء المجلد
mkdir ai_service
cd ai_service
python -m venv venv
source venv/bin/activate

# تثبيت الـ packages
pip install -r requirements.txt
# (الـ requirements.txt الكامل في V4)

# تحقق
python -c "import fastapi, xgboost, pandas; print('All packages OK')"
```

---

## الخطوة 8: Python config.py

```python
# ai_service/config.py
# (الكود الكامل في V4)
# مهم: تأكد إن قيم DB_HOST, DB_NAME, etc.
# مطابقة لـ .env تبع Laravel
```

---

## الخطوة 9: feature_engineering.py

هاد أهم ملف في الـ Python code.

**اختبره قبل ما تكمل:**
```python
# ai_service/test_features.py
from training.feature_engineering import FeatureEngineer

engineer = FeatureEngineer()
df = engineer.compute_features([1, 2, 3])  # IDs موجودة في قاعدة بياناتك

print(df.shape)        # يجب يطلع (3, 15) — 3 donors، 15 features
print(df.columns.tolist())  # تحقق من أسماء الـ features
print(df.head())       # شوف الأرقام، هل منطقية؟
print(df.isnull().sum())  # تأكد ما في null values
```

---

## الخطوة 10: data_pipeline.py

```python
# ai_service/training/data_pipeline.py
# (الكود الكامل في V4)
```

**اختبره:**
```python
from training.data_pipeline import DataPipeline

pipeline = DataPipeline()
df = pipeline.fetch_training_data()

print(f"Training records: {len(df)}")
print(f"Unique donors: {df['donor_id'].nunique()}")
print(f"Acceptance rate: {df['acceptance'].mean():.2%}")
# لو الـ acceptance rate > 60% → بياناتك غير متوازنة، طبيعي
```

---

## الخطوة 11: train.py

```python
# ai_service/training/train.py
# (الكود الكامل في V4)
```

**شغّل أول تدريب:**
```bash
cd ai_service
python -c "
from training.train import ModelTrainer
trainer = ModelTrainer()
result = trainer.train(model_version='v20260319')
print(result)
"
# المتوقع:
# {'status': 'success', 'metrics': {'auc_roc': 0.73, ...}}
# أو
# {'status': 'baseline', 'reason': 'insufficient_data'}
# (طبيعي في البداية — ما في بيانات كافية)
```

---

## الخطوة 12: api/routes.py

```python
# ai_service/api/routes.py
# (الكود الكامل في V4)
```

**شغّل الـ server:**
```bash
uvicorn app:app --host 0.0.0.0 --port 8000 --reload
```

**اختبر الـ endpoints:**
```bash
# Health check
curl http://localhost:8000/api/health
# المتوقع: {"status": "healthy", "model_loaded": false, ...}

# Score (بعد ما تدرّب الـ model)
curl -X POST http://localhost:8000/api/score \
  -H "Content-Type: application/json" \
  -d '{"donor_ids": [1, 2, 3]}'
# المتوقع: {"scores": {1: {"score": 0.75, "is_cold_start": false}, ...}}

# Swagger UI (افتحه بالمتصفح)
# http://localhost:8000/docs
```

---

## الخطوة 13: DonorScoringService

```php
// app/Services/DonorScoringService.php
// (الكود الكامل في V4)
```

**اختبر الـ waterfall:**
```php
php artisan tinker

// اختبر لما FastAPI شغال
> $service = app(App\Services\DonorScoringService::class)
> $donors = App\Models\Donor::take(5)->get()
> $result = $service->scoreAndSelect($donors, 'normal')
> $result['selected']->count()  // يجب < 5
> $result['source_breakdown']   // شوف من وين جاءت الـ scores

// أوقف FastAPI وجرب مجدداً
> $result2 = $service->scoreAndSelect($donors, 'normal')
> $result2['source_breakdown']  // رح يظهر 'rule_based' بدل 'fastapi'
```

---

## الخطوة 14: MarkIgnoredResponsesJob

```php
// app/Jobs/MarkIgnoredResponsesJob.php
// (الكود الكامل في V4)
```

**اختبره:**
```php
php artisan tinker

// أنشئ response وخليها pending
> $response = App\Models\RequestResponse::factory()->create([
    'status' => 0, // PENDING
    'created_at' => now()->subHours(3) // قبل 3 ساعات
  ])

// شغّل الـ job
> App\Jobs\MarkIgnoredResponsesJob::dispatchSync($response->blood_request_id)

// تحقق
> App\Models\RequestResponse::find($response->id)->status
// يجب: 4 (IGNORED)
```

---

## الخطوة 15: Wire Into BroadcastService

هاد آخر ربط.

```php
// في BloodRequestBroadcastService
// أضف الـ constructor injection
// (الكود الكامل في V4)
```

**اختبر broadcast كامل:**
```php
php artisan tinker

// فعّل الـ ML scoring
> $settings = app(App\Settings\ScoringSettings::class)
> $settings->ml_scoring_enabled = true
> $settings->save()

// شغّل broadcast تجريبي
> $request = App\Models\BloodRequest::first()
> app(App\Services\BloodRequestBroadcastService::class)->broadcast($request)

// تحقق من الـ logs
// storage/logs/laravel.log
// ابحث عن: "DonorScoringService::scoreAndSelect"
```

---

## الخطوة 16: Commands و Scheduler

```bash
# إنشاء الـ commands
php artisan make:command DecayEpsilonCommand
php artisan make:command RecalculateExperimentMetricsCommand

# (نسخ الكود من V4 لكل منهم)

# اختبر يدوياً
php artisan scoring:decay-epsilon
# المتوقع: "First run — ml_enabled_since recorded"

php artisan scoring:recalculate-experiments
# المتوقع: "No active experiments to recalculate."
```

```php
// routes/console.php
Schedule::command('scoring:decay-epsilon')->weekly()->sundays()->at('03:00');
Schedule::command('scoring:recalculate-experiments')->hourly();
```

---

## الخطوة 17: Filament Widget

```php
// app/Filament/Admin/Widgets/MLScoringMonitorWidget.php
// (الكود الكامل في V4)
```

**سجّله في الـ Admin Panel Provider:**
```php
// app/Providers/Filament/AdminPanelProvider.php
->widgets([
    // ... existing widgets
    App\Filament\Admin\Widgets\MLScoringMonitorWidget::class,
])
```

---

## الخطوة 18: التفعيل التدريجي

**هاد الترتيب مهم — لا تفعّل كل شي مرة وحدة:**

```
الأسبوع 1: ml_scoring_enabled = true
           a_b_testing_enabled = false
           → كل المتبرعين يحصلوا على ML scoring
           → راقب الـ acceptance rate

الأسبوع 2: a_b_testing_enabled = true
           a_b_test_control_percentage = 0.50
           → 50% يحصلوا ML، 50% بدون
           → قارن النتائج

الأسبوع 3+: قلّل الـ control إلى 0.20
            → 80% ML، 20% control
            → استمر بالمراقبة
```

---

## الخطوة 19: Tests

```bash
# شغّل كل الـ tests
php artisan test tests/Feature/DonorScoringServiceTest.php
php artisan test tests/Feature/CircuitBreakerTest.php
php artisan test tests/Feature/MarkIgnoredResponsesJobTest.php
php artisan test tests/Feature/ExperimentMetricsTest.php
```

**أهم الـ tests اللي لازم تكتبها:**

```php
// tests/Feature/DonorScoringServiceTest.php

// 1. الـ waterfall يشتغل
it('falls back to rule-based when FastAPI is down', function () {
    Http::fake(['*' => Http::response(null, 503)]);

    $donors = Donor::factory()->count(3)->create();
    $result = app(DonorScoringService::class)->scoreAndSelect($donors, 'normal');

    expect($result['source_breakdown'])->toHaveKey('rule_based');
});

// 2. الـ budget cap يشتغل
it('never exceeds max notifications budget', function () {
    $settings = app(ScoringSettings::class);
    $settings->max_notifications_per_broadcast = 5;
    $settings->save();

    $donors = Donor::factory()->count(100)->create();
    $result = app(DonorScoringService::class)->scoreAndSelect($donors, 'normal');

    expect($result['selected']->count())->toBeLessThanOrEqual(5);
});

// 3. Cold-start donors يروحوا للـ exploration
it('routes cold start donors to exploration bucket', function () {
    $newDonor = Donor::factory()->create();
    // ما عنده أي response history

    $result = app(DonorScoringService::class)->scoreAndSelect(
        collect([$newDonor]), 'normal'
    );

    expect($newDonor->scoringResult->isColdStart)->toBeTrue();
});
```

---

## قائمة مراجعة قبل الإطلاق

### Laravel Side ✅
- [ ] كل الـ migrations شغالة
- [ ] `idx_rr_donor_time_status` موجود
- [ ] `ScoringSettings` تُحمَّل صح من Filament
- [ ] `MarkIgnoredResponsesJob` بيشتغل وبيغيّر status
- [ ] `DonorScoringService` الـ waterfall شغال (tested)
- [ ] الـ circuit breaker بيفتح وبيقفل صح
- [ ] `BroadcastExperimentResult` بينتشئ مع كل broadcast

### Python Side ✅
- [ ] `uvicorn app:app` بيشتغل بدون errors
- [ ] `/api/health` يرجع `{"status": "healthy"}`
- [ ] `/api/score` يرجع scores صحيحة
- [ ] الـ blood type rates بتتكاش (مش query في كل مرة)
- [ ] الـ training query بتشتغل في < 30 ثانية
- [ ] الـ model بيتدرب ويُحفظ في `models/donor_scorer.pkl`

### Integration ✅
- [ ] Laravel يتصل بـ FastAPI بنجاح
- [ ] الـ circuit breaker يشتغل لما FastAPI واقع
- [ ] الـ A/B experiment بينسجّل مع كل broadcast
- [ ] `RecalculateExperimentMetricsCommand` بيحدّث الـ acceptance rate

---

## مصادر مرجعية (للرجوع إليها دايماً)

### المصادر التقنية

| الموضوع | المصدر |
|---|---|
| Python أساسيات | [Corey Schafer YouTube](https://www.youtube.com/@coreyms) |
| Pandas و NumPy | [freeCodeCamp Data Analysis Course](https://www.youtube.com/watch?v=r-uOLxNrNk8) |
| XGBoost (نظري) | [StatQuest Playlist](https://www.youtube.com/@statquest) |
| XGBoost (كود) | [XGBoost Official Docs](https://xgboost.readthedocs.io/) |
| FastAPI | [FastAPI Official Docs](https://fastapi.tiangolo.com/) |
| SQL Window Functions | [Mode SQL Tutorial](https://mode.com/sql-tutorial/sql-window-functions/) |
| MySQL Window Functions | [MySQL 8.0 Docs](https://dev.mysql.com/doc/refman/8.0/en/window-functions.html) |
| SQLAlchemy | [SQLAlchemy Tutorial — DataCamp](https://www.datacamp.com/tutorial/sqlalchemy-tutorial-examples) |
| Circuit Breaker | [ByteByteGo YouTube](https://www.youtube.com/@ByteByteGo) |
| Epsilon-Greedy | [Multi-Armed Bandits — Arxiv Insights](https://www.youtube.com/watch?v=e3L4VocZnnQ) |
| AUC-ROC | [StatQuest ROC Explained](https://www.youtube.com/watch?v=4jRBRDbJemM) |

### وثائق المشروع (بالترتيب)

| الوثيقة | الغرض |
|---|---|
| `PREDICTIVE_DONOR_SCORING_SYSTEM_V4.md` | المرجع الرئيسي — كل الكود |
| `NOTIFICATIONS_SYSTEM.md` | فهم كيف الإشعارات تشتغل |
| `PROJECT_KNOWLEDGE_BASE.md` | فهم بنية المشروع الكاملة |
| `README.md` | الصورة العامة |

---

## الأوامر اليومية (احفظها)

```bash
# Python
uvicorn app:app --host 0.0.0.0 --port 8000 --reload  # شغّل FastAPI
curl http://localhost:8000/api/health                  # تحقق من الصحة
curl http://localhost:8000/docs                        # Swagger UI

# Training
python -c "from training.train import ModelTrainer; ModelTrainer().train('v20260319')"

# Laravel
php artisan queue:listen                    # شغّل queue worker
php artisan scoring:decay-epsilon           # حدّث الـ epsilon
php artisan scoring:recalculate-experiments # حدّث الـ A/B metrics
php artisan tinker                          # تجريب تفاعلي

# Logs
tail -f storage/logs/laravel.log | grep -i "scoring\|circuit\|ignored"
```

---

## شو رح يحصل بعد أسبوع من التفعيل؟

```
الأيام 1-3:  البيانات تبدأ تتجمع في broadcast_experiment_results
الأيام 4-7:  donor_predictive_scores تبدأ تنملى بـ scores حقيقية
بعد أسبوع:  قارن treatment vs control في الـ Filament widget
             إذا treatment > control → الـ ML شغال ✅
             إذا treatment ≈ control → راجع الـ model ⚠️
             إذا treatment < control → أوقف ML فوراً ❌
```

---

*هاي الخطة تأخذك من الصفر الكامل لنظام ML في production.*
*كل خطوة مبنية على اللي قبلها. لا تتخطى خطوة.*
*اسأل عن أي خطوة مش واضحة قبل ما تكملها.*
