@extends('layouts.app')

@section('title', 'Library Manager')
@section('header_title', 'Library')

@section('content')
    <div class="flex flex-col md:flex-row  justify-between   items-center   mb-6  gap-3">
        <div class=" flex  gap-2 w-100 w-md-auto">
            <button class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium flex-fill flex-md-grow-0"
                onclick="App.resetForm(document.forms['createBookForm']);" data-bs-toggle="modal"
                data-bs-target="#createBookModal">
                <i class="bi bi-plus-lg me-1"></i> Add Book
            </button>
            <button class="px-4 py-2 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg transition-all inline-flex items-center gap-2 font-medium flex-fill flex-md-grow-0"><i class="bi bi-arrow-left-right me-1"></i>
                Borrow/Return</button>
        </div>
        <div class="w-100 w-md-25">
            <input type="text" id="bookSearch" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" placeholder="Search books..."
                oninput="reloadBooks()">
        </div>
    </div>

    <!-- Library Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr>
                            <th>Book Title</th>
                            <th>Author</th>
                            <th>ISBN</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
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
                <form name="createBookForm" action="/api/v1/library/books" method="POST"
                    onsubmit="App.submitForm(event, reloadBooks, 'library', 'createBookModal')">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Add New Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Title</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Author</label>
                            <input type="text" name="author" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">ISBN</label>
                                <input type="text" name="isbn" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Quantity</label>
                                <input type="number" name="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" value="1" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Add Book</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editBookForm" method="POST"
                    onsubmit="App.submitForm(event, reloadBooks, 'library', 'editBookModal')">
                    @csrf @method('PUT')
                    <div class="modal-header">
                        <h5 class="modal-title font-bold">Edit Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Title</label>
                            <input type="text" name="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class=" mb-6 ">
                            <label class="block text-sm font-medium text-gray-700  mb-6 ">Author</label>
                            <input type="text" name="author" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" required>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4  gap-4 ">
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">ISBN</label>
                                <input type="text" name="isbn" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                            </div>
                            <div class=" md:col-span-6 col-span-1 ">
                                <label class="block text-sm font-medium text-gray-700  mb-6 ">Quantity</label>
                                <input type="number" name="quantity" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all" min="1">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-all inline-flex items-center gap-2 font-medium" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all inline-flex items-center gap-2 font-medium">Update Book</button>
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
