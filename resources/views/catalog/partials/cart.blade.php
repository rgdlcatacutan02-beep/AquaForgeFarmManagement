{{-- AquaForge Live Catalog Cart & Facebook Messenger Order Drawer --}}
<!-- FLOATING STICKY CART BAR -->
<div x-show="cartCount > 0" x-cloak
     x-transition:enter="transition ease-out duration-300 transform"
     x-transition:enter-start="translate-y-full opacity-0"
     x-transition:enter-end="translate-y-0 opacity-100"
     x-transition:leave="transition ease-in duration-200 transform"
     x-transition:leave-start="translate-y-0 opacity-100"
     x-transition:leave-end="translate-y-full opacity-0"
     class="fixed bottom-0 inset-x-0 z-40 bg-slate-900/95 backdrop-blur border-t border-emerald-500/40 p-4 shadow-2xl">
    <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-emerald-500/20 border border-emerald-500 flex items-center justify-center text-emerald-400">
                    <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                </div>
                <span class="absolute -top-1 -right-1 px-1.5 py-0.5 rounded-full bg-emerald-500 text-slate-950 font-black text-[10px]" x-text="cartCount"></span>
            </div>
            <div>
                <div class="text-xs text-slate-400">Order Basket (<span x-text="cartCount"></span> items)</div>
                <div class="text-base font-extrabold text-white font-mono">
                    &#8369;<span x-text="cartTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" @click="clearCart()" class="hidden sm:inline-flex px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 text-xs font-semibold transition">
                Clear
            </button>
            <button type="button" @click="cartOpen = true" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition transform hover:-translate-y-0.5">
                <span>Review Order &amp; Total</span>
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>

<!-- SLIDE-OVER ORDER BASKET DRAWER -->
<div x-show="cartOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden">
    <!-- Backdrop -->
    <div x-show="cartOpen" 
         x-transition:enter="transition-opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="cartOpen = false" 
         class="absolute inset-0 bg-slate-950/80 backdrop-blur-sm"></div>

    <!-- Drawer Panel -->
    <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
        <div x-show="cartOpen"
             x-transition:enter="transform transition ease-in-out duration-300"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transform transition ease-in-out duration-200"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="w-screen max-w-md bg-slate-900 border-l border-slate-800 shadow-2xl flex flex-col">
            
            <!-- Header -->
            <div class="p-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/60">
                <div class="flex items-center gap-2">
                    <i data-lucide="shopping-cart" class="w-5 h-5 text-emerald-400"></i>
                    <h3 class="text-sm font-bold text-white">Your Order Basket</h3>
                    <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-950 text-emerald-300 border border-emerald-800 font-mono font-bold" x-text="cartCount"></span>
                </div>
                <button type="button" @click="cartOpen = false" class="p-1 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Cart Body -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <template x-if="cart.length === 0">
                    <div class="text-center py-16 text-slate-500 space-y-3">
                        <i data-lucide="package-open" class="w-12 h-12 mx-auto stroke-1 text-slate-600"></i>
                        <p class="text-xs">Your order basket is currently empty.</p>
                        <p class="text-[11px] text-slate-600">Click &ldquo;+ Add to Order&rdquo; on any available fish to calculate your total and send your order to Facebook Messenger.</p>
                    </div>
                </template>

                <template x-if="cart.length > 0">
                    <div class="space-y-3">
                        <template x-for="item in cart" :key="item.id">
                            <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 flex items-center gap-3">
                                <template x-if="item.photo">
                                    <img :src="item.photo" class="w-12 h-12 rounded-lg object-cover bg-slate-900 border border-slate-800">
                                </template>
                                <template x-if="!item.photo">
                                    <div class="w-12 h-12 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-slate-700">
                                        <i data-lucide="fish" class="w-5 h-5"></i>
                                    </div>
                                </template>

                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold text-white truncate" x-text="item.title"></div>
                                    <div class="text-[10px] text-slate-400 font-mono">
                                        <span x-text="item.code"></span> &bull; <span x-text="item.grade"></span> &bull; <span x-text="item.sex"></span>
                                    </div>
                                    <div class="text-xs font-mono text-emerald-400 font-bold mt-0.5">
                                        &#8369;<span x-text="item.price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                        <span class="text-[10px] text-slate-500 font-normal">ea</span>
                                    </div>
                                </div>

                                <!-- Qty controls -->
                                <div class="flex flex-col items-end gap-1.5">
                                    <div class="flex items-center border border-slate-800 rounded-lg overflow-hidden bg-slate-900">
                                        <button type="button" @click="updateQty(item.id, -1)" class="px-2 py-1 text-slate-400 hover:text-white hover:bg-slate-800 text-xs font-bold">-</button>
                                        <span class="px-2 py-1 text-xs font-mono text-white" x-text="item.qty || 1"></span>
                                        <button type="button" @click="updateQty(item.id, 1)" class="px-2 py-1 text-slate-400 hover:text-white hover:bg-slate-800 text-xs font-bold">+</button>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[11px] font-mono text-white font-bold">
                                            &#8369;<span x-text="((item.price) * (item.qty || 1)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span>
                                        </span>
                                        <button type="button" @click="removeFromCart(item.id)" class="text-slate-500 hover:text-rose-400 text-xs transition">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Footer / Calculation & Checkout to Messenger -->
            <template x-if="cart.length > 0">
                <div class="p-4 border-t border-slate-800 bg-slate-950/80 space-y-3">
                    <div class="flex items-center justify-between text-xs text-slate-400">
                        <span>Items Subtotal (<span x-text="cartCount"></span> items)</span>
                        <span class="font-mono text-white font-bold">&#8369;<span x-text="cartTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-white font-bold border-t border-slate-800 pt-2">
                        <span>Estimated Total</span>
                        <span class="font-mono text-emerald-400 text-base font-extrabold">&#8369;<span x-text="cartTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></span></span>
                    </div>

                    <!-- Buyer Details Form for Pre-filling Messenger Message -->
                    <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Your Name</label>
                            <input type="text" x-model="buyerName" placeholder="e.g. Juan Dela Cruz" 
                                   class="w-full rounded-lg bg-slate-950 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-medium mb-1">Delivery City / Location</label>
                            <input type="text" x-model="buyerLocation" placeholder="e.g. Quezon City / Bulacan" 
                                   class="w-full rounded-lg bg-slate-950 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-medium mb-1">Preferred Courier / Shipping</label>
                        <select x-model="buyerCourier" class="w-full rounded-lg bg-slate-950 border border-slate-700 text-white text-xs px-3 py-2 focus:ring-1 focus:ring-cyan-500">
                            <option value="Lalamove / Grab Express (Same-Day Metro Manila)">Lalamove / Grab Express (Same-Day Metro Manila)</option>
                            <option value="Bus Terminal-to-Terminal Cargo (Provincial)">Bus Terminal-to-Terminal Cargo (Provincial)</option>
                            <option value="Local Farm Pickup / Meetup">Local Farm Pickup / Meetup</option>
                        </select>
                    </div>

                    <!-- IMPORTANT AWAIT CONFIRMATION NOTICE -->
                    <div class="p-3 rounded-lg bg-amber-950/60 border border-amber-800/80 text-amber-300 text-[11px] flex items-start gap-2 leading-relaxed">
                        <i data-lucide="info" class="w-4 h-4 flex-shrink-0 mt-0.5 text-amber-400"></i>
                        <div>
                            <strong>Order Process:</strong> Clicking below sends your complete order directly to our Facebook Messenger chat. <strong>Please wait for our confirmation reply and shipping fee before sending any payment via GCash/Maya.</strong>
                        </div>
                    </div>

                    <button type="button" 
                            @click="sendToMessenger('{{ $owner->messenger_username ?? 'AquaForgePH' }}')" 
                            class="w-full flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm shadow-xl shadow-blue-900/30 transition transform hover:-translate-y-0.5">
                        <i data-lucide="message-circle" class="w-5 h-5"></i>
                        <span>Send Order to Facebook Messenger &amp; Await Reply</span>
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>


<!-- BUYER CARE & ACCLIMATION GUIDE MODAL -->
<div x-show="guideModalOpen" x-cloak 
     class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 bg-slate-950/85 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95">
    <div @click.away="guideModalOpen = false" 
         class="relative bg-slate-900 border border-slate-800 rounded-2xl max-w-2xl w-full max-h-[90vh] shadow-2xl flex flex-col overflow-hidden">
        
        <!-- Modal Header -->
        <div class="px-5 py-4 border-b border-slate-800 bg-slate-950/80 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-cyan-950/80 border border-cyan-800/80 flex items-center justify-center text-cyan-400 flex-shrink-0">
                    <i data-lucide="book-open" class="w-4 h-4"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        Buyer Care &amp; Acclimation Guide
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-emerald-950 text-emerald-400 border border-emerald-800">Best Practices</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Step-by-step instructions to acclimate your new live specimens safely.</p>
                </div>
            </div>
            <button @click="guideModalOpen = false" 
                    class="p-1.5 text-slate-400 hover:text-white rounded-lg bg-slate-800/80 border border-slate-700/80 transition focus:outline-none"
                    title="Close">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body (Scrollable with Touch Support) -->
        <div class="p-5 overflow-y-auto space-y-4 text-xs text-slate-300 touch-scroll overscroll-contain flex-1">
            
            <!-- Step 1: Temperature Equalization -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
                <div class="flex items-center gap-2 text-cyan-400 font-bold text-xs uppercase tracking-wider">
                    <span class="w-5 h-5 rounded-full bg-cyan-950 border border-cyan-700 text-cyan-300 flex items-center justify-center font-mono text-[10px]">1</span>
                    <span>Float the Sealed Bag (15 &ndash; 20 Mins)</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed pl-7">
                    Float the unopened transport bag directly in your receiving or quarantine tank. This equalizes the transport bag water temperature with your tank water without exposing the fish to sudden thermal shock.
                </p>
            </div>

            <!-- Step 2: Drip Acclimation / Chemistry Adjustment -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
                <div class="flex items-center gap-2 text-emerald-400 font-bold text-xs uppercase tracking-wider">
                    <span class="w-5 h-5 rounded-full bg-emerald-950 border border-emerald-700 text-emerald-300 flex items-center justify-center font-mono text-[10px]">2</span>
                    <span>Water Chemistry Equalization (30 &ndash; 45 Mins)</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed pl-7">
                    Cut the top of the bag open and roll the rim down to float, or transfer the fish and bag water into a clean container. Slowly add small amounts of your tank water (1/4 cup every 5 minutes, or a slow airline drip of 2–3 drops per second) until the volume has doubled. This matches pH, hardness, and TDS gradually.
                </p>
            </div>

            <!-- Step 3: Gentle Transfer -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
                <div class="flex items-center gap-2 text-amber-400 font-bold text-xs uppercase tracking-wider">
                    <span class="w-5 h-5 rounded-full bg-amber-950 border border-amber-700 text-amber-300 flex items-center justify-center font-mono text-[10px]">3</span>
                    <span>Net the Fish &bull; Never Pour Bag Water</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed pl-7">
                    Gently capture the fish with a soft net or specimen cup and release only the fish into your tank. <strong class="text-amber-300">Never pour transport bag water into your main aquarium</strong>, as it contains ammonia, waste, and shipping stress hormones. Discard the bag water down the drain.
                </p>
            </div>

            <!-- Step 4: Lights & Feeding -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800/80 space-y-2">
                <div class="flex items-center gap-2 text-indigo-400 font-bold text-xs uppercase tracking-wider">
                    <span class="w-5 h-5 rounded-full bg-indigo-950 border border-indigo-700 text-indigo-300 flex items-center justify-center font-mono text-[10px]">4</span>
                    <span>Lights Off &amp; First 24 Hours</span>
                </div>
                <p class="text-[11px] text-slate-300 leading-relaxed pl-7">
                    Keep tank lights dimmed or turned off for the first 12–24 hours to reduce travel anxiety and let the specimen explore quietly. Avoid feeding on the first day; start with small pinches of live baby brine shrimp (artemia) or high-grade flake/pellet food the following day once settled.
                </p>
            </div>

            <!-- Recommended Parameters & Guarantee -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                <div class="p-3 rounded-xl bg-cyan-950/30 border border-cyan-800/50 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-cyan-400 flex items-center gap-1.5">
                        <i data-lucide="droplet" class="w-3.5 h-3.5"></i>
                        Recommended Water Parameters
                    </span>
                    <ul class="text-[10px] text-slate-300 space-y-0.5 font-mono">
                        <li>&bull; Temperature: 25&deg;C &ndash; 28&deg;C (77&deg;F &ndash; 82&deg;F)</li>
                        <li>&bull; pH Range: 6.5 &ndash; 7.5 (Neutral / Stable)</li>
                        <li>&bull; Ammonia &amp; Nitrite: 0 ppm (Cycled Filter)</li>
                    </ul>
                </div>

                <div class="p-3 rounded-xl bg-emerald-950/30 border border-emerald-800/50 space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                        Live Arrival Guarantee (DOA Policy)
                    </span>
                    <p class="text-[10px] text-slate-300 leading-normal">
                        All our fish are guaranteed alive on arrival. In the rare event of transit mortality, please take a clear unboxing photo/video of the unopened bag within 1 hour of delivery and message us directly on Messenger for replacement or refund.
                    </p>
                </div>
            </div>

        </div>

        <!-- Modal Footer -->
        <div class="px-5 py-3.5 border-t border-slate-800 bg-slate-950/90 flex items-center justify-between flex-shrink-0">
            <span class="text-[10px] text-slate-500 font-mono">AquaForge Quality Standard &bull; Happy Fishkeeping!</span>
            <button type="button" @click="guideModalOpen = false" 
                    class="px-4 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white font-bold text-xs shadow-md shadow-cyan-950 transition">
                Got It, Close Guide
            </button>
        </div>

    </div>
</div>

<!-- CART SCRIPT -->
<script>
    function catalogCart() {
        return {
            copiedToast: false,
            toastMessage: '',
            fbModalOpen: false,
            guideModalOpen: false,

            init() {
                this.$watch('guideModalOpen', val => {
                    if (val) setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                });
                this.$watch('cartOpen', val => {
                    if (val) setTimeout(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); }, 50);
                });
            },

            cartOpen: false,
            buyerName: '',
            buyerLocation: '',
            buyerCourier: 'Lalamove / Grab Express (Same-Day Metro Manila)',
            buyerNotes: '',
            cart: JSON.parse(localStorage.getItem('aquaforge_cart') || '[]'),

            showToast(msg) {
                this.toastMessage = msg;
                this.copiedToast = true;
                setTimeout(() => this.copiedToast = false, 3000);
            },

            copyText(txt) {
                navigator.clipboard.writeText(txt).then(() => {
                    this.showToast('Copied to clipboard!');
                });
            },

            addToCart(item) {
                const existing = this.cart.find(i => i.id === item.id);
                if (existing) {
                    existing.qty = (existing.qty || 1) + 1;
                } else {
                    this.cart.push({
                        id: item.id,
                        code: item.code,
                        title: item.title,
                        price: parseFloat(item.price) || 0,
                        photo: item.photo,
                        grade: item.grade || 'Standard',
                        sex: item.sex || 'Solo',
                        qty: 1
                    });
                }
                this.saveCart();
                this.showToast('Added ' + item.title + ' to your order basket!');
                this.cartOpen = true;
                setTimeout(() => {
                    if (window.lucide) window.lucide.createIcons();
                }, 50);
            },

            removeFromCart(id) {
                this.cart = this.cart.filter(i => i.id !== id);
                this.saveCart();
            },

            updateQty(id, delta) {
                const item = this.cart.find(i => i.id === id);
                if (!item) return;
                item.qty = Math.max(1, (item.qty || 1) + delta);
                this.saveCart();
            },

            clearCart() {
                this.cart = [];
                this.saveCart();
            },

            saveCart() {
                localStorage.setItem('aquaforge_cart', JSON.stringify(this.cart));
            },

            get cartTotal() {
                return this.cart.reduce((sum, i) => sum + (i.price * (i.qty || 1)), 0);
            },

            get cartCount() {
                return this.cart.reduce((sum, i) => sum + (i.qty || 1), 0);
            },

            sendToMessenger(messengerHandle) {
                if (this.cart.length === 0) return;

                const farmName = '{{ addslashes($owner->farm_name ?? "AquaForge Farm") }}';
                let msg = '\ud83d\udc1f Hello ' + farmName + '! I would like to place an order from your live catalog:\n\n';
                msg += '\ud83d\udce6 ORDER ITEMS:\n';
                this.cart.forEach((item, idx) => {
                    const lineTotal = item.price * (item.qty || 1);
                    msg += `${idx + 1}. ${item.title} (${item.code}) [${item.grade}, ${item.sex}] - \u20b1${item.price.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} \u00d7 ${item.qty || 1} = \u20b1${lineTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}\n`;
                });

                msg += `\n\ud83d\udcb0 ESTIMATED TOTAL: \u20b1${this.cartTotal.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}\n`;
                if (this.buyerName && this.buyerName.trim()) {
                    msg += `\ud83d\udc64 Customer Name: ${this.buyerName.trim()}\n`;
                }
                if (this.buyerLocation && this.buyerLocation.trim()) {
                    msg += `\ud83d\udccd Delivery Location: ${this.buyerLocation.trim()}\n`;
                }
                msg += `\ud83d\ude9a Preferred Shipping: ${this.buyerCourier}\n`;

                msg += '\n\u26a0\ufe0f I will wait for your reply to confirm if all items are still available and for the final shipping quotation before I proceed with payment (GCash/Maya). Thank you!';

                const cleanHandle = (messengerHandle || 'AquaForgePH').replace(/^@/, '').replace(/^https?:\/\/m\.me\//, '');
                const url = 'https://m.me/' + cleanHandle + '?text=' + encodeURIComponent(msg);
                window.open(url, '_blank');
            }
        }
    }
</script>
