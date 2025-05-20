@extends('layouts.app')

@section('title', 'Home Page')

@section('content')
<style>
    .suggestions {
        list-style: none;
        padding: 0;
        margin: 0;
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        width: 85%;
        max-height: 150px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .suggestions li {
        padding: 8px;
        cursor: pointer;
    }

    .suggestions li:hover,
    .suggestions li.selected {
        background: #980003;
        color: white;
    }

    #tagSearch{
        position: sticky;
        overflow-y: auto;
        top: 50px;
        height: auto;
    }

    @media (max-width: 768px) {
        .row {
            display: flex;
            flex-direction: column-reverse;
        }

        #tagSearch {
            position: relative;
        }
    }
</style>

<div class="page-heading">
    <div class="page-content">
        <section class="section">
            <div class="row">
                <div class="col-sm-12" id="tagsSearch">
                    <div class="card">
                        <div class="card-header">
                            <h1 class="card-title">Pencarian Dokumen Aktivitas</h1>
                        </div>
                        <div class="card-body">
                            <div class="form-group position-relative has-icon-left">
                                <div class="form-control-icon">
                                    <i class="bi bi-search"></i>
                                </div>
                                <input type="text" class="form-control form-control-lg" id="tagInput" placeholder="Masukkan kata kunci pencarian ..." autocomplete="off" />
                                <ul class="suggestions" id="suggestions"></ul>
                            </div>
                            <div class="tag-container" id="tags"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12" id="content">
                    <div id="content_card"></div>
                </div>
            </div>
        </section>
    </div>
</div>

<div class="modal fade text-left w-100" id="readModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel33"
    aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel33" id="modalTitle"></h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="modal-body row">
                <div class="col-md-6">
                    <label><b> Nama Aktivitas : </b></label>
                    <div class="form-group">
                        <p class="form-control-static" id="activity_name"></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <label><b> Judul Dokumen : </b></label>
                    <div class="form-group">
                        <p class="form-control-static" id="doc_title"></p>
                    </div>
                </div>
                <div class="col-md-12">
                    <label><b>Deskripsi : </b></label>
                    <div class="form-group">
                        <p class="form-control-static" id="description"></p>
                    </div>
                </div>
                <div class="col-md-8">
                    <label><b> Tag : </b></label>
                    <div class="form-group" id="tags">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="fullPageLoader" class="full-page-loader" style="display: none">
    <div class="spinner-border text-light" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    let tags = [];
    let tagOptions = [];
    let selectedIndex = -1;
    let baseUrl = 'https://bepm.hanatekindo.com/api/v1/';
    let access_token = @json(session('user.access_token'))

    let dataDoc = {!! json_encode($activityDoc) !!}
    $(document).ready(function () {
        renderDocs(dataDoc);
        mergeTags();
    });

    function filterDocsByTags() {
        let filteredDocs = dataDoc.filter(doc => {
            return tags.some(tag => doc.description.toLowerCase().includes(tag.toLowerCase()));
        });

        renderDocs(filteredDocs);
    }


    function renderDocs(filteredDocs) {
        $('#content_card').empty();

        if (filteredDocs.length === 0) {
            $('#content_card').html('<div class="card card-body"><p class="card-text text-center">Dokumen tidak ditemukan</p></div>');
            return;
        }

        let content_doc = '';
        $.each(filteredDocs, function (index, doc) {
            let content_tag = '';
            $.each(doc.tags, function (indexs, tag) {
                content_tag += `<span class="badge bg-light-secondary" style="margin:5px;">${tag}</span>`;
            });

            let shortDescription = doc.description.length > 350
                ? doc.description.substring(0, 350) + " ...."
                : doc.description;
                console.log(doc)
            content_doc += `
                <div class="card" style="margin-bottom: 1.1rem;">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-7 col-12">
                                <h4 class="card-title">${doc.title}</h4>
                            </div>
                            <div class="col-md-5 d-md-flex justify-content-end d-none">
                                <small>${dateFormat(doc.created_at) || 'Unknown Date'}</small>
                            </div>
                        </div>
                        <p class="card-text" style="text-align: justify;text-justify: inter-word;">${shortDescription}</p>
                        <div class="d-flex justify-content-between">
                            <small class="d-md-none mt-2">${dateFormat(doc.created_at) || 'Unknown Date'}</small>
                            <button type="submit" class="btn btn-primary me-1 justify-content-end" onclick="readModal(${doc.id})">Selengkapnya</button>
                        </div>
                    </div>
                    <div class="card-body" style="padding-top:1px;">
                        <div class="badges">
                            ${content_tag}
                        </div>
                    </div>
                </div>`;
        });

        $('#content_card').html(content_doc);
    }

    function readModal(id) {
        let doc = dataDoc.find(doc => doc.id === id);
        console.log("readModal",doc);
        let modal = $('#readModal');
        modal.find('#activity_name').text(doc.title);
        modal.find('#doc_title').text(doc.title);
        modal.find('#description').html(doc.description);
        let tagsShow = doc.tags.map(tag =>
                            `<button class="btn btn-info me-2">${tag}</button>`
                        ).join("");
        modal.find('#tags').html(tagsShow);
        modal.find('#modalTitle').text('Document Activity - MOM (' + dateFormat(doc.created_at) + ')');
        modal.modal('show');
    }

    function mergeTags() {
        let mergedTags = new Set();
        dataDoc.forEach(doc => {
            if (doc.tags && Array.isArray(doc.tags)) {
            doc.tags.forEach(tag => mergedTags.add(tag));
            }
        });

        tagOptions = Array.from(mergedTags);
    }

    function renderTags() {
        let tagContainer = $("#tags");
        tagContainer.find(".tag").remove();

        tags.forEach((tag, index) => {
            let tagElement = $(`
                <div class="tag">
                    ${tag}
                    <span class="remove" data-index="${index}">&times;</span>
                </div>
            `);
            tagContainer.append(tagElement);
        });

        if(tags.length > 0){
            filterDocsByTags();
        }else{
            renderDocs(dataDoc);
        }
    }

    $("#tagInput").on("input", function () {
        let inputText = this.value.trim().toLowerCase();
        let suggestionList = $("#suggestions");
        selectedIndex = -1;

        if (inputText === "") {
            suggestionList.hide();
            return;
        }

        let filteredOptions = tagOptions.filter(tag => tag.toLowerCase().includes(inputText));

        if (filteredOptions.length > 0) {
            suggestionList.empty().show();
            filteredOptions.forEach((tag, index) => {
                suggestionList.append(`<li data-index="${index}">${tag}</li>`);
            });
        } else {
            suggestionList.hide();
        }
    });

    $("#tagInput").on("keydown", function (event) {
        let items = $("#suggestions").find("li");

        if (event.key === "ArrowDown") {
            event.preventDefault();
            if (selectedIndex < items.length - 1) selectedIndex++;
        } else if (event.key === "ArrowUp") {
            event.preventDefault();
            if (selectedIndex > 0) selectedIndex--;
        } else if (event.key === "Enter") {
            event.preventDefault();
            if (selectedIndex >= 0 && selectedIndex < items.length) {
                addTag($(items[selectedIndex]).text());
            } else if (this.value.trim() !== "") {
                addTag(this.value.trim());
            }
            return;
        }

        items.removeClass("selected");
        if (selectedIndex >= 0) $(items[selectedIndex]).addClass("selected");
    });

    $(document).on("click", "#suggestions li", function () {
        addTag($(this).text());
    });

    $(document).on("click", ".remove", function () {
        let index = $(this).data("index");
        tags.splice(index, 1);
        renderTags();
    });

    $(document).on("click", function (event) {
        if (!$(event.target).closest("#tagInput, #suggestions").length) {
            $("#suggestions").hide();
        }
    });

    function addTag(tagText) {
        if (!tags.includes(tagText)) {
            tags.push(tagText);
            renderTags();
        }
        $("#tagInput").val("").focus();
        $("#suggestions").hide();
    }

    function dateFormat(dateTimeString) {
        if (!dateTimeString || typeof dateTimeString !== "string") {
            return "Invalid Date";
        }

        let datePart = dateTimeString.split(" ")[0];
        let [year, month, day] = datePart.split("-");

        if (!year || !month || !day) {
            return "Invalid Date";
        }

        let date = new Date(year, month - 1, day);

        if (isNaN(date.getTime())) {
            return "Invalid Date";
        }

        let formattedDate = new Intl.DateTimeFormat("id-ID", {
            day: "2-digit",
            month: "long",
            year: "numeric"
        }).format(date);

        return formattedDate;
    }
</script>

@endsection