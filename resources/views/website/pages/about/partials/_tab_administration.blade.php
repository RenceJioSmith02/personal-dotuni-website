{{-- resources/views/website/about/partials/_tab_administration.blade.php --}}
{{-- Replace the cards below with your actual administrators --}}

<div class="about-content-wrapper">
    <div class="content-section">
        <div class="divider"></div>
        <h2 class="section-title">Administration</h2>

        <div class="admin-grid">

            <div class="admin-card">
                <div class="admin-avatar">
                    <img src="{{ asset('assets/system_images/admin/placeholder.jpg') }}" alt="Administrator" 
                         onerror="this.src='https://ui-avatars.com/api/?name=Administrator&background=038303&color=fff&size=120'">
                </div>
                <div class="admin-info">
                    <h4 class="admin-name">Dr. Juan Dela Cruz</h4>
                    <span class="admin-role">University President</span>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-avatar">
                    <img src="{{ asset('assets/system_images/admin/placeholder.jpg') }}" alt="Administrator"
                         onerror="this.src='https://ui-avatars.com/api/?name=DOT-Uni+Dean&background=038303&color=fff&size=120'">
                </div>
                <div class="admin-info">
                    <h4 class="admin-name">Dr. Maria Santos</h4>
                    <span class="admin-role">DOT-Uni Dean</span>
                </div>
            </div>

            <div class="admin-card">
                <div class="admin-avatar">
                    <img src="{{ asset('assets/system_images/admin/placeholder.jpg') }}" alt="Administrator"
                         onerror="this.src='https://ui-avatars.com/api/?name=Program+Director&background=038303&color=fff&size=120'">
                </div>
                <div class="admin-info">
                    <h4 class="admin-name">Dr. Pedro Reyes</h4>
                    <span class="admin-role">Program Director</span>
                </div>
            </div>

            {{-- Add more admin cards as needed --}}

        </div>
    </div>
</div>
