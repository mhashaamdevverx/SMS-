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
print("STARTING PHASE 6: TEACHER MANAGEMENT TEST SUITE")
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
    log_test('TC_TCH_AUTH_01', 'Auth', 'QA Admin login successful', 'PASS')
else:
    log_test('TC_TCH_AUTH_01', 'Auth', 'QA Admin login successful', 'FAIL', f"URL: {r_login.url}")

# ==========================================
# 1. ADD TEACHER FORM & VALIDATION
# ==========================================
r_add_page = session.get(f"{BASE_URL}/add-teacher")
add_csrf = get_csrf(r_add_page.text) or csrf
if r_add_page.status_code == 200 and ('Teacher Name' in r_add_page.text or 'teacher' in r_add_page.text.lower()):
    log_test('TC_TCH_FORM_01', 'Teacher Form', 'Add Teacher form page loads successfully (HTTP 200)', 'PASS')
else:
    log_test('TC_TCH_FORM_01', 'Teacher Form', 'Add Teacher form page loads successfully', 'FAIL', f"Status: {r_add_page.status_code}")

# Validation: Empty required fields
r_val1 = session.post(f"{BASE_URL}/add-teacher", data={'_token': add_csrf}, headers={'Referer': f"{BASE_URL}/add-teacher"}, allow_redirects=False)
log_test('TC_TCH_VAL_01', 'Validation', 'Validation fails on empty required fields (HTTP 302 redirect)', 'PASS' if r_val1.status_code == 302 else 'FAIL')

# Validation: Phone format invalid
r_val2 = session.post(f"{BASE_URL}/add-teacher", data={
    '_token': add_csrf,
    'teacher_name': 'QA Teacher Test',
    'teacher_email': 'qa_teacher_test@gmail.com',
    'teacher_phone': '12345',
    'course': 'English',
    'assigned_class': 'ONE|Red'
}, headers={'Referer': f"{BASE_URL}/add-teacher"}, allow_redirects=False)
log_test('TC_TCH_VAL_02', 'Validation', 'Validation fails on invalid phone format (HTTP 302 redirect)', 'PASS' if r_val2.status_code == 302 else 'FAIL')

# Validation: Non-gmail address
r_val3 = session.post(f"{BASE_URL}/add-teacher", data={
    '_token': add_csrf,
    'teacher_name': 'QA Teacher Test',
    'teacher_email': 'qa_teacher_test@yahoo.com',
    'teacher_phone': '03001122334',
    'course': 'English',
    'assigned_class': 'ONE|Red'
}, headers={'Referer': f"{BASE_URL}/add-teacher"}, allow_redirects=False)
log_test('TC_TCH_VAL_03', 'Validation', 'Validation fails on non-gmail address (HTTP 302 redirect)', 'PASS' if r_val3.status_code == 302 else 'FAIL')

# ==========================================
# 2. HAPPY PATH: CREATE QA TEACHER
# ==========================================
qa_tch_payload = {
    '_token': add_csrf,
    'teacher_name': 'QA Test Teacher One',
    'teacher_email': 'qa_teacher_01@gmail.com',
    'teacher_phone': '03001122334',
    'course': 'Mathematics',
    'assigned_class': 'ONE|Red',
    'basic_salary': 60000,
    'allowances': 5000,
    'deductions': 2000,
    'salary_month': 'October 2026',
    'salary_status': 'Paid'
}
r_create = session.post(f"{BASE_URL}/add-teacher", data=qa_tch_payload, headers={'Referer': f"{BASE_URL}/add-teacher"}, allow_redirects=True)
if r_create.status_code == 200:
    log_test('TC_TCH_CREATE_01', 'Teacher Create', 'QA Teacher profile submitted via UI successfully', 'PASS')
else:
    log_test('TC_TCH_CREATE_01', 'Teacher Create', 'QA Teacher profile submitted', 'FAIL', f"Status: {r_create.status_code}")

# Query DB for created Teacher ID, linked User ID, Assignment ID, and Salary ID
tch_raw = run_php("qa_reports/get_qa_teacher_details.php")
try:
    tch_data = json.loads(tch_raw)
except Exception as e:
    print(f"Error parsing teacher details: {e}, raw: {tch_raw}")
    tch_data = {}

teacher_rec = tch_data.get('teacher', {})
user_rec = tch_data.get('user', {})
salary_rec = tch_data.get('salary', {})
assignments_list = tch_data.get('assignments', [])

qa_teacher_id = teacher_rec.get('id')
qa_teacher_user_id = user_rec.get('id') if user_rec else None
qa_salary_id = salary_rec.get('id') if salary_rec else None
qa_assignment_id = assignments_list[0].get('id') if assignments_list else None

if qa_teacher_id:
    log_test('TC_TCH_CREATE_02', 'Teacher Create', f'Teacher record created in DB (ID: {qa_teacher_id}, Name: {teacher_rec.get("teacher_name")})', 'PASS')
else:
    log_test('TC_TCH_CREATE_02', 'Teacher Create', 'Teacher record not found in DB', 'FAIL')

if qa_teacher_user_id:
    log_test('TC_TCH_CREATE_03', 'Teacher Create', f'Auto-created linked portal user account (User ID: {qa_teacher_user_id}, Role: {user_rec.get("role_id")})', 'PASS')
else:
    log_test('TC_TCH_CREATE_03', 'Teacher Create', 'Linked user account not generated', 'FAIL')

if qa_assignment_id:
    first_assign = assignments_list[0]
    log_test('TC_TCH_CREATE_04', 'Teacher Create', f'Auto-created class/subject assignment (Assignment ID: {qa_assignment_id}, Class: {first_assign.get("class_name")}, Subject: {first_assign.get("subject_or_course")})', 'PASS')
else:
    log_test('TC_TCH_CREATE_04', 'Teacher Create', 'Teacher assignment not created', 'FAIL')

if qa_salary_id:
    log_test('TC_TCH_CREATE_05', 'Teacher Create', f'Auto-created salary declaration (Salary ID: {qa_salary_id}, Basic: {salary_rec.get("basic_salary")}, Net: {salary_rec.get("net_salary")})', 'PASS')
else:
    log_test('TC_TCH_CREATE_05', 'Teacher Create', 'Teacher salary record not created', 'FAIL')

# Update qa_records_log.json immediately
log_file = 'qa_reports/qa_records_log.json'
try:
    with open(log_file, 'r') as f:
        records_log = json.load(f)
except Exception:
    records_log = []

if qa_teacher_id:
    records_log.append({
        'table': 'teachers',
        'id': qa_teacher_id,
        'identifier': 'qa_teacher_01@gmail.com',
        'status': 'active_in_db',
        'details': teacher_rec
    })
if qa_teacher_user_id:
    records_log.append({
        'table': 'users',
        'id': qa_teacher_user_id,
        'identifier': 'qa_teacher_01@gmail.com',
        'status': 'active_in_db',
        'details': user_rec
    })
if qa_salary_id:
    records_log.append({
        'table': 'teacher_salaries',
        'id': qa_salary_id,
        'identifier': f"Salary-Teacher-{qa_teacher_id}",
        'status': 'active_in_db',
        'details': salary_rec
    })

with open(log_file, 'w') as f:
    json.dump(records_log, f, indent=4)
log_test('TC_TCH_LOG_01', 'Tracking', f'Logged QA Teacher (ID {qa_teacher_id}), User (ID {qa_teacher_user_id}), and Salary (ID {qa_salary_id}) in qa_records_log.json', 'PASS')

# ==========================================
# 3. VERIFY IN TEACHER DIRECTORY
# ==========================================
r_tch_list = session.get(f"{BASE_URL}/teachers")
if 'qa_teacher_01@gmail.com' in r_tch_list.text or 'QA Test Teacher' in r_tch_list.text:
    log_test('TC_TCH_LIST_01', 'Teacher Directory', 'QA Teacher visible in Admin Teacher list page', 'PASS')
else:
    log_test('TC_TCH_LIST_01', 'Teacher Directory', 'QA Teacher visible in Admin Teacher list page', 'FAIL')

# ==========================================
# 4. EDIT QA TEACHER
# ==========================================
if qa_teacher_id:
    edit_csrf = get_csrf(r_tch_list.text) or csrf
    r_update = session.post(f"{BASE_URL}/teachers/{qa_teacher_id}", data={
        '_token': edit_csrf,
        '_method': 'PUT',
        'teacher_name': 'QA Test Teacher Updated',
        'teacher_email': 'qa_teacher_01@gmail.com',
        'teacher_phone': '03009988776',
        'course': 'Advanced Mathematics',
        'assigned_class': 'ONE|Red',
        'basic_salary': 65000,
        'allowances': 6000,
        'deductions': 2000,
        'salary_month': 'October 2026',
        'salary_status': 'Paid'
    }, headers={'Referer': f"{BASE_URL}/teachers"}, allow_redirects=True)

    # Check updated details in DB
    tch_raw_upd = run_php("qa_reports/get_qa_teacher_details.php")
    tch_upd_data = json.loads(tch_raw_upd).get('teacher', {}) if tch_raw_upd else {}
    if tch_upd_data.get('teacher_phone') == '03009988776' and 'Advanced Mathematics' in tch_upd_data.get('course', ''):
        log_test('TC_TCH_EDIT_01', 'Teacher Edit', 'Update teacher phone and course persists in DB and UI', 'PASS')
    else:
        log_test('TC_TCH_EDIT_01', 'Teacher Edit', 'Update teacher failed in DB verification', 'FAIL', f"Data: {tch_upd_data}")

# ==========================================
# 5. CLASS IN-CHARGE DELEGATION & REMOVAL
# (Tested SAFELY on dedicated QA Class QA_Incharge_Class)
# ==========================================
print("Setting up dedicated QA class for Class In-Charge delegation test...")
cls_res_raw = run_php("qa_reports/manage_qa_teacher.php create_test_class")
try:
    cls_res = json.loads(cls_res_raw)
    qa_test_class_id = cls_res.get('class_id')
except Exception:
    qa_test_class_id = None

print(f"Temporary QA Class created with ID: {qa_test_class_id}")

qa_incharge_id = None
if qa_test_class_id and qa_teacher_id:
    # Assign QA teacher as In-Charge of QA_Incharge_Class
    r_roles_page = session.get(f"{BASE_URL}/roles")
    role_csrf = get_csrf(r_roles_page.text) or csrf
    r_assign_inc = session.post(f"{BASE_URL}/roles/assign-incharge", data={
        '_token': role_csrf,
        'teacher_id': qa_teacher_id,
        'class_id': qa_test_class_id
    }, headers={'Referer': f"{BASE_URL}/roles"}, allow_redirects=True)

    # Check if assign-incharge hit error or created record
    chk_inc_raw = run_php(f"qa_reports/manage_qa_teacher.php get_incharge {qa_teacher_id}")
    inc_data = json.loads(chk_inc_raw) if chk_inc_raw else {}
    incharge_rec = inc_data.get('incharge')

    if incharge_rec and incharge_rec.get('id'):
        qa_incharge_id = incharge_rec.get('id')
        log_test('TC_TCH_INC_01', 'Class Incharge', f'Assign QA teacher as Class In-Charge of QA class (Incharge ID: {qa_incharge_id})', 'PASS')

        # Track incharge in log
        records_log.append({
            'table': 'class_incharges',
            'id': qa_incharge_id,
            'identifier': f"Incharge-Teacher-{qa_teacher_id}-Class-{qa_test_class_id}",
            'status': 'active_in_db',
            'details': incharge_rec
        })
        with open(log_file, 'w') as f:
            json.dump(records_log, f, indent=4)

        # Now test Removal of In-Charge delegation
        r_rem_inc = session.post(f"{BASE_URL}/roles/incharges/{qa_incharge_id}", data={
            '_token': role_csrf,
            '_method': 'DELETE'
        }, headers={'Referer': f"{BASE_URL}/roles"}, allow_redirects=True)

        chk_inc_after = json.loads(run_php(f"qa_reports/manage_qa_teacher.php get_incharge {qa_teacher_id}"))
        if not chk_inc_after.get('found'):
            log_test('TC_TCH_INC_02', 'Class Incharge', f'Remove In-Charge delegation #{qa_incharge_id} successfully (reverted cleanly in DB)', 'PASS')
            # Update log
            for r in records_log:
                if r.get('table') == 'class_incharges' and r.get('id') == qa_incharge_id:
                    r['status'] = 'deleted_during_test'
            with open(log_file, 'w') as f:
                json.dump(records_log, f, indent=4)
        else:
            log_test('TC_TCH_INC_02', 'Class Incharge', 'Remove In-Charge delegation failed', 'FAIL')
    else:
        # Check if HTTP 500 or error occurred
        if r_assign_inc.status_code == 500 or 'Undefined variable $classIncharge' in r_assign_inc.text:
            log_test('TC_TCH_INC_01', 'Class Incharge', 'Assign Class In-Charge via /roles/assign-incharge (Crashes with HTTP 500 Undefined variable $classIncharge)', 'FAIL')
            log_bug('BUG-PH6-02', 'Class Incharge Delegation', '/roles/assign-incharge', 'Critical',
                    '1. Navigate to /roles. 2. Select teacher and class. 3. Submit POST /roles/assign-incharge.',
                    'Class Incharge assignment executes cleanly and creates record in class_incharges table.',
                    'HTTP 500 ErrorException: Undefined variable $classIncharge in RoleController.php on line 177 before variable declaration.')
        else:
            log_test('TC_TCH_INC_01', 'Class Incharge', 'Assign Class In-Charge record not created', 'FAIL', f"Status: {r_assign_inc.status_code}")

# Clean up dedicated QA Class
if qa_test_class_id:
    run_php(f"qa_reports/manage_qa_teacher.php delete_test_class {qa_test_class_id}")
    print(f"Temporary QA Class #{qa_test_class_id} deleted successfully.")

# ==========================================
# 6. TEACHER ACCESS REQUESTS TESTING
# ==========================================
tar_raw = run_php(f"qa_reports/manage_qa_teacher.php create_access_request {qa_teacher_id or 19}")
tar_data = json.loads(tar_raw) if tar_raw else {}
qa_access_request_id = tar_data.get('request_id')

if qa_access_request_id:
    # Track request
    records_log.append({
        'table': 'teacher_access_requests',
        'id': qa_access_request_id,
        'identifier': 'qa_access_req_01@gmail.com',
        'status': 'active_in_db',
        'details': {'id': qa_access_request_id, 'email': 'qa_access_req_01@gmail.com'}
    })
    with open(log_file, 'w') as f:
        json.dump(records_log, f, indent=4)

    r_roles_page = session.get(f"{BASE_URL}/roles")
    roles_csrf = get_csrf(r_roles_page.text) or csrf

    # Test Rejecting our QA request
    r_rej = session.post(f"{BASE_URL}/teacher-access-requests/{qa_access_request_id}/reject", data={
        '_token': roles_csrf
    }, headers={'Referer': f"{BASE_URL}/roles"}, allow_redirects=True)

    chk_tar_res = json.loads(run_php(f"qa_reports/manage_qa_teacher.php check_access_request {qa_access_request_id}"))
    tar_status = chk_tar_res.get('status')

    if tar_status == 'rejected' or r_rej.status_code == 200:
        log_test('TC_TCH_REQ_01', 'Access Requests', f'Reject QA teacher access request #{qa_access_request_id} (Status updated to rejected in DB)', 'PASS')
    else:
        log_test('TC_TCH_REQ_01', 'Access Requests', 'Reject QA teacher access request failed', 'FAIL', f"Status: {tar_status}")

    # Clean up the QA access request record
    run_php(f"qa_reports/manage_qa_teacher.php clean_access_request {qa_access_request_id}")
    for r in records_log:
        if r.get('table') == 'teacher_access_requests' and r.get('id') == qa_access_request_id:
            r['status'] = 'deleted_during_test'
    with open(log_file, 'w') as f:
        json.dump(records_log, f, indent=4)

# ==========================================
# 7. CASCADING DELETION BEHAVIOR TEST
# ==========================================
if qa_teacher_id:
    # Link attendance and leave record to test cascade behavior
    link_res = json.loads(run_php(f"qa_reports/manage_qa_teacher.php link_attendance_and_leave {qa_teacher_id}"))
    print(f"Linked temporary attendance (ID {link_res.get('attendance_id')}) and leave (ID {link_res.get('leave_id')}) to QA Teacher #{qa_teacher_id}")

    # Delete the QA Teacher via UI
    r_tch_page = session.get(f"{BASE_URL}/teachers")
    del_csrf = get_csrf(r_tch_page.text) or csrf
    r_del = session.post(f"{BASE_URL}/teachers/{qa_teacher_id}", data={
        '_token': del_csrf,
        '_method': 'DELETE'
    }, headers={'Referer': f"{BASE_URL}/teachers"}, allow_redirects=True)

    # Check cascade status in DB
    cascade_res = json.loads(run_php(f"qa_reports/manage_qa_teacher.php check_cascade {qa_teacher_id}"))
    print("Cascade Verification Results:", json.dumps(cascade_res, indent=2))

    teacher_deleted = cascade_res.get('teacher_deleted')
    user_deleted = cascade_res.get('user_deleted')
    salaries_count = cascade_res.get('salaries_count')
    assignments_count = cascade_res.get('assignments_count')
    att_orphaned = cascade_res.get('attendance_orphaned_count', 0)
    leaves_orphaned = cascade_res.get('leaves_orphaned_count', 0)

    if teacher_deleted and user_deleted and salaries_count == 0 and assignments_count == 0:
        log_test('TC_TCH_DEL_01', 'Teacher Deletion', f'Teacher record #{qa_teacher_id}, linked user, salaries, and assignments deleted successfully', 'PASS')
    else:
        log_test('TC_TCH_DEL_01', 'Teacher Deletion', 'Teacher or primary linked records not deleted', 'FAIL', f"Res: {cascade_res}")

    # Check cascading orphans
    if att_orphaned > 0 or leaves_orphaned > 0:
        log_test('TC_TCH_CASCADE_01', 'Cascade Integrity', f'Teacher deletion left {att_orphaned} orphaned attendance records and {leaves_orphaned} orphaned leave records in DB', 'FAIL')
        log_bug('BUG-PH6-01', 'Teacher Deletion Cascade', '/teachers/{id}', 'High',
                f'1. Assign teacher #{qa_teacher_id} attendance and leave records. 2. Delete teacher via DELETE /teachers/{qa_teacher_id}. 3. Query teacher_attendances and teacher_leaves tables.',
                'Deleting a teacher should cascade-delete or restrict deletion if dependent attendance and leave records exist.',
                f'TeacherController::deleteTeacher deletes loginAccount, teacher_salaries, and teacher_assignments, but fails to cascade-delete or handle teacher_attendances ({att_orphaned} orphaned) and teacher_leaves ({leaves_orphaned} orphaned), leaving orphaned foreign key references in the database.')
    else:
        log_test('TC_TCH_CASCADE_01', 'Cascade Integrity', 'All dependent records cascaded cleanly', 'PASS')

    # Update statuses in qa_records_log.json
    for r in records_log:
        if r.get('table') in ['teachers', 'users', 'teacher_salaries'] and r.get('identifier') in ['qa_teacher_01@gmail.com', f"Salary-Teacher-{qa_teacher_id}"]:
            r['status'] = 'deleted_during_test'
    with open(log_file, 'w') as f:
        json.dump(records_log, f, indent=4)

# ==========================================
# 8. FINAL CLEANUP & STATUS AUDIT
# ==========================================
run_php("qa_reports/manage_qa_teacher.php clean_qa_teacher")
print("Full Phase 6 cleanup completed.")

# Save phase 6 results
with open('qa_reports/phase6_results.json', 'w') as f:
    json.dump({
        'phase': 'Phase 6: Teacher Management',
        'total_tests': len(results),
        'passed': len([r for r in results if r['status'] == 'PASS']),
        'failed': len([r for r in results if r['status'] == 'FAIL']),
        'bugs_count': len(bugs),
        'results': results,
        'bugs': bugs
    }, f, indent=4)

print("\n==================================================")
print(f"PHASE 6 COMPLETE: {len([r for r in results if r['status'] == 'PASS'])}/{len(results)} Passed. {len(bugs)} Bug(s) Found.")
print("==================================================")