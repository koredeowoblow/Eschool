@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
<div x-data="forgotPasswordForm()" class="w-full max-w-[440px] bg-white/98 border border-white/10 shadow-2xl shadow-black/10 rounded-3xl overflow-hidden animate-in">
    <!-- Header Gradient -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-8 py-10  text-center  text-white">
        <i class="bi bi-key-fill text-5xl  mb-6  inline-block drop-shadow-md"></i>
        <h2 class="text-3xl font-bold mb-2 tracking-tight">Forgot Password</h2>
        <p class="text-white/80 font-medium">Enter your email to receive a reset link</p>
    </div>

    <!-- Body -->
    <div class="p-8">
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Alert -->
            <div x-show="message" x-transition.opacity class="p-4 rounded-lg flex items-start gap-3 border" :class="isError ? 'bg-red-50 text-red-700 border-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'" style="display: none;">
                <i class="bi mt-0.5" :class="isError ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill'"></i>
                <span x-text="message" class="text-sm font-medium"></span>
            </div>

            <!-- Email Field -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                <div class="relative group" :class="{ 'ring-2 ring-red-500 rounded-lg': fieldErrors.email }">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-envelope text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input 
                        type="email" 
                        x-model="email" 
                        @input="fieldErrors.email = ''"
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all text-slate-900" 
                        required 
                        autofocus
                        placeholder="name@school.com"
                    >
                </div>
                <p x-show="fieldErrors.email" x-text="fieldErrors.email" class="mt-1 text-sm text-red-600 font-medium" style="display: none;"></p>
            </div>

            <div class="space-y-3">
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm shadow-blue-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Sending...' : 'Send Reset Link'">Send Reset Link</span>
                </button>
                
                <a href="{{ route('login') }}" class="w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium rounded-lg transition-all flex items-center justify-center gap-2">
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('forgotPasswordForm', () => ({
            email: '',
            loading: false,
            message: '',
            isError: false,
            fieldErrors: { email: '' },
            
            async submit() {
                this.message = '';
                this.fieldErrors = { email: '' };
                
                if (!this.email) {
                    this.fieldErrors.email = 'Email is required';
                    return;
                }

                this.loading = true;

                try {
                    const response = await axios.post('/api/v1/forgot-password', { email: this.email });
                    this.isError = false;
                    this.message = response.data.message || 'Reset link sent! Please check your email.';
                    this.email = ''; // Clear form on success
                } catch (err) {
                    this.isError = true;
                    if (err.response?.status === 422) {
                        const errors = err.response.data.errors;
                        if(errors.email) this.fieldErrors.email = errors.email[0];
                    } else {
                        this.message = err.response?.data?.message || 'Failed to send reset link. Please try again.';
                    }
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endsection
