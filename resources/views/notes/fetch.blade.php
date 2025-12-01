<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ajax CRUD (Fetch API)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container py-5">
        <h2 class="text-center text-primary">CRUD with Fetch API (Laravel 12 API Controller)</h2>

        <!-- Add Note Button -->
        <button type="button" class="btn btn-primary my-3" data-bs-toggle="modal" data-bs-target="#addNote">
            Add
        </button>

        <!-- Notes Table -->
        <table class="table table-bordered" id="notesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Body</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Add Note Modal -->
    <div class="modal fade" id="addNote" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Note</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" id="title" class="form-control" placeholder="Enter title">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Body</label>
                        <input type="text" id="body" class="form-control" placeholder="Optional">
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-primary" id="saveNote">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Edit Note Modal -->
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


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Load all notes
        async function loadNotes() {
            const res = await fetch('/api/notes');
            const notes = await res.json();

            let rows = '';

            notes.forEach((note, index) => {
                rows += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${note.title}</td>
                        <td>${note.body ?? ''}</td>
                        <td>
                            <button class="btn btn-warning btn-sm editBtn" data-id="${note.id}" data-bs-toggle="modal" data-bs-target="#editNote">Edit</button>
                            <button class="btn btn-danger btn-sm deleteBtn" data-id="${note.id}">Delete</button>
                        </td>
                    </tr>
                `;
            });

            document.querySelector('#notesTable tbody').innerHTML = rows;
        }

        loadNotes();

        // Save note
        document.querySelector('#saveNote').addEventListener('click', async () => {
            const title = document.querySelector('#title').value;
            const body = document.querySelector('#body').value;

            const res = await fetch('/api/notes', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ title, body })
            });

            if (res.ok) {
                document.querySelector('#addNote').querySelector('.btn-close').click();
                document.querySelector('#title').value = '';
                document.querySelector('#body').value = '';
                loadNotes();
            } else {
                alert("Validation failed");
            }
        });

        // Fill edit modal
        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('editBtn')) {
                const id = e.target.dataset.id;

                const res = await fetch('/api/notes/' + id);
                const note = await res.json();

                document.querySelector('#edit_id').value = note.id;
                document.querySelector('#edit_title').value = note.title;
                document.querySelector('#edit_body').value = note.body;
            }
        });

        // Update note
        document.querySelector('#updateNote').addEventListener('click', async () => {
            const id = document.querySelector('#edit_id').value;
            const title = document.querySelector('#edit_title').value;
            const body = document.querySelector('#edit_body').value;

            const res = await fetch('/api/notes/' + id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ title, body })
            });

            if (res.ok) {
                document.querySelector('#editNote').querySelector('.btn-close').click();
                loadNotes();
            } else {
                alert("Update failed");
            }
        });

        // Delete note
        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('deleteBtn')) {

                const id = e.target.dataset.id;

                if (!confirm("Are you sure you want to delete this note?")) {
                    return;
                }

                const res = await fetch('/api/notes/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrf
                    }
                });

                if (res.ok) {
                    loadNotes();
                } else {
                    alert("Delete failed");
                }
            }
        });
    </script>

</body>

</html>
