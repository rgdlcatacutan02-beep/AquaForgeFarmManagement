import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';
import { Html5Qrcode } from 'html5-qrcode';

window.Alpine = Alpine;
window.Chart = Chart;
window.Html5Qrcode = Html5Qrcode;
window.createIcons = () => createIcons({ icons });

// Register PWA Service Worker
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js').catch(err => {
            console.log('AquaForge SW registration failed:', err);
        });
    });
}

// Global Tank QR Scanner Component
window.tankScanner = function() {
    return {
        scannerModalOpen: false,
        scanning: false,
        html5QrCode: null,
        errorMessage: '',
        scannedCode: '',
        successMessage: '',
        cameras: [],
        selectedCamera: '',

        init() {
            this.$watch('scannerModalOpen', (value) => {
                if (value) {
                    this.$nextTick(() => {
                        this.startScanner();
                    });
                } else {
                    this.stopScanner();
                }
            });
        },

        async startScanner(elementId = 'qr-reader') {
            this.errorMessage = '';
            this.successMessage = '';
            this.scannedCode = '';

            try {
                if (!this.html5QrCode) {
                    this.html5QrCode = new Html5Qrcode(elementId);
                }

                const config = {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                };

                const qrCodeSuccessCallback = (decodedText) => {
                    this.onScanSuccess(decodedText);
                };

                const qrCodeErrorCallback = () => {
                    // ignore normal frame-by-frame non-detection
                };

                // Use environment (back) camera by default on phones
                await this.html5QrCode.start(
                    { facingMode: 'environment' },
                    config,
                    qrCodeSuccessCallback,
                    qrCodeErrorCallback
                );

                this.scanning = true;
            } catch (err) {
                console.error('Camera QR start error:', err);
                this.errorMessage = 'Unable to access camera. Please allow camera permissions or enter Tank Code manually.';
                this.scanning = false;
            }
        },

        async stopScanner() {
            if (this.html5QrCode && this.scanning) {
                try {
                    await this.html5QrCode.stop();
                } catch (e) {
                    console.error('Stop scanner error:', e);
                }
                this.scanning = false;
            }
        },

        onScanSuccess(decodedText) {
            this.scannedCode = decodedText;
            this.successMessage = 'Tank QR recognized! Redirecting...';

            if (navigator.vibrate) {
                navigator.vibrate(100);
            }

            this.stopScanner();

            setTimeout(() => {
                // If full URL
                if (decodedText.startsWith('http://') || decodedText.startsWith('https://')) {
                    window.location.href = decodedText;
                    return;
                }

                // If path
                if (decodedText.startsWith('/tanks/')) {
                    window.location.href = decodedText;
                    return;
                }

                // If numeric ID or tank code like T001
                window.location.href = '/tanks/lookup/' + encodeURIComponent(decodedText.trim());
            }, 600);
        }
    };
};

document.addEventListener('DOMContentLoaded', () => {
    window.createIcons();
});

Alpine.start();
