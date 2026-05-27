{{-- resources/views/reservations/create.blade.php --}}
@extends('layouts.app')

@section('title', __('reservations.create_reservation'))
@section('page-title', __('reservations.reservations'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <!-- Header -->
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('reservations.create_reservation') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('reservations.create_subtitle') }}</p>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('reservations.store') }}" enctype="multipart/form-data" class="p-6" x-data="reservationForm()">
            @csrf

            <div class="space-y-6">
                <!-- Guest Selection Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        {{ __('reservations.sections.guest_information') }}
                    </h3>

                    <!-- Guest Type Toggle -->
                    <div class="flex gap-4 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="guest_type" value="existing" x-model="guestType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('reservations.guest_type.existing') }}</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="guest_type" value="new" x-model="guestType" class="text-primary focus:ring-primary">
                            <span class="text-sm font-semibold text-secondary">{{ __('reservations.guest_type.new') }}</span>
                        </label>
                    </div>

                    <!-- Existing Guest Selection -->
                    <div x-show="guestType === 'existing'" x-transition class="space-y-4">
                        <input type="hidden" name="create_new_guest" x-bind:value="guestType === 'new' ? '1' : '0'">
                        
                        <div>
                            <label for="guest_id" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.guest_id') }} <span class="text-red-500">*</span>
                            </label>
                            <select 
                                name="guest_id" 
                                id="guest_id"
                                x-model="selectedGuestId"
                                @change="updateSelectedGuest()"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id') border-red-500 @enderror">
                                <option value="">{{ __('reservations.placeholders.select_guest') }}</option>
                                @foreach($guests as $guest)
                                <option value="{{ $guest->id }}" 
                                    data-name="{{ $guest->full_name }}"
                                    data-phone="{{ $guest->phone_number }}"
                                    data-email="{{ $guest->email }}"
                                    {{ (old('guest_id') == $guest->id || (isset($selectedGuest) && $selectedGuest && $selectedGuest->id == $guest->id)) ? 'selected' : '' }}>
                                    {{ $guest->full_name }} - {{ $guest->phone_number }}
                                </option>
                                @endforeach
                            </select>
                            @error('guest_id')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Selected Guest Preview -->
                        <div x-show="selectedGuestId" x-transition class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-primary to-blue-600 rounded-xl flex items-center justify-center text-white font-bold">
                                    <span x-text="getGuestInitials()"></span>
                                </div>
                                <div>
                                    <p class="font-bold text-secondary" x-text="selectedGuestName"></p>
                                    <p class="text-sm text-gray-600"><span x-text="selectedGuestPhone"></span> • <span x-text="selectedGuestEmail || '{{ __('reservations.labels.no_email') }}'"></span></p>
                                </div>
                                <a :href="'/guests/' + selectedGuestId" target="_blank" class="ml-auto text-sm text-primary hover:underline font-medium">
                                    {{ __('reservations.labels.view_profile') }} →
                                </a>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span>{{ __('reservations.info.cant_find_guest') }}</span>
                            <button type="button" @click="guestType = 'new'" class="text-primary hover:underline font-semibold">
                                {{ __('reservations.info.create_new_guest') }}
                            </button>
                            <span>{{ __('reservations.info.or') }}</span>
                            <a href="{{ route('guests.create', ['return_to_reservation' => 1]) }}" class="text-primary hover:underline font-semibold">
                                {{ __('reservations.info.add_full_profile') }}
                            </a>
                        </div>
                    </div>

                    <!-- New Guest Form -->
                    <div x-show="guestType === 'new'" x-transition class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- First Name -->
                            <div>
                                <label for="guest_first_name" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.guest_first_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_first_name" 
                                    id="guest_first_name"
                                    value="{{ old('guest_first_name') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_first_name') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.enter_first_name') }}"
                                    x-bind:required="guestType === 'new'">
                                @error('guest_first_name')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="guest_last_name" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.guest_last_name') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_last_name" 
                                    id="guest_last_name"
                                    value="{{ old('guest_last_name') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_last_name') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.enter_last_name') }}"
                                    x-bind:required="guestType === 'new'">
                                @error('guest_last_name')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="guest_email" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.email') }} <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    name="guest_email" 
                                    id="guest_email"
                                    value="{{ old('guest_email') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_email') border-red-500 @enderror"
                                    placeholder="{{ __('reservations.placeholders.email') }}"
                                    x-bind:required="guestType === 'new'">
                                @error('guest_email')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- ID Type -->
                            <div>
                                <label for="guest_id_type" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.id_type') }} <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="guest_id_type" 
                                    id="guest_id_type"
                                    x-model="idType"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id_type') border-red-500 @enderror"
                                    x-bind:required="guestType === 'new'">
                                    <option value="">{{ __('reservations.placeholders.select_id_type') }}</option>
                                    <option value="passport" {{ old('guest_id_type') === 'passport' ? 'selected' : '' }}>{{ __('reservations.id_types.passport') }}</option>
                                    <option value="driver_license" {{ old('guest_id_type') === 'driver_license' ? 'selected' : '' }}>{{ __('reservations.id_types.driver_license') }}</option>
                                    <option value="nida" {{ old('guest_id_type') === 'nida' ? 'selected' : '' }}>{{ __('reservations.id_types.nida') }}</option>
                                </select>
                                @error('guest_id_type')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- ID Number (shown after selecting ID type) -->
                            <div x-show="idType" x-transition>
                                <label for="guest_id_number" class="block text-sm font-semibold text-secondary mb-2">
                                    <span x-text="idTypeLabel"></span> <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    name="guest_id_number" 
                                    id="guest_id_number"
                                    value="{{ old('guest_id_number') }}" 
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_id_number') border-red-500 @enderror"
                                    x-bind:placeholder="idTypePlaceholder"
                                    x-bind:required="guestType === 'new' && idType">
                                @error('guest_id_number')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Nationality -->
                            <div>
                                <label for="guest_nationality" class="block text-sm font-semibold text-secondary mb-2">
                                    {{ __('reservations.fields.nationality') }} <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="guest_nationality" 
                                    id="guest_nationality"
                                    x-model="nationality"
                                    @change="updateCountryCode()"
                                    class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_nationality') border-red-500 @enderror"
                                    x-bind:required="guestType === 'new'">
                                    <option value="">{{ __('reservations.placeholders.select_nationality') }}</option>
                                    <optgroup label="{{ __('reservations.nationality_groups.east_africa') }}">
                                        <option value="Tanzanian" {{ old('guest_nationality') === 'Tanzanian' ? 'selected' : '' }}>Tanzanian</option>
                                        <option value="Kenyan" {{ old('guest_nationality') === 'Kenyan' ? 'selected' : '' }}>Kenyan</option>
                                        <option value="Ugandan" {{ old('guest_nationality') === 'Ugandan' ? 'selected' : '' }}>Ugandan</option>
                                        <option value="Rwandan" {{ old('guest_nationality') === 'Rwandan' ? 'selected' : '' }}>Rwandan</option>
                                        <option value="Burundian" {{ old('guest_nationality') === 'Burundian' ? 'selected' : '' }}>Burundian</option>
                                        <option value="South Sudanese" {{ old('guest_nationality') === 'South Sudanese' ? 'selected' : '' }}>South Sudanese</option>
                                        <option value="Congolese (DRC)" {{ old('guest_nationality') === 'Congolese (DRC)' ? 'selected' : '' }}>Congolese (DRC)</option>
                                        <option value="Ethiopian" {{ old('guest_nationality') === 'Ethiopian' ? 'selected' : '' }}>Ethiopian</option>
                                        <option value="Somali" {{ old('guest_nationality') === 'Somali' ? 'selected' : '' }}>Somali</option>
                                        <option value="Eritrean" {{ old('guest_nationality') === 'Eritrean' ? 'selected' : '' }}>Eritrean</option>
                                        <option value="Djiboutian" {{ old('guest_nationality') === 'Djiboutian' ? 'selected' : '' }}>Djiboutian</option>
                                        <option value="Malawian" {{ old('guest_nationality') === 'Malawian' ? 'selected' : '' }}>Malawian</option>
                                        <option value="Zambian" {{ old('guest_nationality') === 'Zambian' ? 'selected' : '' }}>Zambian</option>
                                        <option value="Zimbabwean" {{ old('guest_nationality') === 'Zimbabwean' ? 'selected' : '' }}>Zimbabwean</option>
                                        <option value="Mozambican" {{ old('guest_nationality') === 'Mozambican' ? 'selected' : '' }}>Mozambican</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.southern_africa') }}">
                                        <option value="South African" {{ old('guest_nationality') === 'South African' ? 'selected' : '' }}>South African</option>
                                        <option value="Namibian" {{ old('guest_nationality') === 'Namibian' ? 'selected' : '' }}>Namibian</option>
                                        <option value="Botswanan" {{ old('guest_nationality') === 'Botswanan' ? 'selected' : '' }}>Botswanan</option>
                                        <option value="Angolan" {{ old('guest_nationality') === 'Angolan' ? 'selected' : '' }}>Angolan</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.west_africa') }}">
                                        <option value="Nigerian" {{ old('guest_nationality') === 'Nigerian' ? 'selected' : '' }}>Nigerian</option>
                                        <option value="Ghanaian" {{ old('guest_nationality') === 'Ghanaian' ? 'selected' : '' }}>Ghanaian</option>
                                        <option value="Senegalese" {{ old('guest_nationality') === 'Senegalese' ? 'selected' : '' }}>Senegalese</option>
                                        <option value="Ivorian" {{ old('guest_nationality') === 'Ivorian' ? 'selected' : '' }}>Ivorian</option>
                                        <option value="Cameroonian" {{ old('guest_nationality') === 'Cameroonian' ? 'selected' : '' }}>Cameroonian</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.north_africa') }}">
                                        <option value="Egyptian" {{ old('guest_nationality') === 'Egyptian' ? 'selected' : '' }}>Egyptian</option>
                                        <option value="Moroccan" {{ old('guest_nationality') === 'Moroccan' ? 'selected' : '' }}>Moroccan</option>
                                        <option value="Tunisian" {{ old('guest_nationality') === 'Tunisian' ? 'selected' : '' }}>Tunisian</option>
                                        <option value="Algerian" {{ old('guest_nationality') === 'Algerian' ? 'selected' : '' }}>Algerian</option>
                                        <option value="Libyan" {{ old('guest_nationality') === 'Libyan' ? 'selected' : '' }}>Libyan</option>
                                        <option value="Sudanese" {{ old('guest_nationality') === 'Sudanese' ? 'selected' : '' }}>Sudanese</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.europe') }}">
                                        <option value="British" {{ old('guest_nationality') === 'British' ? 'selected' : '' }}>British</option>
                                        <option value="German" {{ old('guest_nationality') === 'German' ? 'selected' : '' }}>German</option>
                                        <option value="French" {{ old('guest_nationality') === 'French' ? 'selected' : '' }}>French</option>
                                        <option value="Italian" {{ old('guest_nationality') === 'Italian' ? 'selected' : '' }}>Italian</option>
                                        <option value="Spanish" {{ old('guest_nationality') === 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                        <option value="Dutch" {{ old('guest_nationality') === 'Dutch' ? 'selected' : '' }}>Dutch</option>
                                        <option value="Belgian" {{ old('guest_nationality') === 'Belgian' ? 'selected' : '' }}>Belgian</option>
                                        <option value="Swiss" {{ old('guest_nationality') === 'Swiss' ? 'selected' : '' }}>Swiss</option>
                                        <option value="Swedish" {{ old('guest_nationality') === 'Swedish' ? 'selected' : '' }}>Swedish</option>
                                        <option value="Norwegian" {{ old('guest_nationality') === 'Norwegian' ? 'selected' : '' }}>Norwegian</option>
                                        <option value="Danish" {{ old('guest_nationality') === 'Danish' ? 'selected' : '' }}>Danish</option>
                                        <option value="Finnish" {{ old('guest_nationality') === 'Finnish' ? 'selected' : '' }}>Finnish</option>
                                        <option value="Polish" {{ old('guest_nationality') === 'Polish' ? 'selected' : '' }}>Polish</option>
                                        <option value="Portuguese" {{ old('guest_nationality') === 'Portuguese' ? 'selected' : '' }}>Portuguese</option>
                                        <option value="Greek" {{ old('guest_nationality') === 'Greek' ? 'selected' : '' }}>Greek</option>
                                        <option value="Austrian" {{ old('guest_nationality') === 'Austrian' ? 'selected' : '' }}>Austrian</option>
                                        <option value="Irish" {{ old('guest_nationality') === 'Irish' ? 'selected' : '' }}>Irish</option>
                                        <option value="Russian" {{ old('guest_nationality') === 'Russian' ? 'selected' : '' }}>Russian</option>
                                        <option value="Ukrainian" {{ old('guest_nationality') === 'Ukrainian' ? 'selected' : '' }}>Ukrainian</option>
                                        <option value="Turkish" {{ old('guest_nationality') === 'Turkish' ? 'selected' : '' }}>Turkish</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.americas') }}">
                                        <option value="American" {{ old('guest_nationality') === 'American' ? 'selected' : '' }}>American</option>
                                        <option value="Canadian" {{ old('guest_nationality') === 'Canadian' ? 'selected' : '' }}>Canadian</option>
                                        <option value="Mexican" {{ old('guest_nationality') === 'Mexican' ? 'selected' : '' }}>Mexican</option>
                                        <option value="Brazilian" {{ old('guest_nationality') === 'Brazilian' ? 'selected' : '' }}>Brazilian</option>
                                        <option value="Argentine" {{ old('guest_nationality') === 'Argentine' ? 'selected' : '' }}>Argentine</option>
                                        <option value="Colombian" {{ old('guest_nationality') === 'Colombian' ? 'selected' : '' }}>Colombian</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.asia_oceania') }}">
                                        <option value="Chinese" {{ old('guest_nationality') === 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                        <option value="Japanese" {{ old('guest_nationality') === 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                        <option value="South Korean" {{ old('guest_nationality') === 'South Korean' ? 'selected' : '' }}>South Korean</option>
                                        <option value="Indian" {{ old('guest_nationality') === 'Indian' ? 'selected' : '' }}>Indian</option>
                                        <option value="Pakistani" {{ old('guest_nationality') === 'Pakistani' ? 'selected' : '' }}>Pakistani</option>
                                        <option value="Bangladeshi" {{ old('guest_nationality') === 'Bangladeshi' ? 'selected' : '' }}>Bangladeshi</option>
                                        <option value="Filipino" {{ old('guest_nationality') === 'Filipino' ? 'selected' : '' }}>Filipino</option>
                                        <option value="Indonesian" {{ old('guest_nationality') === 'Indonesian' ? 'selected' : '' }}>Indonesian</option>
                                        <option value="Malaysian" {{ old('guest_nationality') === 'Malaysian' ? 'selected' : '' }}>Malaysian</option>
                                        <option value="Singaporean" {{ old('guest_nationality') === 'Singaporean' ? 'selected' : '' }}>Singaporean</option>
                                        <option value="Thai" {{ old('guest_nationality') === 'Thai' ? 'selected' : '' }}>Thai</option>
                                        <option value="Vietnamese" {{ old('guest_nationality') === 'Vietnamese' ? 'selected' : '' }}>Vietnamese</option>
                                        <option value="Australian" {{ old('guest_nationality') === 'Australian' ? 'selected' : '' }}>Australian</option>
                                        <option value="New Zealander" {{ old('guest_nationality') === 'New Zealander' ? 'selected' : '' }}>New Zealander</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.middle_east') }}">
                                        <option value="Emirati" {{ old('guest_nationality') === 'Emirati' ? 'selected' : '' }}>Emirati</option>
                                        <option value="Saudi" {{ old('guest_nationality') === 'Saudi' ? 'selected' : '' }}>Saudi</option>
                                        <option value="Qatari" {{ old('guest_nationality') === 'Qatari' ? 'selected' : '' }}>Qatari</option>
                                        <option value="Omani" {{ old('guest_nationality') === 'Omani' ? 'selected' : '' }}>Omani</option>
                                        <option value="Kuwaiti" {{ old('guest_nationality') === 'Kuwaiti' ? 'selected' : '' }}>Kuwaiti</option>
                                        <option value="Bahraini" {{ old('guest_nationality') === 'Bahraini' ? 'selected' : '' }}>Bahraini</option>
                                        <option value="Israeli" {{ old('guest_nationality') === 'Israeli' ? 'selected' : '' }}>Israeli</option>
                                        <option value="Lebanese" {{ old('guest_nationality') === 'Lebanese' ? 'selected' : '' }}>Lebanese</option>
                                        <option value="Jordanian" {{ old('guest_nationality') === 'Jordanian' ? 'selected' : '' }}>Jordanian</option>
                                    </optgroup>
                                    <optgroup label="{{ __('reservations.nationality_groups.other') }}">
                                        <option value="Other" {{ old('guest_nationality') === 'Other' ? 'selected' : '' }}>Other</option>
                                    </optgroup>
                                </select>
                                @error('guest_nationality')
                                    <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Phone with Country Code -->
                        <div class="mt-4">
                            <label for="guest_phone" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.phone_number') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <div class="w-28">
                                    <input 
                                        type="text" 
                                        name="guest_phone_code" 
                                        id="guest_phone_code"
                                        x-model="phoneCode"
                                        readonly
                                        class="w-full px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-center text-sm font-semibold text-gray-600">
                                </div>
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        name="guest_phone" 
                                        id="guest_phone"
                                        value="{{ old('guest_phone') }}" 
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_phone') border-red-500 @enderror"
                                        placeholder="{{ __('reservations.placeholders.phone_number') }}"
                                        x-bind:required="guestType === 'new'">
                                </div>
                            </div>
                            @error('guest_phone')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="mt-4">
                            <label for="guest_address" class="block text-sm font-semibold text-secondary mb-2">
                                Address <span class="text-red-500">*</span>
                            </label>
                            <textarea name="guest_address" id="guest_address" rows="2"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('guest_address') border-red-500 @enderror"
                                placeholder="Enter address" x-bind:required="guestType === 'new'">{{ old('guest_address') }}</textarea>
                            @error('guest_address')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- ID Photo Upload -->
                        <div class="mt-4" x-show="idType" x-transition>
                            <label class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.id_photo') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="guest_id_photo" accept="image/*" capture="environment"
                                x-ref="idPhotoInput" class="hidden"
                                @change="handleIdPhotoUpload">
                            <div class="flex items-center gap-3">
                                <button type="button"
                                    @click="$refs.idPhotoInput.click()"
                                    class="px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:border-primary hover:text-primary transition-all flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span x-text="idPhotoFile ? '{{ __('reservations.labels.change_photo') }}' : '{{ __('reservations.labels.upload_id_photo') }}'"></span>
                                </button>
                                <span x-show="idPhotoFile" class="text-sm text-green-600 font-semibold" x-text="idPhotoFile"></span>
                                <button type="button" x-show="idPhotoFile" @click="$refs.idPhotoInput.value = ''; idPhotoFile = null; idPhotoPreview = null" class="text-xs text-red-500 hover:text-red-700 font-semibold">Remove</button>
                            </div>
                            <div x-show="idPhotoPreview" class="mt-3">
                                <img :src="idPhotoPreview" class="w-48 h-32 object-contain rounded-xl border border-gray-200">
                            </div>
                            @error('guest_id_photo')
                                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="bg-gradient-to-br from-yellow-50 to-amber-50 border border-yellow-200 rounded-xl p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <p class="text-sm text-yellow-800">
                                    {{ __('reservations.messages.new_guest_created') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                <!-- Reservation Details Section -->
                <div>
                    <h3 class="text-lg font-bold text-secondary mb-4 flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-primary/10 to-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        {{ __('reservations.sections.reservation_details') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Check-In Date -->
                        <div>
                            <label for="check_in_date" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.check_in_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="check_in_date" 
                                id="check_in_date"
                                value="{{ old('check_in_date') }}" 
                                min="{{ date('Y-m-d') }}"
                                x-model="checkInDate"
                                @input="calculateNights(); searchRooms()"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('check_in_date') border-red-500 @enderror"
                                required>
                            @error('check_in_date')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Check-Out Date -->
                        <div>
                            <label for="check_out_date" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.check_out_date') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="date" 
                                name="check_out_date" 
                                id="check_out_date"
                                value="{{ old('check_out_date') }}" 
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                x-model="checkOutDate"
                                @input="calculateNights(); searchRooms()"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('check_out_date') border-red-500 @enderror"
                                required>
                            @error('check_out_date')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Number of Guests -->
                        <div>
                            <label for="number_of_guests" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.number_of_guests') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="number_of_guests" 
                                id="number_of_guests"
                                value="{{ old('number_of_guests', 1) }}" 
                                min="1"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('number_of_guests') border-red-500 @enderror"
                                required>
                            @error('number_of_guests')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Estimated Amount -->
                        <div>
                            <label for="estimated_amount" class="block text-sm font-semibold text-secondary mb-2">
                                {{ __('reservations.fields.estimated_amount') }} <span class="text-red-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="estimated_amount" 
                                id="estimated_amount"
                                value="{{ old('estimated_amount', 0) }}" 
                                step="0.01"
                                min="0"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('estimated_amount') border-red-500 @enderror"
                                placeholder="{{ __('reservations.placeholders.amount') }}"
                                required>
                            @error('estimated_amount')
                                <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <!-- Room Assignment -->
                    <div class="mt-6">
                        <label for="room_id" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('reservations.fields.assigned_room') }} <span class="text-red-500">*</span>
                            <span class="text-xs text-gray-500 ml-2" x-show="!checkInDate || !checkOutDate">{{ __('reservations.form.select_dates_hint') }}</span>
                        </label>

                        <template x-if="!checkInDate || !checkOutDate">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-center">
                                <p class="text-sm text-yellow-800">{{ __('reservations.form.search_rooms_hint') }}</p>
                            </div>
                        </template>

                        <template x-if="checkInDate && checkOutDate">
                            <div>
                                <div x-show="roomsLoading" class="flex items-center justify-center py-4">
                                    <svg class="animate-spin h-5 w-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('reservations.messages.loading_rooms') }}</span>
                                </div>
                                <div x-show="!roomsLoading && availableRooms.length > 0">
                                    <select 
                                        name="room_id" 
                                        id="room_id"
                                        x-model="selectedRoom"
                                        @change="updateEstimatedAmount()"
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('room_id') border-red-500 @enderror"
                                        required>
                                        <option value="">{{ __('reservations.placeholders.select_room') }}</option>
                                        <template x-for="room in availableRooms" :key="room.id">
                                            <option :value="room.id" :data-rate="room.room_type.price_per_night ?? 0" x-text="room.room_number + ' - ' + (room.room_type?.name || 'N/A') + ' (' + (room.room_type?.formatted_rate || '') + '/night)'"></option>
                                        </template>
                                    </select>
                                </div>
                                <div x-show="!roomsLoading && availableRooms.length === 0 && checkInDate && checkOutDate" class="bg-red-50 border border-red-200 rounded-xl p-4 text-center">
                                    <p class="text-sm text-red-700">{{ __('reservations.messages.no_available_rooms') }}</p>
                                </div>
                            </div>
                        </template>
                        @error('room_id')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Estimated Amount Preview -->
                    <div x-show="nights > 0 && selectedRoom" x-transition class="mt-4">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-green-800">{{ __('reservations.labels.estimated_total') }}</p>
                                    <p class="text-xs text-green-600 mt-1">
                                        <span x-text="nights"></span> {{ __('reservations.labels.nights') }} × <span x-text="formatCurrency(pricePerNight)"></span>/{{ __('reservations.labels.night') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-2xl font-extrabold text-green-700"><span x-text="formatCurrency(calculatedAmount)"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mt-6">
                        <label for="status" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('reservations.fields.status') }} <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('status') border-red-500 @enderror"
                            required>
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>{{ __('reservations.status.pending') }}</option>
                            <option value="confirmed" {{ old('status') == 'confirmed' ? 'selected' : '' }}>{{ __('reservations.status.confirmed') }}</option>
                        </select>
                        @error('status')
                            <p class="mt-1.5 text-sm text-red-600 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Info Box -->
                <div class="bg-gradient-to-br from-primary/5 to-blue-50 border border-primary/20 rounded-xl p-4">
                    <div class="flex gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary/10 to-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="text-sm">
                            <p class="font-bold text-secondary mb-1">{{ __('reservations.info.reservation_tips') }}</p>
                            <ul class="list-disc list-inside space-y-1 text-gray-600">
                                <li>{{ __('reservations.info.tip_select_guest') }}</li>
                                <li>{{ __('reservations.info.tip_room_assignment') }}</li>
                                <li>{{ __('reservations.info.tip_amount') }}</li>
                                <li>{{ __('reservations.info.tip_auto_number') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('reservations.index') }}" 
                   class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all">
                    {{ __('reservations.actions.cancel') }}
                </a>
                <button 
                    type="submit"
                    class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all">
                    {{ __('reservations.actions.create') }}
                </button>
            </div>

        </form>
    </div>
</div>
</div>

<script>
const countryCodes = {
    'Tanzanian': '+255', 'Kenyan': '+254', 'Ugandan': '+256', 'Rwandan': '+250', 'Burundian': '+257',
    'South Sudanese': '+211', 'Congolese (DRC)': '+243', 'Ethiopian': '+251', 'Somali': '+252',
    'Eritrean': '+291', 'Djiboutian': '+253', 'Malawian': '+265', 'Zambian': '+260', 'Zimbabwean': '+263',
    'Mozambican': '+258', 'South African': '+27', 'Namibian': '+264', 'Botswanan': '+267', 'Angolan': '+244',
    'Nigerian': '+234', 'Ghanaian': '+233', 'Senegalese': '+221', 'Ivorian': '+225', 'Cameroonian': '+237',
    'Egyptian': '+20', 'Moroccan': '+212', 'Tunisian': '+216', 'Algerian': '+213', 'Libyan': '+218',
    'Sudanese': '+249', 'British': '+44', 'German': '+49', 'French': '+33', 'Italian': '+39',
    'Spanish': '+34', 'Dutch': '+31', 'Belgian': '+32', 'Swiss': '+41', 'Swedish': '+46',
    'Norwegian': '+47', 'Danish': '+45', 'Finnish': '+358', 'Polish': '+48', 'Portuguese': '+351',
    'Greek': '+30', 'Austrian': '+43', 'Irish': '+353', 'Russian': '+7', 'Ukrainian': '+380',
    'Turkish': '+90', 'American': '+1', 'Canadian': '+1', 'Mexican': '+52', 'Brazilian': '+55',
    'Argentine': '+54', 'Colombian': '+57', 'Chinese': '+86', 'Japanese': '+81', 'South Korean': '+82',
    'Indian': '+91', 'Pakistani': '+92', 'Bangladeshi': '+880', 'Filipino': '+63', 'Indonesian': '+62',
    'Malaysian': '+60', 'Singaporean': '+65', 'Thai': '+66', 'Vietnamese': '+84',
    'Australian': '+61', 'New Zealander': '+64',
    'Emirati': '+971', 'Saudi': '+966', 'Qatari': '+974', 'Omani': '+968',
    'Kuwaiti': '+965', 'Bahraini': '+973', 'Israeli': '+972', 'Lebanese': '+961',
    'Jordanian': '+962', 'Other': ''
};

const currencySymbol = '{{ config("app.currency_symbol", "$") }}' || '$';
const currencyPosition = '{{ config("app.currency_position", "before") }}' || 'before';
const currencyDecimals = {{ config("app.currency_decimals", 2) }};

function formatMoney(amount) {
    const formatted = Math.abs(amount).toFixed(currencyDecimals);
    if (currencyPosition === 'before') return currencySymbol + formatted;
    return formatted + ' ' + currencySymbol;
}

function reservationForm() {
    return {
        guestType: '{{ isset($selectedGuest) && $selectedGuest ? "existing" : (old("create_new_guest") == "1" ? "new" : "existing") }}',
        selectedGuestId: '{{ isset($selectedGuest) ? $selectedGuest->id : old("guest_id", "") }}',
        selectedGuestName: '{{ isset($selectedGuest) ? $selectedGuest->full_name : "" }}',
        selectedGuestPhone: '{{ isset($selectedGuest) ? $selectedGuest->phone_number : "" }}',
        selectedGuestEmail: '{{ isset($selectedGuest) ? $selectedGuest->email : "" }}',
        idType: '{{ old("guest_id_type", "") }}',
        nationality: '{{ old("guest_nationality", "") }}',
        phoneCode: '',
        showIdPhotoModal: false,
        idPhotoFile: null,
        idPhotoPreview: null,
        selectedRoom: '{{ old("room_id", "") }}',
        pricePerNight: 0,
        calculatedAmount: 0,
        nights: 0,
        checkInDate: '{{ old("check_in_date", "") }}',
        checkOutDate: '{{ old("check_out_date", "") }}',
        availableRooms: [],
        roomsLoading: false,
        searchDebounce: null,

        init() {
            this.updateCountryCode();
            if (this.selectedRoom) this.updateEstimatedAmount();
            if (this.checkInDate && this.checkOutDate) this.searchRooms();
        },

        get idTypeLabel() {
            return { passport: 'Passport', driver_license: 'Driver License', nida: 'NIDA' }[this.idType] || 'ID Number';
        },

        get idTypePlaceholder() {
            return {
                passport: 'Enter passport number',
                driver_license: 'Enter license number',
                nida: 'Enter NIDA number'
            }[this.idType] || 'Enter ID number';
        },

        formatCurrency(amount) { return formatMoney(amount); },

        updateCountryCode() { this.phoneCode = countryCodes[this.nationality] || ''; },

        handleIdPhotoUpload() {
            const file = this.$refs.idPhotoInput.files[0];
            if (file) {
                this.idPhotoFile = file.name;
                const reader = new FileReader();
                reader.onload = (e) => { this.idPhotoPreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        },

        updateSelectedGuest() {
            const select = document.getElementById('guest_id');
            const option = select.options[select.selectedIndex];
            this.selectedGuestName = option?.getAttribute('data-name') || '';
            this.selectedGuestPhone = option?.getAttribute('data-phone') || '';
            this.selectedGuestEmail = option?.getAttribute('data-email') || '';
        },

        getGuestInitials() {
            if (!this.selectedGuestName) return '';
            return this.selectedGuestName.split(' ').map(n => n.charAt(0).toUpperCase()).slice(0, 2).join('');
        },

        updateEstimatedAmount() {
            if (!this.selectedRoom) { this.pricePerNight = 0; return this.calculateNights(); }
            const room = this.availableRooms.find(r => r.id == this.selectedRoom);
            this.pricePerNight = room?.room_type?.price_per_night ? parseFloat(room.room_type.price_per_night) : 0;
            this.calculateNights();
        },

        searchRooms() {
            if (this.searchDebounce) clearTimeout(this.searchDebounce);
            this.searchDebounce = setTimeout(() => {
                if (!this.checkInDate || !this.checkOutDate || new Date(this.checkOutDate) <= new Date(this.checkInDate)) {
                    this.availableRooms = []; this.selectedRoom = ''; return;
                }
                this.roomsLoading = true; this.selectedRoom = ''; this.pricePerNight = 0;
                fetch('{{ route("bookings.available-rooms") }}?check_in=' + this.checkInDate + '&check_out=' + this.checkOutDate)
                    .then(r => r.json())
                    .then(data => { this.availableRooms = data.rooms || []; this.roomsLoading = false; })
                    .catch(() => { this.availableRooms = []; this.roomsLoading = false; });
            }, 500);
        },

        calculateNights() {
            if (this.checkInDate && this.checkOutDate && new Date(this.checkOutDate) > new Date(this.checkInDate)) {
                this.nights = Math.round((new Date(this.checkOutDate) - new Date(this.checkInDate)) / (1000 * 60 * 60 * 24));
            } else { this.nights = 0; }
            this.calculatedAmount = this.nights * this.pricePerNight;
            document.getElementById('estimated_amount').value = this.calculatedAmount;
        }
    };
}
</script>
@endsection
