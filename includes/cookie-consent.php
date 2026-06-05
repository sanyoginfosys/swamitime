<?php
$cookie_consent = $_COOKIE['cookie_consent'] ?? null;
if (!$cookie_consent):
?>

<style>
.cookie-consent-banner {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 9999;
    background: #004E53;
    padding: 20px 0;
    box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(100%);
}
.cookie-icon i {
    font-size: 28px;
    color: #fff;
}
.cookie-consent-banner .btn-light {
    font-weight: 600;
    font-size: 0.85rem;
    padding: 8px 20px;
    border-radius: 50px;
    color: #004E53;
}
.cookie-consent-banner .btn-outline-light {
    font-weight: 600;
    font-size: 0.85rem;
    padding: 8px 20px;
    border-radius: 50px;
}
.cookie-consent-banner .btn-link {
    font-weight: 500;
    font-size: 0.85rem;
}
.cookie-preference-item {
    background: #fff;
}
.cookie-preference-item .form-check-input:checked {
    background-color: #078E91;
    border-color: #078E91;
}
.cookie-preference-item .form-check-input:disabled {
    opacity: 0.7;
}
@media (max-width: 991px) {
    .cookie-consent-banner {
        padding: 15px 0;
    }
}
</style>

<div id="cookie-consent-banner" class="cookie-consent-banner" style="display: none;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 mb-3 mb-lg-0">
                <div class="d-flex align-items-center">
                    <div class="cookie-icon me-3">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <div>
                        <h5 class="text-white mb-1">Cookie Preferences</h5>
                        <p class="text-white-50 mb-0 small">We use cookies to improve your experience.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 mb-3 mb-lg-0">
                <p class="text-white small mb-0">
                    We use essential cookies to make our site work. With your consent, we may also use analytics and functional cookies to improve your experience. 
                    <a href="/cookie-policy" class="text-white text-decoration-underline">Learn more</a>
                </p>
            </div>
            <div class="col-lg-3 text-lg-end">
                <button class="btn btn-light btn-sm me-2 mb-2 mb-lg-0" onclick="acceptAllCookies()">Accept All</button>
                <button class="btn btn-outline-light btn-sm me-2 mb-2 mb-lg-0" onclick="rejectAllCookies()">Reject All</button>
                <button class="btn btn-link btn-sm text-white p-0" onclick="openCookiePreferences()">Manage</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="cookiePreferencesModal" tabindex="-1" aria-labelledby="cookiePreferencesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="cookiePreferencesLabel">Cookie Preferences</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Manage your cookie preferences below. Essential cookies cannot be disabled as they are required for the website to function properly.</p>
                
                <div class="cookie-preference-item d-flex justify-content-between align-items-start p-3 border rounded mb-2">
                    <div>
                        <strong>Essential Cookies</strong>
                        <p class="text-muted mb-0 small">Required for the website to function. These cannot be disabled.</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" checked disabled>
                    </div>
                </div>
                
                <div class="cookie-preference-item d-flex justify-content-between align-items-start p-3 border rounded mb-2">
                    <div>
                        <strong>Analytics Cookies</strong>
                        <p class="text-muted mb-0 small">Help us understand how visitors interact with our website by collecting anonymous information.</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input cookie-pref-toggle" type="checkbox" id="pref-analytics" data-category="analytics">
                    </div>
                </div>
                
                <div class="cookie-preference-item d-flex justify-content-between align-items-start p-3 border rounded mb-2">
                    <div>
                        <strong>Functional Cookies</strong>
                        <p class="text-muted mb-0 small">Enable enhanced functionality and personalisation, such as remembering your preferences.</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input cookie-pref-toggle" type="checkbox" id="pref-functional" data-category="functional">
                    </div>
                </div>
                
                <div class="cookie-preference-item d-flex justify-content-between align-items-start p-3 border rounded">
                    <div>
                        <strong>Marketing Cookies</strong>
                        <p class="text-muted mb-0 small">Used to deliver relevant advertisements and track marketing campaign performance.</p>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input cookie-pref-toggle" type="checkbox" id="pref-marketing" data-category="marketing">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="rejectAllCookies()">Reject All</button>
                <button type="button" class="btn btn-teal" onclick="saveCookiePreferences()">Save Preferences</button>
            </div>
        </div>
    </div>
</div>

<script>
function setCookie(name, value, days) {
    var expires = '';
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = '; expires=' + date.toUTCString();
    }
    document.cookie = name + '=' + encodeURIComponent(JSON.stringify(value)) + expires + '; path=/; SameSite=Lax';
}

function getCookie(name) {
    var nameEQ = name + '=';
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i].trim();
        if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
}

function acceptAllCookies() {
    var consent = {
        essential: true,
        analytics: true,
        functional: true,
        marketing: true,
        timestamp: new Date().toISOString()
    };
    setCookie('cookie_consent', consent, 365);
    hideBanner();
    window.location.reload();
}

function rejectAllCookies() {
    var consent = {
        essential: true,
        analytics: false,
        functional: false,
        marketing: false,
        timestamp: new Date().toISOString()
    };
    setCookie('cookie_consent', consent, 365);
    hideBanner();
}

function saveCookiePreferences() {
    var consent = {
        essential: true,
        analytics: document.getElementById('pref-analytics').checked,
        functional: document.getElementById('pref-functional').checked,
        marketing: document.getElementById('pref-marketing').checked,
        timestamp: new Date().toISOString()
    };
    setCookie('cookie_consent', consent, 365);
    hideBanner();
    var modal = bootstrap.Modal.getInstance(document.getElementById('cookiePreferencesModal'));
    if (modal) modal.hide();
}

function openCookiePreferences() {
    var existing = getCookie('cookie_consent');
    if (existing) {
        try {
            var prefs = JSON.parse(decodeURIComponent(existing));
            document.getElementById('pref-analytics').checked = prefs.analytics || false;
            document.getElementById('pref-functional').checked = prefs.functional || false;
            document.getElementById('pref-marketing').checked = prefs.marketing || false;
        } catch(e) {}
    }
    var modal = new bootstrap.Modal(document.getElementById('cookiePreferencesModal'));
    modal.show();
}

function hideBanner() {
    var banner = document.getElementById('cookie-consent-banner');
    if (banner) {
        banner.style.opacity = '0';
        banner.style.transform = 'translateY(100%)';
        setTimeout(function() {
            banner.style.display = 'none';
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var consent = getCookie('cookie_consent');
    if (!consent) {
        var banner = document.getElementById('cookie-consent-banner');
        if (banner) {
            banner.style.display = 'block';
            setTimeout(function() {
                banner.style.opacity = '1';
                banner.style.transform = 'translateY(0)';
            }, 100);
        }
    }
});
</script>
<?php endif; ?>
