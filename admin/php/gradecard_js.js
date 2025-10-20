        document.getElementById('classDropdown').addEventListener('change', function() {
            const classCode = this.value;
            const studentList = document.getElementById('studentList');
            studentList.innerHTML = '';
            document.getElementById('reportContainer').style.display = 'none';
            
            if (!classCode) {
                studentList.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <h3>No Class Selected</h3>
                        <p>Please select a class to view students</p>
                    </div>
                `;
                return;
            }

            fetch('get_students.php?classCode=' + encodeURIComponent(classCode))
                .then(res => res.json())
                .then(students => {
                    if (!Array.isArray(students) || students.length === 0) {
                        studentList.innerHTML = `
                            <div class="empty-state">
                                <i class="fas fa-user-slash"></i>
                                <h3>No Students Found</h3>
                                <p>This class doesn't have any students yet</p>
                            </div>
                        `;
                        return;
                    }
                    
                    students.forEach((student) => {
                        const div = document.createElement('div');
                        div.className = 'student-item';
                        div.innerHTML = `
                            <div class="student-info">
                                <div class="student-avatar">${student.name ? student.name.charAt(0) : 'S'}</div>
                                <div>
                                    <div class="student-name">${student.name || 'Unknown'}</div>
                                </div>
                            </div>
                            <button class="btn btn-outline" data-id="${student.id}">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </button>
                        `;
                        studentList.appendChild(div);
                    });

                    document.querySelectorAll('.student-item button').forEach(btn => {
                        btn.onclick = function() {
                            viewReport(this.dataset.id);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error loading students:', error);
                });
        });

        function viewReport(studentId) {
            fetch('gradecard.php?ajax=studentinfo&id=' + encodeURIComponent(studentId))
                .then(res => res.json())
                .then(data => {
                    document.getElementById('infoName').textContent = data.name || 'Unknown';
                    document.getElementById('infoStd').textContent = data.std || '-';
                    document.getElementById('infoClass').textContent = data.class || '-';
                    document.getElementById('infoEmail').textContent = data.email || '-';
                    
                    const marksSection = document.getElementById('marksSection');
                    marksSection.innerHTML = `
                        <div class="marks-header">
                            <div>Subject</div>
                            <div>MCQ</div>
                            <div>Theory</div>
                        </div>
                    `;
                    addMarksRow();
                    document.getElementById('reportContainer').style.display = 'block';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('infoName').textContent = 'Unknown Student';
                    document.getElementById('infoStd').textContent = '-';
                    document.getElementById('infoClass').textContent = '-';
                    document.getElementById('infoEmail').textContent = '-';
                    
                    const marksSection = document.getElementById('marksSection');
                    marksSection.innerHTML = `
                        <div class="marks-header">
                            <div>Subject</div>
                            <div>MCQ</div>
                            <div>Theory</div>
                        </div>
                    `;
                    addMarksRow();
                    document.getElementById('reportContainer').style.display = 'block';
                });
        }

        function addMarksRow() {
            const marksSection = document.getElementById('marksSection');
            const row = document.createElement('div');
            row.className = 'marks-row';
            row.innerHTML = `
                <input type="text" placeholder="Enter subject name">
                <input type="number" placeholder="0" min="0" max="100">
                <input type="number" placeholder="0" min="0" max="100">
            `;
            marksSection.appendChild(row);
        }

        document.getElementById('addMoreBtn').addEventListener('click', function(e) {
            e.preventDefault();
            addMarksRow();
        });

        document.getElementById('generateReportBtn').addEventListener('click', function() {
            const name = document.getElementById('infoName').textContent;
            const std = document.getElementById('infoStd').textContent;
            const className = document.getElementById('infoClass').textContent;
            const email = document.getElementById('infoEmail').textContent;

            const rows = document.querySelectorAll('.marks-row');
            let marksTableRows = '';
            
            rows.forEach(row => {
                const inputs = row.querySelectorAll('input');
                const subject = inputs[0].value.trim();
                const mcq = inputs[1].value;
                const theory = inputs[2].value;
                
                if(subject) {
                    marksTableRows += `
                        <tr>
                            <td>${subject}</td>
                            <td>${mcq || '-'}</td>
                            <td>${theory || '-'}</td>
                        </tr>
                    `;
                }
            });

            let html = `
                <div style="max-width:800px; margin:0 auto;">
                    <div class="report-header">
                        <h1 class="report-title">Student Report Card</h1>
                        <p class="report-subtitle">Academic Performance Report</p>
                    </div>
                    
                    <div class="report-student-info">
                        <div class="detail-item">
                            <label>Student Name</label>
                            <div>${name}</div>
                        </div>
                        <div class="detail-item">
                            <label>Grade</label>
                            <div>${std}</div>
                        </div>
                        <div class="detail-item">
                            <label>Class</label>
                            <div>${className}</div>
                        </div>
                        <div class="detail-item">
                            <label>Email</label>
                            <div>${email}</div>
                        </div>
                    </div>
                    
                    <table class="report-table">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>MCQ Score</th>
                                <th>Theory Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${marksTableRows || '<tr><td colspan="3" style="text-align:center;">No marks entered</td></tr>'}
                        </tbody>
                    </table>
                </div>
            `;
            
            document.getElementById('reportCardContainer').innerHTML = html;
            document.getElementById('downloadReportBtn').style.display = 'inline-block';
        });

        document.getElementById('downloadReportBtn').onclick = async function() {
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
            this.disabled = true;
            
            try {
                const reportDiv = document.getElementById('reportCardContainer').firstElementChild;
                const canvas = await html2canvas(reportDiv, { scale: 2 });
                const imgData = canvas.toDataURL('image/png');
                const pdf = new window.jspdf.jsPDF('p', 'pt', 'a4');
                const pageWidth = pdf.internal.pageSize.getWidth();
                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = pageWidth - 40;
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;
                
                pdf.addImage(imgData, 'PNG', 20, 20, pdfWidth, pdfHeight);
                pdf.save('student_report.pdf');
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF. Please try again.');
            } finally {
                this.innerHTML = '<i class="fas fa-download"></i> Download Report as PDF';
                this.disabled = false;
            }
        };

        window.onload = loadClasses;