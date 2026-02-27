    <!-- PAGE HEADER BAR -->
    <div class="container-fluid page-header">
      <div class="d-flex justify-content-between">
        <div class="header-contacts">
          <a href="mailto: dotuni@clsu.edu.ph " class="me-3 text-white"
            ><i class="fa-solid fa-envelope"></i>  dotuni@clsu.edu.ph </a
          >
          <a href="#" class="me-3 text-white"
            ><i class="fa-solid fa-phone"></i>  +63 912 345 6789 </a
          >
        </div>
        <div class="header-socials social-media">
          <a
            href="#"
            class="me-3 text-white"
            target="_blank"
            ><i class="fa-brands fa-facebook-f"></i
          ></a>
          <a href="#" class="me-3 text-white"><i class="fa-brands fa-twitter"></i></a>
          <a
            href="#"
            class="me-3 text-white"
            target="_blank"
            ><i class="fa-brands fa-youtube"></i
          ></a>
          <a href="#" class="text-white"><i class="fa-brands fa-instagram"></i></a>
        </div>
      </div>
    </div>

    <!-- NAVIGATION HEADER BAR -->
    <div class="container-fluid navbar-header justify-content-center">
      <div class="row justify-content-between">
        <div class="col-md-10 nav-header-college align-items-start">
          <div
            class="navbar-header-logo-container"
            id="navbar-header-logo-container"
          >
            <a href="index.html">
              <img
                src="{{ asset('assets/system_images/logo.png') }}"
                alt="DOTUNI Logo"
                class="img-fluid navbar-header-logo"
                style="width: 80px"
              />
            </a>
          </div>
          <div class="nav-header-left align-items-center">
            <div class="row nav-header-address">
              <h6>
                Institue of Graduate Studies
              </h6>
            </div>
           <div class="row nav-header-college">
              <h2> DISTANCE, OPEN, AND TRANSNATIONAL UNIVERSITY</h2>
            </div>
            <div class="row nav-header-address">
              <h6>
                Central Luzon State University, Science City of Muñoz, Nueva
                Ecija, 3120
              </h6>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- NAVIGATION BAR -->
    <nav
      class="navbar navbar-expand-lg navbar-dark position-relative sticky-top"
    >
      <div class="container">
        <div></div>
        <!-- Hamburger Menu -->
        <button
          id="navbar-toggler"
          class="navbar-toggler custom-toggler"
          type="button"
          data-bs-toggle="offcanvas"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="custom-toggler-icon"
            ><i class="bi bi-list fs-2"></i
          ></span>
        </button>

        <!-- Offcanvas Sidebar -->
        <div
          class="offcanvas offcanvas-end"
          tabindex="-1"
          id="navbarNav"
          aria-labelledby="navbarNavLabel"
          data-bs-backdrop="false"
        >
          <div class="offcanvas-header">
            <div class="row w-100">
              <div class="col-auto">
                <button
                  id="offcanvas-close"
                  type="button"
                  class="custom-close"
                  data-bs-dismiss="offcanvas"
                  aria-label="Close"
                  style="margin: 20px"
                >
                  ✖
                </button>
              </div>
              <div class="col justify-self-center" style="margin-left: 20px">
                <img
                  src="{{ asset('assets/system_images/logo.png') }}"
                  alt="DOTUNI Logo"
                  style="height: 120px"
                />
              </div>
              <div class="col"></div>
            </div>
          </div>

          <div class="offcanvas-body">
            <ul class="navbar-nav m-auto">
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('website.home') ? 'active' : '' }}" href="{{ route('website.home') }}">Home</a>
              </li>

              <!-- About -->
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('website.pages.about') ? 'active' : '' }}" href="{{ route('website.pages.about') }}">About Us</a>
              </li>

              <!-- Course -->
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('website.courses') ? 'active' : '' }}" href="{{ route('website.courses') }}">Course</a>
              </li>

              <!-- Gallery -->
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('website.gallery') ? 'active' : '' }}" href="{{ route('website.gallery') }}">Gallery</a>
              </li>

              <!-- News and Announcement -->
              <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('website.news') ? 'active' : '' }}" href="{{ route('website.news') }}">News and Announcement</a>
              </li>

              <!-- Admission dropdown -->
              <li class="nav-item dropdown">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  role="button"
                  data-bs-toggle="dropdown"
                  >Admissions</a
                >
                <ul class="dropdown-menu">
                  <li>
                    <a class="nav-link {{ request()->routeIs('website.admissionRequirements') ? 'active' : '' }}" href="{{ route('website.admissionRequirements') }}">Admission Requirements</a>
                  </li>
                  <li>
                    <a class="nav-link {{ request()->routeIs('website.fees') ? 'active' : '' }}" href="{{ route('website.fees') }}">Schedule of School Fees</a>
                  </li>
                  <li>
                    <a class="nav-link {{ request()->routeIs('website.pages.online_payment') ? 'active' : '' }}" href="{{ route('website.pages.online-payment') }}">Online Payment of School Fees</a>
                  </li>
                  <li>
                    <a class="nav-link {{ request()->routeIs('website.faqs') ? 'active' : '' }}" href="{{ route('website.faqs') }}">FAQs</a>
                  </li>
                </ul>
              </li>

              <!-- Student services dropdown -->
              <li class="nav-item dropdown">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  role="button"
                  data-bs-toggle="dropdown"
                  >Student Services</a
                >
                <ul class="dropdown-menu">
                  <li>
                    <a
                      class="dropdown-item"
                      href="{{ route('website.rules-and-regulations') }}"
                      >Rules and Regulation</a
                    >
                  </li>
                  <li>
                    <a class="dropdown-item" href="#"
                      >Academic Calendar</a
                    >
                  </li>
                  <li>
                    <a class="dropdown-item" href="#"
                      >Student Handbook</a
                    >
                  </li>
                  <li>
                    <a class="dropdown-item" href="{{ route('website.eresources') }}"
                      >eResources</a
                    >
                  </li>
                </ul>
              </li>

              <!-- Online Services -->
              <li class="nav-item dropdown">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  role="button"
                  data-bs-toggle="dropdown"
                  >Online Services</a
                >
                <ul class="dropdown-menu">
                  <li>
                      <a class="dropdown-item" 
                        href="https://cais.oad.clsu2.edu.ph/login"
                        target="_blank"
                        rel="noopener noreferrer">
                        Online Application
                      </a>
                  </li>

                  <li>
                      <a class="dropdown-item" 
                        href="https://clsu.turnitin.com/home/sign-in"
                        target="_blank"
                        rel="noopener noreferrer">
                        Turnitin
                      </a>
                  </li>
                </ul>
              </li>

              <!-- Downloads -->
              <li class="nav-item dropdown">
                <a
                  class="nav-link dropdown-toggle"
                  href="#"
                  role="button"
                  data-bs-toggle="dropdown"
                  >Downloads
                </a>
                <ul class="dropdown-menu">
                  <li class="dropdown-item">
                      <a href="{{ route('website.downloads', 'forms') }}" style="color: white;">
                          Forms
                      </a>
                  </li>

                  <li class="dropdown-item">
                      <a href="{{ route('website.downloads', 'course-prospectus') }}" style="color: white;">
                          Course Prospectus
                      </a>
                  </li>
                </ul>
              </li>

            </ul>
          </div>
        </div>
      </div>
    </nav>