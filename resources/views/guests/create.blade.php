{{-- resources/views/guests/create.blade.php --}}
@extends('layouts.app')

@section('title', __('guests.add_guest'))
@section('page-title', __('guests.title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100">
        <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-white rounded-t-2xl">
            <h2 class="text-xl font-extrabold text-secondary">{{ __('guests.add_new_guest') }}</h2>
            <p class="text-sm text-gray-500 mt-1">{{ __('guests.create_subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('guests.store') }}" enctype="multipart/form-data" class="p-6" x-data="guestForm()">
            @csrf

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="first_name" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('guests.fields.first_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('first_name') border-red-500 @enderror"
                            placeholder="{{ __('guests.forms.first_name_placeholder') }}" required>
                        @error('first_name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('guests.fields.last_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('last_name') border-red-500 @enderror"
                            placeholder="{{ __('guests.forms.last_name_placeholder') }}" required>
                        @error('last_name')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-semibold text-secondary mb-2">
                            {{ __('guests.fields.email_address') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('email') border-red-500 @enderror"
                            placeholder="{{ __('guests.forms.email_placeholder') }}" required>
                        @error('email')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="id_type" class="block text-sm font-semibold text-secondary mb-2">
                            ID Type <span class="text-red-500">*</span>
                        </label>
                        <select name="id_type" id="id_type" x-model="idType"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('id_type') border-red-500 @enderror" required>
                            <option value="">Select ID type</option>
                            <option value="passport" {{ old('id_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                            <option value="driver_license" {{ old('id_type') === 'driver_license' ? 'selected' : '' }}>Driver License</option>
                            <option value="nida" {{ old('id_type') === 'nida' ? 'selected' : '' }}>NIDA</option>
                        </select>
                        @error('id_type')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="idType" x-transition>
                        <label for="id_number" class="block text-sm font-semibold text-secondary mb-2">
                            <span x-text="idTypeLabel"></span> <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('id_number') border-red-500 @enderror"
                            x-bind:placeholder="idTypePlaceholder" x-bind:required="idType">
                        @error('id_number')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="nationality" class="block text-sm font-semibold text-secondary mb-2">
                            Nationality <span class="text-red-500">*</span>
                        </label>
                        <select name="nationality" id="nationality" x-model="nationality" @change="updateCountryCode()"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('nationality') border-red-500 @enderror" required>
                            <option value="">Select nationality</option>
                            <optgroup label="East Africa">
                                <option value="Tanzanian" {{ old('nationality') === 'Tanzanian' ? 'selected' : '' }}>Tanzanian</option>
                                <option value="Kenyan" {{ old('nationality') === 'Kenyan' ? 'selected' : '' }}>Kenyan</option>
                                <option value="Ugandan" {{ old('nationality') === 'Ugandan' ? 'selected' : '' }}>Ugandan</option>
                                <option value="Rwandan" {{ old('nationality') === 'Rwandan' ? 'selected' : '' }}>Rwandan</option>
                                <option value="Burundian" {{ old('nationality') === 'Burundian' ? 'selected' : '' }}>Burundian</option>
                                <option value="South Sudanese" {{ old('nationality') === 'South Sudanese' ? 'selected' : '' }}>South Sudanese</option>
                                <option value="Congolese (DRC)" {{ old('nationality') === 'Congolese (DRC)' ? 'selected' : '' }}>Congolese (DRC)</option>
                                <option value="Ethiopian" {{ old('nationality') === 'Ethiopian' ? 'selected' : '' }}>Ethiopian</option>
                                <option value="Somali" {{ old('nationality') === 'Somali' ? 'selected' : '' }}>Somali</option>
                                <option value="Malawian" {{ old('nationality') === 'Malawian' ? 'selected' : '' }}>Malawian</option>
                                <option value="Zambian" {{ old('nationality') === 'Zambian' ? 'selected' : '' }}>Zambian</option>
                                <option value="Zimbabwean" {{ old('nationality') === 'Zimbabwean' ? 'selected' : '' }}>Zimbabwean</option>
                                <option value="Mozambican" {{ old('nationality') === 'Mozambican' ? 'selected' : '' }}>Mozambican</option>
                            </optgroup>
                            <optgroup label="Southern Africa">
                                <option value="South African" {{ old('nationality') === 'South African' ? 'selected' : '' }}>South African</option>
                                <option value="Namibian" {{ old('nationality') === 'Namibian' ? 'selected' : '' }}>Namibian</option>
                                <option value="Botswanan" {{ old('nationality') === 'Botswanan' ? 'selected' : '' }}>Botswanan</option>
                                <option value="Angolan" {{ old('nationality') === 'Angolan' ? 'selected' : '' }}>Angolan</option>
                            </optgroup>
                            <optgroup label="West Africa">
                                <option value="Nigerian" {{ old('nationality') === 'Nigerian' ? 'selected' : '' }}>Nigerian</option>
                                <option value="Ghanaian" {{ old('nationality') === 'Ghanaian' ? 'selected' : '' }}>Ghanaian</option>
                                <option value="Senegalese" {{ old('nationality') === 'Senegalese' ? 'selected' : '' }}>Senegalese</option>
                                <option value="Ivorian" {{ old('nationality') === 'Ivorian' ? 'selected' : '' }}>Ivorian</option>
                                <option value="Cameroonian" {{ old('nationality') === 'Cameroonian' ? 'selected' : '' }}>Cameroonian</option>
                            </optgroup>
                            <optgroup label="North Africa">
                                <option value="Egyptian" {{ old('nationality') === 'Egyptian' ? 'selected' : '' }}>Egyptian</option>
                                <option value="Moroccan" {{ old('nationality') === 'Moroccan' ? 'selected' : '' }}>Moroccan</option>
                                <option value="Tunisian" {{ old('nationality') === 'Tunisian' ? 'selected' : '' }}>Tunisian</option>
                                <option value="Algerian" {{ old('nationality') === 'Algerian' ? 'selected' : '' }}>Algerian</option>
                                <option value="Sudanese" {{ old('nationality') === 'Sudanese' ? 'selected' : '' }}>Sudanese</option>
                            </optgroup>
                            <optgroup label="Europe">
                                <option value="British" {{ old('nationality') === 'British' ? 'selected' : '' }}>British</option>
                                <option value="German" {{ old('nationality') === 'German' ? 'selected' : '' }}>German</option>
                                <option value="French" {{ old('nationality') === 'French' ? 'selected' : '' }}>French</option>
                                <option value="Italian" {{ old('nationality') === 'Italian' ? 'selected' : '' }}>Italian</option>
                                <option value="Spanish" {{ old('nationality') === 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                <option value="Dutch" {{ old('nationality') === 'Dutch' ? 'selected' : '' }}>Dutch</option>
                                <option value="Swiss" {{ old('nationality') === 'Swiss' ? 'selected' : '' }}>Swiss</option>
                                <option value="Swedish" {{ old('nationality') === 'Swedish' ? 'selected' : '' }}>Swedish</option>
                                <option value="Norwegian" {{ old('nationality') === 'Norwegian' ? 'selected' : '' }}>Norwegian</option>
                                <option value="Danish" {{ old('nationality') === 'Danish' ? 'selected' : '' }}>Danish</option>
                                <option value="Finnish" {{ old('nationality') === 'Finnish' ? 'selected' : '' }}>Finnish</option>
                                <option value="Polish" {{ old('nationality') === 'Polish' ? 'selected' : '' }}>Polish</option>
                                <option value="Russian" {{ old('nationality') === 'Russian' ? 'selected' : '' }}>Russian</option>
                                <option value="Ukrainian" {{ old('nationality') === 'Ukrainian' ? 'selected' : '' }}>Ukrainian</option>
                                <option value="Turkish" {{ old('nationality') === 'Turkish' ? 'selected' : '' }}>Turkish</option>
                            </optgroup>
                            <optgroup label="Americas">
                                <option value="American" {{ old('nationality') === 'American' ? 'selected' : '' }}>American</option>
                                <option value="Canadian" {{ old('nationality') === 'Canadian' ? 'selected' : '' }}>Canadian</option>
                                <option value="Mexican" {{ old('nationality') === 'Mexican' ? 'selected' : '' }}>Mexican</option>
                                <option value="Brazilian" {{ old('nationality') === 'Brazilian' ? 'selected' : '' }}>Brazilian</option>
                                <option value="Argentine" {{ old('nationality') === 'Argentine' ? 'selected' : '' }}>Argentine</option>
                            </optgroup>
                            <optgroup label="Asia & Oceania">
                                <option value="Chinese" {{ old('nationality') === 'Chinese' ? 'selected' : '' }}>Chinese</option>
                                <option value="Japanese" {{ old('nationality') === 'Japanese' ? 'selected' : '' }}>Japanese</option>
                                <option value="South Korean" {{ old('nationality') === 'South Korean' ? 'selected' : '' }}>South Korean</option>
                                <option value="Indian" {{ old('nationality') === 'Indian' ? 'selected' : '' }}>Indian</option>
                                <option value="Pakistani" {{ old('nationality') === 'Pakistani' ? 'selected' : '' }}>Pakistani</option>
                                <option value="Filipino" {{ old('nationality') === 'Filipino' ? 'selected' : '' }}>Filipino</option>
                                <option value="Australian" {{ old('nationality') === 'Australian' ? 'selected' : '' }}>Australian</option>
                                <option value="New Zealander" {{ old('nationality') === 'New Zealander' ? 'selected' : '' }}>New Zealander</option>
                            </optgroup>
                            <optgroup label="Middle East">
                                <option value="Emirati" {{ old('nationality') === 'Emirati' ? 'selected' : '' }}>Emirati</option>
                                <option value="Saudi" {{ old('nationality') === 'Saudi' ? 'selected' : '' }}>Saudi</option>
                                <option value="Qatari" {{ old('nationality') === 'Qatari' ? 'selected' : '' }}>Qatari</option>
                                <option value="Omani" {{ old('nationality') === 'Omani' ? 'selected' : '' }}>Omani</option>
                                <option value="Kuwaiti" {{ old('nationality') === 'Kuwaiti' ? 'selected' : '' }}>Kuwaiti</option>
                                <option value="Lebanese" {{ old('nationality') === 'Lebanese' ? 'selected' : '' }}>Lebanese</option>
                                <option value="Jordanian" {{ old('nationality') === 'Jordanian' ? 'selected' : '' }}>Jordanian</option>
                            </optgroup>
                            <optgroup label="Other">
                                <option value="Other" {{ old('nationality') === 'Other' ? 'selected' : '' }}>Other</option>
                            </optgroup>
                        </select>
                        @error('nationality')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="phone_number" class="block text-sm font-semibold text-secondary mb-2">
                        {{ __('guests.fields.phone') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <div class="w-28">
                            <input type="text" id="phone_code" x-model="phoneCode" readonly
                                class="w-full px-3 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-center text-sm font-semibold text-gray-600">
                        </div>
                        <div class="flex-1">
                            <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('phone_number') border-red-500 @enderror"
                                placeholder="{{ __('guests.forms.phone_placeholder') }}" required>
                        </div>
                    </div>
                    @error('phone_number')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div x-show="idType" x-transition>
                    <label class="block text-sm font-semibold text-secondary mb-2">
                        ID Photo <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="photo" accept="image/*" capture="environment"
                        x-ref="idPhotoInput" class="hidden"
                        @change="handleIdPhotoUpload">
                    <div class="flex items-center gap-3">
                        <button type="button"
                            @click="$refs.idPhotoInput.click()"
                            class="px-4 py-2.5 border-2 border-dashed border-gray-300 rounded-xl text-sm font-semibold text-gray-600 hover:border-primary hover:text-primary transition-all flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span x-text="idPhotoFile ? 'Change photo' : 'Upload ID photo'"></span>
                        </button>
                        <span x-show="idPhotoFile" class="text-sm text-green-600 font-semibold" x-text="idPhotoFile"></span>
                        <button type="button" x-show="idPhotoFile" @click="$refs.idPhotoInput.value = ''; idPhotoFile = null; idPhotoPreview = null" class="text-xs text-red-500 hover:text-red-700 font-semibold">Remove</button>
                    </div>
                    <div x-show="idPhotoPreview" class="mt-3">
                        <img :src="idPhotoPreview" class="w-48 h-32 object-contain rounded-xl border border-gray-200">
                    </div>
                    @error('photo')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-semibold text-secondary mb-2">
                        {{ __('guests.fields.address') }} <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" id="address" rows="2" required
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary focus:border-primary transition-all @error('address') border-red-500 @enderror"
                        placeholder="Enter address">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('guests.index') }}" class="px-6 py-2.5 text-sm font-semibold text-secondary bg-white border border-gray-200 rounded-xl hover:bg-gray-50 transition-all">
                    {{ __('guests.actions.cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-semibold text-white bg-gradient-to-r from-primary to-blue-600 rounded-xl hover:shadow-lg transition-all">
                    {{ __('guests.create_guest') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function guestForm() {
    return {
        nationality: '{{ old('nationality') }}',
        phoneCode: '+255',
        idType: '{{ old('id_type') }}',
        idPhotoFile: null,
        idPhotoPreview: null,

        handleIdPhotoUpload() {
            const file = this.$refs.idPhotoInput.files[0];
            if (file) {
                this.idPhotoFile = file.name;
                const reader = new FileReader();
                reader.onload = (e) => { this.idPhotoPreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        },

        get idTypeLabel() {
            const labels = { passport: 'Passport Number', driver_license: 'Driver License Number', nida: 'NIDA Number' };
            return labels[this.idType] || 'ID Number';
        },

        get idTypePlaceholder() {
            const placeholders = { passport: 'Enter passport number', driver_license: 'Enter license number', nida: 'Enter NIDA number' };
            return placeholders[this.idType] || 'Enter ID number';
        },

        updateCountryCode() {
            const codes = {
                'Tanzanian': '+255', 'Kenyan': '+254', 'Ugandan': '+256', 'Rwandan': '+250', 'Burundian': '+257',
                'South Sudanese': '+211', 'Congolese (DRC)': '+243', 'Ethiopian': '+251', 'Somali': '+252',
                'Malawian': '+265', 'Zambian': '+260', 'Zimbabwean': '+263', 'Mozambican': '+258',
                'South African': '+27', 'Namibian': '+264', 'Botswanan': '+267', 'Angolan': '+244',
                'Nigerian': '+234', 'Ghanaian': '+233', 'Senegalese': '+221', 'Ivorian': '+225', 'Cameroonian': '+237',
                'Egyptian': '+20', 'Moroccan': '+212', 'Tunisian': '+216', 'Algerian': '+213', 'Sudanese': '+249',
                'British': '+44', 'German': '+49', 'French': '+33', 'Italian': '+39', 'Spanish': '+34',
                'Dutch': '+31', 'Swiss': '+41', 'Swedish': '+46', 'Norwegian': '+47', 'Danish': '+45',
                'Finnish': '+358', 'Polish': '+48', 'Russian': '+7', 'Ukrainian': '+380', 'Turkish': '+90',
                'American': '+1', 'Canadian': '+1', 'Mexican': '+52', 'Brazilian': '+55', 'Argentine': '+54',
                'Chinese': '+86', 'Japanese': '+81', 'South Korean': '+82', 'Indian': '+91', 'Pakistani': '+92',
                'Filipino': '+63', 'Australian': '+61', 'New Zealander': '+64',
                'Emirati': '+971', 'Saudi': '+966', 'Qatari': '+974', 'Omani': '+968', 'Kuwaiti': '+965',
                'Lebanese': '+961', 'Jordanian': '+962', 'Other': '+255'
            };
            this.phoneCode = codes[this.nationality] || '+255';
        }
    };
}
</script>
@endpush
