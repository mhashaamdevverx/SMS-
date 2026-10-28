import requests
import re
import json
import subprocess
import os

BASE_URL = "http://127.0.0.1:8000"
session = requests.Session()

results = []
bugs = []

def log_test(test_id, module, description, status, details=None):
    results.append({
        'test_id': test_id,
        'module': module,
        'description': description,
        'status': status,
        'details': details or ''
    })
    print(f"[{status}] {test_id} - {module}: {description}")

def log_bug(bug_id, module, url, severity, steps, expected, actual):
    bugs.append({
        'bug_id': bug_id,
        'module': module,
        'url': url,
        'severity': severity,
        'steps': steps,
        'expected': expected,
        'actual': actual
    })
    print(f"[BUG - {severity}] {bug_id} ({module}): {expected} vs {actual}")

def get_csrf(html):
    m = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', html)
    if not m:
        m = re.search(r'content=["\']([^"\']+)["\']\s+name=["\']csrf-token["\']', html)
    if not m:
        m = re.search(r'name=["\']csrf-token["\']\s+content=["\']([^"\']+)["\']', html)
    return m.group(1) if m else None

def run_php(cmd):
    proc = subprocess.run(f"php {cmd}", shell=True, capture_output=True, text=True)
    return proc.stdout.strip()

print("==================================================")
print("STARTING PHASE 7: EXAMINATION SYSTEM TEST SUITE")
print("==================================================")

# Step 1: Admin Login with our QA Admin
r_login_page = session.get(f"{BASE_URL}/login")
csrf = get_csrf(r_login_page.text)
login_payload = {
    '_token': csrf,
    'email': 'qa_admin@gmail.com',
    'password': 'QA_Admin@2026!',
    'account_type': 'admin'
}
r_login = session.post(f"{BASE_URL}/login", data=login_payload, allow_redirects=True)
if r_login.status_code == 200 and '/admin/dashboard' in r_login.url:
    log_test('TC_EXAM_AUTH_01', 'Auth', 'QA Admin login successful', 'PASS')
else:
    log_test('TC_EXAM_AUTH_01', 'Auth', 'QA Admin login successful', 'FAIL', f"URL: {r_login.url}")

# ==========================================
# 1. SETUP DEDICATED QA CLASS, SUBJECT & STUDENT
# ==========================================
print("Setting up dedicated QA records for examination testing...")
setup_res_raw = run_php("qa_reports/manage_phase7_records.php setup")
try:
    setup_data = json.loads(setup_res_raw)
except Exception as e:
    print(f"Error parsing setup data: {e}, raw: {setup_res_raw}")
    setup_data = {}

qa_class_id = setup_data.get('class_id')
qa_subject_id = setup_data.get('subject_id')
qa_student_id = setup_data.get('student_id')
qa_std_id = setup_data.get('std_id')

if qa_class_id and qa_subject_id and qa_student_id:
    log_test('TC_EXAM_SETUP_01', 'Setup', f'Dedicated QA Class (#{qa_class_id}), Subject (#{qa_subject_id}), and Student (#{qa_student_id}) initialized', 'PASS')
else:
    log_test('TC_EXAM_SETUP_01', 'Setup', 'Failed to initialize QA foundation records', 'FAIL')

# Update qa_records_log.json immediately
log_file = 'qa_reports/qa_records_log.json'
try:
    with open(log_file, 'r') as f:
        records_log = json.load(f)
except Exception:
    records_log = []

if qa_class_id:
    records_log.append({
        'table': 'classes',
        'id': qa_class_id,
        'identifier': 'QA_Class_Exam',
        'status': 'active_in_db',
        'details': {'class_name': 'QA_Class_Exam', 'class_no': 998, 'chairs': 30}
    })
if qa_subject_id:
    records_log.append({
        'table': 'subjects',
        'id': qa_subject_id,
        'identifier': 'QA_Subject_Physics',
        'status': 'active_in_db',
        'details': {'subject_name': 'QA_Subject_Physics', 'total_marks': 100}
    })
if qa_student_id:
    records_log.append({
        'table': 'students',
        'id': qa_student_id,
        'identifier': 'qa_exam_student01@gmail.com',
        'status': 'active_in_db',
        'details': {'name': 'QA_Test Student_Exam01', 'std_id': 9901, 'email': 'qa_exam_student01@gmail.com'}
    })

with open(log_file, 'w') as f:
    json.dump(records_log, f, indent=4)
print(f"Logged QA Class #{qa_class_id}, Subject #{qa_subject_id}, and Student #{qa_student_id} in qa_records_log.json")

# ==========================================
# 2. SEATING ARRANGEMENTS TESTING
# ==========================================
r_arr_list = session.get(f"{BASE_URL}/arrangements")
if r_arr_list.status_code == 200:
    log_test('TC_ARR_LIST_01', 'Seating Arrangements', 'Arrangements list page loads (HTTP 200)', 'PASS')
else:
    log_test('TC_ARR_LIST_01', 'Seating Arrangements', 'Arrangements list page load failed', 'FAIL', f"Status: {r_arr_list.status_code}")

r_arr_create = session.get(f"{BASE_URL}/add-arrangement")
arr_csrf = get_csrf(r_arr_create.text) or csrf
if r_arr_create.status_code == 200:
    log_test('TC_ARR_FORM_01', 'Seating Arrangements', 'Generate Seating Arrangement form page loads (HTTP 200)', 'PASS')
else:
    log_test('TC_ARR_FORM_01', 'Seating Arrangements', 'Generate Seating Arrangement form load failed', 'FAIL')

# Validation: Empty class selection
r_arr_val = session.post(f"{BASE_URL}/add-arrangement", data={'_token': arr_csrf}, headers={'Referer': f"{BASE_URL}/add-arrangement"}, allow_redirects=False)
log_test('TC_ARR_VAL_01', 'Seating Arrangements', 'Validation fails on empty class selection (HTTP 302 redirect)', 'PASS' if r_arr_val.status_code == 302 else 'FAIL')

# Create Seating Arrangement for QA Class
r_arr_store = session.post(f"{BASE_URL}/add-arrangement", data={
    '_token': arr_csrf,
    'class_ids[]': [qa_class_id]
}, headers={'Referer': f"{BASE_URL}/add-arrangement"}, allow_redirects=True)

# Verify Arrangement in DB
arr_raw = run_php("qa_reports/manage_phase7_records.php get_arrangement")
try:
    arr_data = json.loads(arr_raw)
except Exception:
    arr_data = {}

arrangement_rec = arr_data.get('arrangement')
qa_arrangement_id = arrangement_rec.get('id') if arrangement_rec else None

if qa_arrangement_id:
    log_test('TC_ARR_STORE_01', 'Seating Arrangements', f'Seating arrangement generated for QA Student #{qa_std_id} (Arrangement ID: {qa_arrangement_id}, Room: {arrangement_rec.get("room")}, Chair: {arrangement_rec.get("chairs")})', 'PASS')
    records_log.append({
        'table': 'arrangements',
        'id': qa_arrangement_id,
        'identifier': f"Arrangement-Roll-{qa_std_id}",
        'status': 'active_in_db',
        'details': arrangement_rec
    })
    with open(log_file, 'w') as f:
        json.dump(records_log, f, indent=4)
else:
    log_test('TC_ARR_STORE_01', 'Seating Arrangements', 'Failed to generate seating arrangement in DB', 'FAIL')

# ==========================================
# 3. ROLL SLIP & EXAM DATESHEET TESTING
# ==========================================
r_slip_page = session.get(f"{BASE_URL}/issue-roll-slips")
slip_csrf = get_csrf(r_slip_page.text) or csrf
if r_slip_page.status_code == 200:
    log_test('TC_SLIP_FORM_01', 'Roll Slips', 'Issue Roll Slips form page loads (HTTP 200)', 'PASS')
else:
    log_test('TC_SLIP_FORM_01', 'Roll Slips', 'Issue Roll Slips form load failed', 'FAIL')

# Validation: Empty exam name
r_slip_val = session.post(f"{BASE_URL}/issue-roll-slips", data={
    '_token': slip_csrf,
    'class_name': 'QA_Class_Exam'
}, headers={'Referer': f"{BASE_URL}/issue-roll-slips"}, allow_redirects=False)
log_test('TC_SLIP_VAL_01', 'Roll Slips', 'Validation fails on missing exam name and schedule (HTTP 302 redirect)', 'PASS' if r_slip_val.status_code == 302 else 'FAIL')

# Issue Roll Slip for QA Class
slip_payload = {
    '_token': slip_csrf,
    'exam_name': 'QA_Midterm_Exam_2026',
    'exam_session': '2026-2027',
    'class_name': 'QA_Class_Exam',
    'schedules[0][subject_id]': qa_subject_id,
    'schedules[0][exam_date]': '2026-11-15'
}
r_issue_slip = session.post(f"{BASE_URL}/issue-roll-slips", data=slip_payload, headers={'Referer': f"{BASE_URL}/issue-roll-slips"}, allow_redirects=True)

# Verify Exam, Schedules, and Roll Slip in DB
slip_db_raw = run_php("qa_reports/manage_phase7_records.php get_roll_slip")
try:
    slip_db_data = json.loads(slip_db_raw)
except Exception:
    slip_db_data = {}

exam_rec = slip_db_data.get('exam')
roll_slip_rec = slip_db_data.get('roll_slip')
schedules_list = slip_db_data.get('schedules', [])

qa_exam_id = exam_rec.get('id') if exam_rec else None
qa_roll_slip_id = roll_slip_rec.get('id') if roll_slip_rec else None
qa_schedule_id = schedules_list[0].get('id') if schedules_list else None

if qa_exam_id:
    log_test('TC_EXAM_STORE_01', 'Exams', f'Exam entity created in DB (Exam ID: {qa_exam_id}, Name: {exam_rec.get("name")})', 'PASS')
    records_log.append({
        'table': 'exams',
        'id': qa_exam_id,
        'identifier': 'QA_Midterm_Exam_2026',
        'status': 'active_in_db',
        'details': exam_rec
    })
else:
    log_test('TC_EXAM_STORE_01', 'Exams', 'Exam record not created in DB', 'FAIL')

if qa_schedule_id:
    log_test('TC_SCHED_STORE_01', 'Exam Schedule', f'Datesheet schedule created in DB (Schedule ID: {qa_schedule_id}, Date: {schedules_list[0].get("exam_date")})', 'PASS')
    records_log.append({
        'table': 'exam_schedules',
        'id': qa_schedule_id,
        'identifier': f"Schedule-Exam-{qa_exam_id}",
        'status': 'active_in_db',
        'details': schedules_list[0]
    })
else:
    log_test('TC_SCHED_STORE_01', 'Exam Schedule', 'Exam schedule not created in DB', 'FAIL')

if qa_roll_slip_id:
    log_test('TC_SLIP_STORE_01', 'Roll Slips', f'Roll Slip issued and stored in DB (Roll Slip ID: {qa_roll_slip_id}, Student: {roll_slip_rec.get("student_name")}, Room: {roll_slip_rec.get("room_no")}, Seat: {roll_slip_rec.get("chair_no")})', 'PASS')
    records_log.append({
        'table': 'roll_slips',
        'id': qa_roll_slip_id,
        'identifier': f"RollSlip-{qa_roll_slip_id}-Std-{qa_std_id}",
        'status': 'active_in_db',
        'details': roll_slip_rec
    })
else:
    log_test('TC_SLIP_STORE_01', 'Roll Slips', 'Roll slip record not created in DB', 'FAIL')

with open(log_file, 'w') as f:
    json.dump(records_log, f, indent=4)

# Test PDF Download of Roll Slip
if qa_roll_slip_id:
    r_pdf = session.get(f"{BASE_URL}/roll-slips/{qa_roll_slip_id}/download")
    content_type = r_pdf.headers.get('Content-Type', '')
    is_pdf = 'application/pdf' in content_type and len(r_pdf.content) > 1000
    if r_pdf.status_code == 200 and is_pdf:
        log_test('TC_SLIP_PDF_01', 'Roll Slips PDF', f'Roll Slip PDF generated and downloaded successfully (HTTP 200, Content-Type: {content_type}, Size: {len(r_pdf.content)} bytes)', 'PASS')
    else:
        log_test('TC_SLIP_PDF_01', 'Roll Slips PDF', f'Roll Slip PDF download failed (Status: {r_pdf.status_code}, Type: {content_type})', 'FAIL')

    # Test Roll Slip Update (Room/Chair reassignment)
    r_slip_list = session.get(f"{BASE_URL}/roll-slips")
    upd_slip_csrf = get_csrf(r_slip_list.text) or csrf
    r_upd_slip = session.post(f"{BASE_URL}/roll-slips/{qa_roll_slip_id}", data={
        '_token': upd_slip_csrf,
        '_method': 'PUT',
        'student_name': 'QA_Test Student_Exam01',
        'roll_no': qa_std_id,
        'class_name': 'QA_Class_Exam',
        'section_name': 'A',
        'room_no': 'QA_Room_202',
        'chair_no': 5
    }, headers={'Referer': f"{BASE_URL}/roll-slips"}, allow_redirects=True)

    slip_recheck = json.loads(run_php("qa_reports/manage_phase7_records.php get_roll_slip")).get('roll_slip', {})
    if slip_recheck and slip_recheck.get('room_no') == 'QA_Room_202' and slip_recheck.get('chair_no') == 5:
        log_test('TC_SLIP_UPD_01', 'Roll Slips', 'Roll Slip room and seat update persists in DB', 'PASS')
    else:
        log_test('TC_SLIP_UPD_01', 'Roll Slips', 'Roll Slip update failed in DB', 'FAIL')

# ==========================================
# 4. RESULTS ENTRY & GRADING CALCULATION
# ==========================================
r_results_list = session.get(f"{BASE_URL}/results")
if r_results_list.status_code == 200:
    log_test('TC_RES_LIST_01', 'Results Management', 'Results list page loads (HTTP 200)', 'PASS')
else:
    log_test('TC_RES_LIST_01', 'Results Management', 'Results list page load failed', 'FAIL')

r_add_res = session.get(f"{BASE_URL}/add-result")
res_csrf = get_csrf(r_add_res.text) or csrf
if r_add_res.status_code == 200:
    log_test('TC_RES_FORM_01', 'Results Management', 'Add Result page loads (HTTP 200)', 'PASS')
else:
    log_test('TC_RES_FORM_01', 'Results Management', 'Add Result page load failed', 'FAIL')

# Validation: Marks exceeding total marks (> 100)
r_val_res1 = session.post(f"{BASE_URL}/add-result/student-result", data={
    '_token': res_csrf,
    'student_id': qa_student_id,
    'student_class': 'QA_Class_Exam',
    'term': 'First Term',
    'marks[QA_Subject_Physics]': 120
}, headers={'Referer': f"{BASE_URL}/add-result"}, allow_redirects=False)
log_test('TC_RES_VAL_01', 'Results Validation', 'Validation rejects marks exceeding total marks (120/100) (HTTP 302 redirect)', 'PASS' if r_val_res1.status_code == 302 else 'FAIL')

# Validation: Negative marks
r_val_res2 = session.post(f"{BASE_URL}/add-result/student-result", data={
    '_token': res_csrf,
    'student_id': qa_student_id,
    'student_class': 'QA_Class_Exam',
    'term': 'First Term',
    'marks[QA_Subject_Physics]': -15
}, headers={'Referer': f"{BASE_URL}/add-result"}, allow_redirects=False)
log_test('TC_RES_VAL_02', 'Results Validation', 'Validation rejects negative marks (-15) (HTTP 302 redirect)', 'PASS' if r_val_res2.status_code == 302 else 'FAIL')

# Test Results Entry - Passing Scenario (85 / 100 -> Grade 'A')
r_entry_pass = session.post(f"{BASE_URL}/add-result/student-result", data={
    '_token': res_csrf,
    'student_id': qa_student_id,
    'student_class': 'QA_Class_Exam',
    'term': 'First Term',
    'marks[QA_Subject_Physics]': 85
}, headers={'Referer': f"{BASE_URL}/add-result"}, allow_redirects=True)

res_pass_raw = run_php("qa_reports/manage_phase7_records.php get_result 'First Term'")
res_pass_data = json.loads(res_pass_raw).get('result', {}) if res_pass_raw else {}
qa_result_id = res_pass_data.get('id') if res_pass_data else None

if qa_result_id and float(res_pass_data.get('percentage', 0)) == 85.00 and res_pass_data.get('grade') == 'A':
    log_test('TC_RES_ENTRY_01', 'Grading Calculation', f'Result entry & grade calculation verified for Passing score: 85/100 -> 85.00%, Grade: {res_pass_data.get("grade")} (Result ID: {qa_result_id})', 'PASS')
    records_log.append({
        'table': 'results',
        'id': qa_result_id,
        'identifier': f"Result-Student-{qa_student_id}-FirstTerm",
        'status': 'active_in_db',
        'details': res_pass_data
    })
    with open(log_file, 'w') as f:
        json.dump(records_log, f, indent=4)
else:
    log_test('TC_RES_ENTRY_01', 'Grading Calculation', 'Passing score result calculation mismatch', 'FAIL', f"Data: {res_pass_data}")

# Test Results Entry - Failing Scenario (42 / 100 -> Grade 'F')
r_entry_fail = session.post(f"{BASE_URL}/add-result/student-result", data={
    '_token': res_csrf,
    'student_id': qa_student_id,
    'student_class': 'QA_Class_Exam',
    'term': 'Mid Term',
    'marks[QA_Subject_Physics]': 42
}, headers={'Referer': f"{BASE_URL}/add-result"}, allow_redirects=True)

res_fail_raw = run_php("qa_reports/manage_phase7_records.php get_result 'Mid Term'")
res_fail_data = json.loads(res_fail_raw).get('result', {}) if res_fail_raw else {}
if res_fail_data and float(res_fail_data.get('percentage', 0)) == 42.00 and res_fail_data.get('grade') == 'F':
    log_test('TC_RES_ENTRY_02', 'Grading Calculation', f'Result entry & grade calculation verified for Failing score: 42/100 -> 42.00%, Grade: {res_fail_data.get("grade")} (Fail)', 'PASS')
    records_log.append({
        'table': 'results',
        'id': res_fail_data.get('id'),
        'identifier': f"Result-Student-{qa_student_id}-MidTerm",
        'status': 'active_in_db',
        'details': res_fail_data
    })
else:
    log_test('TC_RES_ENTRY_02', 'Grading Calculation', 'Failing score result calculation mismatch', 'FAIL', f"Data: {res_fail_data}")

# Test Results Entry - Distinction Scenario (94 / 100 -> Grade 'A+')
r_entry_dist = session.post(f"{BASE_URL}/add-result/student-result", data={
    '_token': res_csrf,
    'student_id': qa_student_id,
    'student_class': 'QA_Class_Exam',
    'term': 'Final Term',
    'marks[QA_Subject_Physics]': 94
}, headers={'Referer': f"{BASE_URL}/add-result"}, allow_redirects=True)

res_dist_raw = run_php("qa_reports/manage_phase7_records.php get_result 'Final Term'")
res_dist_data = json.loads(res_dist_raw).get('result', {}) if res_dist_raw else {}
if res_dist_data and float(res_dist_data.get('percentage', 0)) == 94.00 and res_dist_data.get('grade') == 'A+':
    log_test('TC_RES_ENTRY_03', 'Grading Calculation', f'Result entry & grade calculation verified for Distinction score: 94/100 -> 94.00%, Grade: {res_dist_data.get("grade")} (A+)', 'PASS')
    records_log.append({
        'table': 'results',
        'id': res_dist_data.get('id'),
        'identifier': f"Result-Student-{qa_student_id}-FinalTerm",
        'status': 'active_in_db',
        'details': res_dist_data
    })
else:
    log_test('TC_RES_ENTRY_03', 'Grading Calculation', 'Distinction score calculation mismatch', 'FAIL', f"Data: {res_dist_data}")

# Test Results Deletion
if qa_result_id:
    r_del_res = session.post(f"{BASE_URL}/results/{qa_result_id}", data={
        '_token': res_csrf,
        '_method': 'DELETE'
    }, headers={'Referer': f"{BASE_URL}/results"}, allow_redirects=True)
    log_test('TC_RES_DEL_01', 'Results Management', f'Result record #{qa_result_id} deleted via UI successfully', 'PASS' if r_del_res.status_code == 200 else 'FAIL')

# ==========================================
# 5. CLEANUP ALL PHASE 7 QA RECORDS
# ==========================================
print("Running complete cleanup of Phase 7 QA records...")
run_php("qa_reports/manage_phase7_records.php cleanup")

# Update statuses in qa_records_log.json
for r in records_log:
    if r.get('table') in ['classes', 'subjects', 'students', 'arrangements', 'exams', 'exam_schedules', 'roll_slips', 'results']:
        if any(qa_kw in str(r.get('identifier', '')) for qa_kw in ['QA_', 'qa_', '9901']):
            r['status'] = 'deleted_during_test'

with open(log_file, 'w') as f:
    json.dump(records_log, f, indent=4)

# Save Phase 7 results
with open('qa_reports/phase7_results.json', 'w') as f:
    json.dump({
        'phase': 'Phase 7: Examination System',
        'total_tests': len(results),
        'passed': len([r for r in results if r['status'] == 'PASS']),
        'failed': len([r for r in results if r['status'] == 'FAIL']),
        'bugs_count': len(bugs),
        'results': results,
        'bugs': bugs
    }, f, indent=4)

print("\n==================================================")
print(f"PHASE 7 COMPLETE: {len([r for r in results if r['status'] == 'PASS'])}/{len(results)} Passed. {len(bugs)} Bug(s) Found.")
print("==================================================")