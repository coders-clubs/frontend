function setMode(mode) {
    const form = document.getElementById('admissionForm');
    
    if (mode === 'add') {
        form.reset();
        document.getElementById('receipt_no').value = '(Auto-generated on Save)';
        document.getElementById('application_no').value = '(Auto-generated on Save)';
        document.getElementById('action_type').value = 'save';
        document.getElementById('existing_id').value = '';
        document.getElementById('saveBtn').innerText = 'SUBMIT REGISTRATION';
        alert("Form cleared. You can now enter a new student record.");
    } else if (mode === 'delete') {
        const id = document.getElementById('existing_id').value;
        if (!id) {
            alert("Please search and fetch a record first before deleting.");
            return;
        }
        if (confirm("Are you sure you want to PERMANENTLY delete this student record?")) {
            const formData = new FormData();
            formData.append('action_type', 'delete');
            formData.append('existing_id', id);
            
            fetch('admission_handler.php', {
                method: 'POST',
                body: formData
            }).then(() => {
                alert("Record deleted successfully.");
                window.location.reload();
            });
        }
    }
}

function enableModify() {
    const bar = document.getElementById('modify-search-bar');
    if (bar) {
        bar.style.display = (bar.style.display === 'none') ? 'block' : 'none';
        if (bar.style.display === 'block') {
            document.getElementById('search_app_no').focus();
        }
    }
}

function fetchRecord() {
    const searchVal = document.getElementById('search_app_no').value.trim();
    if (!searchVal) return alert("Please enter a Receipt No or Application No to search.");

    fetch(`core/admission_fetch.php?search=${searchVal}`)
        .then(res => res.json())
        .then(data => {
            if (data.status === 'multiple') {
                let msg = "Multiple records found. Please search again using the exact Receipt No from these options:\n\n";
                data.records.forEach(r => {
                    msg += `- ${r.receipt_no} : ${r.student_name} (${r.department || 'N/A'})\n`;
                });
                alert(msg);
            } else if (data.status === 'success') {
                const r = data.record;
                document.getElementById('action_type').value = 'update';
                document.getElementById('existing_id').value = r.id;
                document.getElementById('saveBtn').innerText = 'UPDATE REGISTRATION';
                
                // Map all fields
                const fields = [
                    'receipt_no', 'admission_type', 'student_name', 'gender', 'father_name', 
                    'mother_name', 'address', 'city', 'pincode', 'cell_1', 'cell_2', 
                    'religion', 'community', 'caste', 'father_occupation', 'mother_occupation',
                    'application_no', 'department', 'quota', 'reg_no', 
                    'date_of_joining', 'degree', 'hostel', 'bus_stop', 'bus_route_no',
                    'school_name', 'percentage'
                ];
                
                fields.forEach(f => {
                    const el = document.getElementById(f);
                    if (el) el.value = r[f] || '';
                });
                
                alert("Student data fetched successfully. You can now modify and update.");
            } else {
                alert("No record found for: " + searchVal);
            }
        })
        .catch(err => {
            console.error("Fetch Error:", err);
            alert("Error connecting to server. Please try again.");
        });
}

document.addEventListener('DOMContentLoaded', () => {
    if (window.AUTO_FETCH && window.AUTO_FETCH !== "") {
        document.getElementById('search_app_no').value = window.AUTO_FETCH;
        fetchRecord();
    }
});
