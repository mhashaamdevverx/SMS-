(() => {
const read=(node,key,fallback=[])=>{try{return JSON.parse(node?.dataset[key]||JSON.stringify(fallback))}catch{return fallback}};
const hideMessages=()=>document.querySelectorAll('.auto-hide-message').forEach(node=>setTimeout(()=>{node.style.opacity='0';node.style.transform='translateY(-8px)';setTimeout(()=>node.remove(),400)},3000));
const grade=value=>value>=90?'A+':value>=80?'A':value>=75?'B+':value>=70?'B':value>=65?'C+':value>=60?'C':value>=55?'D+':value>=50?'D':'F';

window.togglePasswordVisibility = function(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
};

const initShell=()=>{
    const theme=localStorage.getItem('form-task-theme');
    if(theme==='dark')document.body.classList.add('dark-theme');
    const close=modal=>{modal.hidden=true;document.body.classList.remove('modal-open')};
    document.querySelectorAll('.profile-menu').forEach(menu=>{
        const button=menu.querySelector('.profile-trigger'),dropdown=menu.querySelector('.profile-dropdown');
        button?.addEventListener('click',()=>{const open=!dropdown.hidden;dropdown.hidden=open;button.setAttribute('aria-expanded',String(!open))});
        menu.querySelectorAll('[data-modal-target]').forEach(trigger=>trigger.addEventListener('click',()=>{const modal=document.getElementById(trigger.dataset.modalTarget);dropdown.hidden=true;if(modal){modal.hidden=false;document.body.classList.add('modal-open');modal.querySelector('[data-modal-close]')?.focus()}}));
        document.addEventListener('click',event=>{if(!menu.contains(event.target))dropdown.hidden=true})
    });
    document.querySelectorAll('.modal-backdrop').forEach(modal=>modal.addEventListener('click',event=>{if(event.target===modal||event.target.closest('[data-modal-close]'))close(modal)}));
    document.querySelectorAll('[data-crud-modal-open]').forEach(button=>button.addEventListener('click',()=>{const modal=document.getElementById(button.dataset.crudModalOpen);if(modal){modal.hidden=false;document.body.classList.add('modal-open');modal.querySelector('[data-crud-modal-close]')?.focus()}}));
    document.querySelectorAll('.crud-backdrop').forEach(modal=>{if(modal.dataset.open==='true'){modal.hidden=false;document.body.classList.add('modal-open')}modal.addEventListener('click',event=>{if(event.target===modal||event.target.closest('[data-crud-modal-close]'))close(modal)})});
    const confirm=document.getElementById('crud-confirm-modal');
    let pending;
    document.querySelectorAll('form[data-crud-action]').forEach(form=>form.addEventListener('submit',event=>{if(!confirm)return;event.preventDefault();pending=form;const update=form.dataset.crudAction==='update',submit=confirm.querySelector('[data-crud-confirm-submit]');confirm.querySelector('#crud-confirm-message').textContent=update?'Are you sure you want to save these changes?':'Are you sure you want to delete this record?';submit.textContent=update?'Yes, Update':'Yes, Delete';submit.className=update?'modal-primary-btn':'modal-danger-btn';confirm.hidden=false;document.body.classList.add('modal-open');submit.focus()}));
    confirm?.addEventListener('click',event=>{if(event.target===confirm||event.target.closest('[data-crud-confirm-close]')||event.target.closest('[data-crud-confirm-cancel]')){confirm.hidden=true;pending=null;document.body.classList.remove('modal-open')}});
    confirm?.querySelector('[data-crud-confirm-submit]')?.addEventListener('click',()=>pending?.submit());
    const toggle=document.getElementById('theme-toggle');
    if(toggle){toggle.checked=theme==='dark';toggle.addEventListener('change',()=>{document.body.classList.toggle('dark-theme',toggle.checked);localStorage.setItem('form-task-theme',toggle.checked?'dark':'light')})}
};

const initClassSections=()=>{
    const host=document.querySelector('[data-assigned-sections]');if(!host)return;
    const assigned=read(host,'assignedSections',{}),norm=v=>String(v||'').trim().toLowerCase(),update=(input,select,oldClass='',oldSection='')=>{const used=(assigned[norm(input.value)]||[]).map(norm);[...select.options].forEach(option=>{if(option.value)option.disabled=used.includes(norm(option.value))&&!(norm(input.value)===oldClass&&norm(option.value)===oldSection)});if(select.selectedOptions[0]?.disabled)select.value=''};
    const input=document.getElementById('class-name'),select=document.getElementById('section-name');
    if(input&&select){const run=()=>update(input,select);input.addEventListener('input',run);input.addEventListener('change',run);run()}
    document.querySelectorAll('.class-name-input').forEach(input=>{const select=input.closest('form')?.querySelector('.class-section-select');if(select){const run=()=>update(input,select,norm(input.dataset.originalClass),norm(select.dataset.originalSection));input.addEventListener('input',run);input.addEventListener('change',run);run()}})
};

const initAddStudent=()=>{
    const host=document.querySelector('[data-add-student]'),box=document.querySelector('[data-class-combobox]');if(!host||!box)return;
    const classes=read(host,'classes'),input=box.querySelector('[data-class-input]'),menu=box.querySelector('[data-class-menu]'),toggle=box.querySelector('[data-class-toggle]'),close=()=>{menu.hidden=true;input.setAttribute('aria-expanded','false')},show=()=>{const matches=classes.filter(item=>item.toLowerCase().includes(input.value.trim().toLowerCase()));menu.innerHTML=matches.length?'':'<p class="result-combobox-empty">No matching class. You can use your typed value.</p>';matches.forEach(item=>{const option=document.createElement('button');option.type='button';option.className='result-combobox-option';option.textContent=item;option.addEventListener('click',()=>{input.value=item;close()});menu.append(option)});menu.hidden=false;input.setAttribute('aria-expanded','true')};
    ['focus','click','input'].forEach(name=>input.addEventListener(name,show));
    toggle.addEventListener('click',()=>menu.hidden?show():close());
    document.addEventListener('click',event=>{if(!box.contains(event.target))close()})
};

const initStudents=()=>{
    const host=document.querySelector('[data-student-page]');if(!host)return;
    const subjects=read(host,'savedSubjects'),form=document.querySelector('.student-filter-form'),search=document.querySelector('.student-search-field input');
    let timer;
    if(form&&search){search.addEventListener('input',()=>{clearTimeout(timer);timer=setTimeout(()=>form.requestSubmit(),350)});form.querySelectorAll('.student-class-filter').forEach(field=>field.addEventListener('change',()=>form.requestSubmit()))}
    const setup=box=>{const input=box.querySelector('[data-subject-input]'),menu=box.querySelector('[data-subject-menu]'),toggle=box.querySelector('[data-subject-toggle]'),close=()=>{menu.hidden=true;input.setAttribute('aria-expanded','false')},show=()=>{const matches=subjects.filter(item=>item.toLowerCase().includes(input.value.trim().toLowerCase()));menu.innerHTML=matches.length?'':'<p class="student-subject-empty">No saved subject found - you can use the typed value.</p>';matches.forEach(item=>{const option=document.createElement('button');option.type='button';option.className='student-subject-option';option.textContent=item;option.addEventListener('click',()=>{input.value=item;close();input.focus()});menu.append(option)});menu.hidden=false;input.setAttribute('aria-expanded','true')};input.addEventListener('focus',show);input.addEventListener('input',show);toggle.addEventListener('click',()=>menu.hidden?show():close());document.addEventListener('click',event=>{if(!box.contains(event.target))close()})};
    const makeRow=()=>{const row=document.createElement('div');row.className='student-subject-row';row.innerHTML='<div class="student-subject-combobox" data-subject-combobox><input type="text" name="subjects[]" placeholder="Select or enter a subject" autocomplete="off" data-subject-input><button type="button" class="student-subject-toggle" data-subject-toggle><i class="bi bi-chevron-down"></i></button><div class="student-subject-menu" data-subject-menu hidden></div></div><button type="button" class="student-subject-remove" data-remove-subject><i class="bi bi-trash3"></i></button>';setup(row.querySelector('[data-subject-combobox]'));return row};
    document.querySelectorAll('[data-subject-picker]').forEach(picker=>{const rows=picker.querySelector('[data-subject-rows]');rows.querySelectorAll('[data-subject-combobox]').forEach(setup);picker.querySelector('[data-add-subject]').addEventListener('click',()=>{const row=makeRow();rows.append(row);row.querySelector('[data-subject-input]').focus()});rows.addEventListener('click',event=>{const button=event.target.closest('[data-remove-subject]');if(!button)return;const row=button.closest('.student-subject-row');if(rows.children.length===1)row.querySelector('[data-subject-input]').value='';else row.remove()})});

    // EDIT STUDENT MODAL FEE AUTO-FETCH & TOTAL FEE CALCULATION
    const classFeeData = JSON.parse(host.getAttribute('data-class-fee-data') || '{}');
    document.querySelectorAll('.student-edit-class-select').forEach(select => {
        const modalForm = select.closest('form');
        if (!modalForm) return;

        const mInput = modalForm.querySelector('[data-fee-monthly]');
        const tInput = modalForm.querySelector('[data-fee-test]');
        const aInput = modalForm.querySelector('[data-fee-admission]');
        const totInput = modalForm.querySelector('[data-fee-total]');

        const calcTotal = () => {
            const m = parseFloat(mInput ? mInput.value : 0) || 0;
            const t = parseFloat(tInput ? tInput.value : 0) || 0;
            if (totInput) totInput.value = m + t;
        };

        select.addEventListener('change', function () {
            const selectedClass = this.value;
            if (classFeeData[selectedClass]) {
                const feeInfo = classFeeData[selectedClass];
                if (mInput) mInput.value = feeInfo.monthly_fee || 3500;
                if (tInput) tInput.value = feeInfo.test_session_fee || 500;
                if (aInput) aInput.value = feeInfo.admission_fee || 5000;
                calcTotal();
            }
        });

        if (mInput) mInput.addEventListener('input', calcTotal);
        if (tInput) tInput.addEventListener('input', calcTotal);
        calcTotal();
    });
};

const initArrangement=()=>{
    const host=document.querySelector('[data-arrangement-page]');if(!host)return;
    const records=read(host,'arrangementRecords'),selector=document.querySelector('[data-arrangement-selector]'),trigger=document.querySelector('[data-arrangement-trigger]'),text=document.querySelector('[data-arrangement-trigger-text]'),menu=document.querySelector('[data-arrangement-menu]'),search=document.querySelector('[data-arrangement-search]'),none=document.querySelector('[data-arrangement-no-results]'),options=[...document.querySelectorAll('[data-arrangement-option]')];
    if(!selector)return;
    const refresh=()=>{const ids=[...document.querySelectorAll('input[name="class_ids[]"]:checked')].map(input=>Number(input.value)),selected=records.filter(record=>ids.includes(record.id)),rooms=selected.map(record=>record.class_no).sort((a,b)=>a-b),chairs=selected.reduce((sum,record)=>sum+Number(record.chairs||0),0);document.getElementById('arrangement-rooms').value=rooms.length?rooms.join(', '):'No class-section selected';document.getElementById('arrangement-chairs').value=chairs;document.getElementById('arrangement-preview-title').textContent=selected.length?`${selected.length} class-section room${selected.length===1?'':'s'} selected`:'No class-section selected';document.getElementById('arrangement-preview-detail').textContent=selected.length?`Class Nos: ${rooms.join(', ')} · Total available chairs: ${chairs}`:'Choose one or more class-sections.';text.textContent=selected.length?`${selected.length} class-section${selected.length===1?'':'s'} selected`:'Select class-sections'};
    trigger.addEventListener('click',()=>{menu.hidden=!menu.hidden;trigger.setAttribute('aria-expanded',String(!menu.hidden));if(!menu.hidden)search.focus()});
    document.addEventListener('click',event=>{if(!selector.contains(event.target))menu.hidden=true});
    search.addEventListener('input',()=>{const query=search.value.trim().toLowerCase();let visible=0;options.forEach(option=>{option.hidden=!option.dataset.search.includes(query);if(!option.hidden)visible++});none.hidden=visible!==0});
    document.querySelectorAll('input[name="class_ids[]"]').forEach(input=>input.addEventListener('change',refresh));
    refresh()
};

const initResultPicker=()=>{
    const form=document.querySelector('[data-result-students]');if(!form)return;
    const options=read(form,'classOptions',[]),students=read(form,'resultStudents',[]),classes=options.length?options:[...new Set(['Pre-KG','KG','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN'].concat(students.map(student=>student.class).filter(Boolean)))],input=document.getElementById('result-class'),menu=document.getElementById('result-class-dropdown-menu'),toggle=document.getElementById('classToggle'),next=document.getElementById('nextClassButton'),norm=value=>String(value||'').trim().toLowerCase(),valid=()=>norm(input.value)!=='',close=()=>{menu.hidden=true;input.setAttribute('aria-expanded','false')},show=()=>{const matches=classes.filter(item=>norm(item).includes(norm(input.value)));menu.innerHTML=matches.length?'':'<p class="result-combobox-empty">No matching class & section found.</p>';matches.forEach(item=>{const option=document.createElement('button');option.type='button';option.className='result-combobox-option';option.textContent=item;option.addEventListener('click',()=>{input.value=item;if(next)next.disabled=false;close();if(next)next.focus()});menu.append(option)});menu.hidden=false;input.setAttribute('aria-expanded','true')};
    ['focus','click'].forEach(event=>input.addEventListener(event,show));
    input.addEventListener('input',()=>{if(next)next.disabled=!valid();show()});
    toggle?.addEventListener('click',()=>menu.hidden?show():close());
    form.addEventListener('submit',event=>{if(!valid()){event.preventDefault();input.focus();show()}});
    document.addEventListener('click',event=>{if(!event.target.closest('.result-combobox'))close()});
    if(next)next.disabled=!valid()
};

const initResultEdits=()=>document.querySelectorAll('.group-result-edit-form').forEach(form=>{
    const update=()=>{let total=0,obtained=0;form.querySelectorAll('tbody tr').forEach(row=>{const max=Number(row.dataset.total)||0,input=row.querySelector('[data-edit-obtained]');let value=Math.min(Number(input.value)||0,max);if(Number(input.value)>max)input.value=max;total+=max;obtained+=value;const percent=max?value/max*100:0;row.querySelector('[data-edit-percentage]').textContent=percent.toFixed(2)+'%';row.querySelector('[data-edit-grade]').textContent=grade(percent)});const percent=total?obtained/total*100:0;form.querySelector('[data-edit-total]').textContent=total;form.querySelector('[data-edit-obtained-total]').textContent=obtained;form.querySelector('[data-edit-overall-percentage]').textContent=percent.toFixed(2)+'%';form.querySelector('[data-edit-overall-grade]').textContent=grade(percent)};
    form.querySelectorAll('[data-edit-obtained]').forEach(input=>input.addEventListener('input',update));
    update()
});

const initClassResults=()=>{
    const form=document.getElementById('classResultsForm');if(!form)return;
    const inputs=[...form.querySelectorAll('.marks-input[data-total]')],filled=document.getElementById('filledCount'),save=document.getElementById('saveClassResultsButton'),count=()=>{if(filled)filled.textContent=inputs.filter(input=>input.value!=='').length},valid=input=>{const ok=input.value!==''&&Number(input.value)>=0&&Number(input.value)<=Number(input.dataset.total);input.classList.toggle('is-invalid',!ok);return ok};
    inputs.forEach(input=>{input.addEventListener('input',()=>{if(Number(input.value)>Number(input.dataset.total))input.value=input.dataset.total;if(Number(input.value)<0)input.value=0;input.classList.remove('is-invalid');count()});input.addEventListener('blur',()=>valid(input))});
    form.addEventListener('submit',event=>{if(!inputs.every(valid)){event.preventDefault();form.querySelector('.marks-input.is-invalid')?.focus();return}if(save){save.disabled=true;save.innerHTML='<i class="bi bi-arrow-repeat"></i> Saving Results...'}});
    count()
};

const initCustomMultiselect=()=>{
    document.querySelectorAll('[data-custom-multiselect]').forEach(container=>{
        const trigger=container.querySelector('[data-multiselect-trigger]'),menu=container.querySelector('[data-multiselect-menu]'),search=container.querySelector('[data-multiselect-search]'),textSpan=container.querySelector('[data-multiselect-text]'),hiddenInputsContainer=container.querySelector('[data-multiselect-inputs]'),options=[...container.querySelectorAll('.custom-multiselect-option input[type="checkbox"]')];
        if(!trigger||!menu)return;
        const updateSelected=()=>{const checked=options.filter(cb=>cb.checked);hiddenInputsContainer.innerHTML='';if(checked.length===0){textSpan.innerHTML='<span class="custom-multiselect-placeholder">Select Course / Subject...</span>'}else{textSpan.innerHTML='';const tagsWrapper=document.createElement('div');tagsWrapper.className='custom-multiselect-tags';checked.forEach(cb=>{const hidden=document.createElement('input');hidden.type='hidden';hidden.name='subject_course_selected[]';hidden.value=cb.value;hiddenInputsContainer.appendChild(hidden);const tag=document.createElement('span');tag.className='custom-multiselect-tag';tag.innerHTML=`${cb.value} <span class="custom-multiselect-tag-remove">&times;</span>`;tag.querySelector('.custom-multiselect-tag-remove').addEventListener('click',e=>{e.stopPropagation();cb.checked=false;updateSelected()});tagsWrapper.appendChild(tag)});textSpan.appendChild(tagsWrapper)}};
        trigger.addEventListener('click',()=>{const isHidden=menu.hidden;menu.hidden=!isHidden;trigger.classList.toggle('is-active',isHidden);if(isHidden&&search)search.focus()});
        if(search){search.addEventListener('input',()=>{const q=search.value.trim().toLowerCase();container.querySelectorAll('.custom-multiselect-option').forEach(opt=>{const label=opt.textContent.trim().toLowerCase();opt.style.display=label.includes(q)?'flex':'none'})})}
        options.forEach(cb=>cb.addEventListener('change',updateSelected));
        document.addEventListener('click',e=>{if(!container.contains(e.target)){menu.hidden=true;trigger.classList.remove('is-active')}});
        updateSelected()
    })
};

const initLoginPage=()=>{
    const toggleBtn = document.getElementById('togglePasswordBtn');
    const passInput = document.getElementById('login-password');
    const eyeIcon = document.getElementById('eyeIcon');
    if (toggleBtn && passInput && eyeIcon) {
        toggleBtn.addEventListener('click', function() {
            const isPassword = passInput.type === 'password';
            passInput.type = isPassword ? 'text' : 'password';
            eyeIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye';
        });
    }
    const googleBtn = document.getElementById('btn-social-google');
    if (googleBtn) {
        googleBtn.addEventListener('click', () => alert('Google authentication will connect with your school Workspace account.'));
    }
    const msBtn = document.getElementById('btn-social-ms');
    if (msBtn) {
        msBtn.addEventListener('click', () => alert('Microsoft Edu login is enabled for registered school staff and students.'));
    }
};

const initSignupPage=()=>{
    document.addEventListener('change', function(e) {
        if (e.target && e.target.id === 'signup-image') {
            const display = document.getElementById('file-name-display');
            if (display) {
                display.innerText = e.target.files[0] ? e.target.files[0].name : 'Choose file...';
            }
        }
    });
};

const initAddStudentPage=()=>{
    const container = document.getElementById('add-student-container');
    if (!container) return;

    const classSectionsMap = JSON.parse(container.getAttribute('data-class-sections') || '{}');
    const classRollData = JSON.parse(container.getAttribute('data-class-roll-data') || '{}');
    const classFeeData = JSON.parse(container.getAttribute('data-class-fee-data') || '{}');

    const classSelect = document.getElementById('student-class');
    const sectionSelect = document.getElementById('student-section');
    const rollInput = document.getElementById('std-id');
    const rollHint = document.getElementById('roll-no-hint');
    const regInput = document.getElementById('registration-no');
    const monthlyFeeInput = document.getElementById('monthly-fee');
    const testSessionFeeInput = document.getElementById('test-session-fee');
    const admissionFeeInput = document.getElementById('admission-fee');
    const totalMonthlyFeeInput = document.getElementById('total-monthly-fee');

    function calcTotalFee() {
        const m = parseFloat(monthlyFeeInput ? monthlyFeeInput.value : 0) || 0;
        const t = parseFloat(testSessionFeeInput ? testSessionFeeInput.value : 0) || 0;
        if (totalMonthlyFeeInput) totalMonthlyFeeInput.value = m + t;
    }

    function updateFeeDetails() {
        const selectedClass = classSelect ? classSelect.value : '';
        if (classFeeData[selectedClass]) {
            const feeInfo = classFeeData[selectedClass];
            if (monthlyFeeInput) monthlyFeeInput.value = feeInfo.monthly_fee || 3500;
            if (testSessionFeeInput) testSessionFeeInput.value = feeInfo.test_session_fee || 500;
            if (admissionFeeInput) admissionFeeInput.value = feeInfo.admission_fee || 5000;
        }
        calcTotalFee();
    }

    if (monthlyFeeInput) monthlyFeeInput.addEventListener('input', calcTotalFee);
    if (testSessionFeeInput) testSessionFeeInput.addEventListener('input', calcTotalFee);

    function updateRegistrationNo() {
        const selectedClass = classSelect ? classSelect.value : '';
        const rollNo = rollInput ? rollInput.value : '1';
        if (!selectedClass || !regInput) {
            if (regInput) regInput.value = '';
            return;
        }

        const cleanClass = selectedClass.replace(/[^A-Za-z0-9]/g, '').toUpperCase() || 'STD';
        const currentYear = new Date().getFullYear();
        const formattedRoll = String(rollNo).padStart(3, '0');
        regInput.value = `REG-${currentYear}-${cleanClass}-${formattedRoll}`;
    }

    function applyRollMeta(data, className) {
        if (!data || !rollInput) return;

        rollInput.value = data.next_roll_no || 1;

        if (data.is_first_student) {
            rollInput.readOnly = false;
            rollInput.classList.remove('is-readonly-field');
            if (rollHint) rollHint.textContent = `First student for ${className}. You can set the starting Roll No if needed.`;
        } else {
            rollInput.readOnly = true;
            if (rollHint) rollHint.textContent = `Auto-calculated Roll No for ${className} (Student #${data.next_roll_no}).`;
        }

        updateRegistrationNo();
    }

    function handleClassChange() {
        const selectedClass = classSelect ? classSelect.value : '';
        if (!selectedClass) return;

        updateFeeDetails();

        if (sectionSelect && classSectionsMap[selectedClass] && classSectionsMap[selectedClass].length > 0) {
            const currentSec = sectionSelect.value;
            sectionSelect.innerHTML = '<option value="" disabled>Select section</option>';

            classSectionsMap[selectedClass].forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec;
                opt.textContent = sec;
                if (sec === currentSec) opt.selected = true;
                sectionSelect.appendChild(opt);
            });

            if (!sectionSelect.value && sectionSelect.options.length > 1) {
                sectionSelect.selectedIndex = 1;
            }
        }

        const rollMeta = classRollData[selectedClass];
        if (rollMeta) {
            applyRollMeta(rollMeta, selectedClass);
        } else {
            fetch(`/api/student-roll-info?class=${encodeURIComponent(selectedClass)}`)
                .then(res => res.json())
                .then(data => applyRollMeta(data, selectedClass))
                .catch(() => {});
        }
    }

    if (classSelect) classSelect.addEventListener('change', handleClassChange);
    if (rollInput) rollInput.addEventListener('input', updateRegistrationNo);

    if (classSelect && classSelect.value) {
        handleClassChange();
    }
};

const initRollSlipsPage=()=>{
    const bulkForm = document.getElementById('bulk-download-form');
    const bulkBtn = document.getElementById('bulk-download-btn');
    const modal = document.getElementById('all-downloads-modal');

    if (bulkForm && bulkBtn) {
        bulkForm.addEventListener('submit', function () {
            const originalHtml = bulkBtn.innerHTML;
            bulkBtn.disabled = true;
            bulkBtn.innerHTML = '<i class="bi bi-arrow-repeat" style="display:inline-block;animation:spin 1s linear infinite;"></i> Downloading PDF...';

            setTimeout(function () {
                bulkBtn.disabled = false;
                bulkBtn.innerHTML = originalHtml;
                if (modal) {
                    modal.hidden = true;
                    modal.setAttribute('data-open', 'false');
                }
            }, 2500);
        });
    }
};

const initSubjectsPage=()=>{
    const classSelect = document.getElementById('subject-class-select');
    const filterForm = document.getElementById('subject-filter-form');
    const searchInput = document.getElementById('subject-search-input');
    const resetBtn = document.getElementById('subject-reset-btn');
    const rows = document.querySelectorAll('.subject-row');
    const noMatchRow = document.getElementById('client-no-match-row');
    const countDisplay = document.getElementById('subject-count-display');

    if (classSelect && filterForm) {
        classSelect.addEventListener('change', function () {
            filterForm.submit();
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (searchInput) searchInput.value = '';
            if (classSelect) classSelect.value = '';
            window.location.href = resetBtn.getAttribute('href') || '/subjects';
        });
    }

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', function () {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            rows.forEach(function (row) {
                const subjectName = row.getAttribute('data-subject-name') || '';
                const classLabel = row.getAttribute('data-class-label') || '';
                const matches = subjectName.includes(query) || classLabel.includes(query);

                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noMatchRow) {
                noMatchRow.style.display = visibleCount === 0 && rows.length > 0 ? '' : 'none';
            }

            if (countDisplay) {
                countDisplay.innerHTML = '<strong>' + visibleCount + '</strong> subject' + (visibleCount === 1 ? '' : 's');
            }
        });
    }
};

const initEmailAutoSuggestions=()=>{
    const generateGmail=(name1,name2='')=>{
        const clean=s=>(s||'').toLowerCase().replace(/^(sir|madam|ma'am|mr\.?|mrs\.?|ms\.?|dr\.?|prof\.?)\s+/i,'').replace(/[^a-z0-9]/g,'');
        if(!name2&&name1){
            const parts=(name1||'').replace(/^(sir|madam|ma'am|mr\.?|mrs\.?|ms\.?|dr\.?|prof\.?)\s+/i,'').trim().toLowerCase().split(/\s+/).map(p=>p.replace(/[^a-z0-9]/g,'')).filter(Boolean);
            if(parts.length===0)return '';
            if(parts.length===1)return `${parts[0]}@gmail.com`;
            return `${parts[0]}.${parts.slice(1).join('.')}@gmail.com`;
        }
        const first=clean(name1),second=clean(name2);
        if(first&&second)return `${first}.${second}@gmail.com`;
        if(first)return `${first}@gmail.com`;
        if(second)return `${second}@gmail.com`;
        return '';
    };

    // 1. Student Create Form
    const fn=document.getElementById('first-name'),ln=document.getElementById('last-name'),se=document.getElementById('student-email');
    if(se&&(fn||ln)){
        let edited=se.value.trim().length>0;
        se.addEventListener('input',()=>{edited=se.value.trim().length>0;});
        const sync=()=>{if(!edited){se.value=generateGmail(fn?.value,ln?.value);}};
        fn?.addEventListener('input',sync);
        ln?.addEventListener('input',sync);
    }

    // 2. Teacher Add Form (standalone or modal)
    document.querySelectorAll('form[action*="add-teacher"], #addTeacherForm').forEach(form=>{
        const tn=form.querySelector('input[name="teacher_name"]'),te=form.querySelector('input[name="teacher_email"]');
        if(tn&&te){
            let edited=te.value.trim().length>0;
            te.addEventListener('input',()=>{edited=te.value.trim().length>0;});
            tn.addEventListener('input',()=>{if(!edited){te.value=generateGmail(tn.value);}});
        }
    });

    // 3. Signup Form
    const sn=document.getElementById('signup-name'),sem=document.getElementById('signup-email');
    if(sn&&sem){
        let edited=sem.value.trim().length>0;
        sem.addEventListener('input',()=>{edited=sem.value.trim().length>0;});
        sn.addEventListener('input',()=>{if(!edited){sem.value=generateGmail(sn.value);}});
    }
};

const initMobileNavDrawer=()=>{
    const toggleBtn=document.getElementById('mobileNavToggle');
    const overlay=document.getElementById('mobileNavOverlay');
    const sidebar=document.querySelector('.admin-side')||document.querySelector('.student-side')||document.querySelector('.sidebar');
    if(toggleBtn&&sidebar){
        toggleBtn.addEventListener('click',e=>{
            e.stopPropagation();
            sidebar.classList.toggle('mobile-open');
            overlay?.classList.toggle('active');
        });
        overlay?.addEventListener('click',()=>{
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    initShell();
    hideMessages();
    initClassSections();
    initAddStudent();
    initStudents();
    initArrangement();
    initResultPicker();
    initResultEdits();
    initClassResults();
    initCustomMultiselect();
    initLoginPage();
    initSignupPage();
    initAddStudentPage();
    initRollSlipsPage();
    initSubjectsPage();
    initEmailAutoSuggestions();
    initMobileNavDrawer();
    document.querySelector('[data-print-roll-slip]')?.addEventListener('click',()=>window.print());
});
})();