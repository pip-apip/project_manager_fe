<div id="fullPageLoader" class="full-page-loader">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="{{ asset('assets/vendors/simple-datatables/simple-datatables.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- filepond -->
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>

<script>
    FilePond.create(document.querySelector('.basic-filepond-activity1'), {
        allowImagePreview: false,
        allowMultiple: false,
        allowFileEncode: false,
        required: false
    });
    FilePond.create( document.querySelector('.filepond-project'), {
        // allowImagePreview: false,
        // allowMultiple: true,
        // allowFileEncode: false,
        // required: true,
        // acceptedFileTypes: ['pdf'],
        // fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
        //     // Do custom type detection here and return with promise
        //     resolve(type);
        // })
        allowImagePreview: false,
        allowMultiple: false,
        allowFileEncode: false,
        required: false
    });
</script>

<script>
    let id_project = 0;
    let backendUrl = env('API_BASE_URL').'/';
    let access_token = @json(session('user.access_token'));
    $(document).ready(function () {
        let data_projects = [];
        loadProjects();
        getCategory();
        getDoc();
        getCompany();
    });

    $('#formModal').on('hidden.bs.modal', function () {
        document.getElementById("post-form").reset();
        $('.invalid-feedback').remove(); // Remove error messages
        $('.form-control').removeClass('is-invalid');
        $('.form-group').removeClass('is-invalid');
        id_project = 0;
    });

    function getCompany(){
        $.ajax({
            url: backendUrl + "companies",
            type: "GET",
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            dataType: "json",
            success: function (response) {
                data_company = response.data;
                // console.log(data_projects);
                if (!response || !response.data) {
                    console.error("Invalid API response:", response);
                    return;
                }

                let rows = `<option value="#">Select Company</option>`; // Variable to store generated rows

                $.each(response.data, function (index, company) {
                    rows += `<option value="${company.id}">${company.name}</option>`;
                });

                $("#company_id").html(rows);
            },
            error: function (xhr, status, error) {
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    // refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    function loadProjects() {
        $('#fullPageLoader').show();
        $("#table-body").empty();

        $.ajax({
            url: backendUrl + "projects",
            type: "GET",
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            dataType: "json",
            success: function (response) {
                if(response.status === 200){
                    data_projects = response.data;
                    // console.log(data_projects);
                    if (!response || !response.data) {
                        console.error("Invalid API response:", response);
                        return;
                    }

                    let rows = ""; // Variable to store generated rows

                    $.each(response.data, function (index, project) {
                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${project.name}</td>
                            <td>${project.company.name}</td>
                            <td>` + dateFormat(project.start_date) + `</td>
                            <td>` + dateFormat(project.end_date) + `</td>
                            <td>
                                <span class="badge ${project.status === 'Active' ? 'bg-success' : 'bg-danger'}">
                                    ${project.status}
                                </span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning rounded-pill" data-bs-toggle="modal"
                                    data-bs-target="#detailModal" onclick="detailModal(${project.id})">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-info rounded-pill" data-bs-toggle="modal"
                                    data-bs-target="#docModal" onclick="showDoc(${project.id})">
                                    <i class="fa-solid fa-file"></i>
                                </a>
                                <button class="btn btn-sm btn-danger rounded-pill" onclick="activityPage(${project.id})">
                                    <i class="fa-solid fa-chart-line"></i>
                                </button>
                            </td>
                        </tr>`;
                    });

                    $("#table-body").html(rows);

                    let table1 = document.querySelector('#table1');
                    let dataTable = new simpleDatatables.DataTable(table1);
                }else{
                    console.log(response);
                }
                $('#fullPageLoader').hide();
            },
            error: function (xhr, status, error) {
                // console.log(@json(session('user.access_token')));
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    function activityPage(project_id){
        $.ajax({
            url: `/activity-project/${project_id}`,
            type: "GET",
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            success: function(response) {
                window.location.href = `/activity-project/${project_id}`;
            }
        });
    }

    function detailModal(id) {
        let project = data_projects.find(project => project.id === id);
        id_project = project.id;
        console.log('detail',project);

        $("#project_name_detail").text(project.name);
        $("#company_name_detail").text(project.company.name);
        $("#company_address_detail").text(project.company.address);
        $("#director_name_detail").text(project.company.director_name);
        $("#director_phone_detail").text(project.company.director_phone);
        $("#start_project_detail").text(dateFormat(project.start_date));
        $("#end_project_detail").text(dateFormat(project.end_date));
        $('#editButton').attr("onclick", "setId(" + project.id + ")")
        // document.getElementById("editButton").setAttribute("onclick", "setId(" + project.id + ")");
    }

    function setId(id) {
        // document.getElementById("addButton").setAttribute("onclick", "set(" + id + ")");
        id_project = id;
        console.log(id_project+" in setId()")
        $('#formTitle').empty();

        if (id_project > 0) {
            // Load project data
            let project = data_projects.find(project => project.id === id);
            console.log(project);

            document.querySelector('input[name="name"]').value = project.name;
            $('select[name="company_id"]').val(project.company.id).change();
            document.querySelector('input[name="start_date"]').value = project.start_date;
            document.querySelector('input[name="end_date"]').value = project.end_date;

            $('#formTitle').text('Form Edit Project');
            document.getElementById("addButton").click();
        } else {
            $('#formTitle').text('Form Add Project');
        }
    }

    function updateProject(formData){
        $('#fullPageLoader').show();
        let apiUrl = backendUrl + `projects/${id_project}`;

        let jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        $.ajax({
            url: apiUrl, // Use the API URL
            type: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            data: JSON.stringify(jsonData),
            processData: false,
            contentType: 'application/json',
            success: function (response) {
                $('#fullPageLoader').hide();
                if(response.status === 400) {
                    let errors = response.errors;

                    $.each(errors, function (key, messages) {
                        let inputField = $(`[name="${key}"]`);
                        inputField.addClass("is-invalid")
                            .after(`<div class="invalid-feedback">${messages[0]}</div>`);
                    });
                }else if(response.status === 200 || response.status === 201){
                    loadProjects();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Project has been Edited",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        $('#closeFormModal').click();
                        // location.reload(); // Refresh after the SweetAlert
                    });
                }else{
                    console.log(response)
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    function submitPostForm(formData) {
        $('#fullPageLoader').show();
        console.log("id_project : " + id_project);
        formData.append("name", $("#project_name").val());
        formData.append("company_id", $("#company_id").val());
        console.log($("#company_id").val());

        $.ajax({
            url: backendUrl + 'projects',
            type: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $('#fullPageLoader').hide();
                if(response.status === 400) {
                    let errors = response.errors;

                    $.each(errors, function (key, messages) {
                        let inputField = $(`[name="${key}"]`);
                        inputField.addClass("is-invalid")
                            .after(`<div class="invalid-feedback">${messages[0]}</div>`);
                    });
                }else if(response.status === 200 || response.status === 201){
                    loadProjects();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Your Project successfully Created",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        $('#closeFormModal').click();
                        // location.reload(); // Refresh after the SweetAlert
                    });
                }else{
                    console.log(response)
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    $('#post-form').on('submit', function (e) {
        // console.log("submit clicked");
        e.preventDefault();

        var formData = new FormData(this); // Gunakan FormData untuk semua input (termasuk file)
        // console.log("FormData Content:");
        // for (let pair of formData.entries()) {
        //     console.log(pair[0] + ": " + pair[1]);
        // }

        // Tambahkan CSRF token jika mengakses Laravel langsung
        // formData.append('_token', '{{ csrf_token() }}');

        if(id_project > 0){
            updateProject(formData);
        }else{
            submitPostForm(formData);
        }
    });

    function deleteProject() {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: backendUrl + `projects/${id_project}`,
                    type: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + access_token,
                    },
                    success: function (response) {
                        if(response.status === 200){
                            loadProjects();
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Project Data has been Deleted",
                                showConfirmButton: false,
                                timer: 1000
                            })
                        }else{
                            console.log(response);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 401) {
                        console.log("Token expired. Refreshing token...");
                        refreshToken();
                    } else {
                        console.log(xhr);
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error"
                        });
                    }
                    }
                });
            }
        });

    }

    // ================== Document Modal ==================

    let data_doc = [];

    $('#docModal').on('hidden.bs.modal', function () {
        document.getElementById("docForm").reset();
        $('.invalid-feedback').remove(); // Remove error messages
        $('.form-control').removeClass('is-invalid');
        $('.form-select').removeClass('is-invalid');
        id_project = 0;
        data_doc = [];
        $("#table_doc").empty();
    });

    function getCategory(){
        $("#category_input").empty();
        $("#category_show").empty();

        $.ajax({
            url: backendUrl + "admin-doc-categories", // Replace with your API URL
            type: "GET",
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            dataType: "json",
            success: function (response) {
                if(response.status === 200){
                    // console.log(response);

                    if (!response || !response.data) {
                        console.error("Invalid API response:", response);
                        return;
                    }

                    let rows = ""; // Variable to store generated rows
                    rows += `<option value="#">Select Category</option>`;

                    $.each(response.data, function (index, category) {
                        rows += `
                        <option value="${ category.id }">${ category.name }</option>`;
                    });

                    $("#category_input").html(rows);
                    $("#category_show").html(rows);
                }else{
                    console.log(response);
                }
            },
            error: function (xhr, status, error) {
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    function showDoc(id) {
        let project = data_projects.find(project => project.id === id);
        id_project = project.id;
        // console.log(project);
        getDoc();

        $("#project_name_doc").text(project.name);
    }

    function storeDoc(){
        $('#fullPageLoader').show();
        event.preventDefault();

        let form = document.getElementById("docForm");
        if (!form) {
            console.error("Form #docForm not found!");
            return;
        }

        let formData = new FormData(form);

        if (typeof id_project !== 'undefined' && id_project !== null) {
            formData.append("project_id", id_project);
        } else {
            console.error("Error: id_project is undefined or null!");
            alert("Invalid project ID.");
            return;
        }

        let filePondInstance = FilePond.find(document.querySelector('#fileDocProject'));
        if (filePondInstance) {
            let files = filePondInstance.getFiles();

            if (files.length > 0) {
                formData.append("file", files[0].file);
            } else {
                console.warn("No file selected!");
                alert("Please select a file.");
                return;
            }
        } else {
            console.error("FilePond instance not found!");
            alert("File input not found.");
            return;
        }

        let apiUrl = backendUrl + `admin-docs`;

        $.ajax({
            url: apiUrl,
            type: 'POST',
            data: formData,
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            processData: false,
            contentType: false,
            success: function (response) {
                $('#fullPageLoader').hide();
                if(response.status === 400) {
                    let errors = response.errors;

                    $.each(errors, function (key, messages) {
                        let inputField = $(`[name="${key}"]`);
                        inputField.addClass("is-invalid")
                            .after(`<div class="invalid-feedback">${messages[0]}</div>`);
                    });
                }else if(response.status === 200 || response.status === 201){
                    loadProjects();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Document Administration successfully Created",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        $('#closeDocModal').click();
                        // location.reload(); // Refresh after the SweetAlert
                    });
                }else{
                    console.log(response)
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    $('#category_show').change(function(){
        let id_category = $(this).val();
        let url = "https://bepm.hanatekindo.com";
        console.log(id_category);
        let filteredDoc = data_doc.filter(doc =>
            doc.admin_doc_category.id == id_category
        );
        console.log("Filtered Doc:", filteredDoc);
        $("#table_doc").empty();
        if(filteredDoc.length > 0){
            let rows = "";
            $.each(filteredDoc, function (index, doc) {
                rows += `
                <tr class="mt-2">
                    <td><a onclick="openPDFModal('${url}${ doc.file }')" style="text-decoration: none; color: grey; cursor: pointer"><i class="fa-solid fa-file-pdf"></i></a></td>
                    <td width="400px">${doc.title}</td>
                    <td>
                        <button type="button" class="btn btn-danger ml-1 btn-sm" onclick="deleteDoc(${doc.id})">
                            <i class="bx bx-check d-block d-sm-none"></i>
                            <span class="d-none d-sm-block"><i class="fa-solid fa-trash"></i></span>
                        </button>
                    </td>
                </tr>`;
            });

            $("#table_doc").html(rows);
        }else{
            let rows = "";
                rows += `
                <tr class="mt-2" style="text-align: center;">
                    <td style="padding: 0 auto"><b>no files have been uploaded yet</b></td>
                </tr>`;

            $("#table_doc").html(rows);
        }
    });

    function getDoc(){
        $.ajax({
            url: backendUrl + `admin-docs`,
            type: "GET",
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            dataType: "json",
            success: function (response) {
                if(response.status == 200){
                    console.log(response);
                    // $('#category_show').onchange(function(){
                        // let id_category = 2;
                        // console.log(id_category);
                        let filteredData = response.data.filter(doc =>
                            doc.project.id === id_project
                        );

                        data_doc = filteredData;

                        console.log("Filtered Data:", data_doc);
                    // });
                }else{
                    console.log(response);
                }
            },
            error: function (xhr) {
                if (xhr.status === 401) {
                    console.log("Token expired. Refreshing token...");
                    refreshToken();
                } else {
                    console.log(xhr);
                }
            }
        });
    }

    function deleteDoc(id_doc) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: backendUrl + `admin-docs/${id_doc}`,
                    type: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + access_token,
                    },
                success: function (response) {
                    loadProjects();
                    if(response.status === 200){
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Document Administration has been Deleted",
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            $('#closeDocModal').click();
                            // location.reload(); // Refresh after the SweetAlert
                        });
                    }else{
                        console.log(response);
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        console.log("Token expired. Refreshing token...");
                        refreshToken();
                    } else {
                        console.log(xhr);
                        Swal.fire({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error"
                        });
                    }
                }});
            }
        });
    }

    function dateFormat(dateString) {
        let [year, month, day] = dateString.split("-"); // Split by "-"

        // Convert to a valid Date object
        let date = new Date(year, month - 1, day); // Month is zero-based in JS

        // Format using Indonesian locale
        let formattedDate = new Intl.DateTimeFormat("id-ID", {
            day: "2-digit",
            month: "long",
            year: "numeric"
        }).format(date);

        return formattedDate;
    }

    function openPDFModal(pdfUrl) {
        document.getElementById('pdfViewer').src = pdfUrl;
        document.getElementById('modernPDFModal').style.display = "flex";
    }

    function closePDFModal() {
        document.getElementById('modernPDFModal').style.display = "none";
        document.getElementById('pdfViewer').src = ""; // Clear src to stop loading
    }
</script>

