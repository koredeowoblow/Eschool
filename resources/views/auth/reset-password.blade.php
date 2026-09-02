@extends('layouts.guest')
@section('title', 'Reset Password')

@section('content')
<div x-data="resetPasswordForm()" class="w-full max-w-[440px] bg-white/98 border border-white/10 shadow-2xl shadow-black/10 rounded-3xl overflow-hidden animate-in">
    <!-- Header Gradient -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-8 py-10  text-center  text-white">
        <i class="bi bi-shield-lock-fill text-5xl  mb-6  inline-block drop-shadow-md"></i>
        <h2 class="text-3xl font-bold mb-2 tracking-tight">Reset Password</h2>
        <p class="text-white/80 font-medium">Enter your email and new password</p>
    </div>

    <!-- Body -->
    <div class="p-8">
        <form @submit.prevent="submit" class="space-y-6">
            <input type="hidden" x-model="token">
            
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
                        placeholder="Confirm your email"
                    >
                </div>
                <p x-show="fieldErrors.email" x-text="fieldErrors.email" class="mt-1 text-sm text-red-600 font-medium" style="display: none;"></p>
            </div>

            <!-- New Password Field -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">New Password</label>
                <div class="relative group" :class="{ 'ring-2 ring-red-500 rounded-lg': fieldErrors.password }">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-lock text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        x-model="password" 
                        @input="fieldErrors.password = ''"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all text-slate-900" 
                        required
                        placeholder="New password"
                    >
                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                        <i class="bi" :class="showPassword ? 'bi-eye-slash' : 'bi-eye'"></i>
                    </button>
                </div>
                <p x-show="fieldErrors.password" x-text="fieldErrors.password" class="mt-1 text-sm text-red-600 font-medium" style="display: none;"></p>
            </div>

            <!-- Confirm Password Field -->
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-2">Confirm Password</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-shield-check text-slate-400 group-focus-within:text-blue-500 transition-colors"></i>
                    </div>
                    <input 
                        :type="showPassword ? 'text' : 'password'" 
                        x-model="password_confirmation" 
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all text-slate-900" 
                        required
                        placeholder="Confirm new password"
                    >
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <button 
                    type="submit" 
                    :disabled="loading"
                    class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm shadow-blue-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <svg x-show="loading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span x-text="loading ? 'Processing...' : 'Reset Password'">Reset Password</span>
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
        Alpine.data('resetPasswordForm', () => ({
            token: '{{ $token }}',
            email: '{{ request()->email ?? "" }}',
            password: '',
            password_confirmation: '',
            showPassword: false,
            loading: false,
            message: '',
            isError: false,
            fieldErrors: { email: '', password: '' },
            
            async submit() {
                this.message = '';
                this.fieldErrors = { email: '', password: '' };
                
                if (this.password !== this.password_confirmation) {
                    this.isError = true;
                    this.message = 'Passwords do not match.';
                    return;
                }

                if (!this.email || !this.password) return;

                this.loading = true;

                try {
                    const response = await axios.post('/api/v1/reset-password', {
                        token: this.token,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password_confirmation
                    });
                    
                    this.isError = false;
                    this.message = response.data.message || 'Password reset successfully!';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Your password has been reset. Redirecting...',
                        timer: 2000,
                        showConfirmButton: false
                    });

                    setTimeout(() => window.location.href = '/login', 2000);
                } catch (err) {
                    this.isError = true;
                    if (err.response?.status === 422) {
                        const errors = err.response.data.errors;
                        Object.keys(errors).forEach(field => {
                            if(this.fieldErrors[field] !== undefined) {
                                this.fieldErrors[field] = errors[field][0];
                            }
                        });
                    } else {
                        this.message = err.response?.data?.message || 'Failed to reset password.';
                    }
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endsection
