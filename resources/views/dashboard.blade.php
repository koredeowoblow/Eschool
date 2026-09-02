@extends('layouts.app')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard Overview')

@section('content')
<div x-data="dashboardPage()" x-init="init()" class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <button 
            @click="$dispatch('open-modal', 'linkAccountModal')"
            class="w-full sm:w-auto px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center justify-center gap-2 font-medium shadow-sm"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
            <span>Link Account</span>
        </button>
    </div>

    <!-- Dynamic Dashboard Root -->
    <div id="dashboard-stats-root" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="col-span-full  text-center  py-12">
            <svg class="animate-spin h-8 w-8 text-indigo-600 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-gray-500 mt-4 text-sm">Loading dashboard...</p>
        </div>
    </div>

    <!-- Charts Container -->
    <div id="dashboard-charts-root" class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-2"></div>

    <!-- Recent Activity Container -->
    <div id="dashboard-activity-root" class="mt-6"></div>

    <!-- Account Linking Modal -->
    <x-modal id="linkAccountModal" title="Link Account">
        <div x-data="{ step: 1, email: '', otp: '', loading: false }">
            <!-- Step 1: Initiate -->
            <div x-show="step === 1" x-transition.opacity>
                <p class="text-gray-600 text-sm mb-6">Enter the email address of the account you want to link. A verification code will be sent to them.</p>
                <form @submit.prevent="initiateLinking" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input 
                            type="email" 
                            x-model="email" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" 
                            placeholder="user@example.com" 
                            required
                        >
                    </div>
                    <x-button type="submit" class="w-full" x-bind:disabled="loading" x-bind:loading="loading">
                        Send Verification Code
                    </x-button>
                </form>
            </div>

            <!-- Step 2: Verify -->
            <div x-show="step === 2" x-transition.opacity style="display: none;">
                <p class="text-gray-600 text-sm mb-6">A 6-digit code has been sent to <span class="font-bold text-gray-900" x-text="email"></span>. Please enter it below.</p>
                <form @submit.prevent="verifyLinking" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Verification Code</label>
                        <input 
                            type="text" 
                            x-model="otp" 
                            class="w-full px-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500  text-center  text-2xl font-bold tracking-widest transition-all" 
                            maxlength="6" 
                            placeholder="000000" 
                            required
                        >
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="step = 1" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                            Back
                        </button>
                        <x-button type="submit" class="flex-1" x-bind:disabled="loading" x-bind:loading="loading">
                            Verify & Link
                        </x-button>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboardPage', () => ({
            init() {
                // The actual dashboard chart logic can be injected here
                // or we can call App.loadDashboard() if premium-app.js is still loaded.
                if(typeof App !== 'undefined' && typeof App.loadDashboard === 'function') {
                    // Slight delay to let Alpine render
                    setTimeout(() => App.loadDashboard(), 100);
                }
            },
            
            async initiateLinking() {
                this.loading = true;
                try {
                    const res = await fetch('/api/v1/link/initiate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email: this.email })
                    });
                    
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Failed to send code');
                    
                    window.showToast('Verification code sent!', 'success');
                    this.step = 2;
                } catch (e) {
                    window.showToast(e.message, 'error');
                } finally {
                    this.loading = false;
                }
            },
            
            async verifyLinking() {
                this.loading = true;
                try {
                    const res = await fetch('/api/v1/link/verify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ email: this.email, code: this.otp })
                    });
                    
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Failed to verify code');
                    
                    window.showToast('Account linked successfully!', 'success');
                    this.$dispatch('close-modal', 'linkAccountModal');
                    setTimeout(() => window.location.reload(), 1000);
                } catch (e) {
                    window.showToast(e.message, 'error');
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endsection
