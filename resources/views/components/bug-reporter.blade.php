<div x-data="bugReporter()" class="fixed bottom-6 right-6 z-[9999]">
    <button type="button"
        @click="open = true"
        class="w-14 h-14 rounded-full bg-red-600 text-white shadow-lg flex items-center justify-center hover:bg-red-700 transition-colors"
        aria-label="Report a bug">
        <span class="text-xl">🐛</span>
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 flex items-center justify-center z-[9999]">
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="close()"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg mx-4 p-6"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2">
            <div class="flex items-start justify-between mb-4">
                <h2 class="text-lg font-bold text-secondary">Report a bug</h2>
                <button type="button" @click="close()" class="text-gray-400 hover:text-gray-600">&times;</button>
            </div>

            <form @submit.prevent="submit()" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-secondary mb-1">Title</label>
                    <input type="text" x-model="form.title" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-1">Module</label>
                        <select x-model="form.module" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Select module</option>
                            <option>Front Desk</option>
                            <option>Rooms</option>
                            <option>Billing</option>
                            <option>Guests</option>
                            <option>Housekeeping</option>
                            <option>Reports</option>
                            <option>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-secondary mb-1">Severity</label>
                        <select x-model="form.severity" required
                            class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-secondary mb-1">Details</label>
                    <textarea x-model="form.details" rows="4" required
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-secondary mb-1">Your name</label>
                    <input type="text" x-model="form.reported_by"
                        class="w-full px-3 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>

                <div class="flex items-center justify-between pt-2">
                    <p x-show="success" class="text-sm text-green-600 font-semibold">Bug reported! ✓</p>
                    <p x-show="error" class="text-sm text-red-600 font-semibold" x-text="error"></p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" @click="close()"
                        class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit" :disabled="loading"
                        class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-xl hover:bg-red-700 disabled:opacity-60">
                        <span x-show="!loading">Submit</span>
                        <span x-show="loading">Submitting...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function bugReporter() {
    return {
        open: false,
        loading: false,
        success: false,
        error: '',
        form: {
            title: '',
            module: '',
            severity: 'medium',
            details: '',
            reported_by: '',
            page_url: window.location.href
        },
        close() {
            this.open = false;
            this.error = '';
        },
        reset() {
            this.form.title = '';
            this.form.module = '';
            this.form.severity = 'medium';
            this.form.details = '';
            this.form.reported_by = '';
            this.form.page_url = window.location.href;
        },
        async submit() {
            this.loading = true;
            this.error = '';
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const response = await fetch('/bug-reports', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.form)
                });

                if (!response.ok) {
                    const data = await response.json().catch(() => ({}));
                    throw new Error(data.message || 'Unable to submit bug report.');
                }

                this.success = true;
                this.reset();
                setTimeout(() => {
                    this.success = false;
                    this.close();
                }, 2000);
            } catch (err) {
                this.error = err.message || 'Something went wrong.';
            } finally {
                this.loading = false;
            }
        }
    };
}
</script>
