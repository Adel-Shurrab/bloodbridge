<!-- Terms and Conditions Modal -->
<div class="modal-overlay" id="termsModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0, 0, 0, 0.5); z-index: 1000; overflow-y: auto;">
  <div style="position: relative; background: white; max-width: 700px; margin: 2rem auto; border-radius: 12px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); animation: slideUp 0.3s ease;">
    <!-- Modal Header -->
    <div style="padding: 2rem; border-bottom: 2px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
      <h2 style="margin: 0; color: #d32f2f; font-size: 1.5rem; font-weight: 700;">الشروط والأحكام</h2>
      <button onclick="closeTermsModal()" style="background: none; border: none; font-size: 1.5rem; color: #9ca3af; cursor: pointer; padding: 0;">✕</button>
    </div>

    <!-- Modal Content -->
    <div style="padding: 2rem; max-height: 60vh; overflow-y: auto; color: #4a5568; line-height: 1.8; font-size: 0.95rem;">
      
      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-top: 0; margin-bottom: 0.75rem;">1. قبول الشروط</h3>
      <p>باستخدامك لمنصة BloodBridge، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا كنت لا توافق على أي جزء منها، يرجى عدم استخدام المنصة.</p>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">2. الخدمات المقدمة</h3>
      <p>تقدم BloodBridge خدمة ربط المتبرعين بطلبات التبرع بالدم وتنظيم مواعيد التبرع. نحن لا نقدم استشارات طبية، والقرار النهائي يبقى للمستشفيات والهيئات الطبية.</p>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">3. مسؤوليات المستخدم</h3>
      <ul style="margin: 0; padding-right: 1.5rem;">
        <li>توفير معلومات دقيقة وصحيحة</li>
        <li>الالتزام بالقوانين المحلية والدولية</li>
        <li>عدم استخدام المنصة في أغراض غير قانونية</li>
        <li>الإفصاح عن أي حالات طبية قد تؤثر على أهليتك للتبرع</li>
      </ul>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">4. الخصوصية والبيانات</h3>
      <p>بياناتك الطبية والشخصية حساسة وستُعامل بسرية تامة. راجع <a href="{{ route('privacy') }}" style="color: #d32f2f; text-decoration: none;">سياسة الخصوصية</a> لمزيد من التفاصيل.</p>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">5. عدم المسؤولية</h3>
      <p>BloodBridge غير مسؤولة عن:</p>
      <ul style="margin: 0; padding-right: 1.5rem;">
        <li>أي مضاعفات طبية تحدث أثناء أو بعد التبرع</li>
        <li>دقة المعلومات الطبية التي يوفرها المستخدمون</li>
        <li>تأخر الاستجابة للطلبات الطارئة</li>
      </ul>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">6. حقوقنا</h3>
      <p>نحتفظ بالحق في:</p>
      <ul style="margin: 0; padding-right: 1.5rem;">
        <li>حذف الحسابات التي تنتهك الشروط</li>
        <li>تعديل أو إيقاف الخدمات في أي وقت</li>
        <li>استخدام بياناتك المجهلة للبحث الطبي</li>
      </ul>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">7. التعديل على الشروط</h3>
      <p>قد نعدل هذه الشروط في أي وقت. الاستخدام المستمر للمنصة يعني قبولك للتغييرات.</p>

      <h3 style="color: #d32f2f; font-size: 1.1rem; margin-bottom: 0.75rem;">8. القانون الحاكم</h3>
      <p>تخضع هذه الشروط للقانون المحلي حيث تعمل BloodBridge.</p>

      <div style="background: #f0f4ff; border-right: 4px solid #d32f2f; padding: 1rem; border-radius: 4px; margin-top: 1.5rem;">
        <p style="margin: 0; font-size: 0.9rem;"><strong>آخر تحديث:</strong> {{ date('d-m-Y') }}</p>
      </div>
    </div>

    <!-- Modal Footer -->
    <div style="padding: 1.5rem; border-top: 1px solid #e5e7eb; display: flex; gap: 1rem; justify-content: flex-end;">
      <button onclick="closeTermsModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #e5e7eb; background: white; color: #4a5568; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
        إغلاق
      </button>
      <button onclick="acceptTerms()" style="padding: 0.75rem 1.5rem; background: #d32f2f; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s;">
        أوافق
      </button>
    </div>
  </div>
</div>

<style>
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(2rem);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  #termsModal button:hover {
    background: #f3f4f6;
  }

  #termsModal button[onclick="acceptTerms()"]:hover {
    background: #b91c1c;
    transform: translateY(-2px);
  }
</style>

<script>
  function openTermsModal() {
    document.getElementById('termsModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
  }

  function closeTermsModal() {
    document.getElementById('termsModal').style.display = 'none';
    document.body.style.overflow = '';
  }

  function acceptTerms() {
    const termsCheckbox = document.getElementById('termsAgree');
    if (termsCheckbox) {
      termsCheckbox.checked = true;
    }
    closeTermsModal();
  }

  // Close modal when clicking outside
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('termsModal');
    if (modal) {
      modal.addEventListener('click', function(e) {
        if (e.target === modal) {
          closeTermsModal();
        }
      });
    }
  });
</script>
