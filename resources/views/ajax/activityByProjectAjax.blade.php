<div id="fullPageLoader" class="full-page-loader">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>


<script>
    let id_activity = 0;
    let id_acttivity_doc = 0;
    let activity_data = [];
    let baseUrl = "https://bepm.hanatekindo.com/api/v1/"
    let access_token = @json(session('user.access_token'));
    let statusDocForm = "";

    $(document).ready(function () {
        renderTags();
        getCategory();
        getProjectId();
    });
</script>

<script>
    function getProjectId(){
        let url = window.location.pathname;
        let match = url.match(/activity-project\/(\d+)/);
        let id;

        if (match) {
                id = match[1];
        }
        loadActivityData(id);
        getProjectById(id);
    }
    function loadActivityData(project_id) {
        console.log("project_id",project_id);
        $('#fullPageLoader').show();
        $('#table_body').empty();
        $.ajax({
            url: baseUrl + 'activities/search?project_id='+ project_id,
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            success: function (response) {
                if(response.status === 200 ){
                    activity_data = response.data;
                    console.log("activities_data",activity_data);
                    let rows = ""; // Variable to store generated rows
    
                    $.each(response.data, function (index, activity) {
                        rows += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${activity.title}</td>
                            <td>${activity.project.company.name}</td>
                            <td>`+ dateFormat(activity.start_date) +`</td>
                            <td>`+ dateFormat(activity.end_date) +`</td>
                            <td>
                                <span class="badge ${activity.status === 'Active' ? 'bg-success' : 'bg-danger'}">
                                    ${activity.status}
                                </span>
                            </td>
                            <td>
                                <a href="#" class="btn btn-sm btn-warning rounded-pill modalll" data-bs-toggle="modal"
                                    data-bs-target="#formModal" onclick="fillForm(${activity.id})"><i
                                        class="fa-solid fa-pen"></i></a>
                                <a href="#" class="btn btn-sm btn-danger rounded-pill modalll" onclick="deleteActivity(${activity.id})"><i class="fa-solid fa-trash"></i></a>
                                <a href="#" class="btn btn-sm btn-info rounded-pill modalll" data-bs-toggle="modal"
                                    data-bs-target="#formDocModal" onclick="showDocModal(${activity.id})"><i class="fa-solid fa-file"></i></a>
                                </a>
                            </td>
                        </tr>`;
                    });
    
                    $("#table_body").html(rows);
                    let table1 = document.querySelector('#table1');
                    let dataTable = new simpleDatatables.DataTable(table1);
                }else{
                    console.log(response);
                }
                $('#fullPageLoader').hide();
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

    function getCategory(){
        $("#documentCat").empty();

        $.ajax({
            url: baseUrl + "activity-doc-categories", // Replace with your API URL
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
    
                    $("#documentCat").html(rows);
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

    function getProjectById(project_id) {
        $.ajax({
            url: baseUrl + 'projects/' + project_id,
            type: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + access_token,
            },
            success: function (response) {
                let project = response.data[0];
                console.log('project',project);

                $("#project_name").text(project.name);
                $("#project_id").val(project.id);

                $("#project_name_detail").text(project.name);
                $("#company_name_detail").text(project.company.name);
                $("#company_address_detail").text(project.company.address);
                $("#director_name_detail").text(project.company.director_name);
                $("#director_phone_detail").text(project.company.director_phone);
                $("#start_project_detail").text(dateFormat(project.start_date));
                $("#end_project_detail").text(dateFormat(project.end_date));
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

    $('#formModal').on('hidden.bs.modal', function () {
        document.getElementById("activityForm").reset();
        $('.invalid-feedback').remove(); // Remove error messages
        $('.form-control').removeClass('is-invalid');
        $('.form-select').removeClass('is-invalid');
        id_activity = 0;
    });

    function fillForm(id) {
        let activity = activity_data.find(activity => activity.id === id);
        $('#project_id').val(activity.project_id);
        $('#title_activity').val(activity.title);
        $('#start_date').val(activity.start_date);
        $('#end_date').val(activity.end_date);
        id_activity = id;
    }

    function submitForm(id) {
        if (id_activity == 0) {
            console.log('store');
            storeActivity();
        } else {
            console.log('update');
            updateActivity(id_activity);
        }
    }

    function updateActivity(id_activity) {
        $('#fullPageLoader').show();
        let form = document.getElementById("activityForm");
        if (!form) {
            console.error("Form #activityForm not found!");
            return;
        }

        let formData = new FormData(form);

        let jsonData = {};
        formData.forEach((value, key) => {
            jsonData[key] = value;
        });

        $.ajax({
            url: baseUrl + `activities/${id_activity}`,
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
                }else if(response.status === 200){
                    loadActivityData();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Acticity Data has been Edited",
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

    function storeActivity() {
        $('#fullPageLoader').show();
        let form = document.getElementById("activityForm");
        if (!form) {
            console.error("Form #activityForm not found!");
            return;
        }

        let formData = new FormData(form);

        $.ajax({
            url: baseUrl + 'activities',
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
                }else if(response.status === 200){
                    loadActivityData();
                    Swal.fire({
                        position: "center",
                        icon: "success",
                        title: "Activity Data successfully Created",
                        showConfirmButton: false,
                        timer: 1000
                    }).then(() => {
                        $('#closeFormModal').click();
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
                }
            }
        });
    }

    function deleteActivity(id_activity) {
        // console.log("ID : "+id)
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
                // Make AJAX request only after confirmation
                $.ajax({
                    url: baseUrl + `activities/${id_activity}`,
                    type: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + access_token,
                    },
                    success: function (response) {
                        if(response.status === 200) {
                            loadActivityData();
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Activity Data has been deleted",
                                showConfirmButton: false,
                                timer: 1000
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
                            console.error('Error:', xhr);
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


    function showDocModal(id) {
        id_activity = id;
        let activity = activity_data.find(activity => activity.id === id_activity);
        console.log("Activity:", activity);
        $('#activity_name_doc').text(activity.title);

    }
</script>

{{-- Doc Script --}}
<script>
    $(document).ready(function () {
        $("#documentCat").change(async function () {
            let selectedValue = $(this).val();
            console.log("Dropdown changed! Selected Value:", selectedValue);

            // Check if id_activity is defined
            if (typeof id_activity === "undefined") {
                console.error("Error: id_activity is not defined!");
                return;
            }

            if (selectedValue == 1) {
                try {
                    // TODO : Menunggu EndPoint Backend - getActivityDocId(id_activity, category_id)
                    let actFindDoc = await getActivityId_n_catId(id_activity, selectedValue);
                    console.log("Result from getActivityDocId:", actFindDoc);
                    $('#saveButton').empty();
                    let footer = '';

                    if (actFindDoc) {
                        $('#title_static').text(actFindDoc.title);
                        statusDocForm = "show";

                        let tagsShow = actFindDoc.tags.map(tag =>
                            `<button class="btn btn-info me-2">${tag}</button>`
                        ).join("");

                        let section2Show = `
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <label>Description:</label>
                                            <div id="descriptionShow">
                                                <p>${actFindDoc.description}</p>
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <label>Tags:</label>
                                            <div id="tagShow">${tagsShow}</div>
                                        </div>
                                    </div>
                                </div>`+
                                // <div class="col-sm-2 d-flex justify-content-center align-items-center">
                                //     <a href="#" class="btn btn-danger" onclick="deleteDoc(${actFindDoc.id})">Delete</a>
                                // </div>
                                // <div class="col-sm-2 d-flex justify-content-center align-items-center">
                                //     <a href="#" class="btn btn-warning" onclick="editDoc(${actFindDoc.id})">Edit</a>
                                // </div>
                            `</div>`;

                        $("#section2Show").html(section2Show);
                        $("#section1Show, #section2Show").show();

                        footer = `
                            <button type="button" class="btn btn-danger ml-1" onclick="deleteDoc()">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block"><i class="fa-solid fa-trash"></i> Delete</span>
                            </button>`;
                    } else {
                        statusDocForm = "form";
                        $("#section1Form, #section2Form").show();
                        footer = `
                            <button type="button" class="btn btn-primary ml-1" onclick="storeDoc()">
                                <i class="bx bx-check d-block d-sm-none"></i>
                                <span class="d-none d-sm-block"><i class="fa-solid fa-floppy-disk"></i> Save</span>
                            </button>`;
                    }
                    $('#saveButton').html(footer);
                } catch (error) {
                    console.error("Error fetching document:", error);
                }
            } else {
                $("#section1Show, #section2Show").hide();
            }
        });
    });

    // Function to edit document
    async function editDoc(id) {
        console.log("Editing document ID:", id);

        try {
            id_acttivity_doc = id;
            let actFindDoc = await getActivityDocId(id);
            if (!actFindDoc) {
                console.error("Error: No document found for ID:", id);
                return;
            }

            $('#title_activity_doc').val(actFindDoc.title);
            quill.root.innerHTML = actFindDoc.description;
            tags = actFindDoc.tags;
            renderTags();

            // Hide display sections and show edit form
            $("#section1Show, #section2Show").hide();
            $("#section1Form, #section2Form").show();
        } catch (error) {
            if (error.status === 401) {
                console.log("Token expired. Refreshing token...");
                refreshToken();
            } else {
                console.log(xhr);
            }
        }
    }

    function deleteDOc(id) {
        // console.log("ID : "+id)
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
                // Make AJAX request only after confirmation
                $.ajax({
                    url: baseUrl + `activity-docs/${id}`,
                    type: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + access_token,
                    },
                    success: function (response) {
                        if(response.status === 200){
                            loadActivityData();
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: "Activity Doc has been deleted",
                                showConfirmButton: false,
                                timer: 1000
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
                            console.error('Error:', xhr);
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

    // Function to fetch activity document by ID
    async function getActivityDocId(id_activity) {
        try {
            let response = await $.ajax({
                url: `${baseUrl}activity-docs/${id_activity}`,
                type: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + access_token,
                }
            });
            console.log(response.data[0])
            return response.data[0];
        } catch (error) {
            if (error.status === 401) {
                console.log("Token expired. Refreshing token...");
                refreshToken();
            } else {
                console.error("Error fetching document:", error.responseJSON || error);
                return null;
            }
        }
    }
    async function getActivityId_n_catId(id_activity, category_id) {
        try {
            let response = await $.ajax({
                url: `${baseUrl}activity-docs/search?activity_doc_category_id=${category_id}&activity_id=${id_activity}`,
                type: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + access_token,
                }
            });
            console.log(response.data[0])
            return response.data[0];
        } catch (error) {
            if (error.status === 401) {
                console.log("Token expired. Refreshing token...");
                refreshToken();
            } else {
                console.error("Error fetching document:", error.responseJSON || error);
                return null;
            }
        }
    }

    $('#formDocModal').on('hidden.bs.modal', function () {
        document.getElementById("activityDocForm").reset();
        $('.invalid-feedback').remove();
        $('.form-control, .form-select').removeClass('is-invalid');

        quill.root.innerHTML = "";  // Clear editor content
        quill.enable(true);         // Ensure editor is enabled
        quill.focus();              // Refocus Quill so typing is possible

        $('#section1Form, #section2Form, #section1Show, #section2Show').hide();

        id_activity = 0;
        id_acttivity_doc = 0;
        tags = [];
        renderTags();
    });

    function storeDoc() {
        $('fullPageLoader').show();
        let formData = new FormData();
        console.log('storeDoc',id_activity)
        console.log('title', $("#title_activity_doc").val())

        formData.append("title", $("#title_activity_doc").val());
        formData.append("description", quill.root.innerHTML);
        formData.append("tags", JSON.stringify(tags));
        formData.append("activity_doc_category_id", $("#documentCat").val());
        formData.append("activity_id", id_activity);

        formData.append("document_category", $("#documentCat").val());

        let urlAjax = "";
        let method = "";
        if(id_acttivity_doc === 0){
            urlAjax = baseUrl + "activity-docs";
            method = "POST";
        }else{
            urlAjax = baseUrl + "activity-docs/" + id_acttivity_doc;
            method = "PATCH"
        }

        $.ajax({
            url: urlAjax,
            type: method,
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
                }else if(response.status === 200){
                    loadActivityData();
                    if(id_acttivity_doc === 0){
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Document Activity has been Created",
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            $('#closeFormDocModal').click();
                            // location.reload(); // Refresh after the SweetAlert
                        });
                    }else{
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Document Activity has been Edited",
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            $('#closeFormDocModal').click();
                            // location.reload(); // Refresh after the SweetAlert
                        });
                    }
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

    let tags = [];

    function renderTags() {
        let tagContainer = $("#tags");
        tagContainer.find(".tag").remove();

        if (tags.length === 0) return;

        tags.forEach((tag, index) => {
            let tagElement = $(`
                <div class="tag">
                    ${tag}
                    <span class="remove" data-index="${index}">&times;</span>
                </div>
            `);
            tagContainer.append(tagElement);
        });
    }

    $(document).on("keydown", "#tagInput", function (event) {
        if (event.key === "Enter" && this.value.trim() !== "") {
            event.preventDefault();

            let tagText = this.value.trim().toLowerCase();

            if (!tags.includes(tagText)) {
                tags.push(tagText);
                renderTags();
            }

            this.value = "";
        }
    });

    $(document).on("click", ".remove", function () {
        let index = $(this).data("index");
        tags.splice(index, 1);
        renderTags();
    });

    // DateFormating
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

</script>
