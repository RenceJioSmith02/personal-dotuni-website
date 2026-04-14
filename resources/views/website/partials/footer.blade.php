    <!-- FOOTER -->
    <footer class="home-footer">

      <div class="footer-overlay"></div>

      <div class="footer-container">

        <!-- LEFT -->
        <div class="footer-col footer-about">
          <div class="footer-logo">
            <img src="{{ asset('assets/system_images/clsu.png') }}" alt="CLSU Logo">
            <img src="{{ asset('assets/system_images/logo.png') }}" alt="DOTUNI Logo">
          </div>
          <br>
          <h5 style="margin-bottom: 0.15rem !important;">Institute of Graduate Studies</h5>
          <h5 style="margin-bottom: 0.15rem !important;">Central Luzon State University</h5>
          <h3 style="margin-bottom: 0.15rem !important;">Distance, Open, And Transnational University (DOT-Uni)</h3>
          <br>
          <p class="follow-label">Follow us</p>
          <div class="footer-social">
            <a href="https://www.facebook.com/clsudotuni#" target="_blank"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="https://www.instagram.com/clsudotuni/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
            <a href="https://x.com/DOTUni1" target="_blank"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="mailto: dotuni@clsu.edu.ph" target="_blank"><i class="fa-solid fa-envelope"></i></a>
          </div>

        </div>

        <!-- CENTER -->
        <div class="footer-col" style="flex: 1.2">
          <h4>Quick Links</h4>
          <div class="footer-links-container">
            <ul class="footer-links">
            <li><a href="{{ route('website.faqs') }}">FAQs</a></li>
            <li><a href="{{ route('website.pages.about') }}">About</a></li>
            <li><a href="{{ route('website.downloads', 'forms') }}">Forms</a></li>
            <li><a href="{{ route('website.gallery') }}">Gallery</a></li>
            <li><a href="{{ route('website.home') }}#partners-section">Linkages</a></li>
            <li><a href="{{ route('website.admissionRequirements') }}">Info for Prospective Students</a></li>
            <li><a href="{{ route('website.courses') }}">Courses</a></li>
          </ul>
          <ul class="footer-links">
            <li><a href="{{ route('website.downloads', 'course-prospectus') }}">Course Prospectus</a></li>
            <li><a href="{{ route('website.news') }}">News and Announcement</a></li>
            <li><a href="https://cais.oad.clsu2.edu.ph/login" target="_blank">Online Application</a></li>
            <li><a href="{{ route('website.rules-and-regulations') }}">Rules and Regulations</a></li>
            <li><a href="{{ route('website.fees') }}">Schedule of School Fees </a></li>
            <li><a href="{{ route('website.admissionRequirements') }}#Addmission Requirements">Addmission Requirements</a></li>
            <li><a href="{{ route('website.pages.online-payment') }}">Online Payment of School Fees</a></li>
          </ul>
          </div>
        </div>

        <!-- RIGHT -->
        <div class="footer-col">
          <h4>Find Us</h4>

          <div class="footer-map">
            <iframe
                src="https://www.google.com/maps/embed/v1/directions?key=AIzaSyB2NIWI3Tv9iDPrlnowr_0ZqZWoAQydKJU&origin=CLSU%20Main%20Gate%2C%20University%20Avenue%2C%20Mu%C3%B1oz%2C%20Nueva%20Ecija%2C%20Philippines&destination=CLSU%20Distance%2C%20Open%2C%20and%20Transnational%20University%20(DOT-Uni)%2C%20Central%20Luzon%20State%20University%20(CLSU)%2C%20Milbuen%20Street%2C%20Mu%C3%B1oz%2C%20Nueva%20Ecija%2C%20Philippines&mode=driving&maptype=roadmap"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>          
          </div>

          <div class="footer-contact">
            <p>
              <i class="fa-solid fa-location-dot"></i>
              Milbuen St, CLSU Compound, Brgy. Bantug, Science City of Muñoz,
              3120 Nueva Ecija, Philippines
            </p>

            <p>
              <i class="fa-solid fa-phone"></i>
              +63 912 345 6789
            </p>

            <p>
              <i class="fa-solid fa-envelope"></i>
              dotuni@clsu.edu.ph
            </p>
          </div>

        </div>

      </div>

      <div class="footer-bottom">
        © 2026 CLSU Distance, Open, And Transnational University. All rights reserved.
      </div>
    </footer>