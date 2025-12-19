# Donor Registration Form Updates

## Overview
Updated the donor registration form to include a comprehensive health profile section with real-time eligibility checking and temporary ineligibility status management.

## Changes Made

### 1. **Database Models** ✅

#### DonorHealthProfile.php
- Added full model with fillable attributes for all health-related fields
- Implemented date casting for `surgery_date`, `next_eligible_date`, and `last_donation_date`
- Added boolean casting for all health condition fields
- Added soft deletes support
- Added relationship to Donor model

#### Donor.php
- Added `healthProfile()` hasOne relationship

### 2. **Registration Controller** ✅

#### RegisteredUserController.php
- Added health profile field validation (weight, height, health conditions, dates)
- Implemented `checkEligibility()` private method for eligibility calculation
- Added automatic health profile creation during registration
- Eligibility rules implemented:
  - **Weight**: Minimum 50kg
  - **Height**: Minimum 140cm
  - **Chronic Disease**: Makes donor ineligible
  - **Infection**: Makes donor ineligible
  - **Recent Donation**: 90-day waiting period (if donated within 90 days)
  - **Recent Surgery**: 4-week waiting period (if surgery within 28 days)
- Automatically calculates `next_eligible_date` based on whichever restriction lasts longer

### 3. **Blade View (register-donor.blade.php)** ✅

#### Progress Steps (Now 3 steps instead of 2)
1. Personal Information (Step 1)
2. Health Profile (Step 2) - **NEW**
3. Review and Confirm (Step 3)

#### New Health Profile Section (Step 2)
Fields included:
- Weight (kg) - Required, minimum 50kg
- Height (cm) - Required, minimum 140cm
- Chronic Disease (checkbox)
- Is Smoker (checkbox)
- Recent Donation (checkbox)
- Current Infection (checkbox)
- Recent Surgery (checkbox)
- Surgery Date (date picker)
- Last Donation Date (date picker)

#### Real-time Eligibility Status Display
- Shows ineligibility reasons in yellow warning box as user fills the form
- Auto-calculates and displays `next_eligible_date`
- Updates dynamically when health information changes

#### Review Step (Step 3)
- Shows personal information summary
- Shows health profile summary
- Displays eligibility status with reasons and next eligible date
- Green success box if eligible
- Yellow warning box if temporarily ineligible

### 4. **JavaScript Form Handler (registration-donor.js)** ✅

#### New Functions
- `checkEligibility()` - Analyzes health data and calculates eligibility status
- `displayEligibilityStatus()` - Shows eligibility status in real-time
- `initHealthProfileChangeListeners()` - Listens for health field changes

#### Updated Functions
- `validateStep()` - Now handles all 3 steps
  - Step 1: Personal information validation (unchanged)
  - Step 2: Health profile validation (NEW)
  - Step 3: Terms agreement validation

- `populateReview()` - Now includes health information in review section

#### Eligibility Logic
```javascript
// All criteria must pass:
✓ Weight ≥ 50kg
✓ Height ≥ 140cm
✓ No chronic disease
✓ No current infection
✓ If donated: last donation ≥ 90 days ago
✓ If had surgery: surgery ≥ 28 days ago
```

#### Important: Temporary Ineligibility
**Key Feature**: Users can still register even if temporarily ineligible!
- Registration is NOT prevented
- Status shows "غير مؤهل مؤقتًا" (Temporarily Ineligible)
- `next_eligible_date` is automatically calculated and displayed
- When the date is reached, user automatically becomes eligible

### 5. **Form Flow**

```
Step 1: Personal Information
↓
Step 2: Health Profile (with real-time eligibility checking)
↓
Step 3: Review & Confirm (shows full eligibility status)
↓
Submit → Validation on server → Create User, Donor, and DonorHealthProfile records
```

## Database Fields Used

### donors table
- user_id, national_id, gender, birth_date, blood_type, city, lat, lng, points, level, total_donations

### donor_health_profiles table
- donor_id
- weight (kg)
- height (cm)
- chronic_disease (boolean)
- recent_donation (boolean)
- infection (boolean)
- is_smoker (boolean)
- has_recent_surgery (boolean)
- surgery_date (date)
- is_eligible (boolean)
- next_eligible_date (date)
- last_donation_date (date)

## UI/UX Improvements

1. **Step-by-step form** - Easy to understand and less overwhelming
2. **Real-time feedback** - Users know eligibility status immediately
3. **Info boxes** - Color-coded (yellow warning, green success)
4. **Clear messaging** - Arabic labels for all fields
5. **Flexible registration** - Don't prevent registration for temporary issues
6. **Auto-calculated dates** - System handles date calculations
7. **Responsive design** - Works on desktop and mobile

## Server-side Eligibility Calculation

The backend also calculates eligibility using Carbon dates for accuracy:
- Compares donation dates with 90-day threshold
- Compares surgery dates with 28-day threshold
- Stores actual `next_eligible_date` in database
- Ensures accuracy regardless of client timezone

## Testing Checklist

- [ ] Fill personal information → Next button works
- [ ] Fill health profile with eligible data → Shows eligible status
- [ ] Fill health profile with ineligible data (chronic disease) → Shows warning
- [ ] Fill health profile with recent donation (< 90 days) → Shows next eligible date
- [ ] Fill health profile with recent surgery (< 28 days) → Shows next eligible date
- [ ] Submit form → Records created in database
- [ ] Verify DonorHealthProfile has correct `is_eligible` and `next_eligible_date`

## Future Enhancements

1. Add blood type field to health profile
2. Add medication history
3. Add travel history (for disease risk assessment)
4. Admin dashboard to manually adjust eligibility status
5. Automatic eligibility update via scheduled job when next_eligible_date is reached
6. Email notification when donor becomes eligible
