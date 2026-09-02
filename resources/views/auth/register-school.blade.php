@extends('layouts.guest')

@section('title', 'School Registration')

@section('content')
    <div x-data="registerSchoolForm()"
        class="w-full max-w-4xl mx-auto bg-white/98 border border-white/10 shadow-2xl shadow-black/10 rounded-3xl overflow-hidden animate-in my-8">
        <!-- Header Gradient -->
        <div
            class="bg-gradient-to-br from-blue-500 to-indigo-600 px-8 py-12  text-center  text-white relative overflow-hidden">
            <!-- SVG Pattern Background -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid-pattern" width="40" height="40" patternUnits="userSpaceOnUse">
                            <path d="M0 40L40 0H20L0 20M40 40V20L20 40" fill="currentColor" fill-opacity="0.2" />
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid-pattern)" />
                </svg>
            </div>

            <div class="relative z-10">
                <i class="bi bi-mortarboard-fill text-6xl  mb-6  inline-block drop-shadow-md"></i>
                <h2 class="text-4xl font-bold mb-2 tracking-tight">Begin Your Journey</h2>
                <p class="text-white/80 font-medium text-lg">Register your school today and transform your administration.
                </p>
            </div>
        </div>

        <!-- Body -->
        <div class="p-6 md:p-10">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Hidden Fields -->
                <input type="hidden" x-model="formData.slug">
                <input type="hidden" x-model="formData.admin_name">
                <input type="hidden" x-model="formData.admin_email">
                <input type="hidden" x-model="formData.status">

                <!-- Section 1: School Details -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-blue-600 flex items-center gap-2 border-b pb-2">
                        <div class="bg-blue-50 p-2 rounded-lg"><i class="bi bi-building"></i></div>
                        School Information
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- School Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">School Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.name" @input="clearError('name')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.name }" placeholder="e.g. Springfield High">
                            <p x-show="errors.name" x-text="errors.name" class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- Official Email -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Official Email <span
                                    class="text-red-500">*</span></label>
                            <input type="email" x-model="formData.email" @input="clearError('email')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.email }" placeholder="admin@school.com">
                            <p x-show="errors.email" x-text="errors.email" class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" x-model="formData.phone" @input="clearError('phone')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.phone }" placeholder="+1 (555) 000-0000">
                            <p x-show="errors.phone" x-text="errors.phone" class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- Website -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Website URL <span
                                    class="text-slate-400 font-normal">(Optional)</span></label>
                            <input type="url" x-model="formData.website"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="https://www.school.com">
                        </div>
                    </div>
                </div>

                <!-- Section 2: Location -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-blue-600 flex items-center gap-2 border-b pb-2">
                        <div class="bg-blue-50 p-2 rounded-lg"><i class="bi bi-geo-alt"></i></div>
                        Location Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Address -->
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Full Address <span
                                    class="text-red-500">*</span></label>
                            <textarea x-model="formData.address" @input="clearError('address')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all h-24"
                                :class="{ 'ring-2 ring-red-500': errors.address }" placeholder="123 Education Lane..."></textarea>
                            <p x-show="errors.address" x-text="errors.address" class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- City -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">City <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.city" @input="clearError('city')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.city }" placeholder="City">
                            <p x-show="errors.city" x-text="errors.city" class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- State -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">State <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.state" @input="clearError('state')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.state }" placeholder="State">
                            <p x-show="errors.state" x-text="errors.state" class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- Area -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Area/District <span
                                    class="text-slate-400 font-normal">(Optional)</span></label>
                            <input type="text" x-model="formData.area"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                placeholder="District">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Contact Person -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-blue-600 flex items-center gap-2 border-b pb-2">
                        <div class="bg-blue-50 p-2 rounded-lg"><i class="bi bi-person-badge"></i></div>
                        Administrator Contact
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Admin Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Admin Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" x-model="formData.contact_person" @input="clearError('contact_person')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.contact_person }" placeholder="John Doe">
                            <p class="text-xs text-slate-500 mt-1"><i class="bi bi-info-circle"></i> This person will be
                                the default Super Admin.</p>
                            <p x-show="errors.contact_person" x-text="errors.contact_person"
                                class="text-red-500 text-sm mt-1"></p>
                        </div>

                        <!-- Direct Phone -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Direct Phone <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" x-model="formData.contact_person_phone"
                                @input="clearError('contact_person_phone')"
                                class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                                :class="{ 'ring-2 ring-red-500': errors.contact_person_phone }" placeholder="Admin Phone">
                            <p x-show="errors.contact_person_phone" x-text="errors.contact_person_phone"
                                class="text-red-500 text-sm mt-1"></p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Subscription Plan -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-blue-600 flex items-center gap-2 border-b pb-2">
                        <div class="bg-blue-50 p-2 rounded-lg"><i class="bi bi-credit- bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden "></i></div>
                        Subscription Plan
                    </h3>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Select Your Plan <span
                                class="text-red-500">*</span></label>
                        <select x-model="formData.plan" @change="clearError('plan')"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all"
                            :class="{ 'ring-2 ring-red-500': errors.plan }">
                            <option value="">Select a Plan</option>
                            <template x-for="plan in plans" :key="plan.id">
                                <option :value="plan.id" x-text="plan.name"></option>
                            </template>
                        </select>
                        <p x-show="errors.plan" x-text="errors.plan" class="text-red-500 text-sm mt-1"></p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button type="submit" :disabled="loading"
                        class="w-full py-4 px-6 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-lg font-semibold rounded-xl shadow-lg shadow-blue-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-3 transform hover:-translate-y-0.5">
                        <svg x-show="loading" class="animate-spin h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" style="display: none;">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span x-text="loading ? 'Processing...' : 'Submit Registration'">Submit Registration</span>
                        <i x-show="!loading" class="bi bi-arrow-right"></i>
                    </button>
                </div>

                <div class=" text-center  mt-6 border-t border-slate-100 pt-6">
                    <p class="text-slate-500">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="font-bold text-blue-600 hover:text-blue-700 transition-colors">Login Here</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registerSchoolForm', () => ({
                plans: [],
                loading: false,
                errors: {},
                formData: {
                    name: '{{ old('name') }}',
                    email: '{{ old('email') }}',
                    phone: '{{ old('phone') }}',
                    website: '{{ old('website') }}',
                    address: '{{ old('address') }}',
                    city: '{{ old('city') }}',
                    state: '{{ old('state') }}',
                    area: '{{ old('area') }}',
                    contact_person: '{{ old('contact_person') }}',
                    contact_person_phone: '{{ old('contact_person_phone') }}',
                    plan: '{{ old('plan') }}',
                    slug: '',
                    admin_name: '',
                    admin_email: '',
                    status: 'pending'
                },

                async init() {
                    try {
                        const res = await axios.get('/api/v1/plans');
                        this.plans = res.data.data || res.data;
                    } catch (e) {
                        console.error("Failed to load plans", e);
                    }
                },

                clearError(field) {
                    if (this.errors[field]) {
                        delete this.errors[field];
                    }
                },

                async submit() {
                    // Basic frontend validation
                    this.errors = {};
                    let hasErrors = false;
                    const required = ['name', 'email', 'phone', 'address', 'city', 'state',
                        'contact_person', 'contact_person_phone', 'plan'
                    ];

                    required.forEach(field => {
                        if (!this.formData[field]) {
                            this.errors[field] = 'This field is required';
                            hasErrors = true;
                        }
                    });

                    if (hasErrors) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Incomplete Form',
                            text: 'Please fill in all required fields.'
                        });
                        return;
                    }

                    // Dynamic generation of hidden fields
                    this.formData.slug = this.formData.name.toLowerCase().replace(/[^\w ]+/g, '')
                        .replace(/ +/g, '-');
                    this.formData.admin_name = this.formData.contact_person;
                    this.formData.admin_email = this.formData.email;

                    this.loading = true;

                    try {
                        const response = await axios.post(
                            "{{ secure_url(route('school.register.submit', [], false)) }}",
                            this.formData);

                        Swal.fire({
                            icon: 'success',
                            title: 'Welcome!',
                            text: 'Registration successful! Your school account has been created.',
                            confirmButtonText: 'Proceed to Login',
                            confirmButtonColor: '#3b82f6',
                            allowOutsideClick: false
                        }).then(() => {
                            window.location.href = "{{ route('login') }}";
                        });

                    } catch (err) {
                        if (err.response?.status === 422) {
                            const errs = err.response.data.errors;
                            let errHtml =
                                '<ul class="text-left text-sm text-red-500 list-disc pl-5 mt-2">';
                            Object.keys(errs).forEach(field => {
                                this.errors[field] = errs[field][0];
                                errHtml += `<li>${errs[field][0]}</li>`;
                            });
                            errHtml += '</ul>';

                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Failed',
                                html: 'Please check the form for errors.' + errHtml,
                                confirmButtonColor: '#3b82f6'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Registration Failed',
                                text: err.response?.data?.message ||
                                    'An unexpected error occurred. Please try again later.',
                                confirmButtonColor: '#3b82f6'
                            });
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
@endsection
