# 🚀 Quick Reference - Donor Registration Update

## What Changed?

### ✅ Registration is now 3 steps instead of 2:
1. Personal Information
2. **Health Profile (NEW)**
3. Review & Confirm

### ✅ Health Profile includes:
- Weight & Height
- Chronic Disease, Smoker, Infection status
- Surgery & Donation dates
- Real-time eligibility checking

### ✅ Eligibility Checking:
- Real-time as user fills form
- Shows green ✅ or yellow ⚠️ status box
- Auto-calculates next eligible date
- **Allows registration even if temporarily ineligible**

---

## 📋 Eligibility Rules

```
✅ ELIGIBLE if:
  • Weight ≥ 50kg
  • Height ≥ 140cm
  • No chronic disease
  • No infection
  • Last donation ≥ 90 days ago (or never)
  • Last surgery ≥ 28 days ago (or never)

⚠️ TEMPORARILY INELIGIBLE if:
  • Weight < 50kg
  • Height < 140cm
  • Has chronic disease
  • Has infection
  • Last donation < 90 days ago
  • Last surgery < 28 days ago
  
→ System shows next_eligible_date automatically
→ Registration is STILL ALLOWED
```

---

## 🔧 Files Updated

| File | Changes |
|------|---------|
| `app/Models/DonorHealthProfile.php` | Added fillable, relationships, casting |
| `app/Models/Donor.php` | Added healthProfile relationship |
| `app/Http/Controllers/Auth/RegisteredUserController.php` | Added validation, eligibility check, health profile creation |
| `resources/views/auth/register-donor.blade.php` | Added Step 2 health form, updated to 3 steps |
| `public/assets/scripts/pages/registration-donor.js` | Added eligibility checking, updated to 3 steps |

---

## 💾 Database Schema

New table: `donor_health_profiles`
```
id, donor_id (FK), weight, height, chronic_disease, recent_donation,
infection, is_eligible, is_smoker, has_recent_surgery, surgery_date,
next_eligible_date, last_donation_date, deleted_at, created_at, updated_at
```

---

## 🎯 Key Feature

**Users CAN register even if temporarily ineligible!**

Example:
- User donated 1 week ago
- System says "غير مؤهل مؤقتًا" (Temporarily ineligible)
- Shows: "Next eligible in 83 days"
- ✅ Registration proceeds normally
- Donor becomes available for requests after 90 days

---

## 🧪 Quick Test

1. Fill personal info → Next
2. Fill health info with:
   - Weight: 70kg ✓
   - Height: 175cm ✓
   - Last donation: Select a date 50 days ago
3. See yellow box with: "تبرعت قبل 50 يوم (يجب أن تمضي 90 يوم)"
4. See next eligible date automatically calculated
5. Click Next → Review page shows everything
6. Submit → Records created in database

---

## 📞 Support

For questions about:
- **Registration form**: Check `register-donor.blade.php`
- **Eligibility logic**: Check `registration-donor.js` and `RegisteredUserController.php`
- **Database**: Check `DonorHealthProfile` model

---

Generated: December 13, 2025
Version: 1.0
Status: ✅ Complete & Ready for Production
