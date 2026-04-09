// Form behavior scripts

function setMode(mode) {
    const form = document.getElementById('admissionForm');
    const modifyBar = document.getElementById('modify-search-bar');
    
    if (mode === 'add') {
        form.reset();
        document.getElementById('receipt_no').value = window.INIT_RECEIPT_NO || '';
        document.getElementById('application_no').value = window.INIT_APP_NO || '';
        document.getElementById('action_type').value = 'save';
        document.getElementById('existing_id').value = '';
        document.getElementById('saveBtn').innerText = 'Save';
        modifyBar.style.display = 'none';
        
        // Ensure readonly is applied correctly on key fields
        document.getElementById('receipt_no').readOnly = true;
        document.getElementById('application_no').readOnly = true;

    } else if (mode === 'delete') {
        if (!document.getElementById('existing_id').value) {
            alert('Please modify to fetch a record first before deleting!');
            return;
        }
        if(confirm("Are you sure you want to delete this record?")) {
            document.getElementById('action_type').value = 'delete';
            form.submit();
        }
    }
}

function enableModify() {
    const modifyBar = document.getElementById('modify-search-bar');
    modifyBar.style.display = 'block';
}

function fetchRecord() {
    const appNo = document.getElementById('search_app_no').value.trim();
    if (!appNo) {
        alert("Please enter an Application No");
        return;
    }

    // Fetch via API
    fetch(`admission_fetch.php?application_no=${appNo}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const r = data.record;
                // Basic
                document.getElementById('existing_id').value = r.id;
                document.getElementById('receipt_no').value = r.receipt_no;
                document.getElementById('admission_type').value = r.admission_type;
                
                // Personal
                document.getElementById('student_name').value = r.student_name;
                document.getElementById('gender').value = r.gender;
                document.getElementById('date_of_birth').value = r.date_of_birth;
                document.getElementById('father_name').value = r.father_name;
                document.getElementById('mother_name').value = r.mother_name;
                document.getElementById('address').value = r.address;
                document.getElementById('city').value = r.city;
                document.getElementById('pincode').value = r.pincode;
                document.getElementById('cell_1').value = r.cell_1;
                document.getElementById('cell_2').value = r.cell_2;
                document.getElementById('community').value = r.community;
                document.getElementById('religion').value = r.religion;
                document.getElementById('caste').value = r.caste;
                document.getElementById('father_occupation').value = r.father_occupation;
                document.getElementById('mother_occupation').value = r.mother_occupation;

                // Academic
                document.getElementById('application_no').value = r.application_no;
                document.getElementById('department').value = r.department;
                document.getElementById('quota').value = r.quota;
                document.getElementById('concession').value = r.concession;

                // Admission
                document.getElementById('admission_no').value = r.admission_no;
                document.getElementById('date_of_joining').value = r.date_of_joining;
                document.getElementById('bus_stop').value = r.bus_stop;
                document.getElementById('bus_route_no').value = r.bus_route_no;
                document.getElementById('degree').value = r.degree;
                document.getElementById('hostel').value = r.hostel;

                document.getElementById('action_type').value = 'update';
                document.getElementById('saveBtn').innerText = 'Update';
                
            } else {
                alert(data.message || 'Record not found.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Failed to fetch record.');
        });
}
