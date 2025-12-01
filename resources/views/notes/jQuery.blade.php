<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ajax crud</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>

<body>
    <div class="container p-5 m-3">
        <h2 class="text-center text-primary">Crud Operation Using jQuery With api Controller</h2>
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#addNote">
            Add
        </button>

        <table class="table table-bordered" id="notesTable">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">title</th>
                    <th scope="col">body</th>
                    <th scope="col">action</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addNote" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add Note's</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="please enter title">
                        </div>

                        <div class="mb-3">
                            <label for="body" class="form-label">Body</label>
                            <input type="text" id="body" name="body" class="form-control" placeholder="optional">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="saveNote" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Modal -->
    <div class="modal fade" id="editNote" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="edit_id">

                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="edit_title" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <input type="text" id="edit_body" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="updateNote">Update</button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

    <script>
        
        function loadNotes() {
            $.ajax({
                url: '/api/notes',
                type: 'GET',
                success: function(notes) {
                    let rows = '';

                    notes.forEach((note, index) => {
                        rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${note.title}</td>
                        <td>${note.body ?? ''}</td>
                        <td>
                            <button class="btn btn-sm btn-warning editBtn" data-bs-toggle="modal" data-bs-target="#editNote"  data-id="${note.id}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${note.id}">Delete</button>
                        </td>
                    </tr>
                `;
                    });

                    $('#notesTable tbody').html(rows);
                }
            });
        }

        // Load on page load
        loadNotes();

// save data into database
        $('#saveNote').on('click', function() {
            let title = $('#title').val();
            let body = $('#body').val();

            $.ajax({
                url: '/api/notes',
                type: 'POST',
                data: {
                    title: title,
                    body: body
                },
                success: function(response) {
                    $('#addNote').modal('hide');
                    $('#title').val('');
                    $('#body').val('');

                    loadNotes(); // reload table after insert
                },
                error: function(err) {
                    alert("Validation failed");
                }
            });
        });

        // edit note data show into form

        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');

            $.ajax({
                url: '/api/notes/' + id,
                type: 'GET',
                success: function(note) {
                    $('#edit_id').val(note.id);
                    $('#edit_title').val(note.title);
                    $('#edit_body').val(note.body);

                    $('#editNote').modal('show');
                }
            });
        });

        // final update the data

        $('#updateNote').on('click', function() {
            let id = $('#edit_id').val();
            let title = $('#edit_title').val();
            let body = $('#edit_body').val();

            $.ajax({
                url: '/api/notes/' + id,
                type: 'PUT',
                data: {
                    title: title,
                    body: body
                },
                success: function(response) {
                    $('#editNote').modal('hide');
                    loadNotes();
                },
                error: function(err) {
                    alert("Update failed");
                }
            });
        });

        // delect the note data from database
        $(document).on('click', '.deleteBtn', function () {
    let id = $(this).data('id');

    if (!confirm("Are you sure you want to delete this note?")) {
        return;
    }

    $.ajax({
        url: '/api/notes/' + id,
        type: 'DELETE',
        success: function(response) {
            loadNotes();
        },
        error: function(err) {
            alert("Delete failed");
        }
    });
});

    </script>

</body>

</html>