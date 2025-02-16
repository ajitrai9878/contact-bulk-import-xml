@extends('master')
@section('content')
    <div class="container mt-4">
        <h2>{{@testGlobalHelper()}}</h2>
        <hr>
        <div class="d-flex justify-content-end gap-2 align-items-center mb-3">
            <div class="input-group w-25">
                <span class="input-group-text">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </span>
                <input type="text" name="search" class="form-control" id="search" placeholder="Searching...">
            </div>

            <div>
                <button type="button" id="bulkDeleteBtn" class="btn btn-danger"><i class="fa fa-trash"></i> Bulk Delete
                </button>
            </div>
            <!-- Button to Open Modal -->
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addContactModal">
                <i class="fa fa-plus"></i> Add Contact
            </button>

            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">
                <i class="fa fa-plus"></i> Bulk Add Contact
            </button>
        </div>

        <div class="card shadow-lg">
            <div class="card-body">
                <table id="contactsTable" class="table table-striped table-bordered">
                    <thead class="table-dark">
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all">
                            <i class="fa fa-check" aria-hidden="true"></i>All
                        </th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Last Name</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="setData">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Single Contact Upload Modal -->
    <div class="modal fade" id="addContactModal" tabindex="-1" aria-labelledby="addContactModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addContactModalLabel">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addContactForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="contactName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="contactName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="contactLastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="contactPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="contactPhone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal for Uploading XML -->
    <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload XML File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('contacts.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="xmlFile" class="form-label">Select XML File</label>
                            <input type="file" class="form-control" id="xmlFile" name="xml_file" accept=".xml" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Contact Modal -->
    <div class="modal fade" id="editContactModal" tabindex="-1" aria-labelledby="editContactModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editContactModalLabel">Edit Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editContactForm">
                    @csrf
                    <input type="hidden" id="editContactId"> <!-- Hidden field for contact ID -->
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="editContactName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="editContactName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="editContactLastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="editContactLastName" name="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="editContactPhone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="editContactPhone" name="phone" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function () {
            fetch_data(1);

            function fetch_data(page) {
                let search = $("#search").val();
                $.ajax({
                    url: "{{route('contacts.index')}}?page=" + page,
                    data: {search: search},
                    success: function (data) {
                        $('#setData').html(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        console.error(errorThrown);
                    }
                });
            }

            $(document).on('keyup', '#search', function () {
                fetch_data(1);
            });

            $(document).on('click', '.pagination a', function (event) {
                event.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                fetch_data(page);
            });
        });
        $(document).ready(function () {
            // Enable/Disable Bulk Delete Button
            $(document).on('change', '.select-item, #select-all', function () {
                let selected = $('.select-item:checked').length > 0;
                $('#bulkDeleteBtn').prop('disabled', !selected);
            });

            // Select/Deselect All Checkboxes
            $('#select-all').on('change', function () {
                $('.select-item').prop('checked', $(this).prop('checked'));
                $('#bulkDeleteBtn').prop('disabled', !$('.select-item:checked').length);
            });

            // Single Delete Contact
            $(document).on('click', '.delete-contact', function () {
                let contactId = $(this).data('id');
                if (confirm('Are you sure you want to delete this contact?')) {
                    $.ajax({
                        url: "{{ route('contacts.delete') }}",
                        type: "DELETE",
                        data: {
                            id: contactId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.status === true) {
                                toastr.success(response.message);
                                setTimeout(function () {
                                    window.location.reload(true);
                                }, 3000);
                            } else {
                                toastr.error(response.message);
                                setTimeout(function () {
                                    window.location.reload(true);
                                }, 3000);
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON.error);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        }
                    });
                }
            });

            // Bulk Delete Contacts
            $('#bulkDeleteBtn').on('click', function () {
                let selectedIds = $('.select-item:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) {
                    toastr.warning("No contacts selected!");
                    return;
                }

                if (confirm('Are you sure you want to delete the selected contacts?')) {
                    $.ajax({
                        url: "{{ route('contacts.bulk-delete') }}",
                        type: "DELETE",
                        data: {
                            ids: selectedIds,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function (response) {
                            if (response.status === true) {
                                toastr.success(response.message);
                                setTimeout(function () {
                                    window.location.reload(true);
                                }, 3000);
                            } else {
                                toastr.error(response.message);
                                setTimeout(function () {
                                    window.location.reload(true);
                                }, 3000);
                            }
                        },
                        error: function (xhr) {
                            toastr.error(xhr.responseJSON.error);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        }
                    });
                }
            });
        });
        $(document).ready(function () {
            $('#addContactForm').submit(function (event) {
                event.preventDefault(); // Prevent default form submission

                let formData = {
                    name: $('#contactName').val(),
                    lastName: $('#contactLastName').val(),
                    phone: $('#contactPhone').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('contacts.store') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.status === true) {
                            toastr.success(response.message);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        } else {
                            toastr.error(response.message);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        }
                    },
                    error: function (xhr) {
                        if (xhr.responseJSON.error) {
                            toastr.error(xhr.responseJSON.error);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        } else {
                            toastr.error("Something went wrong.");
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        }
                    }
                });
            });
        });
        $(document).ready(function () {
            // Open Edit Modal & Fetch Contact Data
            $(document).on('click', '.edit-contact', function () {
                let contactId = $(this).data('id');

                $.ajax({
                    url: "{{ route('contacts.edit') }}",
                    type: "GET",
                    data: {id: contactId},
                    success: function (response) {
                        $('#editContactId').val(response.data.id);
                        $('#editContactName').val(response.data.name);
                        $('#editContactLastName').val(response.data.lastName);
                        $('#editContactPhone').val(response.data.phone);
                        $('#editContactModal').modal('show');
                    },
                    error: function () {
                        toastr.error("Failed to fetch contact details.");
                    }
                });
            });

            // Submit Updated Contact Data
            $('#editContactForm').submit(function (event) {
                event.preventDefault();

                let contactId = $('#editContactId').val();
                let formData = {
                    id: contactId,
                    name: $('#editContactName').val(),
                    lastName: $('#editContactLastName').val(),
                    phone: $('#editContactPhone').val(),
                    _token: "{{ csrf_token() }}"
                };

                $.ajax({
                    url: "{{ route('contacts.update') }}",
                    type: "POST",
                    data: formData,
                    success: function (response) {
                        if (response.status === true) {
                            toastr.success(response.message);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        } else {
                            toastr.error(response.message);
                            setTimeout(function () {
                                window.location.reload(true);
                            }, 3000);
                        }
                    },
                    error: function (xhr) {
                        toastr.error(xhr.responseJSON.error || "Something went wrong.");
                    }
                });
            });
        });
    </script>
@endsection


