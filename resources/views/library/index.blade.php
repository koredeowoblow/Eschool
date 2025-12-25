@extends('layouts.app')

@section('title', 'Library Manager')
@section('header_title', 'Library')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex gap-2">
            <button class="btn btn-primary-premium" onclick="App.resetForm(document.forms['createBookForm']);" data-bs-toggle="modal" data-bs-target="#createBookModal">
                <i class="bi bi-plus-lg me-1"></i> Add Book
            </button>
            <button class="btn btn-outline-secondary"><i class="bi bi-arrow-left-right me-1"></i> Borrow/Return</button>
        </div>
        <div class="w-25">
             <input type="text" id="bookSearch" class="form-control" placeholder="Search books..." oninput="reloadBooks()">
        </div>
    </div>

    <!-- Library Table -->
    <div class="card-premium">
        <div class="card-body p-0">
             <div class="table-responsive">
                <table class="table table-premium table-hover align-middle mb-0 table-mobile-cards">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="libraryTableBody">
                        <!-- Loaded by JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Book Modal -->
    <div class="modal fade" id="createBookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form name="createBookForm" action="/api/v1/library/books" method="POST" onsubmit="App.submitForm(event, reloadBooks, 'library', 'createBookModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add New Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                         <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editBookForm" method="POST" onsubmit="App.submitForm(event, reloadBooks, 'library', 'editBookModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Edit Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                         <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Author</label>
                            <input type="text" name="author" class="form-control" required>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">ISBN</label>
                                <input type="text" name="isbn" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Quantity</label>
                                <input type="number" name="quantity" class="form-control" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary-premium">Update Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Updated table type to 'library' to match renderer
        App.renderTable('/api/v1/library/books', 'libraryTableBody', 'library');
    });

    function reloadBooks() {
        const query = document.getElementById('bookSearch').value;
        App.renderTable('/api/v1/library/books?search=' + query, 'libraryTableBody', 'library');
    }

    function editBook(book) {
        const form = document.getElementById('editBookForm');
        form.action = `/api/v1/library/books/${book.id}`;
        App.populateForm(form, book);
        const modal = new bootstrap.Modal(document.getElementById('editBookModal'));
        modal.show();
    }
</script>
@endsection
