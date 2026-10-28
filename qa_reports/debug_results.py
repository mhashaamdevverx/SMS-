import requests
import re
import json
import subprocess

BASE_URL = "http://127.0.0.1:8000"
session = requests.Session()

def run_php(cmd):
    proc = subprocess.run(f"php {cmd}", shell=True, capture_output=True, text=True)
    return proc.stdout.strip()

# Setup
setup_res_raw = run_php("qa_reports/manage_phase7_records.php setup")
setup_data = json.loads(setup_res_raw)
qa_student_id = setup_data.get('student_id')
qa_class_id = setup_data.get('class_id')
qa_subject_id = setup_data.get('subject_id')
print("Setup:", setup_data)

# Login
r1 = session.get(f"{BASE_URL}/login")
csrf = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', r1.text).group(1)
session.post(f"{BASE_URL}/login", data={'_token': csrf, 'email': 'qa_admin@gmail.com', 'password': 'QA_Admin@2026!', 'account_type': 'admin'})

r_add = session.get(f"{BASE_URL}/add-result")
res_csrf = re.search(r'name=["\']_token["\']\s+value=["\']([^"\']+)["\']', r_add.text).group(1)

# Check class-results view for QA_Class_Exam
r_cls_res = session.get(f"{BASE_URL}/add-result/class?class=QA_Class_Exam&term=First+Term")
print("class-results status:", r_cls_res.status_code)
# Find inputs in form
inputs = re.findall(r'<input[^>]*name=["\']([^"\']+)["\'][^>]*>', r_cls_res.text)
print("Sample inputs on class-results page:", inputs[:15])

# Post single student result
payload = {
    '_token': res_csrf,
    'student_id': qa_student_id,
    'student_class': 'QA_Class_Exam',
    'term': 'First Term',
    'marks[QA_Subject_Physics]': '85'
}
r_post = session.post(f"{BASE_URL}/add-result/student-result", data=payload, headers={'Referer': f"{BASE_URL}/add-result"}, allow_redirects=False)
print("Post status:", r_post.status_code)
print("Location:", r_post.headers.get('Location'))
if r_post.status_code == 302:
    r_follow = session.get(r_post.headers.get('Location'))
    errs = re.findall(r'alert-danger[^>]*>(.*?)</div>', r_follow.text, re.DOTALL)
    print("Alert danger:", errs)
    # Check session errors
    flashed = re.findall(r'class=["\']alert alert-([^"\']+)["\'][^>]*>(.*?)</div>', r_follow.text, re.DOTALL)
    print("Flashed alerts:", [(f[0], f[1].strip()[:100]) for f in flashed])

# Check DB
res_raw = run_php("qa_reports/manage_phase7_records.php get_result 'First Term'")
print("DB result:", res_raw)

# Clean
run_php("qa_reports/manage_phase7_records.php cleanup")