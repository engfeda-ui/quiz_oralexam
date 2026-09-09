<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Arabic strings for quiz_oralexam.
 *
 * @package    quiz_oralexam
 * @copyright  2026 Mahmoud Salem
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['allgroups'] = 'جميع المجموعات';
$string['allstatuses'] = 'جميع المتدربين';
$string['averagegrade'] = 'متوسط درجات المجموعة';
$string['backtolist'] = 'العودة لقائمة المتدربين';
$string['cannotattemptoral'] = 'هذا اختبار شفهي / عملي يتم تقييمه ورصد درجاته مباشرة من قبل المقيم. ' .
    'غير مصرح للطلاب ببدء المحاولة أو إدخال الإجابات ذاتياً.';
$string['clickstudentprompt'] = 'اختر متدرباً من القائمة الجانبية لفتح استمارة التقييم الشفهي ورصد الدرجات.';
$string['competency'] = 'الجدارة المرتبطة';
$string['computedtotal'] = 'المجموع الكلي:';
$string['confirmfinish'] = 'هل أنت متأكد من اعتماد التقييم الشفهي؟ سيتم ترحيل وتحديث الدرجة في سجل الدرجات المعتمد فوراً.';
$string['custommark'] = 'الدرجة';
$string['editingattempt'] = 'تعديل المحاولة رقم #{$a}';
$string['evaluatedby'] = 'تم التقييم بواسطة {$a->examiner} بتاريخ {$a->date}';
$string['evaluatedstudents'] = 'تم التقييم';
$string['evaluating'] = 'جاري التقييم';
$string['evaluationfailed'] = 'حدث خطأ أثناء حفظ التقييم الشفهي، يرجى المحاولة مرة أخرى.';
$string['evaluationsaved'] = 'تم حفظ واعتماد التقييم الشفهي للمتدرب {$a} بنجاح وترحيل درجته لسجل الدرجات!';
$string['evaluationsheet'] = 'استمارة التقييم الشفهي والعملي المباشر';
$string['examinernotes'] = 'ملاحظات وتغذية راجعة من المقيم:';
$string['full'] = '100% (كاملة)';
$string['generalfeedback'] = 'الملاحظات العامة على الاختبار الشفهي';
$string['generalfeedback_placeholder'] = 'أدخل أي ملاحظات عامة حول أداء المتدرب، مهارات التواصل، ' .
    'أو النقاط التي تميز أو تعثر بها...';
$string['gotoquizsettings'] = 'تفعيل الاختبار الشفهي في الإعدادات';
$string['half'] = '50% (نصف)';
$string['lastattemptgrade'] = 'الدرجة المسجلة: {$a->grade} / {$a->maxgrade} ({$a->percent}%)';
$string['maxmark'] = 'الدرجة العظمى: {$a} درجات';
$string['newattempt'] = 'تسجيل محاولة جديدة (إعادة تقييم)';
$string['nocompetency'] = 'لا توجد جدارة مرتبطة';
$string['nostudentsfound'] = 'لا يوجد متدربون مسجلون يطابقون المجموعة أو معايير البحث المحددة.';
$string['notanoralexam_desc'] = 'تم إيقاف إمكانية رصد الدرجات من هذه الصفحة لأن هذا الاختبار غير مصنف كاختبار شفهي أو عملي في إعداداته، مما يتيح للمتدربين الدخول وحل الاختبار ذاتياً. إذا كان هذا الاختبار مخصصاً للتقييم الشفهي المباشر من قبل المدرب، يرجى تفعيل خيار "اختبار شفهي / عملي" من إعدادات الاختبار.';
$string['notanoralexam_title'] = 'هذا الاختبار غير مفعّل كاختبار شفهي / عملي';
$string['oralexam'] = 'التقييم الشفهي والعملي';
$string['oralexam:evaluate'] = 'إجراء وتقييم واعتماد الاختبارات الشفهية والعملية';
$string['oralexam:view'] = 'معاينة تقييمات الاختبار الشفهي والعملي';
$string['oralexamnotice'] = 'تنبيه: اختبار شفهي / عملي';
$string['oralexamnotice_desc'] = 'يتم إجراء وتقييم هذا الاختبار مباشرة من قبل المدرب أو لجنة التقييم. لا يُسمح بالحل الذاتي.';
$string['pendingstudents'] = 'قيد الانتظار';
$string['pluginname'] = 'مقيم الاختبارات الشفهية والعملية';
$string['prevattempts'] = 'المحاولات والتقييمات السابقة:';
$string['privacy:metadata'] = 'إضافة مقيم الاختبارات الشفهية تقوم برصد الدرجات مباشرة في جداول الاختبارات الأساسية ' .
    'التابعة لمودل ولا تخزن أي بيانات شخصية خاصة بشكل مستقل.';
$string['questionno'] = 'السؤال رقم #{$a}';
$string['quickscore'] = 'رصد سريع:';
$string['recordingattempt'] = 'تسجيل محاولة جديدة #{$a}';
$string['resumingattempt'] = 'استئناف التقييم للمحاولة الجارية #{$a}';
$string['saveandfinish'] = 'حفظ واعتماد التقييم الشفهي';
$string['searchstudent'] = 'بحث بالاسم أو الرقم التدريبي...';
$string['selectstudent'] = 'اختر متدرباً لبدء التقييم';
$string['status_evaluated'] = 'تم التقييم';
$string['status_pending'] = 'في انتظار التقييم';
$string['studentnotenrolled'] = 'المتدرب المحدد غير مسجل في هذا المقرر الدراسي.';
$string['submitting'] = 'جاري حفظ التقييم وترحيل الدرجات...';
$string['totalstudents'] = 'إجمالي المتدربين';
$string['unratedwarning'] = "تنبيه: يوجد {$a} أسئلة لم يتم رصد درجات لها.\n" .
    "الأسئلة المتروكة سيتم احتساب درجتها تلقائياً (0.0).\n\n" .
    "هل ترغب في المتابعة واعتماد التقييم؟";
$string['viewquizresults'] = 'معاينة سجل درجات الاختبار المعتمد';
$string['zero'] = '0% (صفر)';
