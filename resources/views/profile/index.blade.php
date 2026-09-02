@extends('layouts.app')

@section('title', 'My Profile')
@section('header_title', 'My Profile')

@section('content')
    {{-- Success Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl p-4 flex items-center justify-between animate-in">
            <div class="flex items-center gap-3">
                <i class="bi bi-check-circle-fill text-emerald-500 text-xl"></i>
                <p class="font-medium">{{ session('success') }}</p>
            </div>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 transition-colors">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    @endif

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Left Column: Profile Header --}}
        <div class="lg:w-1/3">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden  text-center  p-8 sticky top-6">
                <div class="mb-6 relative inline-block">
                    <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}"
                        class="rounded-full border-4 border-blue-100 object-cover w-32 h-32 mx-auto shadow-md">
                    <button class="absolute bottom-0 right-0 bg-blue-600 text-white p-2 rounded-full shadow-lg hover:bg-blue-700 transition-colors">
                        <i class="bi bi-camera-fill text-sm"></i>
                    </button>
                </div>
                <h4 class="text-2xl font-bold text-slate-800 mb-1">{{ $user->name }}</h4>
                <p class="text-slate-500 mb-6 font-medium flex items-center justify-center gap-2">
                    <i class="bi bi-envelope"></i>{{ $user->email }}
                </p>
                <div class="flex flex-wrap justify-center gap-2 mb-6">
                    @foreach ($user->roles as $role)
                        <span class="px-4 py-1.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wider">
                            {{ str_replace('_', ' ', $role->name) }}
                        </span>
                    @endforeach
                </div>
                @if ($user->school)
                    <div class="bg-slate-50 rounded-xl p-4 mb-6 border border-slate-100">
                        <p class="text-sm text-slate-500 mb-1">Affiliated School</p>
                        <p class="font-semibold text-slate-700 flex items-center justify-center gap-2">
                            <i class="bi bi-building text-blue-500"></i>{{ $user->school->name }}
                        </p>
                    </div>
                @endif
                <hr class="border-slate-100 my-6">
                <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Member Since</p>
                <p class="font-medium text-slate-700">{{ $user->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        {{-- Right Column: Editable Forms --}}
        <div class="lg:w-2/3 space-y-6">
            {{-- Personal Information --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-5 bg-slate-50/50">
                    <h5 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                        <i class="bi bi-person-circle text-blue-600"></i> Personal Information
                    </h5>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" name="name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('name') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" name="email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('email') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Phone</label>
                                <input type="tel" name="phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('phone') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('phone', $user->phone) }}" placeholder="+234 xxx xxx xxxx">
                                @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Gender</label>
                                <select name="gender" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('gender') border-red-500 ring-1 ring-red-500 @enderror">
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ old('gender', $user->gender) == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('gender')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Date of Birth</label>
                                <input type="date" name="date_of_birth" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('date_of_birth') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('date_of_birth', $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '') }}">
                                @error('date_of_birth')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-8 pt-6 border-t border-slate-100">
                            <button type="submit" :disabled="loading" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm shadow-blue-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <i x-show="!loading" class="bi bi-save"></i>
                                <span x-text="loading ? 'Saving...' : 'Save Changes'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Address Information --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-5 bg-slate-50/50">
                    <h5 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                        <i class="bi bi-geo-alt text-blue-600"></i> Address Information
                    </h5>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.update') }}" method="POST" x-data="{ loading: false }" @submit="loading = true">
                        @csrf
                        @method('PUT')

                        {{-- Hidden fields to preserve other data --}}
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="email" value="{{ $user->email }}">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Street Address</label>
                                <input type="text" name="address" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('address') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('address', $user->address) }}" placeholder="123 Main Street">
                                @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">City</label>
                                <input type="text" name="city" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('city') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('city', $user->city) }}" placeholder="Lagos">
                                @error('city')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">State/Province</label>
                                <input type="text" name="state" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('state') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('state', $user->state) }}" placeholder="Lagos State">
                                @error('state')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">ZIP/Postal Code</label>
                                <input type="text" name="zip" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('zip') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('zip', $user->zip) }}" placeholder="100001">
                                @error('zip')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Country</label>
                                <input type="text" name="country" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all @error('country') border-red-500 ring-1 ring-red-500 @enderror" value="{{ old('country', $user->country) }}" placeholder="Nigeria">
                                @error('country')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="flex justify-end mt-8 pt-6 border-t border-slate-100">
                            <button type="submit" :disabled="loading" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl shadow-sm shadow-blue-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <i x-show="!loading" class="bi bi-geo"></i>
                                <span x-text="loading ? 'Saving...' : 'Save Address'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="border-b border-slate-100 px-6 py-5 bg-slate-50/50">
                    <h5 class="font-bold text-slate-800 flex items-center gap-2 text-lg">
                        <i class="bi bi-shield-lock text-orange-500"></i> Change Password
                    </h5>
                </div>
                <div class="p-6">
                    <form action="{{ route('profile.password') }}" method="POST" x-data="{ loading: false, showP1: false, showP2: false, showP3: false }" @submit="loading = true">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Current Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="showP1 ? 'text' : 'password'" name="current_password" class="w-full px-4 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all @error('current_password') border-red-500 ring-1 ring-red-500 @enderror" required>
                                    <button type="button" @click="showP1 = !showP1" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <i class="bi" :class="showP1 ? 'bi-eye-slash' : 'bi-eye'"></i>
                                    </button>
                                </div>
                                @error('current_password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">New Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="showP2 ? 'text' : 'password'" name="password" class="w-full px-4 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all @error('password') border-red-500 ring-1 ring-red-500 @enderror" minlength="8" required>
                                    <button type="button" @click="showP2 = !showP2" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <i class="bi" :class="showP2 ? 'bi-eye-slash' : 'bi-eye'"></i>
                                    </button>
                                </div>
                                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                                <p class="text-slate-400 text-xs mt-1">Minimum 8 characters</p>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Confirm New Password <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input :type="showP3 ? 'text' : 'password'" name="password_confirmation" class="w-full px-4 py-2.5 pr-10 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all" minlength="8" required>
                                    <button type="button" @click="showP3 = !showP3" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600">
                                        <i class="bi" :class="showP3 ? 'bi-eye-slash' : 'bi-eye'"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-8 pt-6 border-t border-slate-100">
                            <button type="submit" :disabled="loading" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-xl shadow-sm shadow-orange-500/30 transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" style="display: none;">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <i x-show="!loading" class="bi bi-key"></i>
                                <span x-text="loading ? 'Updating...' : 'Update Password'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
