<section>
    <header>
        <div class="flex items-center gap-2">
            <span class="p-1.5 rounded-lg bg-cyan-950 text-cyan-400 border border-cyan-800/60">
                <i data-lucide="store" class="w-4 h-4"></i>
            </span>
            <h2 class="text-base font-bold text-white">
                Farm Profile, Social Selling & Philippine Payments
            </h2>
        </div>
        <p class="mt-1 text-xs text-slate-400">
            Configure your farm identity, Facebook Messenger inquiry handle, and GCash/Maya scan-to-pay for sales invoices and public catalog inquiries.
        </p>
    </header>

    @if (session('success'))
        <div class="mt-4 p-3 bg-emerald-950/80 border border-emerald-800/80 rounded-lg text-emerald-300 text-xs flex items-center gap-2">
            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mt-4 p-4 bg-rose-950/90 border border-rose-800 rounded-xl text-rose-200 text-xs space-y-1.5 shadow-lg">
            <div class="font-bold text-rose-300 flex items-center gap-2 text-sm">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-400"></i>
                <span>Please fix the following issues:</span>
            </div>
            <ul class="list-disc list-inside text-xs space-y-0.5 pl-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('profile.payment.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <!-- Farm Identity & Custom Branding -->
        <div class="bg-slate-950/60 p-5 rounded-xl border border-cyan-800/40 space-y-5">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                    <i data-lucide="award" class="w-4 h-4 text-cyan-400"></i>
                    <span>Farm Identity &amp; Custom Logo</span>
                </div>
                <!-- Platform Security Badge -->
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-slate-900 border border-cyan-900/60 text-[10px] font-mono text-cyan-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Powered by AquaForge System</span>
                </span>
            </div>

            <!-- Farm Logo Upload & Preview -->
            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-800 flex flex-col sm:flex-row items-center gap-5"
                 x-data="{ logoPreview: '{{ $user->farm_logo_url }}' }">
                <div class="relative flex-shrink-0">
                    <template x-if="logoPreview">
                        <img :src="logoPreview" alt="{{ $user->farm_name }}" 
                             class="w-20 h-20 rounded-2xl object-contain bg-slate-950 border-2 border-cyan-500/50 shadow-lg shadow-cyan-950 p-1">
                    </template>
                    <template x-if="!logoPreview">
                        <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-500 p-0.5 shadow-lg shadow-cyan-950 flex items-center justify-center">
                            <div class="w-full h-full bg-slate-950 rounded-[14px] flex flex-col items-center justify-center text-cyan-400">
                                <i data-lucide="waves" class="w-8 h-8 text-cyan-400"></i>
                                <span class="text-[8px] font-mono font-bold text-teal-300 uppercase mt-0.5">Default</span>
                            </div>
                        </div>
                    </template>
                </div>

                <div class="flex-1 min-w-0 space-y-2 text-center sm:text-left">
                    <label class="block text-xs font-bold text-white">Upload Custom Farm Crest / Logo</label>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Customize your farm's visual brand across the public welcome page, live stocklist catalog, and printed receipts. Supports PNG, JPG, SVG, WebP, GIF (up to 20MB).
                    </p>
                    <div class="pt-1 flex flex-col sm:flex-row items-center gap-3">
                        <input type="file" name="farm_logo" accept="image/*,.svg"
                               @change="const file = $event.target.files[0]; if (file) { logoPreview = URL.createObjectURL(file); }"
                               class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-950 file:text-cyan-300 hover:file:bg-cyan-900 transition cursor-pointer">
                        @if ($user->farm_logo_path)
                            <label class="inline-flex items-center text-xs text-rose-400 hover:text-rose-300 cursor-pointer">
                                <input type="checkbox" name="remove_farm_logo" value="1" 
                                       @change="if ($event.target.checked) { logoPreview = null; }"
                                       class="rounded bg-slate-950 border-slate-700 text-rose-500 focus:ring-0 mr-1.5">
                                <span>Revert to default AquaForge crest</span>
                            </label>
                        @endif
                    </div>
                    <x-input-error class="mt-1" :messages="$errors->get('farm_logo')" />
                </div>
            </div>

            <!-- Farm Name & Location Fields -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="farm_name" class="block text-xs font-semibold text-slate-300">Farm / Brand Name <span class="text-rose-400">*</span></label>
                    <input type="text" id="farm_name" name="farm_name" value="{{ old('farm_name', $user->farm_name ?? 'AquaForge Farm') }}" required
                           placeholder="e.g. RDLC Guppy & Aquatic Farm"
                           class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3.5 py-2.5 focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <span class="text-[10px] text-slate-500 mt-1 block">Your custom farm brand name displayed on your catalog and invoices.</span>
                    <x-input-error class="mt-1" :messages="$errors->get('farm_name')" />
                </div>
                <div>
                    <label for="farm_location" class="block text-xs font-semibold text-slate-300">Location (City / Province)</label>
                    <input type="text" id="farm_location" name="farm_location" value="{{ old('farm_location', $user->farm_location ?? 'Quezon City, Philippines') }}" 
                           placeholder="e.g. Quezon City, Metro Manila"
                           class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3.5 py-2.5 focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <span class="text-[10px] text-slate-500 mt-1 block">Displayed on your catalog so customers know where fish will ship from.</span>
                    <x-input-error class="mt-1" :messages="$errors->get('farm_location')" />
                </div>
            </div>

            <!-- AquaForge Security & Co-Branding Watermark Notice -->
            <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-800 text-[11px] text-slate-400 flex items-start gap-2.5 leading-relaxed">
                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-400 flex-shrink-0 mt-0.5"></i>
                <div>
                    <strong class="text-slate-200">AquaForge Architecture Security:</strong> You have full control over your personalized Farm Name and Logo. To maintain system authenticity and software licensing, your site remains permanently powered and verified by the <strong>AquaForge System Engine &copy; 2026</strong>.
                </div>
            </div>
        </div>

        <!-- Facebook & Messenger Social Selling -->
        <div class="bg-slate-950/60 p-4 rounded-xl border border-blue-900/40 space-y-4">
            <div class="text-xs font-bold text-blue-400 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                <span>Facebook & Messenger Social Inquiries</span>
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                When buyers browse your available fish on the catalog or see your Facebook posts, they can click <strong>"Inquire via Messenger"</strong> to message you directly with fish details already typed!
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="messenger_username" class="block text-xs font-medium text-slate-300">
                        Messenger Username or Page Handle
                    </label>
                    <div class="mt-1 flex rounded-lg shadow-sm">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-slate-700 bg-slate-800 text-slate-400 text-xs">
                            m.me/
                        </span>
                        <input type="text" id="messenger_username" name="messenger_username" 
                               value="{{ old('messenger_username', $user->messenger_username ?? '') }}" 
                               placeholder="yourfarmpage or username"
                               class="block w-full rounded-r-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    </div>
                    <span class="text-[10px] text-slate-500 mt-1 block">Your Facebook Page username or personal username (without @)</span>
                    <x-input-error class="mt-1" :messages="$errors->get('messenger_username')" />
                </div>
                <div>
                    <label for="contact_number" class="block text-xs font-medium text-slate-300">Contact / Viber Mobile Number</label>
                    <input type="text" id="contact_number" name="contact_number" value="{{ old('contact_number', $user->contact_number ?? '') }}" 
                           placeholder="0917-XXX-XXXX"
                           class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                    <x-input-error class="mt-1" :messages="$errors->get('contact_number')" />
                </div>
            </div>
            <div>
                <label for="facebook_page" class="block text-xs font-medium text-slate-300">Facebook Page URL</label>
                <input type="url" id="facebook_page" name="facebook_page" value="{{ old('facebook_page', $user->facebook_page ?? '') }}" 
                       placeholder="https://facebook.com/yourfarmpage"
                       class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500">
                <x-input-error class="mt-1" :messages="$errors->get('facebook_page')" />
            </div>
        </div>

        <!-- Philippine Payments: GCash & Maya -->
        <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800 space-y-4">
            <div class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="qr-code" class="w-3.5 h-3.5"></i>
                <span>Philippine Payments (GCash & Maya)</span>
            </div>

            <!-- GCash -->
            <div class="p-3 rounded-lg bg-blue-950/30 border border-blue-800/40 space-y-3">
                <div class="flex items-center gap-2 text-xs font-semibold text-blue-300">
                    <span class="w-2 h-2 rounded-full bg-blue-400"></span>
                    <span>GCash Payment Settings</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="gcash_name" class="block text-xs text-slate-300">GCash Account Name</label>
                        <input type="text" id="gcash_name" name="gcash_name" value="{{ old('gcash_name', $user->gcash_name) }}" 
                               placeholder="e.g. JUAN D."
                               class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label for="gcash_number" class="block text-xs text-slate-300">GCash Mobile Number</label>
                        <input type="text" id="gcash_number" name="gcash_number" value="{{ old('gcash_number', $user->gcash_number) }}" 
                               placeholder="0917-XXX-XXXX"
                               class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs text-slate-300 mb-1">GCash QR Screenshot / Image</label>
                    <div class="flex items-center gap-4">
                        @if ($user->gcash_qr_path)
                            <div class="p-1 rounded-lg bg-white border border-slate-600">
                                <img src="{{ asset('storage/' . $user->gcash_qr_path) }}" alt="GCash QR" class="w-16 h-16 object-contain">
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" name="gcash_qr" accept="image/*" class="text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-900/60 file:text-cyan-300 hover:file:bg-cyan-800">
                            <p class="text-[10px] text-slate-500 mt-1">Upload your saved GCash QR code image from the GCash app.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Maya -->
            <div class="p-3 rounded-lg bg-emerald-950/30 border border-emerald-800/40 space-y-3">
                <div class="flex items-center gap-2 text-xs font-semibold text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Maya Payment Settings</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label for="maya_name" class="block text-xs text-slate-300">Maya Account Name</label>
                        <input type="text" id="maya_name" name="maya_name" value="{{ old('maya_name', $user->maya_name) }}" 
                               placeholder="e.g. JUAN DELA CRUZ"
                               class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                    </div>
                    <div>
                        <label for="maya_number" class="block text-xs text-slate-300">Maya Mobile Number</label>
                        <input type="text" id="maya_number" name="maya_number" value="{{ old('maya_number', $user->maya_number) }}" 
                               placeholder="0918-XXX-XXXX"
                               class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                    </div>
                </div>
            </div>

            <!-- Bank Details -->
            <div>
                <label for="bank_details" class="block text-xs text-slate-300">Bank Transfer Details (Optional)</label>
                <textarea id="bank_details" name="bank_details" rows="2" 
                          placeholder="e.g. BDO / BPI / UnionBank Account Number & Account Name"
                          class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">{{ old('bank_details', $user->bank_details) }}</textarea>
            </div>

            <!-- Shipping Notes -->
            <div>
                <label for="shipping_notes" class="block text-xs text-slate-300">Shipping & Delivery Policies</label>
                <textarea id="shipping_notes" name="shipping_notes" rows="2" 
                          placeholder="e.g. Lalamove / Grab Express within Metro Manila. Busway or Cargo for provincial shipping. DOA (Dead On Arrival) policy: Send unboxing video within 1 hour."
                          class="mt-1 block w-full rounded-lg bg-slate-900 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">{{ old('shipping_notes', $user->shipping_notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Farm & Payment Settings</span>
            </button>
        </div>
    </form>
</section>
