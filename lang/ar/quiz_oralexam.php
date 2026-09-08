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

$string['pluginname'] = 'مقيم الاختبارات الشفهية والعملية';
$string['oralexam'] = 'التقييم الشفهي';
$string['oralexam:view'] = 'عرض تقييمات الاختبار الشفهي';
$string['oralexam:evaluate'] = 'إجراء واعتماد تقييمات الاختبار الشفهي';
$string['privacy:metadata'] = 'إضافة تقييم الاختبارات الشفهية تسجل الدرجات مباشرة في جداول الاختبارات وبنك الأسئلة الرسمية في مودل ولا تخزن بيانات شخصية إضافية.';

$string['evaluationsheet'] = 'استمارة التقييم الشفهي المباشر';
$string['selectstudent'] = 'اختر الطالب لبدء التقييم';
$string['nostudentsfound'] = 'لم يتم العثور على أي طالب مطابق للمجموعة أو التصفية المختارة.';
$string['allgroups'] = 'جميع المجموعات';
$string['allstatuses'] = 'جميع الحالات';
$string['status_evaluated'] = 'تم التقييم';
$string['status_pending'] = 'بانتظار التقييم';
$string['searchstudent'] = 'البحث بالاسم أو الرقم الأكاديمي...';

$string['totalstudents'] = 'إجمالي الطلاب';
$string['evaluatedstudents'] = 'الطلاب المقيمين';
$string['pendingstudents'] = 'المتبقين';
$string['averagegrade'] = 'متوسط الدرجات';

$string['evaluating'] = 'تقييم الطالب';
$string['evaluatedby'] = 'تم التقييم بواسطة {$a->examiner} بتاريخ {$a->date}';
$string['lastattemptgrade'] = 'الدرجة الحالية: {$a->grade} من {$a->maxgrade} ({$a->percent}%)';
$string['newattempt'] = 'تسجيل محاولة جديدة (إعادة)';
$string['editingattempt'] = 'تعديل المحاولة رقم #{$a}';

$string['questionno'] = 'السؤال رقم #{$a}';
$string['competency'] = 'الكفاية';
$string['nocompetency'] = 'غير مرتبط بكفاية';
$string['maxmark'] = 'الدرجة: {$a}';
$string['quickscore'] = 'الدرجة السريعة:';
$string['zero'] = '0% (صفر)';
$string['half'] = '50% (نصف)';
$string['full'] = '100% (كاملة)';
$string['custommark'] = 'الدرجة';
$string['examinernotes'] = 'ملاحظات المقيم على السؤال:';
$string['generalfeedback'] = 'الملاحظات العامة على الاختبار الشفهي';
$string['generalfeedback_placeholder'] = 'اكتب ملاحظاتك الشاملة حول أداء الطالب، مهارات التواصل، أو أي تعليق ختامي...';

$string['saveandfinish'] = 'حفظ واعتماد التقييم الشفهي';
$string['submitting'] = 'جاري الاعتماد ورصد الدرجة...';
$string['evaluationsaved'] = 'تم حفظ واعتماد التقييم الشفهي للطالب {$a} بنجاح وترحيل الدرجة لسجل الدرجات والكفايات!';
$string['evaluationfailed'] = 'حدث خطأ أثناء حفظ التقييم. يرجى المحاولة مرة أخرى.';
$string['backtolist'] = 'العودة لقائمة الطلاب';
$string['confirmfinish'] = 'هل أنت متأكد من اعتماد هذا التقييم الشفهي؟ سيتم تحديث الدرجة فوراً في سجل الدرجات الرسمي للمقرر.';
$string['computedtotal'] = 'المجموع المحسوب:';
$string['viewquizresults'] = 'عرض جدول النتائج المعتمد والدرجات';
$string['cannotattemptoral'] = 'هذا اختبار شفهي / عملي يتم تقييمه ورصد درجاته مباشرة من قبل المقيم. غير مصرح للطلاب ببدء المحاولة أو إدخال الإجابات ذاتياً.';
$string['oralexamnotice'] = 'اختبار شفهي / عملي';
$string['oralexamnotice_desc'] = 'يتم إجراء وتقييم هذا الاختبار شفهياً وعملياً من قِبل المدرب أو لجنة الاختبارات. لا يتطلب حل الاختبار ذاتياً من قِبل المتدرب.';
$string['recordingattempt'] = 'تسجيل محاولة جديدة رقم #{$a}';
$string['resumingattempt'] = 'استكمال المحاولة الجارية رقم #{$a}';
$string['prevattempts'] = 'المحاولات السابقة:';
$string['clickstudentprompt'] = 'اضغط على أي طالب من القائمة الجانبية لبدء استمارة التقييم الشفهي ورصد الدرجات.';
$string['studentnotenrolled'] = 'الطالب المختار غير مسجل في هذا المقرر الدراسي.';
$string['unratedwarning'] = 'تنبيه: يوجد {$a} سؤال لم يتم رصد درجات لها.\nسيتم احتساب الأسئلة المتروكة تلقائياً بدرجة (صفر).\n\nهل تريد المتابعة وحفظ واعتماد التقييم؟';
