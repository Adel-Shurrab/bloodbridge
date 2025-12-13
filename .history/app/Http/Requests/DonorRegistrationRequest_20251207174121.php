<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class DonorRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/'],
            'phone' => ['required', 'string', 'max:15', 'unique:users,phone', 'regex:/^(\+970|0)?[5-9][0-9]{8}$/'],
            'national_id' => ['required', 'string', 'max:20', 'unique:donors,national_id', 'regex:/^\d+$/'],
            'birth_date' => ['required', 'date', 'before:today', 'after:' . Carbon::now()->subYears(100)->toDateString()],
            'gender' => ['required', Rule::in(['male', 'female'])],
            'blood_type' => ['required', Rule::in(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-'])],
            'city' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'الاسم مطلوب',
            'name.string' => 'الاسم يجب أن يكون نصاً',
            'name.max' => 'الاسم لا يجب أن يتجاوز 255 حرف',
            
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني موجود بالفعل',
            'email.lowercase' => 'البريد الإلكتروني يجب أن يكون بأحرف صغيرة',
            'email.max' => 'البريد الإلكتروني لا يجب أن يتجاوز 255 حرف',
            
            'password.required' => 'كلمة السر مطلوبة',
            'password.confirmed' => 'كلمات المرور غير متطابقة',
            'password.min' => 'كلمة السر يجب أن تكون 8 أحرف على الأقل',
            'password.regex' => 'كلمة السر يجب أن تحتوي على أحرف كبيرة وصغيرة وأرقام',
            
            'phone.required' => 'رقم الجوال مطلوب',
            'phone.max' => 'رقم الجوال لا يجب أن يتجاوز 15 رقم',
            'phone.unique' => 'رقم الجوال موجود بالفعل',
            'phone.regex' => 'رقم الجوال غير صحيح',
            
            'national_id.required' => 'رقم الهوية مطلوب',
            'national_id.max' => 'رقم الهوية لا يجب أن يتجاوز 20 حرف',
            'national_id.unique' => 'رقم الهوية موجود بالفعل',
            'national_id.regex' => 'رقم الهوية يجب أن يكون أرقاماً فقط',
            
            'birth_date.required' => 'تاريخ الميلاد مطلوب',
            'birth_date.date' => 'تاريخ الميلاد غير صحيح',
            'birth_date.before' => 'تاريخ الميلاد يجب أن يكون قبل اليوم',
            'birth_date.after' => 'تاريخ الميلاد غير صحيح',
            
            'gender.required' => 'الجنس مطلوب',
            'gender.in' => 'الجنس يجب أن يكون ذكر أو أنثى',
            
            'blood_type.required' => 'فصيلة الدم مطلوبة',
            'blood_type.in' => 'فصيلة الدم غير صحيحة',
            
            'city.required' => 'المدينة / العنوان مطلوب',
            'city.string' => 'المدينة يجب أن تكون نصاً',
            'city.max' => 'المدينة لا يجب أن تتجاوز 255 حرف',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'الاسم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة السر',
            'phone' => 'رقم الجوال',
            'national_id' => 'رقم الهوية',
            'birth_date' => 'تاريخ الميلاد',
            'gender' => 'الجنس',
            'blood_type' => 'فصيلة الدم',
            'city' => 'المدينة',
        ];
    }

    /**
     * Check if donor is at least 18 years old
     */
    public function after(): array
    {
        return [
            function ($validator) {
                $birthDate = Carbon::parse($this->birth_date);
                $age = $birthDate->diffInYears(Carbon::now());
                
                if ($age < 18) {
                    $validator->errors()->add(
                        'birth_date',
                        'يجب أن يكون عمرك 18 سنة على الأقل للتبرع'
                    );
                }
                
                if ($age > 100) {
                    $validator->errors()->add(
                        'birth_date',
                        'تاريخ الميلاد غير صحيح'
                    );
                }
            }
        ];
    }
}
